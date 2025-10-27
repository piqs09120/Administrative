<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SystemNotificationService
{
    /**
     * Send notification for document actions
     */
    public static function notifyDocumentAction($action, $document, $user = null)
    {
        try {
            $usersToNotify = self::getUsersToNotify('document');
            
            foreach ($usersToNotify as $notifyUser) {
                $notifyUser->notify(new \App\Notifications\SystemActionNotification([
                    'title' => ucfirst($action) . ' Document',
                    'message' => "Document '{$document->title}' has been {$action}",
                    'type' => $action === 'deleted' ? 'error' : ($action === 'approved' ? 'success' : 'info'),
                    'action' => $action,
                    'model_type' => 'document',
                    'model_id' => $document->id
                ]));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send document notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send notification for visitor actions
     */
    public static function notifyVisitorAction($action, $data, $user = null)
    {
        try {
            $usersToNotify = self::getUsersToNotify('visitor');
            
            // Handle both visitor objects and data objects
            $visitorName = isset($data->name) ? $data->name : 'Unknown Visitor';
            $modelId = isset($data->id) ? $data->id : null;
            
            foreach ($usersToNotify as $notifyUser) {
                $notifyUser->notify(new \App\Notifications\SystemActionNotification([
                    'title' => ucfirst($action) . ' Visitor',
                    'message' => "Visitor '{$visitorName}' has been {$action}",
                    'type' => $action === 'deleted' ? 'error' : ($action === 'checked_out' ? 'warning' : 'info'),
                    'action' => $action,
                    'model_type' => 'visitor',
                    'model_id' => $modelId
                ]));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send visitor notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send notification for facility reservation actions
     */
    public static function notifyFacilityReservationAction($action, $data, $user = null)
    {
        try {
            \Log::info('NOTIFICATION CALLED - notifyFacilityReservationAction', [
                'action' => $action,
                'data' => $data
            ]);
            
            $usersToNotify = self::getUsersToNotify('facility');
            
            \Log::info('Users to notify count', ['count' => $usersToNotify->count()]);
            
            // Handle both reservation objects and facility data objects
            $facilityName = isset($data->facility) ? $data->facility->name : 'Unknown Facility';
            $modelId = isset($data->id) ? $data->id : null;
            
            foreach ($usersToNotify as $notifyUser) {
                \Log::info('Sending notification to user', [
                    'user_id' => $notifyUser->id,
                    'user_name' => $notifyUser->name,
                    'facility_name' => $facilityName
                ]);
                
                $notifyUser->notify(new \App\Notifications\SystemActionNotification([
                    'title' => ucfirst($action) . ' Facility',
                    'message' => "Facility '{$facilityName}' has been {$action}",
                    'type' => $action === 'deleted' ? 'error' : ($action === 'created' ? 'success' : 'info'),
                    'action' => $action,
                    'model_type' => 'facility',
                    'model_id' => $modelId
                ]));
                
                \Log::info('Notification sent successfully to user', ['user_id' => $notifyUser->id]);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send facility notification', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Send notification for legal case actions
     */
    public static function notifyLegalCaseAction($action, $data, $user = null)
    {
        try {
            $usersToNotify = self::getUsersToNotify('legal');
            
            // Handle both case objects and data objects
            $caseTitle = isset($data->case_title) ? $data->case_title : 'Unknown Case';
            $modelId = isset($data->id) ? $data->id : null;
            
            foreach ($usersToNotify as $notifyUser) {
                $notifyUser->notify(new \App\Notifications\SystemActionNotification([
                    'title' => ucfirst($action) . ' Legal Case',
                    'message' => "Legal case '{$caseTitle}' has been {$action}",
                    'type' => $action === 'deleted' || $action === 'rejected' ? 'error' : ($action === 'closed' ? 'warning' : 'success'),
                    'action' => $action,
                    'model_type' => 'legal_case',
                    'model_id' => $modelId
                ]));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send legal case notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send notification for user account actions
     */
    public static function notifyUserAction($action, $targetUser, $user = null)
    {
        try {
            $usersToNotify = self::getUsersToNotify('user');
            
            foreach ($usersToNotify as $notifyUser) {
                $notifyUser->notify(new \App\Notifications\SystemActionNotification([
                    'title' => ucfirst($action) . ' User',
                    'message' => "User account '{$targetUser->name}' has been {$action}",
                    'type' => $action === 'deleted' || $action === 'suspended' ? 'error' : 'success',
                    'action' => $action,
                    'model_type' => 'user',
                    'model_id' => $targetUser->id
                ]));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send user notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Send notification for system events
     */
    public static function notifySystemEvent($title, $message, $type = 'info', $usersToNotify = null)
    {
        try {
            if ($usersToNotify === null) {
                $usersToNotify = self::getUsersToNotify('admin');
            }
            
            foreach ($usersToNotify as $notifyUser) {
                $notifyUser->notify(new \App\Notifications\SystemActionNotification([
                    'title' => $title,
                    'message' => $message,
                    'type' => $type,
                    'action' => 'system_event',
                    'model_type' => 'system',
                    'model_id' => null
                ]));
            }
        } catch (\Exception $e) {
            Log::error('Failed to send system event notification', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get users to notify based on module type
     */
    private static function getUsersToNotify($module)
    {
        $query = User::query();
        
        switch ($module) {
            case 'document':
                $query->where(function($q) {
                    $q->whereRaw('LOWER(role) IN (?)', ['admin', 'super admin', 'administrator', 'super_admin', 'legal_admin', 'legal officer']);
                })->orWhere('department', 'legal');
                break;
                
            case 'visitor':
                $query->where(function($q) {
                    $q->whereRaw('LOWER(role) IN (?)', ['admin', 'super admin', 'administrator', 'super_admin', 'security']);
                })->orWhere('department', 'security');
                break;
                
            case 'facility':
                $query->where(function($q) {
                    $q->whereRaw('LOWER(role) IN (?)', ['admin', 'super admin', 'administrator', 'super_admin']);
                });
                break;
                
            case 'legal':
                $query->where(function($q) {
                    $q->whereRaw('LOWER(role) IN (?)', ['admin', 'super admin', 'administrator', 'super_admin', 'legal_admin', 'legal officer']);
                })->orWhere('department', 'legal');
                break;
                
            case 'user':
                $query->where(function($q) {
                    $q->whereRaw('LOWER(role) IN (?)', ['admin', 'super admin', 'administrator', 'super_admin']);
                });
                break;
                
            case 'admin':
            default:
                $query->where(function($q) {
                    $q->whereRaw('LOWER(role) IN (?)', ['admin', 'super admin', 'administrator', 'super_admin']);
                });
                break;
        }
        
        \Log::info('getUsersToNotify query result', ['count' => $query->count()]);
        
        return $query->get();
    }
}

