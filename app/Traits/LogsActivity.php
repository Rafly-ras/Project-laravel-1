<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    protected static function bootLogsActivity()
    {
        foreach (static::getRecordEvents() as $event) {
            static::$event(function ($model) use ($event) {
                $model->logActivity($event);
            });
        }
    }

    protected static function getRecordEvents()
    {
        return ['created', 'updated', 'deleted'];
    }

    public function logActivity($event)
    {
        $description = $this->getActivityDescription($event);
        $beforeData = null;
        $afterData = null;

        if ($event === 'updated') {
            $changedFields = array_keys($this->getDirty());
            $beforeData = array_intersect_key($this->getOriginal(), array_flip($changedFields));
            $afterData = $this->getDirty();
            
            // Remove timestamps from comparison
            unset($beforeData['updated_at'], $afterData['updated_at']);
            
            if (empty($afterData)) {
                return; // No meaningful changes
            }
        } elseif ($event === 'created') {
            $afterData = $this->getAttributes();
            unset($afterData['created_at'], $afterData['updated_at']);
        } elseif ($event === 'deleted') {
            $beforeData = $this->getOriginal();
            unset($beforeData['created_at'], $beforeData['updated_at']);
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $event,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'description' => $description,
            'before_data' => $beforeData,
            'after_data' => $afterData,
            'ip_address' => request()->ip(),
        ]);
    }

    protected function getActivityDescription($event)
    {
        $name = class_basename($this);
        return "{$name} was {$event}";
    }
}
