<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Badge extends Model implements TranslatableContract
{
    use Translatable;

    protected $table = 'badges';
    protected $guarded = ['id'];
    public $timestamps = false;

    static $badgeTypes = ['register_date', 'course_count', 'course_rate', 'sale_count', 'support_rate',
        'product_sale_count', 'make_topic', 'send_post_in_topic', 'instructor_blog'
    ];

    public $translatedAttributes = ['title', 'description'];

    public function getTitleAttribute()
    {
        return getTranslateAttributeValue($this, 'title');
    }

    public function getDescriptionAttribute()
    {
        return getTranslateAttributeValue($this, 'description');
    }

    static function getUserBadges($user, $customBadges = false, $getNext = false)
    {
        $earnedBadges = [];
        $nextBadges = []; // for normal user just register_date and for instructor and organization just sale_count
        $nextBadge = null;
        $badges = self::all();
        $badges = $badges->groupBy('type');

        $courses = $user->webinars;

        foreach (self::$badgeTypes as $type) {
            if (!empty($badges[$type]) and !$badges[$type]->isEmpty()) {

                switch ($type) {
                    case 'register_date' :
                        $timeElapsed = time() - $user->created_at;
                        $days = round($timeElapsed / 86400);

                        $registerDateBadges = self::handleCondition($badges[$type], $days);

                        if (!empty($registerDateBadges['result'])) {
                            $earnedBadges[] = $registerDateBadges['result'];
                        }

                        if ($user->isUser() and count($registerDateBadges['nextBadge'])) {
                            $nextBadge = $registerDateBadges['nextBadge'];
                            $nextBadge['earned'] = $registerDateBadges['result'];
                            $nextBadges[] = $nextBadge;
                        }

                        break;

                    case 'course_count':

                        if (!empty($courses) and !$courses->isEmpty()) {
                            $coursesCount = $courses->count();
                            $courseBadges = self::handleCondition($badges[$type], $coursesCount);
                            if (!empty($courseBadges['result'])) {
                                $earnedBadges[] = $courseBadges['result'];
                            }
                        }
                        break;

                    case 'course_rate':

                        if (!empty($courses) and !$courses->isEmpty()) {
                            $rate = 0;
                            foreach ($courses as $course) {
                                $rate += $course->getRate();
                            }

                            $rateBadges = self::handleCondition($badges[$type], $rate);
                            if (!empty($rateBadges['result'])) {
                                $earnedBadges[] = $rateBadges['result'];
                            }
                        }

                        break;

                    case 'sale_count':
                        if (!empty($courses) and !$courses->isEmpty()) {
                            $saleCount = $user->salesCount();

                            $saleBadges = self::handleCondition($badges[$type], $saleCount);

                            if (!empty($saleBadges['result'])) {
                                $earnedBadges[] = $saleBadges['result'];
                            }

                            if (!$user->isUser() and count($saleBadges['nextBadge'])) {
                                $nextBadge = $saleBadges['nextBadge'];
                                $nextBadge['earned'] = $saleBadges['result'];
                                $nextBadges[] = $nextBadge;
                            }
                        }
                        break;

                    case 'support_rate':
                        if (!empty($courses) and !$courses->isEmpty()) {
                            $webinarIds = $courses->pluck('id')->toArray();

                            $supportsRate = webinarReview::whereIn('webinar_id', $webinarIds)
                                ->where('status', 'active')
                                ->avg('support_quality');

                            $supportBadges = self::handleCondition($badges[$type], $supportsRate);
                            if (!empty($supportBadges['result'])) {
                                $earnedBadges[] = $supportBadges['result'];
                            }
                        }
                        break;

                    case 'product_sale_count':
                        $products = $user->products;

                        if (!empty($products) and !$products->isEmpty()) {
                            $saleCount = $user->productsSalesCount();

                            $productsSaleBadges = self::handleCondition($badges[$type], $saleCount);

                            if (!empty($productsSaleBadges['result'])) {
                                $earnedBadges[] = $productsSaleBadges['result'];
                            }
                        }
                        break;

                    case 'make_topic':
                        $forumTopics = $user->forumTopics;

                        if (!empty($forumTopics) and !$forumTopics->isEmpty()) {

                            $forumTopicsBadges = self::handleCondition($badges[$type], $forumTopics->count());

                            if (!empty($forumTopicsBadges['result'])) {
                                $earnedBadges[] = $forumTopicsBadges['result'];
                            }
                        }
                        break;

                    case 'send_post_in_topic':
                        $forumTopicPosts = $user->forumTopicPosts;

                        if (!empty($forumTopicPosts) and !$forumTopicPosts->isEmpty()) {

                            $forumTopicPostsBadges = self::handleCondition($badges[$type], $forumTopicPosts->count());

                            if (!empty($forumTopicPostsBadges['result'])) {
                                $earnedBadges[] = $forumTopicPostsBadges['result'];
                            }
                        }
                        break;

                    case 'instructor_blog':
                        $blogCount = $user->blog()->where('status', 'publish')->count();

                        if ($blogCount) {

                            $blogBadges = self::handleCondition($badges[$type], $blogCount);

                            if (!empty($blogBadges['result'])) {
                                $earnedBadges[] = $blogBadges['result'];
                            }
                        }
                        break;
                }

            }
        }

        if ($customBadges) {
            $customs = $user->customBadges()->with('badge')->get();
            if (!empty($customs) and !$customs->isEmpty()) {
                $earnedBadges = $customs->merge($earnedBadges);
            }
        }

        foreach ($earnedBadges as $earnedBadge) {
            if (!empty($earnedBadge->badge_id)) {
                self::handleBadgeReward($earnedBadge->badge, $user->id);

                sendNotification('new_badge', ['[u.b.title]' => $earnedBadge->badge->title], $user->id);
            } else {
                self::handleBadgeReward($earnedBadge, $user->id);

                sendNotification('new_badge', ['[u.b.title]' => $earnedBadge->title], $user->id);
            }
        }

        if (!empty($nextBadges) and count($nextBadges)) {
            foreach ($nextBadges as $next) {
                if (!empty($nextBadge) and $nextBadge['percent'] < $next['percent']) {
                    $nextBadge = $next;
                } else {
                    $nextBadge = $next;
                }
            }
        }

        if ($getNext) {
            return $nextBadge;
        }

        return $earnedBadges;
    }

    static function handleCondition($badges, $entry)
    {
        $result = null;
        $earnedBadges = [];
        $nextBadge = [];

        foreach ($badges as $badge) {
            $condition = json_decode($badge->condition);

            if ($entry >= $condition->from) {
                $earnedBadges[$condition->from] = $badge;
            } else {
                if (!empty($nextBadge) and !empty($nextBadge['badge'])) {
                    $nextCondition = json_decode($nextBadge['badge']->condition);
                    if ($nextCondition->from > $condition->from) {
                        $nextBadge['badge'] = $badge;
                        $nextBadge['percent'] = round(($entry > 0 ? $entry : 1) / ($condition->from > 0 ? $condition->from : 1) * 100, 2);
                    }
                } else {
                    $nextBadge['badge'] = $badge;
                    $nextBadge['percent'] = round(($entry > 0 ? $entry : 1) / ($condition->from > 0 ? $condition->from : 1) * 100, 2);
                }
            }
        }

        if (!empty($earnedBadges) and count($earnedBadges)) {
            $result = $earnedBadges[max(array_keys($earnedBadges))];

            if (count($nextBadge) < 1) {
                $resultCondition = json_decode($result->condition);
                $percent = round(($entry > 0 ? $entry : 1) / ($resultCondition->to > 0 ? $resultCondition->to : 1) * 100, 2);
                $nextBadge['percent'] = ($percent > 100) ? 100 : $percent;
            }
        }

        return [
            'result' => $result,
            'nextBadge' => $nextBadge
        ];
    }

    static function handleBadgeReward($badge, $userId)
    {
        if (!empty($badge->score)) {
            $rewardScore = RewardAccounting::calculateScore(Reward::BADGE, $badge->score);
            RewardAccounting::makeRewardAccounting($userId, $rewardScore, Reward::BADGE, $badge->id, true);
        }

        return true;
    }
}
