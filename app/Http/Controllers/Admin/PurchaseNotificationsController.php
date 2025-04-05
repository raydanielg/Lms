<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Group;
use App\Models\Product;
use App\Models\PurchaseNotification;
use App\Models\PurchaseNotificationRoleGroupContent;
use App\Models\Role;
use App\Models\Translation\PurchaseNotificationTranslation;
use App\Models\Webinar;
use Illuminate\Http\Request;

class PurchaseNotificationsController extends Controller
{

    public function index()
    {
        $this->authorize("admin_purchase_notifications_lists");

        $notifications = PurchaseNotification::query()
            ->withCount(['webinars', 'bundles', 'products'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $data = [
            'pageTitle' => trans('update.purchase_notifications'),
            'notifications' => $notifications,
        ];

        return view('admin.purchase_notifications.lists.index', $data);
    }


    public function create()
    {
        $this->authorize("admin_purchase_notifications_create");

        $roles = Role::query()->get();
        $userGroups = Group::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'pageTitle' => trans('update.new_notification'),
            'roles' => $roles,
            'userGroups' => $userGroups,
        ];

        return view('admin.purchase_notifications.create.index', $data);
    }

    public function store(Request $request)
    {
        $this->authorize("admin_purchase_notifications_create");

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'popup_duration' => 'required|numeric',
            'popup_delay' => 'required|numeric',
            'maximum_purchase_amount' => 'nullable|numeric',
            'maximum_community_age' => 'nullable|numeric',
            'display_time' => 'required|numeric',
            'users' => 'required|string',
            'times' => 'required|string',
            'contents' => 'required|array',
            'popup_title' => 'required|string',
            'popup_subtitle' => 'required|string',
        ]);

        $storeData = $this->handleStoreData($request);
        $notification = PurchaseNotification::query()->create($storeData);

