<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SendNotifications;
use App\Models\Group;
use App\Models\GroupUser;
use App\Models\Notification;
use App\Models\NotificationStatus;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use App\Models\Api\UserFirebaseSessions;
use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\MulticastSendReport;

class NotificationsController extends Controller
{
    public function index()
    {
        $this->authorize('admin_notifications_list');

        $notifications = Notification::where('user_id', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.notifications'),
            'notifications' => $notifications
        ];

        return view('admin.notifications.lists', $data);
    }

    public function posted()
    {
        $this->authorize('admin_notifications_posted_list');

        $notifications = Notification::where('sender', Notification::$AdminSender)
            ->orderBy('created_at', 'desc')
            ->with([
                'senderUser' => function ($query) {
                    $query->select('id', 'full_name');
                },
                'user' => function ($query) {
                    $query->select('id', 'full_name');
                },
                'notificationStatus'
            ])
            ->paginate(10);

        $data = [
            'pageTitle' => trans('admin/main.posted_notifications'),
            'notifications' => $notifications
        ];

        return view('admin.notifications.posted', $data);
    }

    public function create()
    {
        $this->authorize('admin_notifications_send');

        $userGroups = Group::all();

        $data = [
            'pageTitle' => trans('notification.send_notification'),
            'userGroups' => $userGroups
        ];

        return view('admin.notifications.send', $data);
    }

    public function store(Request $request)
    {
        $this->authorize('admin_notifications_send');

        $this->validate($request, [
            'title' => 'required|string',
            'type' => 'required|string',
            'user_id' => 'required_if:type,single',
            'group_id' => 'required_if:type,group',
            'webinar_id' => 'required_if:type,course_students',
            'message' => 'required|string',
        ]);

        $data = $request->all();

        $user_id = !empty($data['user_id']) ? $data['user_id'] : null;
        $group_id = !empty($data['group_id']) ? $data['group_id'] : null;
        $webinar_id = !empty($data['webinar_id']) ? $data['webinar_id'] : null;

        Notification::create([
            'user_id' => $user_id,
            'group_id' => $group_id,
            'webinar_id' => $webinar_id,
            'sender_id' => auth()->id(),
            'title' => $data['title'],
            'message' => $data['message'],
            'sender' => Notification::$AdminSender,
            'type' => $data['type'],
            'created_at' => time()
        ]);


        //if (!empty($user_id) and env('APP_ENV') == 'production') {
        $user = \App\User::where('id', $user_id)->first();

        if (!empty($user) and !empty($user->email)) {
            try {
                \Mail::to($user->email)->send(new SendNotifications(['title' => $data['title'], 'message' => $data['message']]));
            } catch (\Exception $exception) {
                // dd($exception)
            }
        }

        // Firebase Messages
        $this->handleFirebaseMessages($data, $user_id, $group_id, $webinar_id);
        //}


        return redirect(getAdminPanelUrl() . '/notifications/posted');
    }

