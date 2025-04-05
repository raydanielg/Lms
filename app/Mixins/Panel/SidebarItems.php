<?php

namespace App\Mixins\Panel;

class SidebarItems
{

    static public function getItems(): array
    {
        $user = auth()->user();

        return [
            'main_menu' => self::getMainMenuSectionItems($user),
            'members' => self::getMembersSectionItems($user),
            'education' => self::getEducationSectionItems($user),
            'evaluation' => self::getEvaluationSectionItems($user),
            'financial_&_marketing' => self::getFinancialSectionItems($user),
            'communications' => self::getCommunicationsSectionItems($user),
            'user' => self::getOtherSectionItems($user),
        ];
    }

    static public function getMainMenuSectionItems($user)
    {
        $items = [];

        $items['dashboard'] = [
            'icon' => self::getIcon('dashboard'),
            'text' => trans('panel.dashboard'),
            'url' => '/panel',
            'items' => []
        ];


        return $items;
    }

    static public function getMembersSectionItems($user)
    {
        $items = [];

        if ($user->isOrganization()) {

            if ($user->can('panel_organization_instructors')) {
                $items['instructors'] = [
                    'icon' => self::getIcon('instructors'),
                    'text' => trans('public.instructors'),
                    'url' => '/panel/instructors',
                    'items' => []
                ];

                if ($user->can('panel_organization_instructors_create')) {
                    $items['instructors']['items'][] = ['text' => trans('public.new'), 'url' => '/panel/manage/instructors/new'];
                }

                if ($user->can('panel_organization_instructors_lists')) {
                    $items['instructors']['items'][] = ['text' => trans('public.list'), 'url' => '/panel/manage/instructors'];
                }
            }


            if ($user->can('panel_organization_students')) {
                $items['students'] = [
                    'icon' => self::getIcon('students'),
                    'text' => trans('quiz.students'),
                    'url' => '/panel/students',
                    'items' => []
                ];

                if ($user->can('panel_organization_students_create')) {
                    $items['students']['items'][] = ['text' => trans('public.new'), 'url' => '/panel/manage/students/new'];
                }

                if ($user->can('panel_organization_students_lists')) {
                    $items['students']['items'][] = ['text' => trans('public.list'), 'url' => '/panel/manage/students'];
                }
            }

        }

        return $items;
    }

