<?php

namespace App;
use Tymon\JWTAuth\Facades\JWTAuth;

trait RecordsActivityTrait
{
    /**
     * Boot the trait.
     */
    protected static function bootRecordsActivityTrait()
    {
        if (!JWTAuth::getToken()) return;
        foreach (static::getActivitiesToRecord() as $event) {
            static::$event(function ($model) use ($event) {
                $model->recordActivity($event);
            });
        }

        // static::deleting(function ($model) {
        //     $model->activity()->delete();
        // });
    }

    /**
     * Fetch all model events that require activity recording.
     *
     * @return array
     */
    protected static function getActivitiesToRecord()
    {
        return ['created','updated','deleted'];
    }

    /**
     * Record new activity for the model.
     *
     * @param string $event
     */
    protected function recordActivity($event)
    {
        $this->activity()->create([
            'user_id' => JWTAuth::toUser(JWTAuth::getToken())->id,
            'type' => $this->getActivityType($event)
        ]);
    }
 
    /**
     * Fetch the activity relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function activity()
    {
        return $this->morphMany('App\Activity', 'subject');
    }

    /**
     * Determine the activity type.
     *
     * @param  string $event
     * @return string
     */
    protected function getActivityType($event)
    {
        $type = strtolower((new \ReflectionClass($this))->getShortName());

        return "{$event}_{$type}";
    }
}
