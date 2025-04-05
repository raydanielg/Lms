<?php

namespace App\Mixins\PurchaseNotifications;

use App\Models\PurchaseNotification;
use App\Models\PurchaseNotificationHistory;
use App\Models\PurchaseNotificationRoleGroupContent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;

class PurchaseNotificationsHelper
{

    public function getDisplayableNotifications(): Collection
    {
        $result = collect();
        $notifications = $this->getNotifications();

        if ($notifications->isNotEmpty()) {
            foreach ($notifications as $notification) {
                $show = $this->checkShowNotification($notification);

                if ($show) {
                    $result->add($this->makeNotificationInfo($notification));
                }
            }
        }

        return $result;
    }

    private function getNotifications(): Collection
    {
        $user = auth()->user();
        $group = !empty($user) ? $user->getUserGroup() : null;
        $groupId = !empty($group) ? $group->id : null;
        $roleId = !empty($user) ? $user->role_id : null;
        $time = time();

        $query = PurchaseNotification::query()->where('enable', true)
            ->where(function (Builder $query) use ($time) {
                $query->whereNull('start_at');
                $query->orWhere('start_at', '<', $time);
            })
            ->where(function (Builder $query) use ($time) {
                $query->whereNull('end_at');
                $query->orWhere('end_at', '>', $time);
            });


        if (!empty($groupId)) {
            $query->where(function (Builder $query) use ($groupId) {
                $query->whereDoesntHave('allRelatives', function (Builder $query) {
                    $query->whereNotNull('group_id');
                });

                $query->orWhereHas('allRelatives', function ($query) use ($groupId) {
                    $query->where('group_id', $groupId);
                });
            });
        }

        if (!empty($roleId)) {
            $query->where(function (Builder $query) use ($roleId) {
                $query->whereDoesntHave('allRelatives', function (Builder $query) {
                    $query->whereNotNull('role_id');
                });

                $query->orWhereHas('allRelatives', function ($query) use ($roleId) {
                    $query->where('role_id', $roleId);
                });
            });
        }

        return $query->get();
    }

    private function checkShowNotification(PurchaseNotification $notification): bool
    {
        $show = false;

        $user = auth()->user();
        $displayType = $notification->display_type;
        $displayTime = $notification->display_time;

        if (!empty($user)) {
            $show = $this->checkHistoryFromDB($notification, $user, $displayType, $displayTime);

            if ($show) {
                if (!empty($notification->maximum_purchase_amount) and $notification->maximum_purchase_amount > $user->getPurchaseAmounts()) {
                    $show = false;
                }

                if (!empty($notification->maximum_community_age) and $notification->maximum_community_age > $user->getRegisteredDays()) {
                    $show = false;
                }
            }
        } elseif ($notification->display_for_logged_out_users) {
            $show = $this->checkHistoryFromCookieAndSession($notification, $displayType, $displayTime);
        }

        return $show;
    }

    private function checkHistoryFromDB($notification, $user, $displayType, $displayTime): bool
    {
        $show = false;

        $history = PurchaseNotificationHistory::query()->where('purchase_notification_id', $notification->id)
            ->where('user_id', $user->id)
            ->where('display_type', $displayType)
            ->where('session_ended', false)
            ->first();
        $historyCountView = $history->count_view ?? 0;

        if (empty($history) or $historyCountView < $displayTime) {
            $show = true;
        }


        if ($show) {
            if (!empty($history)) {

                $history->update([
                    'count_view' => $historyCountView + 1
                ]);

            } else {
                PurchaseNotificationHistory::query()->create([
                    'purchase_notification_id' => $notification->id,
                    'user_id' => $user->id,
                    'display_type' => $displayType,
                    'count_view' => 1,
                ]);
            }
        }

        return $show;
    }

    private function checkHistoryFromCookieAndSession($notification, $displayType, $displayTime): bool
    {
        $show = false;

        if ($displayType == "overall") {
            $cookieKey = 'purchase_notifications_overall';
            $histories = Cookie::get($cookieKey);

            if (!empty($histories)) {
                $histories = json_decode($histories, true);
            } else {
                $histories = [];
            }

            $views = (!empty($histories) and !empty($histories[$notification->id])) ? (int)$histories[$notification->id] : 0;

            if ($views < $displayTime) {
                $show = true;
            }

            if ($show) {
                $histories[$notification->id] = $views + 1;

                Cookie::queue($cookieKey, json_encode($histories), 30 * 24 * 60);
            }
        } else {
            $sessionKey = 'purchase_notifications_per_session';

            $histories = Session::get($sessionKey);

            if (!empty($histories)) {
                $histories = json_decode($histories, true);
            } else {
                $histories = [];
            }

            $views = (!empty($histories) and !empty($histories[$notification->id])) ? (int)$histories[$notification->id] : 0;

            if ($views < $displayTime) {
                $show = true;
            }

            if ($show) {
                $histories[$notification->id] = $views + 1;

                Session::put($sessionKey, json_encode($histories));
            }
        }

        return $show;
    }

    private function makeNotificationInfo($notification)
    {
        $users = explode(',', $notification->users);
        $times = explode(',', $notification->times);

        $content = PurchaseNotificationRoleGroupContent::query()
            ->where('purchase_notification_id', $notification->id)
            ->where(function (Builder $query) {
                $query->whereNotNull('webinar_id');
                $query->orWhereNotNull('bundle_id');
                $query->orWhereNotNull('product_id');
            })
            ->inRandomOrder()
            ->first();

        if (!empty($content)) {

            if (!empty($content->webinar_id)) {
                $content = $content->webinar;
            } else if (!empty($content->bundle_id)) {
                $content = $content->bundle;
            } else if (!empty($content->product_id)) {
                $content = $content->product;
            }

            if (!empty($content)) {
                $notification->content = $content;
                $notification->time = $times[array_rand($times)];;

                $user = $users[array_rand($users)];

                $notification->notif_title = $this->replaceTags($notification->popup_title, $notification, $content, $user);
                $notification->notif_subtitle = $this->replaceTags($notification->popup_subtitle, $notification, $content, $user);;
            }
        }

        return $notification;
    }

    private function replaceTags($text, $notification, $content, $userName)
    {
        $text = str_replace("[user]", $userName, $text);
        $text = str_replace("[content]", $content->title, $text);
        $text = str_replace("[price]", handlePrice($content->price), $text);
        $text = str_replace("[time]", $notification->time, $text);

        return $text;
    }

}