    static public function getEducationSectionItems($user)
    {
        $items = [];

        if ($user->can('panel_webinars')) {
            $items['webinars'] = [
                'icon' => self::getIcon('webinars'),
                'text' => trans('panel.webinars'),
                'url' => '/panel/webinars',
                'items' => []
            ];

            if ($user->isOrganization() || $user->isTeacher()) {
                if ($user->can('panel_webinars_create')) {
                    $items['webinars']['items'][] = ['text' => trans('public.new'), 'url' => '/panel/webinars/new'];
                }

                if ($user->can('panel_webinars_lists')) {
                    $items['webinars']['items'][] = ['text' => trans('panel.my_classes'), 'url' => '/panel/webinars'];
                }

                if ($user->can('panel_webinars_invited_lists')) {
                    $items['webinars']['items'][] = ['text' => trans('panel.invited_classes'), 'url' => '/panel/webinars/invitations'];
                }
            }

            if (!empty($user->organ_id) and $user->can('panel_webinars_organization_classes')) {
                $items['webinars']['items'][] = ['text' => trans('panel.organization_classes'), 'url' => '/panel/webinars/organization_classes'];
            }

            if ($user->can('panel_webinars_my_purchases')) {
                $items['webinars']['items'][] = ['text' => trans('panel.my_purchases'), 'url' => '/panel/webinars/purchases'];
            }

            if (($user->isOrganization() || $user->isTeacher()) and $user->can('panel_webinars_my_class_comments')) {
                $items['webinars']['items'][] = ['text' => trans('panel.my_class_comments'), 'url' => '/panel/webinars/comments'];
            }

            if ($user->can('panel_webinars_comments')) {
                $items['webinars']['items'][] = ['text' => trans('panel.my_comments'), 'url' => '/panel/webinars/my-comments'];
            }

            if ($user->can('panel_webinars_favorites')) {
                $items['webinars']['items'][] = ['text' => trans('panel.favorites'), 'url' => '/panel/webinars/favorites'];
            }

            if (!empty(getFeaturesSettings('course_notes_status')) and $user->can('panel_webinars_personal_course_notes')) {
                $items['webinars']['items'][] = ['text' => trans('update.course_notes'), 'url' => '/panel/webinars/personal-notes'];
            }
        }

        if (!empty(getFeaturesSettings('upcoming_courses_status')) and $user->can('panel_upcoming_courses')) {
            $items['upcoming_courses'] = [
                'icon' => self::getIcon('upcoming_courses'),
                'text' => trans('update.upcoming_courses'),
                'url' => '/panel/upcoming_courses',
                'items' => []
            ];

            if ($user->isOrganization() || $user->isTeacher()) {
                if ($user->can('panel_upcoming_courses_create')) {
                    $items['upcoming_courses']['items'][] = ['text' => trans('public.new'), 'url' => '/panel/upcoming_courses/new'];
                }

                if ($user->can('panel_upcoming_courses_lists')) {
                    $items['upcoming_courses']['items'][] = ['text' => trans('update.my_upcoming_courses'), 'url' => '/panel/upcoming_courses'];
                }
            }

            if ($user->can('panel_upcoming_courses_followings')) {
                $items['upcoming_courses']['items'][] = ['text' => trans('update.following_courses'), 'url' => '/panel/upcoming_courses/followings'];
            }
        }


        if (($user->isOrganization() or $user->isTeacher()) and $user->can('panel_bundles')) {
            $items['bundles'] = [
                'icon' => self::getIcon('bundles'),
                'text' => trans('update.bundles'),
                'url' => '/panel/bundles',
                'items' => []
            ];

            if ($user->can('panel_bundles_create')) {
                $items['bundles']['items'][] = ['text' => trans('public.new'), 'url' => '/panel/bundles/new'];
            }

            if ($user->can('panel_bundles_lists')) {
                $items['bundles']['items'][] = ['text' => trans('update.my_bundles'), 'url' => '/panel/bundles'];
            }
        }

        if ($user->can('panel_meetings')) {

            $items['meetings'] = [
                'icon' => self::getIcon('meetings'),
                'text' => trans('panel.meetings'),
                'url' => '/panel/meetings',
                'items' => []
            ];

            if ($user->can('panel_meetings_my_reservation')) {
                $items['meetings']['items'][] = ['text' => trans('public.my_reservation'), 'url' => '/panel/meetings/reservation'];
            }

            if ($user->isOrganization() || $user->isTeacher()) {
                if ($user->can('panel_meetings_requests')) {
                    $items['meetings']['items'][] = ['text' => trans('panel.requests'), 'url' => '/panel/meetings/requests'];
                }

                if ($user->can('panel_meetings_settings')) {
                    $items['meetings']['items'][] = ['text' => trans('panel.settings'), 'url' => '/panel/meetings/settings'];
                }
            }
        }

        return $items;
    }

