<?php

namespace App;

trait RecordsActivity
{
    protected static function bootRecordsActivity()
    {
        // static::created(function($thread) {
        //     $thread->recordActivity('created');
        // });
        
        foreach (self::getActivitiesToRecord() as $event) {
            static::$event(function($model) use ($event) {
                $model->recordActivity($event);
            });
        }
    }

    protected static function getActivitiesToRecord()
    {
        // 增加需要记录的操作只需要增加数组
        return ['created'];
    }

    protected function recordActivity($event)
    {
        if (auth()->guest()) {
            return;
        }
        $this->activity()->create([
            'type' => $this->getActivityType($event),
            'user_id' => auth()->id(),
            // 'subject_id' => $this->id,
            // 'subject_type' => 'App\Thread'
        ]);
    }

    protected function activity()
    {
        return $this->morphMany('App\Activity', 'subject');
    }

    protected function getActivityType($event)
    {
        return $event . '_' . strtolower((new \ReflectionClass($this))->getShortName());
    }
}