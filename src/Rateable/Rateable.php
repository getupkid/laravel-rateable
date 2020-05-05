<?php namespace willvincent\Rateable;

trait Rateable
{
    /**
     * This model has many ratings.
     *
     * @return Rating
     */
    public function ratings()
    {
        return $this->morphMany(config('ratings.model'), 'rateable');
    }
    
    /**
     * Add a rating to the model
     *
     * @return Rating
     */
    public function rate($value)
    {
        if (auth()->check()) {
            $model = config('ratings.model');
            $rating = new $model;
            $rating->rating = $value;
            $rating->user_id = auth()->user()->id;
            $this->ratings()->save($rating);
        }
    }

    /**
     * Only rate the model once / updates the rating if it is different
     *
     * @return Rating
     */
    public function rateSingle($value)
    {
        if (auth()->check()) {
            $model = config('ratings.model');
            $rating = $model::firstOrNew([
                'rateable_type' => $this->getMorphClass(),
                'rateable_id' => $this->id,
                'user_id' => auth()->user()->id
            ]);
            $rating->rating = $value;
            $this->ratings()->save($rating);
        }
    }

    public function averageRating()
    {
        return $this->ratings()->avg('rating');
    }

    public function sumRating()
    {
        return $this->ratings()->sum('rating');
    }

    public function userAverageRating()
    {
        return $this->ratings()->where('user_id', \Auth::id())->avg('rating');
    }

    public function userSumRating()
    {
        return $this->ratings()->where('user_id', \Auth::id())->sum('rating');
    }

    public function ratingPercent($max = 5)
    {
        $quantity = $this->ratings()->count();
        $total = $this->sumRating();

        return ($quantity * $max) > 0 ? $total / (($quantity * $max) / 100) : 0;
    }

    public function getAverageRatingAttribute()
    {
        return $this->averageRating();
    }

    public function getSumRatingAttribute()
    {
        return $this->sumRating();
    }

    public function getUserAverageRatingAttribute()
    {
        return $this->userAverageRating();
    }

    public function getUserSumRatingAttribute()
    {
        return $this->userSumRating();
    }
}