    static public function getEvaluationSectionItems($user)
    {
        $items = [];

        if (getFeaturesSettings('webinar_assignment_status') and $user->can('panel_assignments')) {
            $items['assignments'] = [
                'icon' => self::getIcon('assignments'),
                'text' => trans('update.assignments'),
                'url' => '/panel/assignments',
                'items' => []
            ];

            if ($user->can('panel_assignments_lists')) {
                $items['assignments']['items'][] = ['text' => trans('update.my_assignments'), 'url' => '/panel/assignments/my-assignments'];
            }

            if (($user->isOrganization() || $user->isTeacher()) and $user->can('panel_assignments_my_courses_assignments')) {
                $items['assignments']['items'][] = ['text' => trans('update.students_assignments'), 'url' => '/panel/assignments/my-courses-assignments'];
            }
        }


        if ($user->can('panel_quizzes')) {
            $items['quizzes'] = [
                'icon' => self::getIcon('quizzes'),
                'text' => trans('panel.quizzes'),
                'url' => '/panel/quizzes',
                'items' => []
            ];

            if ($user->isOrganization() || $user->isTeacher()) {
                if ($user->can('panel_quizzes_create')) {
                    $items['quizzes']['items'][] = ['text' => trans('quiz.new_quiz'), 'url' => '/panel/quizzes/new'];
                }

                if ($user->can('panel_quizzes_lists')) {
                    $items['quizzes']['items'][] = ['text' => trans('public.list'), 'url' => '/panel/quizzes'];
                }

                if ($user->can('panel_quizzes_results')) {
                    $items['quizzes']['items'][] = ['text' => trans('public.results'), 'url' => '/panel/quizzes/results'];
                }
            }

            if ($user->can('panel_quizzes_my_results')) {
                $items['quizzes']['items'][] = ['text' => trans('public.my_results'), 'url' => '/panel/quizzes/my-results'];
            }

            if ($user->can('panel_quizzes_not_participated')) {
                $items['quizzes']['items'][] = ['text' => trans('update.not_participated'), 'url' => '/panel/quizzes/opens'];
            }

        }


        if ($user->can('panel_certificates')) {
            $items['certificates'] = [
                'icon' => self::getIcon('certificates'),
                'text' => trans('panel.certificates'),
                'url' => '/panel/certificates',
                'items' => []
            ];

            if (($user->isOrganization() || $user->isTeacher()) and $user->can('panel_certificates_lists')) {
                $items['certificates']['items'][] = ['text' => trans('public.list'), 'url' => '/panel/certificates'];
            }

            if ($user->can('panel_certificates_achievements')) {
                $items['certificates']['items'][] = ['text' => trans('quiz.achievements'), 'url' => '/panel/certificates/achievements'];
            }

            $items['certificates']['items'][] = ['text' => trans('site.certificate_validation'), 'url' => '/certificate_validation'];

            if ($user->can('panel_certificates_course_certificates')) {
                $items['certificates']['items'][] = ['text' => trans('update.course_certificates'), 'url' => '/panel/certificates/webinars'];
            }
        }

        return $items;
    }

