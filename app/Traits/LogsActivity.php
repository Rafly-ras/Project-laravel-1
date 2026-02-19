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
        $changes = null;

        if ($event === 'updated') {
            $changes = [
                'old' => array_intersect_key($this->getOriginal(), $this->getDirty()),
                'new' => $this->getDirty(),
            ];
            
            // Remove sensitive fields or timestamps if needed
            unset($changes['old']['updated_at'], $changes['new']['updated_at']);
            
            if (empty($changes['new'])) {
                return; // No meaningful changes
            }
        }

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $event,
            'model_type' => get_class($this),
            'model_id' => $this->id,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => request()->ip(),
        ]);
    }

    protected function getActivityDescription($event)
    {
        $name = class_basename($this);
        return "{$name} was {$event}";
    }
}
