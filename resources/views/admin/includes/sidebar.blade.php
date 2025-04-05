<div class="main-sidebar">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="/">
                @if(!empty($generalSettings['site_name']))
                    {{ strtoupper($generalSettings['site_name']) }}
                @else
                    Platform Title
                @endif
            </a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="/">
                @if(!empty($generalSettings['site_name']))
                    {{ strtoupper(substr($generalSettings['site_name'],0,2)) }}
                @endif
            </a>
        </div>

        <ul class="sidebar-menu">
            @can('admin_general_dashboard_show')
                <li class="{{ (request()->is(getAdminPanelUrl('/'))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl('') }}" class="nav-link">
                        <i class="fas fa-fire"></i>
                        <span>{{ trans('admin/main.dashboard') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_marketing_dashboard')
                <li class="{{ (request()->is(getAdminPanelUrl('/marketing', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl('/marketing') }}" class="nav-link">
                        <i class="fas fa-chart-pie"></i>
                        <span>{{ trans('admin/main.marketing_dashboard') }}</span>
                    </a>
                </li>
            @endcan

            @if($authUser->can('admin_webinars') or
                $authUser->can('admin_bundles') or
                $authUser->can('admin_categories') or
                $authUser->can('admin_filters') or
                $authUser->can('admin_quizzes') or
                $authUser->can('admin_certificate') or
                $authUser->can('admin_reviews_lists') or
                $authUser->can('admin_webinar_assignments') or
                $authUser->can('admin_enrollment') or
                $authUser->can('admin_waitlists')
            )
                <li class="menu-header">{{ trans('site.education') }}</li>
            @endif

            @can('admin_webinars')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/webinars*', false)) and !request()->is(getAdminPanelUrl('/webinars/comments*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-graduation-cap"></i>
                        <span>{{ trans('panel.classes') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_webinars_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars', false)) and request()->get('type') == 'course') ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['courses']) and $sidebarBeeps['courses']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/webinars?type=course">{{ trans('admin/main.courses') }}</a>
                            </li>

                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars', false)) and request()->get('type') == 'webinar') ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['webinars']) and $sidebarBeeps['webinars']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/webinars?type=webinar">{{ trans('admin/main.live_classes') }}</a>
                            </li>

                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars', false)) and request()->get('type') == 'text_lesson') ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['textLessons']) and $sidebarBeeps['textLessons']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/webinars?type=text_lesson">{{ trans('admin/main.text_courses') }}</a>
                            </li>
                        @endcan()

                        @can('admin_webinars_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/webinars/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()

                        @can('admin_agora_history_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/agora_history', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/agora_history">{{ trans('update.agora_history') }}</a>
                            </li>
                        @endcan

                        @can('admin_course_personal_notes')
                            @if(!empty(getFeaturesSettings('course_notes_status')))
                                <li class="{{ (request()->is(getAdminPanelUrl('/webinars/personal-notes', false))) ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ getAdminPanelUrl() }}/webinars/personal-notes">{{ trans('update.course_notes') }}</a>
                                </li>
                            @endif
                        @endcan

                    </ul>
                </li>
            @endcan()

            @can('admin_bundles')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/bundles*', false)) and !request()->is(getAdminPanelUrl('/bundles/comments*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-cube"></i>
                        <span>{{ trans('update.bundles') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_bundles_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/bundles', false)) and request()->get('type') == 'course') ? 'active' : '' }}">
                                <a href="{{ getAdminPanelUrl() }}/bundles" class="nav-link @if(!empty($sidebarBeeps['bundles']) and $sidebarBeeps['bundles']) beep beep-sidebar @endif">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()

                        @can('admin_bundles_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/bundles/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/bundles/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_upcoming_courses')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/upcoming_courses*', false)) and !request()->is(getAdminPanelUrl('/upcoming_courses/comments*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-calendar"></i>
                        <span>{{ trans('update.upcoming_courses') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_upcoming_courses_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/upcoming_courses', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/upcoming_courses') }}">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()

                        @can('admin_upcoming_courses_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/upcoming_courses/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/upcoming_courses/new') }}">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()

                    </ul>
                </li>
            @endcan()

            @can('admin_quizzes')
                <li class="{{ (request()->is(getAdminPanelUrl('/quizzes*', false))) ? 'active' : '' }}">
                    <a class="nav-link " href="{{ getAdminPanelUrl() }}/quizzes">
                        <i class="fas fa-file"></i>
                        <span>{{ trans('admin/main.quizzes') }}</span>
                    </a>
                </li>
            @endcan()

            @can('admin_certificate')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/certificates*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-certificate"></i>
                        <span>{{ trans('admin/main.certificates') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_certificate_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/certificates', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/certificates">{{ trans('update.quizzes_related') }}</a>
                            </li>
                        @endcan

                        @can('admin_course_certificate_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/certificates/course-competition', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/certificates/course-competition">{{ trans('update.course_certificates') }}</a>
                            </li>
                        @endcan

                        @can('admin_certificate_template_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/certificates/templates', false))) ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="{{ getAdminPanelUrl() }}/certificates/templates">{{ trans('admin/main.certificates_templates') }}</a>
                            </li>
                        @endcan

                        @can('admin_certificate_template_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/certificates/templates/new', false))) ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="{{ getAdminPanelUrl() }}/certificates/templates/new">{{ trans('admin/main.new_template') }}</a>
                            </li>
                        @endcan

                        @can('admin_certificate_settings')
                            <li class="{{ (request()->is(getAdminPanelUrl('/certificates/settings', false))) ? 'active' : '' }}">
                                <a class="nav-link"
                                   href="{{ getAdminPanelUrl() }}/certificates/settings">{{ trans('admin/main.setting') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_webinar_assignments')
                <li class="{{ (request()->is(getAdminPanelUrl('/assignments', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/assignments" class="nav-link">
                        <i class="fas fa-pen"></i>
                        <span>{{ trans('update.assignments') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_course_question_forum_list')
                <li class="{{ (request()->is(getAdminPanelUrl('/webinars/course_forums', false))) ? 'active' : '' }}">
                    <a class="nav-link " href="{{ getAdminPanelUrl() }}/webinars/course_forums">
                        <i class="fas fa-comment-alt"></i>
                        <span>{{ trans('update.course_forum') }}</span>
                    </a>
                </li>
            @endcan()

            @can('admin_course_noticeboards_list')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/course-noticeboards*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-clipboard-check"></i>
                        <span>{{ trans('update.course_notices') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_course_noticeboards_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/course-noticeboards', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/course-noticeboards">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan

                        @can('admin_course_noticeboards_send')
                            <li class="{{ (request()->is(getAdminPanelUrl('/course-noticeboards/send', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/course-noticeboards/send">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_enrollment')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/enrollments*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-user-plus"></i>
                        <span>{{ trans('update.enrollment') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_enrollment_history')
                            <li class="{{ (request()->is(getAdminPanelUrl('/enrollments/history', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/enrollments/history">{{ trans('public.history') }}</a>
                            </li>
                        @endcan

                        @can('admin_enrollment_add_student_to_items')
                            <li class="{{ (request()->is(getAdminPanelUrl('/enrollments/add-student-to-class', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/enrollments/add-student-to-class">{{ trans('update.add_student_to_a_class') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_waitlists_lists')
                <li class="{{ (request()->is(getAdminPanelUrl('/waitlists', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl("/waitlists") }}" class="nav-link">
                        <i class="fas fa-user-graduate"></i>
                        <span>{{ trans('update.waitlists') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_categories')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/categories*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-th"></i>
                        <span>{{ trans('admin/main.categories') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_categories_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/categories', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/categories">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_categories_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/categories/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/categories/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                        @can('admin_trending_categories')
                            <li class="{{ (request()->is(getAdminPanelUrl('/categories/trends', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/categories/trends">{{ trans('admin/main.trends') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_filters')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/filters*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-filter"></i>
                        <span>{{ trans('admin/main.filters') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_filters_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/filters', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/filters">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_filters_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/filters/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/filters/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_reviews_lists')
                <li class="{{ (request()->is(getAdminPanelUrl('/reviews', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/reviews" class="nav-link @if(!empty($sidebarBeeps['reviews']) and $sidebarBeeps['reviews']) beep beep-sidebar @endif">
                        <i class="fas fa-star"></i>
                        <span>{{ trans('admin/main.reviews') }}</span>
                    </a>
                </li>
            @endcan






            @if($authUser->can('admin_consultants_lists') or
                $authUser->can('admin_appointments_lists')
            )
                <li class="menu-header">{{ trans('site.appointments') }}</li>
            @endif

            @can('admin_consultants_lists')
                <li class="{{ (request()->is(getAdminPanelUrl('/consultants', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/consultants" class="nav-link">
                        <i class="fas fa-id-card"></i>
                        <span>{{ trans('admin/main.consultants') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_appointments_lists')
                <li class="{{ (request()->is(getAdminPanelUrl('/appointments', false))) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ getAdminPanelUrl() }}/appointments">
                        <i class="fas fa-address-book"></i>
                        <span>{{ trans('admin/main.appointments') }}</span>
                    </a>
                </li>
            @endcan

            @if($authUser->can('admin_users') or
                $authUser->can('admin_roles') or
                $authUser->can('admin_users_not_access_content') or
                $authUser->can('admin_group') or
                $authUser->can('admin_users_badges') or
                $authUser->can('admin_become_instructors_list') or
                $authUser->can('admin_delete_account_requests') or
                $authUser->can('admin_user_login_history') or
                $authUser->can('admin_user_ip_restriction')
            )
                <li class="menu-header">{{ trans('panel.users') }}</li>
            @endif

            @can('admin_users')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/staffs', false)) or request()->is(getAdminPanelUrl('/students', false)) or request()->is(getAdminPanelUrl('/instructors', false)) or request()->is(getAdminPanelUrl('/organizations', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-users"></i>
                        <span>{{ trans('admin/main.users_list') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_staffs_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/staffs', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/staffs">{{ trans('admin/main.staff') }}</a>
                            </li>
                        @endcan()

                        @can('admin_users_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/students', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/students">{{ trans('public.students') }}</a>
                            </li>
                        @endcan()

                        @can('admin_instructors_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/instructors', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/instructors">{{ trans('home.instructors') }}</a>
                            </li>
                        @endcan()

                        @can('admin_organizations_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/organizations', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/organizations">{{ trans('admin/main.organizations') }}</a>
                            </li>
                        @endcan()

                        @can('admin_users_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/users/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan


            @can('admin_users_not_access_content_lists')
                <li class="{{ (request()->is(getAdminPanelUrl('/users/not-access-to-content', false))) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/not-access-to-content">
                        <i class="fas fa-user-lock"></i> <span>{{ trans('update.not_access_to_content') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_roles')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/roles*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-user-circle"></i> <span>{{ trans('admin/main.roles') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_roles_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/roles', false))) ? 'active' : '' }}">
                                <a class="nav-link active" href="{{ getAdminPanelUrl() }}/roles">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_roles_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/roles/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/roles/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_group')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/users/groups*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-sitemap"></i>
                        <span>{{ trans('admin/main.groups') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_group_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/users/groups', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/groups">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan
                        @can('admin_group_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/users/groups/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/groups/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_users_badges')
                <li class="{{ (request()->is(getAdminPanelUrl('/users/badges', false))) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/badges">
                        <i class="fas fa-trophy"></i>
                        <span>{{ trans('admin/main.badges') }}</span>
                    </a>
                </li>
            @endcan()



            @can('admin_become_instructors_list')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/users/become-instructors*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-list-alt"></i>
                        <span>{{ trans('admin/main.instructor_requests') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="{{ (request()->is(getAdminPanelUrl('/users/become-instructors/instructors', false))) ? 'active' : '' }}">
                            <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/become-instructors/instructors">
                                <span>{{ trans('admin/main.instructors') }}</span>
                            </a>
                        </li>

                        <li class="{{ (request()->is(getAdminPanelUrl('/users/become-instructors/organizations', false))) ? 'active' : '' }}">
                            <a class="nav-link" href="{{ getAdminPanelUrl() }}/users/become-instructors/organizations">
                                <span>{{ trans('admin/main.organizations') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan()

            @can('admin_delete_account_requests')
                <li class="nav-item {{ (request()->is(getAdminPanelUrl('/users/delete-account-requests*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/users/delete-account-requests" class="nav-link">
                        <i class="fa fa-user-times"></i>
                        <span>{{ trans('update.delete-account-requests') }}</span>
                    </a>
                </li>
            @endcan

            @if($authUser->can("admin_user_login_history") or $authUser->can("admin_user_ip_restriction"))
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/users/login-history*', false)) or request()->is(getAdminPanelUrl('/users/ip-restriction*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-user-clock"></i>
                        <span>{{ trans('update.ip_management') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_user_login_history')
                            <li class="nav-item {{ (request()->is(getAdminPanelUrl('/users/login-history*', false))) ? 'active' : '' }}">
                                <a href="{{ getAdminPanelUrl() }}/users/login-history" class="nav-link">
                                    <span>{{ trans('update.login_history') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('admin_user_ip_restriction')
                            <li class="nav-item {{ (request()->is(getAdminPanelUrl('/users/ip-restriction*', false))) ? 'active' : '' }}">
                                <a href="{{ getAdminPanelUrl() }}/users/ip-restriction" class="nav-link">
                                    <span>{{ trans('update.ip_restriction') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif


            @if(
                $authUser->can('admin_forum') or
                $authUser->can('admin_featured_topics')
                )
                <li class="menu-header">{{ trans('update.forum') }}</li>
            @endif

            @can('admin_forum')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/forums*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-comment-dots"></i>
                        <span>{{ trans('update.forums') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_forum_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/forums', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/forums">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_forum_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/forums/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/forums/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_featured_topics')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/featured-topics*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-comment"></i>
                        <span>{{ trans('update.featured_topics') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_featured_topics_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/featured-topics', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/featured-topics">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_featured_topics_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/featured-topics/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/featured-topics/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @can('admin_recommended_topics')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/recommended-topics*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-thumbs-up"></i>
                        <span>{{ trans('update.recommended_topics') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_recommended_topics_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/recommended-topics', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/recommended-topics">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()
                        @can('admin_recommended_topics_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/recommended-topics/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/recommended-topics/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan()

            @if($authUser->can('admin_supports') or
                $authUser->can('admin_comments') or
                $authUser->can('admin_reports') or
                $authUser->can('admin_contacts') or
                $authUser->can('admin_noticeboards') or
                $authUser->can('admin_notifications')
            )
                <li class="menu-header">{{ trans('admin/main.crm') }}</li>
            @endif

            @can('admin_supports')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/supports*', false)) and request()->get('type') != 'course_conversations') ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-headphones"></i>
                        <span>{{ trans('admin/main.supports') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_supports_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/supports', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/supports">{{ trans('public.tickets') }}</a>
                            </li>
                        @endcan

                        @can('admin_support_send')
                            <li class="{{ (request()->is(getAdminPanelUrl('/supports/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/supports/create">{{ trans('admin/main.new_ticket') }}</a>
                            </li>
                        @endcan

                        @can('admin_support_departments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/supports/departments', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/supports/departments">{{ trans('admin/main.departments') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>

                @can('admin_support_course_conversations')
                    <li class="{{ (request()->is(getAdminPanelUrl('/supports*', false)) and request()->get('type') == 'course_conversations') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ getAdminPanelUrl() }}/supports?type=course_conversations">
                            <i class="fas fa-envelope-square"></i>
                            <span>{{ trans('admin/main.classes_conversations') }}</span>
                        </a>
                    </li>
                @endcan
            @endcan

            @can('admin_comments')
                <li class="nav-item dropdown {{ (!request()->is(getAdminPanelUrl('admin/comments/products, false')) and (request()->is(getAdminPanelUrl('/comments*', false)) and !request()->is(getAdminPanelUrl('/comments/webinars/reports', false)))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-comments"></i> <span>{{ trans('admin/main.comments') }}</span></a>
                    <ul class="dropdown-menu">
                        @can('admin_webinar_comments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/webinars', false))) ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['classesComments']) and $sidebarBeeps['classesComments']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/comments/webinars">{{ trans('admin/main.classes_comments') }}</a>
                            </li>
                        @endcan

                        @can('admin_bundle_comments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/bundles', false))) ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['bundleComments']) and $sidebarBeeps['bundleComments']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/comments/bundles">{{ trans('update.bundle_comments') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_comments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/blog', false))) ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['blogComments']) and $sidebarBeeps['blogComments']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/comments/blog">{{ trans('admin/main.blog_comments') }}</a>
                            </li>
                        @endcan

                        @can('admin_product_comments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/products', false))) ? 'active' : '' }}">
                                <a class="nav-link @if(!empty($sidebarBeeps['productComments']) and $sidebarBeeps['productComments']) beep beep-sidebar @endif" href="{{ getAdminPanelUrl() }}/comments/products">{{ trans('update.product_comments') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_reports')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/reports*', false)) or request()->is(getAdminPanelUrl('/comments/webinars/reports', false)) or request()->is(getAdminPanelUrl('/comments/blog/reports', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-info-circle"></i> <span>{{ trans('admin/main.reports') }}</span></a>

                    <ul class="dropdown-menu">
                        @can('admin_webinar_reports')
                            <li class="{{ (request()->is(getAdminPanelUrl('/reports/webinars', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/reports/webinars">{{ trans('panel.classes') }}</a>
                            </li>
                        @endcan

                        @can('admin_webinar_comments_reports')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/webinars/reports', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/comments/webinars/reports">{{ trans('admin/main.classes_comments_reports') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_comments_reports')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/blog/reports', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/comments/blog/reports">{{ trans('admin/main.blog_comments_reports') }}</a>
                            </li>
                        @endcan

                        @can('admin_report_reasons')
                            <li class="{{ (request()->is(getAdminPanelUrl('/reports/reasons', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/reports/reasons">{{ trans('admin/main.report_reasons') }}</a>
                            </li>
                        @endcan()

                        @can('admin_forum_topic_post_reports')
                            <li class="{{ (request()->is(getAdminPanelUrl('/reports/forum-topics', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/reports/forum-topics">{{ trans('update.forum_topics') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_contacts')
                <li class="{{ (request()->is(getAdminPanelUrl('/contacts*', false))) ? 'active' : '' }}">
                    <a class="nav-link" href="{{ getAdminPanelUrl() }}/contacts">
                        <i class="fas fa-phone-square"></i>
                        <span>{{ trans('admin/main.contacts') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_noticeboards')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/noticeboards*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-sticky-note"></i> <span>{{ trans('admin/main.noticeboard') }}</span></a>
                    <ul class="dropdown-menu">
                        @can('admin_noticeboards_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/noticeboards', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/noticeboards">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan

                        @can('admin_noticeboards_send')
                            <li class="{{ (request()->is(getAdminPanelUrl('/noticeboards/send', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/noticeboards/send">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_notifications')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/notifications*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-bell"></i>
                        <span>{{ trans('admin/main.notifications') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_notifications_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications">{{ trans('public.history') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_posted_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications/posted', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications/posted">{{ trans('admin/main.posted') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_send')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications/send', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications/send">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_templates')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications/templates', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications/templates">{{ trans('admin/main.templates') }}</a>
                            </li>
                        @endcan

                        @can('admin_notifications_template_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/notifications/templates/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/notifications/templates/create">{{ trans('admin/main.new_template') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @if($authUser->can('admin_blog') or
                $authUser->can('admin_pages') or
                $authUser->can('admin_additional_pages') or
                $authUser->can('admin_testimonials') or
                $authUser->can('admin_tags') or
                $authUser->can('admin_regions') or
                $authUser->can('admin_store') or
                $authUser->can('admin_forms') or
                $authUser->can('admin_ai_contents')
            )
                <li class="menu-header">{{ trans('admin/main.content') }}</li>
            @endif

            @can('admin_store')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/store*', false)) or request()->is(getAdminPanelUrl('/comments/products*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-store-alt"></i>
                        <span>{{ trans('update.store') }}</span>
                    </a>
                    <ul class="dropdown-menu">

                        @can('admin_store_new_product')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/products/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/products/create">{{ trans('update.new_product') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_products')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/products', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/products">{{ trans('update.products') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_in_house_products')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/in-house-products', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/in-house-products">{{ trans('update.in-house-products') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_products_orders')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/orders', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/orders">{{ trans('update.orders') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_in_house_orders')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/in-house-orders', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/in-house-orders">{{ trans('update.in-house-orders') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_products_sellers')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/sellers', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/sellers">{{ trans('update.sellers') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_categories_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/categories', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/categories">{{ trans('admin/main.categories') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_filters_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/filters', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/filters">{{ trans('update.filters') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_specifications')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/specifications', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/specifications">{{ trans('update.specifications') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_discounts')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/discounts', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/discounts">{{ trans('admin/main.discounts') }}</a>
                            </li>
                        @endcan()

                        @can('admin_store_products_comments')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/products*', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/comments/products">{{ trans('admin/main.comments') }}</a>
                            </li>
                        @endcan()

                        @can('admin_products_comments_reports')
                            <li class="{{ (request()->is(getAdminPanelUrl('/comments/products/reports', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/comments/products/reports">{{ trans('admin/main.comments_reports') }}</a>
                            </li>
                        @endcan

                        @can('admin_store_products_reviews')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/reviews', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/reviews">{{ trans('admin/main.reviews') }}</a>
                            </li>
                        @endcan

                        @can('admin_store_settings')
                            <li class="{{ (request()->is(getAdminPanelUrl('/store/settings', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/store/settings">{{ trans('admin/main.settings') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_blog')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/blog*', false)) and !request()->is(getAdminPanelUrl('/blog/comments', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-rss-square"></i>
                        <span>{{ trans('admin/main.blog') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_blog_lists')
                            <li class="{{ (request()->is(getAdminPanelUrl('/blog', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/blog">{{ trans('site.posts') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/blog/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/blog/create">{{ trans('admin/main.new_post') }}</a>
                            </li>
                        @endcan

                        @can('admin_blog_categories')
                            <li class="{{ (request()->is(getAdminPanelUrl('/blog/categories', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/blog/categories">{{ trans('admin/main.categories') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan()

            @can('admin_pages')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/pages*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-pager"></i>
                        <span>{{ trans('admin/main.pages') }}</span>
                    </a>

                    <ul class="dropdown-menu">
                        @can('admin_pages_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/pages', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/pages">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()

                        @can('admin_pages_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/pages/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/pages/create">{{ trans('admin/main.new_page') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_additional_pages')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/additional_page*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-plus-circle"></i> <span>{{ trans('admin/main.additional_pages_title') }}</span></a>
                    <ul class="dropdown-menu">

                        @can('admin_additional_pages_404')
                            <li class="{{ (request()->is(getAdminPanelUrl('/additional_page/404', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/additional_page/404">{{ trans('admin/main.error_404') }}</a>
                            </li>
                        @endcan()

                        @can('admin_additional_pages_contact_us')
                            <li class="{{ (request()->is(getAdminPanelUrl('/additional_page/contact_us', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/additional_page/contact_us">{{ trans('admin/main.contact_us') }}</a>
                            </li>
                        @endcan()

                        @can('admin_additional_pages_footer')
                            <li class="{{ (request()->is(getAdminPanelUrl('/additional_page/footer', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/additional_page/footer">{{ trans('admin/main.footer') }}</a>
                            </li>
                        @endcan()

                        @can('admin_additional_pages_navbar_links')
                            <li class="{{ (request()->is(getAdminPanelUrl('/additional_page/navbar_links', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/additional_page/navbar_links">{{ trans('admin/main.top_navbar') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_testimonials')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/testimonials*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-address-card"></i>
                        <span>{{ trans('admin/main.testimonials') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_testimonials_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/testimonials', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/testimonials">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()

                        @can('admin_testimonials_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/testimonials/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/testimonials/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_tags')
                <li class="{{ (request()->is(getAdminPanelUrl('/tags', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/tags" class="nav-link">
                        <i class="fas fa-tags"></i>
                        <span>{{ trans('admin/main.tags') }}</span>
                    </a>
                </li>
            @endcan()

            @can('admin_regions')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/regions*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-map-marked"></i>
                        <span>{{ trans('update.regions') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_regions_countries')
                            <li class="{{ (request()->is(getAdminPanelUrl('/regions/countries', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/regions/countries">{{ trans('update.countries') }}</a>
                            </li>
                        @endcan()

                        @can('admin_regions_provinces')
                            <li class="{{ (request()->is(getAdminPanelUrl('/regions/provinces', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/regions/provinces">{{ trans('update.provinces') }}</a>
                            </li>
                        @endcan()

                        @can('admin_regions_cities')
                            <li class="{{ (request()->is(getAdminPanelUrl('/regions/cities', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/regions/cities">{{ trans('update.cities') }}</a>
                            </li>
                        @endcan()

                        @can('admin_regions_districts')
                            <li class="{{ (request()->is(getAdminPanelUrl('/regions/districts', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/regions/districts">{{ trans('update.districts') }}</a>
                            </li>
                        @endcan()
                    </ul>
                </li>
            @endcan

            @can('admin_forms')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/forms*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-alt"></i>
                        <span>{{ trans('update.form_builder') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_forms_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/forms/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/forms/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan()

                        @can('admin_forms_lists')
                            <li class="{{ (request()->is(getAdminPanelUrl('/forms', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/forms">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()

                        @can('admin_forms_submissions')
                            <li class="{{ (request()->is(getAdminPanelUrl('/forms/submissions', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/forms/submissions">{{ trans('update.submissions') }}</a>
                            </li>
                        @endcan()

                    </ul>
                </li>
            @endcan

            @can('admin_ai_contents')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/ai-contents*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-robot"></i>
                        <span>{{ trans('update.ai_contents') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_ai_contents_lists')
                            <li class="{{ (request()->is(getAdminPanelUrl('/ai-contents/lists', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/ai-contents/lists">{{ trans('update.generated_contents') }}</a>
                            </li>
                        @endcan()

                        @can('admin_ai_contents_templates_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/ai-contents/templates/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/ai-contents/templates/create">{{ trans('update.new_template') }}</a>
                            </li>
                        @endcan()

                        @can('admin_ai_contents_templates_lists')
                            <li class="{{ (request()->is(getAdminPanelUrl('/ai-contents/templates', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/ai-contents/templates">{{ trans('update.service_template') }}</a>
                            </li>
                        @endcan()

                        @can('admin_ai_contents_settings')
                            <li class="{{ (request()->is(getAdminPanelUrl('/ai-contents/settings', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/ai-contents/settings">{{ trans('update.settings') }}</a>
                            </li>
                        @endcan()

                    </ul>
                </li>
            @endcan

            @can('admin_content_delete_requests_lists')
                <li class="nav-item {{ (request()->is(getAdminPanelUrl('/content-delete-requests*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl("/content-delete-requests") }}" class="nav-link">
                        <i class="fas fa-trash"></i>
                        <span>{{ trans('update.content_delete_requests') }}</span>
                    </a>
                </li>
            @endcan

            @if($authUser->can('admin_documents') or
                $authUser->can('admin_sales_list') or
                $authUser->can('admin_payouts') or
                $authUser->can('admin_offline_payments_list') or
                $authUser->can('admin_subscribe') or
                $authUser->can('admin_registration_packages') or
                $authUser->can('admin_installments')
            )
                <li class="menu-header">{{ trans('admin/main.financial') }}</li>
            @endif

            @can('admin_documents')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/documents*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-invoice-dollar"></i>
                        <span>{{ trans('admin/main.balances') }}</span>
                    </a>
                    <ul class="dropdown-menu">

                        @can('admin_documents_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/documents', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/documents">{{ trans('admin/main.list') }}</a>
                            </li>
                        @endcan

                        @can('admin_documents_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/documents/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/documents/new">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_sales_list')
                <li class="nav-item {{ (request()->is(getAdminPanelUrl('/financial/sales*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/financial/sales" class="nav-link">
                        <i class="fas fa-list-ul"></i>
                        <span>{{ trans('admin/main.sales_list') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_payouts')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/payouts*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-credit-card"></i> <span>{{ trans('admin/main.payout') }}</span></a>
                    <ul class="dropdown-menu">
                        @can('admin_payouts_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/payouts', false)) and request()->get('payout') == 'requests') ? 'active' : '' }}">
                                <a href="{{ getAdminPanelUrl() }}/financial/payouts?payout=requests" class="nav-link @if(!empty($sidebarBeeps['payoutRequest']) and $sidebarBeeps['payoutRequest']) beep beep-sidebar @endif">
                                    <span>{{ trans('panel.requests') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('admin_payouts_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/payouts', false)) and request()->get('payout') == 'history') ? 'active' : '' }}">
                                <a href="{{ getAdminPanelUrl() }}/financial/payouts?payout=history" class="nav-link">
                                    <span>{{ trans('public.history') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_offline_payments_list')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/offline_payments*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown"><i class="fas fa-university"></i> <span>{{ trans('admin/main.offline_payments') }}</span></a>
                    <ul class="dropdown-menu">
                        <li class="{{ (request()->is(getAdminPanelUrl('/financial/offline_payments', false)) and request()->get('page_type') == 'requests') ? 'active' : '' }}">
                            <a href="{{ getAdminPanelUrl() }}/financial/offline_payments?page_type=requests" class="nav-link @if(!empty($sidebarBeeps['offlinePayments']) and $sidebarBeeps['offlinePayments']) beep beep-sidebar @endif">
                                <span>{{ trans('panel.requests') }}</span>
                            </a>
                        </li>

                        <li class="{{ (request()->is(getAdminPanelUrl('/financial/offline_payments', false)) and request()->get('page_type') == 'history') ? 'active' : '' }}">
                            <a href="{{ getAdminPanelUrl() }}/financial/offline_payments?page_type=history" class="nav-link">
                                <span>{{ trans('public.history') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            @can('admin_subscribe')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/subscribes*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-cart-plus"></i>
                        <span>{{ trans('admin/main.subscribes') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_subscribe_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/subscribes', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/subscribes">{{ trans('admin/main.packages') }}</a>
                            </li>
                        @endcan

                        @can('admin_subscribe_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/subscribes/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/subscribes/new">{{ trans('admin/main.new_package') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan


            @can('admin_rewards')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/rewards*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fa fa-gift"></i>
                        <span>{{ trans('update.rewards') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_rewards_history')
                            <li class="{{ (request()->is(getAdminPanelUrl('/rewards', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/rewards">{{ trans('public.history') }}</a>
                            </li>
                        @endcan
                        @can('admin_rewards_items')
                            <li class="{{ (request()->is(getAdminPanelUrl('/rewards/items', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/rewards/items">{{ trans('update.conditions') }}</a>
                            </li>
                        @endcan
                        @can('admin_rewards_settings')
                            <li class="{{ (request()->is(getAdminPanelUrl('/rewards/settings', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/rewards/settings">{{ trans('admin/main.settings') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_registration_packages')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/registration-packages*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fa fa-gem"></i>
                        <span>{{ trans('update.registration_packages') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_registration_packages_lists')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/registration-packages', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/registration-packages">{{ trans('admin/main.packages') }}</a>
                            </li>
                        @endcan

                        @can('admin_registration_packages_new')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/registration-packages/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/registration-packages/new">{{ trans('admin/main.new_package') }}</a>
                            </li>
                        @endcan

                        @can('admin_registration_packages_reports')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/registration-packages/reports', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/registration-packages/reports">{{ trans('admin/main.reports') }}</a>
                            </li>
                        @endcan

                        @can('admin_registration_packages_settings')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/registration-packages/settings', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/registration-packages/settings">{{ trans('admin/main.settings') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_installments')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/installments*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fa fa-money-bill-wave"></i>
                        <span>{{ trans('update.installments') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_installments_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/installments/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/financial/installments/create') }}">{{ trans('update.new_plan') }}</a>
                            </li>
                        @endcan

                        @can('admin_installments_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/installments', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/financial/installments') }}">{{ trans('update.plans') }}</a>
                            </li>
                        @endcan

                        @can('admin_installments_purchases')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/installments/purchases', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/financial/installments/purchases') }}">{{ trans('update.purchases') }}</a>
                            </li>
                        @endcan

                        @can('admin_installments_overdue_lists')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/installments/overdue', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/financial/installments/overdue') }}">{{ trans('update.overdue') }}</a>
                            </li>
                        @endcan

                        @can('admin_installments_overdue_history')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/installments/overdue_history', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/financial/installments/overdue_history') }}">{{ trans('update.overdue_history') }}</a>
                            </li>
                        @endcan

                        @can('admin_installments_verification_requests')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/installments/verification_requests', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/financial/installments/verification_requests') }}">{{ trans('update.verification_requests') }}</a>
                            </li>
                        @endcan

                        @can('admin_installments_verified_users')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/installments/verified_users', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/financial/installments/verified_users') }}">{{ trans('update.verified_users') }}</a>
                            </li>
                        @endcan

                        @can('admin_installments_settings')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/installments/settings', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/financial/installments/settings') }}">{{ trans('admin/main.settings') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan

            @if($authUser->can('admin_discount_codes') or
                $authUser->can('admin_cart_discount') or
                $authUser->can('admin_abandoned_cart') or
                $authUser->can('admin_product_discount') or
                $authUser->can('admin_feature_webinars') or
                $authUser->can('admin_gift') or
                $authUser->can('admin_promotion') or
                $authUser->can('admin_advertising') or
                $authUser->can('admin_newsletters') or
                $authUser->can('admin_advertising_modal') or
                $authUser->can('admin_registration_bonus') or
                $authUser->can('admin_floating_bar_create') or
                $authUser->can('admin_purchase_notifications') or
                $authUser->can('admin_product_badges')
            )
                <li class="menu-header">{{ trans('admin/main.marketing') }}</li>
            @endif

            @can('admin_discount_codes')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/discounts*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-percent"></i>
                        <span>{{ trans('admin/main.discount_codes') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_discount_codes_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/discounts', false)) and empty(request()->get('instructor_coupons'))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/discounts">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan

                        @can('admin_discount_codes_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/discounts/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/discounts/new">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan

                        @can('admin_discount_codes_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/discounts', false)) and !empty(request()->get('instructor_coupons'))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/discounts?instructor_coupons=1">{{ trans('update.instructor_coupons') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_cart_discount_controls')
                <li class="nav-item {{ (request()->is(getAdminPanelUrl('/cart_discount*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/cart_discount" class="nav-link">
                        <i class="fa fa-percentage"></i>
                        <span>{{ trans('update.cart_discount') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_product_discount')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/special_offers*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fa fa-fire"></i>
                        <span>{{ trans('admin/main.special_offers') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_product_discount_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/special_offers', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/special_offers">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan

                        @can('admin_product_discount_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/special_offers/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/special_offers/new">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_abandoned_cart')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/abandoned-cart*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fa fa-cart-plus"></i>
                        <span>{{ trans('update.abandoned_cart') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_abandoned_cart_rules')
                            <li class="{{ (request()->is(getAdminPanelUrl('/abandoned-cart/rules/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl("/abandoned-cart/rules/create") }}">{{ trans('update.new_rule') }}</a>
                            </li>

                            <li class="{{ (request()->is(getAdminPanelUrl('/abandoned-cart/rules', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl("/abandoned-cart/rules") }}">{{ trans('update.rules') }}</a>
                            </li>
                        @endcan

                        @can('admin_abandoned_cart_users')
                            <li class="{{ (request()->is(getAdminPanelUrl('/abandoned-cart/users-carts', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl("/abandoned-cart/users-carts") }}">{{ trans('update.users_carts') }}</a>
                            </li>
                        @endcan

                        @can('admin_abandoned_cart_settings')
                            <li class="{{ (request()->is(getAdminPanelUrl('/abandoned-cart/settings', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl("/abandoned-cart/settings") }}">{{ trans('update.settings') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan

            @can('admin_feature_webinars')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/webinars/features*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-star"></i>
                        <span>{{ trans('admin/main.feature_webinars') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_feature_webinars')
                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars/features', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/webinars/features">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan()

                        @can('admin_feature_webinars_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/webinars/features/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/webinars/features/create">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_cashback')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/cashback*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-wallet"></i>
                        <span>{{ trans('update.cashback') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_cashback_rules')
                            <li class="{{ (request()->is(getAdminPanelUrl('/cashback/rules/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/cashback/rules/new') }}">{{ trans('update.new_rule') }}</a>
                            </li>
                        @endcan

                        @can('admin_cashback_rules')
                            <li class="{{ (request()->is(getAdminPanelUrl('/cashback/rules', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/cashback/rules') }}">{{ trans('update.rules') }}</a>
                            </li>
                        @endcan

                        @can('admin_cashback_transactions')
                            <li class="{{ (request()->is(getAdminPanelUrl('/cashback/transactions', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/cashback/transactions') }}">{{ trans('update.transactions') }}</a>
                            </li>
                        @endcan

                        @can('admin_cashback_history')
                            <li class="{{ (request()->is(getAdminPanelUrl('/cashback/history', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/cashback/history') }}">{{ trans('update.history') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_gift')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/gifts*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-gift"></i>
                        <span>{{ trans('update.gifts') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_gift_history')
                            <li class="{{ (request()->is(getAdminPanelUrl('/gifts', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl("/gifts") }}">{{ trans('public.history') }}</a>
                            </li>
                        @endcan
                        @can('admin_gift_settings')
                            <li class="{{ (request()->is(getAdminPanelUrl('/gifts/settings', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl("/gifts/settings") }}">{{ trans('admin/main.settings') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan

            @can('admin_promotion')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/financial/promotions*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-rocket"></i>
                        <span>{{ trans('admin/main.promotions') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_promotion_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/promotions', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/promotions">{{ trans('admin/main.plans') }}</a>
                            </li>
                        @endcan
                        @can('admin_promotion_list')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/promotions/sales', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/promotions/sales">{{ trans('admin/main.promotion_sales') }}</a>
                            </li>
                        @endcan

                        @can('admin_promotion_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/financial/promotions/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/financial/promotions/new">{{ trans('admin/main.new_plan') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_advertising')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/advertising*', false)) and !request()->is(getAdminPanelUrl('/advertising_modal*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-file-image"></i>
                        <span>{{ trans('admin/main.ad_banners') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_advertising_banners')
                            <li class="{{ (request()->is(getAdminPanelUrl('/advertising/banners', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/advertising/banners">{{ trans('admin/main.list') }}</a>
                            </li>
                        @endcan

                        @can('admin_advertising_banners_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/advertising/banners/new', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/advertising/banners/new">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_newsletters')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/newsletters*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-newspaper"></i>
                        <span>{{ trans('admin/main.newsletters') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_newsletters_lists')
                            <li class="{{ (request()->is(getAdminPanelUrl('/newsletters', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/newsletters">{{ trans('admin/main.list') }}</a>
                            </li>
                        @endcan

                        @can('admin_newsletters_send')
                            <li class="{{ (request()->is(getAdminPanelUrl('/newsletters/send', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/newsletters/send">{{ trans('admin/main.send') }}</a>
                            </li>
                        @endcan

                        @can('admin_newsletters_history')
                            <li class="{{ (request()->is(getAdminPanelUrl('/newsletters/history', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/newsletters/history">{{ trans('public.history') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_referrals')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/referrals*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fa fa-bullhorn"></i>
                        <span>{{ trans('panel.affiliate') }}</span>
                    </a>
                    <ul class="dropdown-menu">
                        @can('admin_referrals_history')
                            <li class="{{ (request()->is(getAdminPanelUrl('/referrals/history', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/referrals/history">{{ trans('public.history') }}</a>
                            </li>
                        @endcan

                        @can('admin_referrals_users')
                            <li class="{{ (request()->is(getAdminPanelUrl('/referrals/users', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl() }}/referrals/users">{{ trans('admin/main.affiliate_users') }}</a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan

            @can('admin_registration_bonus')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/registration_bonus*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fa fa-gem"></i>
                        <span>{{ trans('update.registration_bonus') }}</span>
                    </a>
                    <ul class="dropdown-menu">

                        @can('admin_registration_bonus_history')
                            <li class="{{ (request()->is(getAdminPanelUrl('/registration_bonus/history', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/registration_bonus/history') }}">{{ trans('update.bonus_history') }}</a>
                            </li>
                        @endcan


                        @can('admin_registration_bonus_settings')
                            <li class="{{ (request()->is(getAdminPanelUrl('/registration_bonus/settings', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/registration_bonus/settings') }}">{{ trans('admin/main.settings') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan

            @can('admin_advertising_modal_config')
                <li class="nav-item {{ (request()->is(getAdminPanelUrl('/advertising_modal*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/advertising_modal" class="nav-link">
                        <i class="fa fa-ad"></i>
                        <span>{{ trans('update.advertising_modal') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_floating_bar_create')
                <li class="nav-item {{ (request()->is(getAdminPanelUrl('/floating_bars*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/floating_bars" class="nav-link">
                        <i class="fa fa-pager"></i>
                        <span>{{ trans('update.top_bottom_bar') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_purchase_notifications')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/purchase_notifications*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fa fa-clipboard-check"></i>
                        <span>{{ trans('update.purchase_notifications') }}</span>
                    </a>
                    <ul class="dropdown-menu">

                        @can('admin_purchase_notifications_lists')
                            <li class="{{ (request()->is(getAdminPanelUrl('/purchase_notifications', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/purchase_notifications') }}">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan


                        @can('admin_purchase_notifications_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/purchase_notifications/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/purchase_notifications/create') }}">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan

            @can('admin_product_badges')
                <li class="nav-item dropdown {{ (request()->is(getAdminPanelUrl('/product-badges*', false))) ? 'active' : '' }}">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fa fa-tag"></i>
                        <span>{{ trans('update.product_badges') }}</span>
                    </a>
                    <ul class="dropdown-menu">

                        @can('admin_product_badges_lists')
                            <li class="{{ (request()->is(getAdminPanelUrl('/product-badges', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/product-badges') }}">{{ trans('admin/main.lists') }}</a>
                            </li>
                        @endcan


                        @can('admin_product_badges_create')
                            <li class="{{ (request()->is(getAdminPanelUrl('/product-badges/create', false))) ? 'active' : '' }}">
                                <a class="nav-link" href="{{ getAdminPanelUrl('/product-badges/create') }}">{{ trans('admin/main.new') }}</a>
                            </li>
                        @endcan

                    </ul>
                </li>
            @endcan

            @if($authUser->can('admin_settings'))
                <li class="menu-header">{{ trans('admin/main.settings') }}</li>
            @endif

            @can('admin_translator')
                <li class="nav-item {{ (request()->is(getAdminPanelUrl('/translator*', false))) ? 'active' : '' }}">
                    <a href="{{ getAdminPanelUrl() }}/translator" class="nav-link">
                        <i class="fa fa-language"></i>
                        <span>{{ trans('update.translator') }}</span>
                    </a>
                </li>
            @endcan

            @can('admin_settings')
                @php
                    $settingClass ='';

                    if (request()->is(getAdminPanelUrl('/settings*', false)) and
                            !(
                                request()->is(getAdminPanelUrl('/settings/404', false)) or
                                request()->is(getAdminPanelUrl('/settings/contact_us', false)) or
                                request()->is(getAdminPanelUrl('/settings/footer', false)) or
                                request()->is(getAdminPanelUrl('/settings/navbar_links', false))
                            )
                        ) {
                            $settingClass = 'active';
                        }
                @endphp

                <li class="nav-item {{ $settingClass ?? '' }}">
                    <a href="{{ getAdminPanelUrl() }}/settings" class="nav-link">
                        <i class="fas fa-cogs"></i>
                        <span>{{ trans('admin/main.settings') }}</span>
                    </a>
                </li>
            @endcan()


            <li>
                <a class="nav-link" href="{{ getAdminPanelUrl() }}/logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>{{ trans('admin/main.logout') }}</span>
                </a>
            </li>

        </ul>
        <br><br><br>
    </aside>
</div>