    static public function getFinancialSectionItems($user)
    {
        $items = [];

        if ($user->checkCanAccessToStore() and $user->can('panel_products')) {
            $items['store'] = [
                'icon' => self::getIcon('store'),
                'text' => trans('update.store'),
                'url' => '/panel/store',
                'items' => []
            ];

            if ($user->isOrganization() || $user->isTeacher()) {

                if ($user->can('panel_products_create')) {
                    $items['store']['items'][] = ['text' => trans('update.new_product'), 'url' => '/panel/store/products/new'];
                }

                if ($user->can('panel_products_lists')) {
                    $items['store']['items'][] = ['text' => trans('update.products'), 'url' => '/panel/store/products'];
                }

                if ($user->can('panel_products_sales')) {
                    $items['store']['items'][] = ['text' => trans('panel.sales'), 'url' => '/panel/store/sales'];
                }
            }

            if ($user->can('panel_products_purchases')) {
                $items['store']['items'][] = ['text' => trans('panel.my_purchases'), 'url' => '/panel/store/purchases'];
            }

            if (($user->isOrganization() || $user->isTeacher()) and $user->can('panel_products_comments')) {
                $items['store']['items'][] = ['text' => trans('update.product_comments'), 'url' => '/panel/store/products/comments'];
            }

            if ($user->can('panel_products_my_comments')) {
                $items['store']['items'][] = ['text' => trans('panel.my_comments'), 'url' => '/panel/store/products/my-comments'];
            }
        }

        if ($user->can('panel_financial')) {

            $items['financial'] = [
                'icon' => self::getIcon('financial'),
                'text' => trans('panel.financial'),
                'url' => '/panel/financial',
                'items' => []
            ];

            if (($user->isOrganization() || $user->isTeacher()) and $user->can('panel_financial_sales_reports')) {
                $items['financial']['items'][] = ['text' => trans('financial.sales_report'), 'url' => '/panel/financial/sales'];
            }

            if ($user->can('panel_financial_summary')) {
                $items['financial']['items'][] = ['text' => trans('financial.financial_summary'), 'url' => '/panel/financial/summary'];
            }

            if ($user->can('panel_financial_payout')) {
                $items['financial']['items'][] = ['text' => trans('financial.payout'), 'url' => '/panel/financial/payout'];
            }

            if ($user->can('panel_financial_charge_account')) {
                $items['financial']['items'][] = ['text' => trans('financial.charge_account'), 'url' => '/panel/financial/account'];
            }

            if ($user->can('panel_financial_subscribes')) {
                $items['financial']['items'][] = ['text' => trans('financial.subscribes'), 'url' => '/panel/financial/subscribes'];
            }

            if (($user->isOrganization() || $user->isTeacher()) and getRegistrationPackagesGeneralSettings('status') and $user->can('panel_financial_registration_packages')) {
                $items['financial']['items'][] = ['text' => trans('update.registration_packages'), 'url' => route('panelRegistrationPackagesLists')];
            }

            if (getInstallmentsSettings('status') and $user->can('panel_financial_installments')) {
                $items['financial']['items'][] = ['text' => trans('update.installments'), 'url' => '/panel/financial/installments'];
            }

        }

        $referralSettings = getReferralSettings();

        if (
            (
                !$user->isUser() or
                (!empty($referralSettings) and $referralSettings['status'] and $user->affiliate) or
                (!empty(getRegistrationBonusSettings('status')) and $user->enable_registration_bonus)
            ) and $user->can('panel_marketing')
        ) {

            $items['marketing'] = [
                'icon' => self::getIcon('marketing'),
                'text' => trans('panel.marketing'),
                'url' => '/panel/marketing',
                'items' => [

                ]
            ];


            if (!$user->isUser()) {
                if ($user->can('panel_marketing_special_offers')) {
                    $items['marketing']['items'][] = ['text' => trans('panel.discounts'), 'url' => '/panel/marketing/special_offers'];
                }

                if ($user->can('panel_marketing_promotions')) {
                    $items['marketing']['items'][] = ['text' => trans('panel.promotions'), 'url' => '/panel/marketing/promotions'];
                }
            }

            if (!empty($referralSettings) and $referralSettings['status'] and $user->affiliate and $user->can('panel_marketing_affiliates')) {
                $items['marketing']['items'][] = ['text' => trans('panel.affiliates'), 'url' => '/panel/marketing/affiliates'];
            }

            if (!empty(getRegistrationBonusSettings('status')) and $user->enable_registration_bonus and $user->can('panel_marketing_registration_bonus')) {
                $items['marketing']['items'][] = ['text' => trans('update.registration_bonus'), 'url' => '/panel/marketing/registration_bonus'];
            }

            if (!empty(getFeaturesSettings('frontend_coupons_status'))) {
                if ($user->can('panel_marketing_coupons')) {
                    $items['marketing']['items'][] = ['text' => trans('update.coupons'), 'url' => '/panel/marketing/discounts'];
                }

                if ($user->can('panel_marketing_new_coupon')) {
                    $items['marketing']['items'][] = ['text' => trans('update.new_coupon'), 'url' => '/panel/marketing/discounts/new'];
                }
            }

        }

        // rewards
        $rewardSetting = getRewardsSettings();

        if (!empty($rewardSetting) and $rewardSetting['status'] == '1' and $user->can('panel_rewards')) {
            $items['rewards'] = [
                'icon' => self::getIcon('rewards'),
                'text' => trans('update.rewards'),
                'url' => '/panel/rewards',
                'items' => []
            ];
        }

        return $items;
    }

