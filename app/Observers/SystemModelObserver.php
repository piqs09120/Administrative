<?php

namespace App\Observers;

use Illuminate\Support\Facades\Log;
use App\Services\SystemNotificationService;
use App\Models\User;

class SystemModelObserver
{
    public function created($model)
    {
        $this->sendNotification('created', $model);
    }

    public function updated($model)
    {
        $this->sendNotification('updated', $model);
    }

    public function deleted($model)
    {
        $this->sendNotification('deleted', $model);
    }

    protected function sendNotification($action, $model)
    {
        try {
            $modelClass = get_class($model);
            
            // Only send notifications for important models
            $importantModels = [
                'App\\Models\\Document',
                'App\\Models\\Visitor',
                'App\\Models\\FacilityReservation',
                'App\\Models\\LegalCase',
                'App\\Models\\LegalDocumentSubmission',
            ];

            if (!in_array($modelClass, $importantModels)) {
                return;
            }

            // Determine which users to notify
            $adminUsers = User::whereIn('role', ['admin', 'super_admin'])->get();
            
            // Send notification based on model type
            foreach ($adminUsers as $user) {
                $notification = new \App\Notifications\SystemActionNotification([
                    'title' => $this->getNotificationTitle($modelClass, $action),
                    'message' => $this->getNotificationMessage($model, $action),
                    'type' => $this->getNotificationType($action),
                    'action' => $action,
                    'model_type' => class_basename($modelClass),
                    'model_id' => $model->id
                ]);
                
                $user->notify($notification);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send system notification', [
                'error' => $e->getMessage(),
                'model' => get_class($model),
                'action' => $action
            ]);
        }
    }

    protected function getNotificationTitle($modelClass, $action)
    {
        $modelName = class_basename($modelClass);
        $actionText = ucfirst($action);
        
        return "{$actionText} {$modelName}";
    }

    protected function getNotificationMessage($model, $action)
    {
        $modelName = class_basename(get_class($model));
        $identifier = $this->getModelIdentifier($model);
        
        return "{$modelName} '{$identifier}' has been {$action}";
    }

    protected function getNotificationType($action)
    {
        return match($action) {
            'created' => 'success',
            'updated' => 'info',
            'deleted' => 'error',
            default => 'info'
        };
    }

    protected function getModelIdentifier($model)
    {
        // Try to get a meaningful identifier for the model
        if (isset($model->title)) {
            return $model->title;
        } elseif (isset($model->name)) {
            return $model->name;
        } elseif (isset($model->case_title)) {
            return $model->case_title;
        } elseif (isset($model->facility_name)) {
            return $model->facility_name;
        } else {
            return 'ID: ' . $model->id;
        }
    }
}