    private function handleFirebaseMessages($data, $user_id, $group_id, $webinar_id)
    {
        $fcmTokensQuery = UserFirebaseSessions::query();

        if ($data["type"] === "single") {
            if (empty($user_id)) {
                return true;
            }

            $fcmTokensQuery->where('user_id', $user_id);
        }

        if ($data["type"] === "all_users") {
            /**/
        }

        if ($data["type"] === "students") {
            $usersIds = User::query()->where("role_id", Role::getUserRoleId())
                ->pluck("id")->toArray();

            $fcmTokensQuery->whereIn('user_id', $usersIds);
        }

        if ($data["type"] === "instructors") {
            $usersIds = User::query()->where("role_id", Role::getTeacherRoleId())
                ->pluck("id")->toArray();

            $fcmTokensQuery->whereIn('user_id', $usersIds);
        }

        if ($data["type"] === "organizations") {
            $usersIds = User::query()->where("role_id", Role::getOrganizationRoleId())
                ->pluck("id")->toArray();

            $fcmTokensQuery->whereIn('user_id', $usersIds);
        }

        if ($data["type"] === "group") {
            if (empty($group_id)) {
                return true;
            }

            $usersIds = GroupUser::where("group_id", $group_id)
                ->pluck("user_id")->toArray();

            $fcmTokensQuery->whereIn('user_id', $usersIds);
        }

        if ($data["type"] === "course_students") {
            if (empty($webinar_id)) {
                return true;
            }

            $usersIds = Sale::where('webinar_id', $webinar_id)
                ->whereNull('refund_at')
                ->pluck('buyer_id')
                ->toArray();

            $fcmTokensQuery->whereIn('user_id', $usersIds);
        }

        $fcmTokensQuery->orderBy('created_at', 'desc');

        $fcmTokens = $fcmTokensQuery->get();
        $deviceTokens = [];

        foreach ($fcmTokens as $fcmToken) {
            if ($fcmToken->fcm_token && strlen($fcmToken->fcm_token) > 0) {
                $deviceTokens[] = $fcmToken->fcm_token;
            }
        }

        if (count($deviceTokens) > 0) {
            $messageFCM = app('firebase.messaging');

            foreach ($deviceTokens as $fcmToken) {
                $fcmMessage = CloudMessage::new();
                $fcmMessage = $fcmMessage->withChangedTarget("token", $fcmToken);
                $fcmMessage = $fcmMessage->withData([
                    'user_id' => $user_id,
                    'group_id' => $group_id,
                    'webinar_id' => $webinar_id,
                    'sender_id' => auth()->id(),
                    'title' => $data['title'],
                    'message' => preg_replace('/<[^>]*>/', '', $data['message']),
                    'sender' => Notification::$AdminSender,
                    'type' => $data['type'],
                    'created_at' => time()
                ]);

                $fcmMessage = $fcmMessage->withNotification(\Kreait\Firebase\Messaging\Notification::create($data["title"], preg_replace('/<[^>]*>/', '', $data["message"])));

                $fcmMessage = $fcmMessage->withAndroidConfig(\Kreait\Firebase\Messaging\AndroidConfig::fromArray([
                    'ttl' => '3600s',
                    'priority' => 'high',
                    'notification' => [
                        'color' => '#f45342',
                        'sound' => 'default',
                    ],
                ]));

                try {
                    $messageFCM->send($fcmMessage);
                } catch (\Exception $exception) {
                    //dd($exception);
                }
            }
        }
    }

    public function edit($id)
    {
        $this->authorize('admin_notifications_edit');

        $notification = Notification::where('id', $id)
            ->with([
                'user' => function ($query) {
                    $query->select('id', 'full_name');
                },
                'group'
            ])->first();

        if (!empty($notification)) {
            $userGroups = Group::all();

            $data = [
                'pageTitle' => trans('notification.edit_notification'),
                'userGroups' => $userGroups,
                'notification' => $notification
            ];

            return view('admin.notifications.send', $data);
        }

        abort(404);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('admin_notifications_edit');

        $this->validate($request, [
            'title' => 'required|string',
            'type' => 'required|string',
            'user_id' => 'required_if:type,single',
            'group_id' => 'required_if:type,group',
            'webinar_id' => 'required_if:type,course_students',
            'message' => 'required|string',
        ]);

        $data = $request->all();

        $notification = Notification::findOrFail($id);

        $notification->update([
            'user_id' => !empty($data['user_id']) ? $data['user_id'] : null,
            'group_id' => !empty($data['group_id']) ? $data['group_id'] : null,
            'webinar_id' => !empty($data['webinar_id']) ? $data['webinar_id'] : null,
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'],
            'created_at' => time()
        ]);

        return redirect(getAdminPanelUrl() . '/notifications');
    }

    public function delete($id)
    {
        $this->authorize('admin_notifications_delete');

        $notification = Notification::findOrFail($id);

        $notification->delete();

        return redirect(getAdminPanelUrl() . '/notifications');
    }

    public function markAllRead()
    {
        $this->authorize('admin_notifications_markAllRead');

        $adminUser = User::getMainAdmin();

        if (!empty($adminUser)) {
            $unreadNotifications = $adminUser->getUnReadNotifications();

            if (!empty($unreadNotifications) and !$unreadNotifications->isEmpty()) {
                foreach ($unreadNotifications as $unreadNotification) {
                    NotificationStatus::updateOrCreate(
                        [
                            'user_id' => $adminUser->id,
                            'notification_id' => $unreadNotification->id,
                        ],
                        [
                            'seen_at' => time()
                        ]
                    );
                }
            }
        }

        return back();
    }

    public function markAsRead($id)
    {
        $this->authorize('admin_notifications_edit');

        $adminUser = User::getMainAdmin();

        if (!empty($adminUser)) {
            NotificationStatus::updateOrCreate(
                [
                    'user_id' => $adminUser->id,
                    'notification_id' => $id,
                ],
                [
                    'seen_at' => time()
                ]
            );
        }

        return response()->json([], 200);
    }
}