        // Store Extra Data
        $this->handleStoreExtraData($request, $notification);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.purchase_notification_created_successfully'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/purchase_notifications/{$notification->id}/edit"))->with(['toast' => $toastData]);
    }


    public function edit(Request $request, $id)
    {
        $this->authorize("admin_purchase_notifications_edit");
        $notification = PurchaseNotification::query()->findOrFail($id);

        $locale = $request->get('locale', app()->getLocale());
        storeContentLocale($locale, $notification->getTable(), $notification->id);

        $roles = Role::query()->get();
        $userGroups = Group::where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();

        $data = [
            'pageTitle' => trans('update.edit_notification') . ' ' . $notification->title,
            'notification' => $notification,
            'locale' => $locale,
            'roles' => $roles,
            'userGroups' => $userGroups,
        ];

        return view('admin.purchase_notifications.create.index', $data);
    }

    public function update(Request $request, $id)
    {
        $this->authorize("admin_purchase_notifications_edit");
        $notification = PurchaseNotification::query()->findOrFail($id);

        $this->validate($request, [
            'title' => 'required|string|max:255',
            'popup_duration' => 'required|numeric',
            'popup_delay' => 'required|numeric',
            'maximum_purchase_amount' => 'nullable|numeric',
            'maximum_community_age' => 'nullable|numeric',
            'display_time' => 'required|numeric',
            'users' => 'required|string',
            'times' => 'required|string',
            'contents' => 'required|array',
            'popup_title' => 'required|string',
            'popup_subtitle' => 'required|string',
        ]);

        $storeData = $this->handleStoreData($request, $notification);
        $notification->update($storeData);

        // Store Extra Data
        $this->handleStoreExtraData($request, $notification);

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.purchase_notification_updated_successfully'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/purchase_notifications/{$notification->id}/edit"))->with(['toast' => $toastData]);
    }

    public function delete($id)
    {
        $this->authorize("admin_purchase_notifications_delete");

        $notification = PurchaseNotification::query()->findOrFail($id);
        $notification->delete();

        $toastData = [
            'title' => trans('public.request_success'),
            'msg' => trans('update.purchase_notification_deleted_successfully'),
            'status' => 'success'
        ];

        return redirect(getAdminPanelUrl("/purchase_notifications"))->with(['toast' => $toastData]);
    }

    public function searchContents(Request $request)
    {
        $this->authorize("admin_purchase_notifications_create");

        $term = $request->get('term');


        $items['webinar'] = Webinar::select('id')
            ->whereTranslationLike('title', "%$term%")
            ->get();

        $items['bundle'] = Bundle::select('id')
            ->whereTranslationLike('title', "%$term%")
            ->get();

        $items['product'] = Product::select('id')
            ->whereTranslationLike('title', "%$term%")
            ->get();

        $data = [];

        foreach ($items as $type => $collection) {
            foreach ($collection as $item) {
                $data[] = [
                    'id' => $item->id,
                    'title' => $item->title . ' - ' . trans("update.{$type}"),
                    'type' => $type
                ];
            }
        }

        return response()->json($data, 200);
    }


    private function handleStoreData(Request $request, $notification = null)
    {
        $data = $request->all();
        $startDate = !empty($data['start_at']) ? convertTimeToUTCzone($data['start_at'], getTimezone())->getTimestamp() : null;
        $endDate = !empty($data['end_at']) ? convertTimeToUTCzone($data['end_at'], getTimezone())->getTimestamp() : null;


        return [
            'start_at' => $startDate,
            'end_at' => $endDate,
            'popup_duration' => !empty($data['popup_duration']) ? $data['popup_duration'] : null,
            'popup_delay' => !empty($data['popup_delay']) ? $data['popup_delay'] : null,
            'maximum_purchase_amount' => !empty($data['maximum_purchase_amount']) ? convertPriceToDefaultCurrency($data['maximum_purchase_amount']) : null,
            'maximum_community_age' => !empty($data['maximum_community_age']) ? $data['maximum_community_age'] : null,
            'display_type' => !empty($data['display_type']) ? $data['display_type'] : null,
            'display_time' => !empty($data['display_time']) ? $data['display_time'] : null,
            'display_for_logged_out_users' => (!empty($data['display_for_logged_out_users']) and $data['display_for_logged_out_users'] == "on"),
            'enable' => (!empty($data['enable']) and $data['enable'] == "on"),
            'created_at' => !empty($notification) ? $notification->created_at : time(),
        ];
    }

    private function handleStoreExtraData(Request $request, $notification)
    {
        $data = $request->all();

        PurchaseNotificationTranslation::query()->updateOrCreate([
            'purchase_notification_id' => $notification->id,
            'locale' => mb_strtolower($data['locale']),
        ], [
            'title' => $data['title'],
            'popup_title' => $data['popup_title'] ?? null,
            'popup_subtitle' => $data['popup_subtitle'] ?? null,
            'users' => !empty($data['users']) ? $data['users'] : null,
            'times' => !empty($data['times']) ? $data['times'] : null,
        ]);

        // Contents
        PurchaseNotificationRoleGroupContent::query()->where('purchase_notification_id', $notification->id)->delete();


        if (!empty($data['role_id'])) {
            $insert = [];

            foreach ($data['role_id'] as $roleId) {
                $insert[] = [
                    'purchase_notification_id' => $notification->id,
                    'role_id' => $roleId,
                ];
            }

            if (!empty($insert)) {
                PurchaseNotificationRoleGroupContent::query()->insert($insert);
            }
        }

        if (!empty($data['group_id'])) {
            $insert = [];

            foreach ($data['group_id'] as $groupId) {
                $insert[] = [
                    'purchase_notification_id' => $notification->id,
                    'group_id' => $groupId,
                ];
            }

            if (!empty($insert)) {
                PurchaseNotificationRoleGroupContent::query()->insert($insert);
            }
        }

        if (!empty($data['contents'])) {

            foreach ($data['contents'] as $content) {
                $content = explode('_', $content);
                $itemId = $content[0];
                $itemColumn = "{$content[1]}_id";

                $insert = [
                    'purchase_notification_id' => $notification->id,
                    "{$itemColumn}" => $itemId,
                ];

                PurchaseNotificationRoleGroupContent::query()->create($insert);
            }
        }

    }

}