    static public function getCommunicationsSectionItems($user)
    {
        $items = [];

        // Support
        if ($user->can('panel_support')) {
            $items['support'] = [
                'icon' => self::getIcon('support'),
                'text' => trans('panel.support'),
                'url' => '/panel/support',
                'items' => []
            ];

            if ($user->can('panel_support_create')) {
                $items['support']['items'][] = ['text' => trans('public.new'), 'url' => '/panel/support/new'];
            }

            if ($user->can('panel_support_lists')) {
                $items['support']['items'][] = ['text' => trans('update.classes_support'), 'url' => '/panel/support'];
            }

            if ($user->can('panel_support_tickets')) {
                $items['support']['items'][] = ['text' => trans('update.support_tickets'), 'url' => '/panel/support/tickets'];
            }
        }

        // Forums
        if (getFeaturesSettings('forums_status') and $user->can('panel_forums')) {
            $items['forums'] = [
                'icon' => self::getIcon('forums'),
                'text' => trans('update.forums'),
                'url' => '/panel/forums',
                'items' => []
            ];

            if ($user->can('panel_forums_new_topic')) {
                $items['forums']['items'][] = ['text' => trans('update.new_topic'), 'url' => '/forums/create-topic'];
            }

            if ($user->can('panel_forums_my_topics')) {
                $items['forums']['items'][] = ['text' => trans('update.my_topics'), 'url' => '/panel/forums/topics'];
            }

            if ($user->can('panel_forums_my_posts')) {
                $items['forums']['items'][] = ['text' => trans('update.my_posts'), 'url' => '/panel/forums/posts'];
            }

            if ($user->can('panel_forums_bookmarks')) {
                $items['forums']['items'][] = ['text' => trans('update.bookmarks'), 'url' => '/panel/forums/bookmarks'];
            }
        }


        // Articles
        if ($user->isTeacher() and $user->can('panel_blog')) {

            $items['blog'] = [
                'icon' => self::getIcon('blog'),
                'text' => trans('update.articles'),
                'url' => '/panel/blog',
                'items' => [

                ]
            ];

            if ($user->can('panel_blog_new_article')) {
                $items['blog']['items'][] = ['text' => trans('update.new_article'), 'url' => '/panel/blog/posts/new'];
            }

            if ($user->can('panel_blog_my_articles')) {
                $items['blog']['items'][] = ['text' => trans('update.my_articles'), 'url' => '/panel/blog/posts'];
            }

            if ($user->can('panel_blog_comments')) {
                $items['blog']['items'][] = ['text' => trans('panel.comments'), 'url' => '/panel/blog/comments'];
            }

        }


        // noticeboard
        if (($user->isOrganization() || $user->isTeacher()) and $user->can('panel_noticeboard')) {
            $items['noticeboard'] = [
                'icon' => self::getIcon('noticeboard'),
                'text' => trans('panel.noticeboard'),
                'url' => '/panel/noticeboard',
                'extraUrl' => '/panel/course-noticeboard',
                'items' => []
            ];

            if ($user->can('panel_noticeboard_history')) {
                $items['noticeboard']['items'][] = ['text' => trans('public.history'), 'url' => '/panel/noticeboard'];
            }

            if ($user->can('panel_noticeboard_create')) {
                $items['noticeboard']['items'][] = ['text' => trans('public.new'), 'url' => '/panel/noticeboard/new'];
            }

            if ($user->can('panel_noticeboard_course_notices')) {
                $items['noticeboard']['items'][] = ['text' => trans('update.course_notices'), 'url' => '/panel/course-noticeboard'];
            }

            if ($user->can('panel_noticeboard_course_notices_create')) {
                $items['noticeboard']['items'][] = ['text' => trans('update.new_course_notices'), 'url' => '/panel/course-noticeboard/new'];
            }
        }

        // AI Contents
        if ($user->checkAccessToAIContentFeature() and $user->can('panel_ai_contents')) {
            $items['ai_contents'] = [
                'icon' => self::getIcon('ai_contents'),
                'text' => trans('update.ai_contents'),
                'url' => '/panel/ai-contents',
                'items' => []
            ];
        }

        if ($user->can('panel_notifications')) {
            $items['notifications'] = [
                'icon' => self::getIcon('notifications'),
                'text' => trans('panel.notifications'),
                'url' => '/panel/notifications',
                'items' => []
            ];
        }

        return $items;
    }

    static public function getOtherSectionItems($user)
    {
        $items = [];

        if (($user->isTeacher() or $user->isOrganization()) and $user->can('panel_others_profile_url')) {
            $items['profile'] = [
                'icon' => self::getIcon('profile'),
                'text' => trans('public.my_profile'),
                'url' => $user->getProfileUrl(),
                'items' => []
            ];
        }

        if ($user->can('panel_others_profile_setting')) {
            $items['setting'] = [
                'icon' => self::getIcon('setting'),
                'text' => trans('panel.settings'),
                'url' => '/panel/setting',
                'items' => []
            ];
        }

        if ($user->can('panel_others_logout')) {
            $items['logout'] = [
                'icon' => self::getIcon('logout'),
                'text' => trans('panel.log_out'),
                'url' => '/logout',
                'className' => 'text-danger',
                'items' => []
            ];
        }

        return $items;
    }

    static public function getIcon($name)
    {
        return view()->make('design_1.panel.includes.sidebar.icons', ['name' => $name]);
    }

}
