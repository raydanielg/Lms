<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationStatus;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    public function index()
    {
        $this->authorize("panel_notifications_lists");

        $user = auth()->user();

        $notifications = Notification::where(function ($query) use ($user) {
            $query->where('notifications.user_id', $user->id)
                ->where('notifications.type', 'single');
        })->orWhere(function ($query) use ($user) {
            if (!$user->isAdmin()) {
                $query->whereNull('notifications.user_id')
                    ->whereNull('notifications.group_id')
                    ->where('notifications.type', 'all_users');
            }
        });

        $userGroup = $user->userGroup()->first();
        if (!empty($userGroup)) {
            $notifications->orWhere(function ($query) use ($userGroup) {
                $query->where('notifications.group_id', $userGroup->group_id)
                    ->where('notifications.type', 'group');
            });
        }

        $notifications->orWhere(function ($query) use ($user) {
            $query->whereNull('notifications.user_id')
                ->whereNull('notifications.group_id')
                ->where(function ($query) use ($user) {
                    if ($user->isUser()) {
                        $query->where('notifications.type', 'students');
                    } elseif ($user->isTeacher()) {
                        $query->where('notifications.type', 'instructors');
                    } elseif ($user->isOrganization()) {
                        $query->where('notifications.type', 'organizations');
                    }
                });
        });

        /* Get Course Students Notifications */
        $userBoughtWebinarsIds = $user->getAllPurchasedWebinarsIds();

        if (!empty($userBoughtWebinarsIds)) {
            $notifications->orWhere(function ($query) use ($userBoughtWebinarsIds) {
                $query->whereIn('webinar_id', $userBoughtWebinarsIds)
                    ->where('type', 'course_students');
            });
        }

        $notifications = $notifications->leftJoin('notifications_status', 'notifications.id', '=', 'notifications_status.notification_id')
            ->selectRaw('notifications.*, count(notifications_status.notification_id) AS `count`')
            ->with(['notificationStatus'])
            ->groupBy('notifications.id')
            ->orderBy('count', 'asc')
            ->orderBy('notifications.created_at', 'DESC')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('panel.notifications'),
            'notifications' => $notifications
        ];

        return view(getTemplate() . '.panel.notifications.index', $data);
    }

    public function saveStatus($id)
    {
        $user = auth()->user();

        $unReadNotifications = $user->getUnReadNotifications();

        if (!empty($unReadNotifications) and !$unReadNotifications->isEmpty()) {
            $notification = $unReadNotifications->where('id', $id)->first();

            if (!empty($notification)) {
                $status = NotificationStatus::where('user_id', $user->id)
                    ->where('notification_id', $notification->id)
                    ->first();

                if (empty($status)) {
                    NotificationStatus::create([
                        'user_id' => $user->id,
                        'notification_id' => $notification->id,
                        'seen_at' => time()
                    ]);
                }
            }
        }

        return response()->json([], 200);
    }

    public function markAllAsRead()
    {
        $user = auth()->user();

        if (!empty($user)) {
            $unReadNotifications = $user->getUnReadNotifications();

            if (!empty($unReadNotifications) and !$unReadNotifications->isEmpty()) {
                foreach ($unReadNotifications as $notification) {
                    $status = NotificationStatus::where('user_id', $user->id)
                        ->where('notification_id', $notification->id)
                        ->first();

                    if (empty($status)) {
                        NotificationStatus::create([
                            'user_id' => $user->id,
                            'notification_id' => $notification->id,
                            'seen_at' => time()
                        ]);
                    }
                }
            }
        }

        return response()->json([
            'code' => 200,
            'title' => trans('public.request_success'),
            'text' => trans('update.all_your_notifications_have_been_marked_as_read'),
            'timeout' => 2000
        ]);
    }
}
