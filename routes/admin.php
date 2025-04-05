<?php

use Illuminate\Support\Facades\Route;

$prefix = getAdminPanelUrlPrefix();

Route::group(['prefix' => $prefix, 'namespace' => 'Admin', 'middleware' => ['web', 'admin_locale']], function () use ($prefix) {

    // Admin Auth Routes
    Route::get('login', 'LoginController@showLoginForm');
    Route::post('login', 'LoginController@login');
    Route::get('logout', 'LoginController@logout');

    Route::get('/forget-password', 'ForgotPasswordController@showLinkRequestForm');
    Route::post('/forget-password', 'ForgotPasswordController@forgot');
    Route::get('/reset-password/{token}', 'ResetPasswordController@showResetForm');
    Route::post('/reset-password', 'ResetPasswordController@updatePassword');

    // Captcha
    Route::group(['prefix' => 'captcha'], function () use ($prefix) {
        Route::post('create', function () use ($prefix) {
            $path = captcha_src('flat');
            $path = str_replace('/captcha', "/$prefix/captcha", $path);

            $response = ['status' => 'success', 'captcha_src' => $path];

            return response()->json($response);
        });
        Route::get('{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');
    });


    Route::group(['middleware' => 'admin'], function () {

        Route::get('/', 'DashboardController@index');
        Route::get('/clear-cache', 'DashboardController@cacheClear');

        Route::group(['prefix' => 'dashboard'], function () {
            Route::post('/getSaleStatisticsData', 'DashboardController@getSaleStatisticsData');
        });

        Route::group(['prefix' => 'marketing'], function () {
            Route::get('/', 'DashboardController@marketing');
            Route::post('/getNetProfitChart', 'DashboardController@getNetProfitChartAjax');
        });

        Route::group(['prefix' => 'roles'], function () {
            Route::get('/', 'RoleController@index');
            Route::get('/create', 'RoleController@create');
            Route::post('/store', 'RoleController@store');
            Route::get('/{id}/edit', 'RoleController@edit');
            Route::post('/{id}/update', 'RoleController@update');
            Route::get('/{id}/delete', 'RoleController@destroy');
        });

        Route::group(['prefix' => 'staffs'], function () {
            Route::get('/', 'UserController@staffs');
        });

        Route::group(['prefix' => 'students'], function () {
            Route::get('/', 'UserController@students');
            Route::get('/excel', 'UserController@exportExcelStudents');
        });

        Route::group(['prefix' => 'instructors'], function () {
            Route::get('/', 'UserController@instructors');
            Route::get('/excel', 'UserController@exportExcelInstructors');
        });

        Route::group(['prefix' => 'organizations'], function () {
            Route::get('/', 'UserController@organizations');
            Route::get('/excel', 'UserController@exportExcelOrganizations');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/create', 'UserController@create');
            Route::post('/store', 'UserController@store');
            Route::post('/search', 'UserController@search');
            Route::get('/{id}/edit', 'UserController@edit');
            Route::post('/{id}/update', 'UserController@update');
            Route::post('/{id}/updateImage', 'UserController@updateImage');
            Route::post('/{id}/updateFormFields', 'UserController@updateFormFields');
            Route::post('/{id}/financialUpdate', 'UserController@financialUpdate');
            Route::post('/{id}/occupationsUpdate', 'UserController@occupationsUpdate');
            Route::post('/{id}/badgesUpdate', 'UserController@badgesUpdate');
            Route::post('/{id}/userRegistrationPackage', 'UserController@userRegistrationPackage');
            Route::post('/{id}/meetingSettings', 'UserController@meetingSettings');
            Route::get('/{id}/deleteBadge/{badge_id}', 'UserController@deleteBadge');
            Route::get('/{id}/delete', 'UserController@destroy');
            Route::get('/{id}/acceptRequestToInstructor', 'UserController@acceptRequestToInstructor');
            Route::get('/{user_id}/impersonate', 'UserController@impersonate');
            Route::get('/{user_id}/disable_cashback_toggle', 'UserController@disableCashbackToggle');
            Route::get('/{user_id}/disable_registration_bonus', 'UserController@disableRegitrationBonusStatus');
            Route::get('/{user_id}/disable_installment_approval', 'UserController@disableInstallmentApproval');

            Route::group(['prefix' => 'badges'], function () {
                Route::get('/', 'BadgesController@index');
                Route::post('/store', 'BadgesController@store');
                Route::get('/{id}/edit', 'BadgesController@edit');
                Route::post('/{id}/update', 'BadgesController@update');
                Route::get('/{id}/delete', 'BadgesController@delete');
            });

            Route::group(['prefix' => 'groups'], function () {
                Route::get('/', 'GroupController@index');
                Route::get('/create', 'GroupController@create');
                Route::post('/store', 'GroupController@store');
                Route::get('/{id}/edit', 'GroupController@edit');
                Route::post('/{id}/update', 'GroupController@update');
                Route::get('/{id}/delete', 'GroupController@destroy');
                Route::post('/{id}/groupRegistrationPackage', 'GroupController@groupRegistrationPackage');
            });

            Route::group(['prefix' => 'become-instructors'], function () {
                Route::get('/{page}', 'BecomeInstructorController@index');
                Route::get('/{id}/reject', 'BecomeInstructorController@reject');
                Route::get('/{id}/delete', 'BecomeInstructorController@delete');
            });

            Route::group(['prefix' => 'not-access-to-content'], function () {
                Route::get('/', 'UsersNotAccessToContentController@index');
                Route::post('/store', 'UsersNotAccessToContentController@store');
                Route::get('/{id}/active', 'UsersNotAccessToContentController@active');
                Route::get('/{id}/reject', 'UsersNotAccessToContentController@reject');
            });

            Route::group(['prefix' => 'delete-account-requests'], function () {
                Route::get('/', 'DeleteAccountRequestsController@index');
                Route::get('/{id}/confirm', 'DeleteAccountRequestsController@confirm');
            });

            Route::group(['prefix' => 'login-history'], function () {
                Route::get('/', 'UserLoginHistoryController@index');
                Route::get('/export', 'UserLoginHistoryController@export');
                Route::get('/{id}/end-session', 'UserLoginHistoryController@endSession');
                Route::get('/{id}/delete', 'UserLoginHistoryController@delete');
            });

            Route::group(['prefix' => 'ip-restriction'], function () {
                Route::get('/', 'UserIpRestrictionController@index');
                Route::get('/get-form', 'UserIpRestrictionController@getForm');
                Route::post('/store', 'UserIpRestrictionController@store');
                Route::get('/{id}/edit', 'UserIpRestrictionController@edit');
                Route::post('/{id}/update', 'UserIpRestrictionController@update');
                Route::get('/{id}/delete', 'UserIpRestrictionController@delete');
            });

            /* end-all-login-sessions */
            Route::get('/{user_id}/end-all-login-sessions', "UserLoginHistoryController@endAllUserSessions");
        });

        Route::group(['prefix' => 'supports'], function () {
            Route::get('/', 'SupportsController@index');
            Route::get('/create', 'SupportsController@create');
            Route::post('/store', 'SupportsController@store');
            Route::get('/{id}/edit', 'SupportsController@edit');
            Route::post('/{id}/update', 'SupportsController@update');
            Route::get('/{id}/delete', 'SupportsController@delete');

            Route::get('/{id}/close', 'SupportsController@conversationClose');
            Route::get('/{id}/conversation', 'SupportsController@conversation');
            Route::post('/{id}/conversation', 'SupportsController@storeConversation');

            Route::group(['prefix' => 'departments'], function () {
                Route::get('/', 'SupportDepartmentsController@index');
                Route::get('/create', 'SupportDepartmentsController@create');
                Route::post('/store', 'SupportDepartmentsController@store');
                Route::get('/{id}/edit', 'SupportDepartmentsController@edit');
                Route::post('/{id}/update', 'SupportDepartmentsController@update');
                Route::get('/{id}/delete', 'SupportDepartmentsController@delete');
            });
        });

        Route::group(['prefix' => 'noticeboards'], function () {
            Route::get('/', 'NoticeboardController@index');
            Route::get('/send', 'NoticeboardController@create');
            Route::post('/store', 'NoticeboardController@store');
            Route::get('{id}/edit', 'NoticeboardController@edit');
            Route::post('{id}/update', 'NoticeboardController@update');
            Route::get('{id}/delete', 'NoticeboardController@delete');
        });

        Route::group(['prefix' => 'course-noticeboards'], function () {
            Route::get('/', 'CourseNoticeboardController@index');
            Route::get('/send', 'CourseNoticeboardController@create');
            Route::post('/store', 'CourseNoticeboardController@store');
            Route::get('{id}/edit', 'CourseNoticeboardController@edit');
            Route::post('{id}/update', 'CourseNoticeboardController@update');
            Route::get('{id}/delete', 'CourseNoticeboardController@delete');
        });

        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', 'NotificationsController@index');
            Route::get('/posted', 'NotificationsController@posted');
            Route::get('/send', 'NotificationsController@create');
            Route::post('/store', 'NotificationsController@store');
            Route::get('{id}/edit', 'NotificationsController@edit');
            Route::post('{id}/update', 'NotificationsController@update');
            Route::get('{id}/delete', 'NotificationsController@delete');
            Route::get('/mark_all_read', 'NotificationsController@markAllRead');
            Route::get('/{id}/mark_as_read', 'NotificationsController@markAsRead');

            Route::group(['prefix' => 'templates'], function () {
                Route::get('/', 'NotificationTemplatesController@index');
                Route::get('/create', 'NotificationTemplatesController@create');
                Route::post('/store', 'NotificationTemplatesController@store');
                Route::get('{id}/edit', 'NotificationTemplatesController@edit');
                Route::post('{id}/update', 'NotificationTemplatesController@update');
                Route::get('{id}/delete', 'NotificationTemplatesController@delete');
            });
        });

        Route::group(['prefix' => 'categories'], function () {
            Route::get('/', 'CategoryController@index');
            Route::get('/create', 'CategoryController@create');
            Route::post('/store', 'CategoryController@store');
            Route::get('/{id}/edit', 'CategoryController@edit');
            Route::post('/{id}/update', 'CategoryController@update');
            Route::get('/{id}/delete', 'CategoryController@destroy');
            Route::post('/search', 'CategoryController@search');

            Route::group(['prefix' => 'trends'], function () {
                Route::get('/', 'TrendCategoriesController@index');
                Route::get('/create', 'TrendCategoriesController@create');
                Route::post('/store', 'TrendCategoriesController@store');
                Route::get('/{id}/edit', 'TrendCategoriesController@edit');
                Route::post('/{id}/update', 'TrendCategoriesController@update');
                Route::get('/{id}/delete', 'TrendCategoriesController@destroy');
            });
        });

        Route::group(['prefix' => 'filters'], function () {
            Route::get('/', 'FilterController@index');
            Route::get('/create', 'FilterController@create');
            Route::post('/store', 'FilterController@store');
            Route::get('/{id}/edit', 'FilterController@edit');
            Route::post('/{id}/update', 'FilterController@update');
            Route::get('/{id}/delete', 'FilterController@destroy');
        });

        Route::group(['prefix' => 'tags'], function () {
            Route::get('/', 'TagController@index');
            Route::get('/create', 'TagController@create');
            Route::post('/store', 'TagController@store');
            Route::get('/{id}/edit', 'TagController@edit');
            Route::post('/{id}/update', 'TagController@update');
            Route::get('/{id}/delete', 'TagController@destroy');
        });

        Route::group(['prefix' => 'comments/{page}'], function () {
            Route::get('/', 'CommentsController@index');
            Route::get('/{comment_id}/toggle', 'CommentsController@toggleStatus');
            Route::get('/{comment_id}/edit', 'CommentsController@edit');
            Route::post('/{comment_id}/update', 'CommentsController@update');
            Route::get('/{comment_id}/reply', 'CommentsController@reply');
            Route::post('/{comment_id}/reply', 'CommentsController@storeReply');
            Route::get('/{comment_id}/delete', 'CommentsController@delete');

            Route::group(['prefix' => 'reports'], function () {
                Route::get('/', 'CommentsController@reports');
                Route::get('/{id}/show', 'CommentsController@reportShow');
                Route::get('/{id}/delete', 'CommentsController@reportDelete');
            });
        });

        Route::group(['prefix' => 'reports'], function () {
            Route::get('/reasons', 'ReportsController@reasons');
            Route::post('/reasons', 'ReportsController@storeReasons');
            Route::get('/webinars', 'ReportsController@webinarsReports');
            Route::get('/webinars/{id}/delete', 'ReportsController@delete');

            Route::group(['prefix' => 'forum-topics'], function () {
                Route::get('/', 'ForumTopicReportsController@index');
                Route::get('/{id}/delete', 'ForumTopicReportsController@delete');
            });
        });

        Route::group(['prefix' => 'webinars'], function () {
            Route::get('/', 'WebinarController@index');
            Route::get('/create', 'WebinarController@create');
            Route::post('/store', 'WebinarController@store');
            Route::get('/{id}/edit', 'WebinarController@edit');
            Route::post('/{id}/update', 'WebinarController@update');
            Route::get('/{id}/delete', 'WebinarController@destroy');
            Route::get('/{id}/approve', 'WebinarController@approve');
            Route::get('/{id}/reject', 'WebinarController@reject');
            Route::get('/{id}/unpublish', 'WebinarController@unpublish');
            Route::post('/search', 'WebinarController@search');
            Route::get('/excel', 'WebinarController@exportExcel');
            Route::get('/{id}/students', 'WebinarController@studentsLists');
            Route::get('/{id}/sendNotification', 'WebinarController@notificationToStudents');
            Route::post('/{id}/sendNotification', 'WebinarController@sendNotificationToStudents');
            Route::post('/add-student-to-course', 'WebinarController@addStudentToCourse');
            Route::post('/order-items', 'WebinarController@orderItems');
            Route::post('/{id}/getContentItemByLocale', 'WebinarController@getContentItemByLocale');

            Route::get('/{id}/statistics', 'WebinarStatisticController@index');

            Route::group(['prefix' => 'features'], function () {
                Route::get('/', 'FeatureWebinarsControllers@index');
                Route::get('/create', 'FeatureWebinarsControllers@create');
                Route::post('/store', 'FeatureWebinarsControllers@store');
                Route::get('/{id}/edit', 'FeatureWebinarsControllers@edit');
                Route::post('/{id}/update', 'FeatureWebinarsControllers@update');
                Route::get('{feature_id}/{toggle}', 'FeatureWebinarsControllers@toggle');
                Route::get('/excel', 'FeatureWebinarsControllers@exportExcel');
            });

            Route::get('/course_forums', 'CourseForumsControllers@index');

            Route::group(['prefix' => '{webinar_id}/forums'], function () {
                Route::get('/', 'CourseForumsControllers@forums');
                Route::get('/{forum_id}/edit', 'CourseForumsControllers@forumEdit');
                Route::get('/{forum_id}/delete', 'CourseForumsControllers@forumDelete');
                Route::post('/{forum_id}/edit', 'CourseForumsControllers@forumUpdate');
                Route::get('/{forum_id}/answers', 'CourseForumsControllers@answers');
                Route::get('/{forum_id}/answers/{id}/edit', 'CourseForumsControllers@answerEdit');
                Route::get('/{forum_id}/answers/{id}/delete', 'CourseForumsControllers@answerDelete');
                Route::post('/{forum_id}/answers/{id}/edit', 'CourseForumsControllers@answerUpdate');
            });

            Route::group(['prefix' => 'personal-notes'], function () {
                Route::get('/', 'CoursePersonalNotesController@index');
                Route::get('/{id}/download-attachment', 'CoursePersonalNotesController@downloadAttachment');
                Route::post('/{id}/update', 'CoursePersonalNotesController@update');
                Route::get('/{id}/delete', 'CoursePersonalNotesController@delete');
            });
        });

        Route::group(['prefix' => 'quizzes'], function () {
            Route::get('/', 'QuizController@index');
            Route::get('/create', 'QuizController@create');
            Route::post('/store', 'QuizController@store');
            Route::get('/{id}/edit', 'QuizController@edit')->name('adminEditQuiz');
            Route::post('/{id}/update', 'QuizController@update');
            Route::get('/{id}/delete', 'QuizController@delete');
            Route::get('/{id}/results', 'QuizController@results');
            Route::get('/{id}/results/excel', 'QuizController@resultsExportExcel');
            Route::get('/result/{result_id}/delete', 'QuizController@resultDelete');
            Route::get('/excel', 'QuizController@exportExcel');
            Route::post('/{id}/order-items', 'QuizController@orderItems');
        });

        Route::group(['prefix' => 'quizzes-questions'], function () {
            Route::post('/store', 'QuizQuestionController@store');
            Route::get('/{id}/edit', 'QuizQuestionController@edit');
            Route::get('/{id}/getQuestionByLocale', 'QuizQuestionController@getQuestionByLocale');
            removeContentLocale();
            Route::post('/{id}/update', 'QuizQuestionController@update');
            Route::get('/{id}/delete', 'QuizQuestionController@destroy');
        });


        Route::group(['prefix' => 'filters'], function () {
            Route::get('/get-by-category-id/{categoryId}', 'FilterController@getByCategoryId');
        });

        Route::group(['prefix' => 'tickets'], function () {
            Route::post('/store', 'TicketController@store');
            Route::post('/{id}/edit', 'TicketController@edit');
            Route::post('/{id}/update', 'TicketController@update');
            Route::get('/{id}/delete', 'TicketController@destroy');
        });

        Route::group(['prefix' => 'chapters'], function () {
            Route::get('/{id}', 'ChapterController@getChapter');
            Route::get('/getAllByWebinarId/{webinar_id}', 'ChapterController@getAllByWebinarId');
            Route::post('/store', 'ChapterController@store');
            Route::post('/{id}/edit', 'ChapterController@edit');
            Route::post('/{id}/update', 'ChapterController@update');
            Route::get('/{id}/delete', 'ChapterController@destroy');
            Route::post('/change', 'ChapterController@change');
        });

        Route::group(['prefix' => 'sessions'], function () {
            Route::post('/store', 'SessionController@store');
            Route::post('/{id}/edit', 'SessionController@edit');
            Route::post('/{id}/update', 'SessionController@update');
            Route::get('/{id}/delete', 'SessionController@destroy');
        });

        Route::group(['prefix' => 'files'], function () {
            Route::post('/store', 'FileController@store');
            Route::post('/{id}/edit', 'FileController@edit');
            Route::post('/{id}/update', 'FileController@update');
            Route::get('/{id}/delete', 'FileController@destroy');
        });

        Route::group(['prefix' => 'text-lesson'], function () {
            Route::post('/store', 'TextLessonsController@store');
            Route::post('/{id}/edit', 'TextLessonsController@edit');
            Route::post('/{id}/update', 'TextLessonsController@update');
            Route::get('/{id}/delete', 'TextLessonsController@destroy');
        });

        Route::group(['prefix' => 'assignments'], function () {
            Route::get('/', 'AssignmentController@index');
            Route::get('/{id}/students', 'AssignmentController@students');
            Route::get('/{assignmentId}/history/{historyId}/conversations', 'AssignmentController@conversations');
            Route::post('/store', 'AssignmentController@store');
            Route::post('/{id}/edit', 'AssignmentController@edit');
            Route::post('/{id}/update', 'AssignmentController@update');
            Route::get('/{id}/delete', 'AssignmentController@destroy');
        });

        Route::group(['prefix' => 'prerequisites'], function () {
            Route::post('/store', 'PrerequisiteController@store');
            Route::post('/{id}/edit', 'PrerequisiteController@edit');
            Route::post('/{id}/update', 'PrerequisiteController@update');
            Route::get('/{id}/delete', 'PrerequisiteController@destroy');
        });

        Route::group(['prefix' => 'relatedCourses'], function () {
            Route::get('/get-form', 'RelatedCoursesController@getForm');
            Route::post('/store', 'RelatedCoursesController@store');
            Route::get('/{id}/edit', 'RelatedCoursesController@getForm');
            Route::post('/{id}/update', 'RelatedCoursesController@update');
            Route::get('/{id}/delete', 'RelatedCoursesController@destroy');
        });

        Route::group(['prefix' => 'faqs'], function () {
            Route::post('/store', 'FAQController@store');
            Route::post('/{id}/description', 'FAQController@description');
            Route::post('/{id}/edit', 'FAQController@edit');
            Route::post('/{id}/update', 'FAQController@update');
            Route::get('/{id}/delete', 'FAQController@destroy');
        });

        Route::group(['prefix' => 'webinar-extra-description'], function () {
            Route::post('/store', 'WebinarExtraDescriptionController@store');
            Route::post('/{id}/edit', 'WebinarExtraDescriptionController@edit');
            Route::post('/{id}/update', 'WebinarExtraDescriptionController@update');
            Route::get('/{id}/delete', 'WebinarExtraDescriptionController@destroy');
        });

        Route::group(['prefix' => 'webinar-quiz'], function () {
            Route::post('/store', 'WebinarQuizController@store');
            Route::post('/{id}/edit', 'WebinarQuizController@edit');
            Route::post('/{id}/update', 'WebinarQuizController@update');
            Route::get('/{id}/delete', 'WebinarQuizController@destroy');
        });

        Route::group(['prefix' => 'certificates'], function () {
            Route::get('/', 'CertificateController@index');
            Route::get('/excel', 'CertificateController@exportExcel');

            Route::group(['prefix' => 'templates'], function () {
                Route::get('/', 'CertificateController@CertificatesTemplatesList');
                Route::get('/new', 'CertificateController@CertificatesNewTemplate');
                Route::post('/store', 'CertificateController@CertificatesTemplateStore');
                Route::post('/preview', 'CertificateController@CertificatesTemplatePreview');
                Route::get('/{template_id}/edit', 'CertificateController@CertificatesTemplatesEdit');
                Route::post('/{template_id}/update', 'CertificateController@CertificatesTemplateStore');
                Route::get('/{template_id}/delete', 'CertificateController@CertificatesTemplatesDelete');
            });
            Route::get('/{id}/download', 'CertificateController@CertificatesDownload');

            Route::group(['prefix' => 'course-competition'], function () {
                Route::get('/', 'WebinarCertificateController@index');
                Route::get('/{certificate_id}/show', 'WebinarCertificateController@show');
            });

            Route::group(['prefix' => 'settings'], function () {
                Route::get('/', 'CertificateController@settings');
                Route::post('/', 'CertificateController@storeSettings');
            });
        });

        Route::group(['prefix' => 'reviews'], function () {
            Route::get('/', 'ReviewsController@index');
            Route::get('/{id}/toggleStatus', 'ReviewsController@toggleStatus');
            Route::get('/{id}/reply', 'ReviewsController@reply');
            Route::get('/{id}/delete', 'ReviewsController@delete');
        });

        Route::group(['prefix' => 'consultants'], function () {
            Route::get('/', 'ConsultantsController@index');
            Route::get('/excel', 'ConsultantsController@exportExcel');

        });

        Route::group(['prefix' => 'appointments'], function () {
            Route::get('/', 'AppointmentsController@index');
            Route::get('/{id}/join', 'AppointmentsController@join');
            Route::get('/{id}/getReminderDetails', 'AppointmentsController@getReminderDetails');
            Route::get('/{id}/sendReminder', 'AppointmentsController@sendReminder');
            Route::get('/{id}/cancel', 'AppointmentsController@cancel');
        });

        Route::group(['prefix' => 'blog'], function () {
            Route::get('/', 'BlogController@index');
            Route::get('/create', 'BlogController@create');
            Route::post('/store', 'BlogController@store');
            Route::post('/search', 'BlogController@search');
            Route::get('/{id}/edit', 'BlogController@edit');
            Route::post('/{id}/update', 'BlogController@update');
            Route::get('/{id}/delete', 'BlogController@delete');

            Route::group(['prefix' => 'categories'], function () {
                Route::get('/', 'BlogCategoriesController@index');
                Route::post('/store', 'BlogCategoriesController@store');
                Route::get('/{id}/edit', 'BlogCategoriesController@edit');
                Route::post('/{id}/update', 'BlogCategoriesController@update');
                Route::get('/{id}/delete', 'BlogCategoriesController@delete');
            });
        });

        Route::group(['prefix' => 'financial'], function () {

            Route::group(['prefix' => 'sales'], function () {
                Route::get('/', 'SaleController@index');
                Route::get('/{id}/refund', 'SaleController@refund');
                Route::get('/{id}/invoice', 'SaleController@invoice');
                Route::get('/export', 'SaleController@exportExcel');
            });

            Route::group(['prefix' => 'payouts'], function () {
                Route::get('/', 'PayoutController@index');
                Route::get('/{id}/reject', 'PayoutController@reject');
                Route::get('/{id}/payout', 'PayoutController@payout');
                Route::get('/excel', 'PayoutController@exportExcel');
            });

            Route::group(['prefix' => 'offline_payments'], function () {
                Route::get('/', 'OfflinePaymentController@index');
                Route::get('/excel', 'OfflinePaymentController@exportExcel');
                Route::get('/{id}/reject', 'OfflinePaymentController@reject');
                Route::get('/{id}/approved', 'OfflinePaymentController@approved');
            });

            Route::group(['prefix' => 'discounts'], function () {
                Route::get('/', 'DiscountController@index');
                Route::get('/new', 'DiscountController@create');
                Route::post('/store', 'DiscountController@store');
                Route::get('/{id}/edit', 'DiscountController@edit');
                Route::post('/{id}/update', 'DiscountController@update');
                Route::get('/{id}/delete', 'DiscountController@destroy');
            });

            Route::group(['prefix' => 'special_offers'], function () {
                Route::get('/', 'SpecialOfferController@index');
                Route::get('/new', 'SpecialOfferController@create');
                Route::post('/store', 'SpecialOfferController@store');
                Route::get('/{id}/edit', 'SpecialOfferController@edit');
                Route::post('/{id}/update', 'SpecialOfferController@update');
                Route::get('/{id}/delete', 'SpecialOfferController@destroy');
            });

            Route::group(['prefix' => 'documents'], function () {
                Route::get('/', 'DocumentController@index');
                Route::get('/new', 'DocumentController@create');
                Route::post('/store', 'DocumentController@store');
                Route::get('/{id}/print', 'DocumentController@printer');
            });

            Route::group(['prefix' => 'subscribes'], function () {
                Route::get('/', 'SubscribesController@index');
                Route::get('/new', 'SubscribesController@create');
                Route::post('/store', 'SubscribesController@store');
                Route::get('/{id}/edit', 'SubscribesController@edit');
                Route::post('/{id}/update', 'SubscribesController@update');
                Route::get('/{id}/delete', 'SubscribesController@delete');
            });

            Route::group(['prefix' => 'promotions'], function () {
                Route::get('/', 'PromotionsController@index');
                Route::get('/new', 'PromotionsController@create');
                Route::get('/sales', 'PromotionsController@sales');
                Route::post('/store', 'PromotionsController@store');
                Route::get('/{id}/edit', 'PromotionsController@edit');
                Route::post('/{id}/update', 'PromotionsController@update');
                Route::get('/{id}/delete', 'PromotionsController@delete');
            });

            Route::group(['prefix' => 'registration-packages'], function () {
                Route::get('/', 'RegistrationPackagesController@index')->name('adminRegistrationPackagesLists');
                Route::get('/new', 'RegistrationPackagesController@create');
                Route::post('/store', 'RegistrationPackagesController@store');
                Route::get('/{id}/edit', 'RegistrationPackagesController@edit');
                Route::post('/{id}/update', 'RegistrationPackagesController@update');
                Route::get('/{id}/delete', 'RegistrationPackagesController@delete');
                Route::get('/settings', 'RegistrationPackagesController@settings');
                Route::get('/reports', 'RegistrationPackagesController@reports');
            });

            Route::group(['prefix' => 'installments'], function () {
                Route::get('/', 'InstallmentsController@index');
                Route::get('/create', 'InstallmentsController@create');
                Route::post('/store', 'InstallmentsController@store');
                Route::get('/{id}/edit', 'InstallmentsController@edit');
                Route::post('/{id}/update', 'InstallmentsController@update');
                Route::get('/{id}/delete', 'InstallmentsController@delete');

                Route::group(['prefix' => 'settings'], function () {
                    Route::get('/', 'InstallmentsController@settings');
                    Route::post('/', 'InstallmentsController@storeSettings');
                });

                Route::group(['prefix' => 'orders'], function () {
                    Route::get('/{id}/details', 'InstallmentsController@details');
                    Route::get('/{id}/cancel', 'InstallmentsController@cancel');
                    Route::get('/{id}/refund', 'InstallmentsController@refund');
                    Route::get('/{id}/approve', 'InstallmentsController@approve');
                    Route::get('/{id}/reject', 'InstallmentsController@reject');
                    Route::get('/{id}/attachments/{attachment_id}/download', 'InstallmentsController@downloadAttachment');
                });

                Route::get('/purchases', 'InstallmentsController@purchases');
                Route::get('/purchases/export', 'InstallmentsController@purchasesExportExcel');

                Route::get('/overdue', 'InstallmentsController@overdueLists');
                Route::get('/overdue/export', 'InstallmentsController@overdueListsExportExcel');

                Route::get('/overdue_history', 'InstallmentsController@overdueHistories');
                Route::get('/overdue_history/export', 'InstallmentsController@overdueHistoriesExportExcel');

                Route::get('/verification_requests', 'InstallmentsController@verificationRequests');

                Route::get('/verified_users', 'InstallmentsController@verifiedUsers');
                Route::get('/verified_users/export', 'InstallmentsController@verifiedUsersExportExcel');
            });
        });

        Route::group(['prefix' => 'advertising'], function () {
            Route::group(['prefix' => 'banners'], function () {
                Route::get('/', 'AdvertisingBannersController@index');
                Route::get('/new', 'AdvertisingBannersController@create');
                Route::post('/store', 'AdvertisingBannersController@store');
                Route::get('/{id}/edit', 'AdvertisingBannersController@edit');
                Route::post('/{id}/update', 'AdvertisingBannersController@update');
                Route::get('/{id}/delete', 'AdvertisingBannersController@delete');
            });
        });

        Route::group(['prefix' => 'newsletters'], function () {
            Route::get('/', 'NewslettersController@index');
            Route::get('/send', 'NewslettersController@send');
            Route::post('/send', 'NewslettersController@sendNewsletter');
            Route::get('/history', 'NewslettersController@history');
            Route::get('/{id}/delete', 'NewslettersController@delete');
            Route::get('/excel', 'NewslettersController@exportExcel');
        });

        Route::group(['prefix' => 'referrals'], function () {
            Route::get('/history', 'ReferralController@history');
            Route::get('/users', 'ReferralController@users');
            Route::get('/excel', 'ReferralController@exportExcel');
        });

        Route::group(['prefix' => 'additional_page'], function () {
            Route::group(['prefix' => '/navbar_links'], function () {
                Route::get('/', 'NavbarLinksSettingsController@index');
                Route::post('/store', 'NavbarLinksSettingsController@store');
                Route::get('/{key}/edit', 'NavbarLinksSettingsController@edit');
                Route::get('/{key}/delete', 'NavbarLinksSettingsController@delete');
            });

            Route::get('/{name}', 'AdditionalPageController@index');
            Route::post('/{name}', 'AdditionalPageController@store');

            Route::post('/footer/store', 'AdditionalPageController@storeFooter');
        });

        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', 'SettingsController@index');

            Route::group(['prefix' => 'personalization'], function () {
                Route::group(['prefix' => 'navbar_button'], function () {
                    Route::get('/', 'SettingsController@navbarButtonSettings');
                    Route::get('/{id}/edit', 'SettingsController@navbarButtonSettingsEdit');
                    Route::post('/', 'SettingsController@storeNavbarButtonSettings');
                    Route::get('/{id}/delete', 'SettingsController@navbarButtonSettingsDelete');
                });

                Route::group(['prefix' => 'home_sections'], function () {
                    Route::get('/', 'HomeSectionSettingsController@index');
                    Route::post('/', 'HomeSectionSettingsController@store');
                    Route::get('/{id}/delete', 'HomeSectionSettingsController@delete');
                    Route::post('/sort', 'HomeSectionSettingsController@sort');
                });

                Route::group(['prefix' => 'statistics'], function () {
                    Route::get('/', 'StatisticSettingsController@index');
                    Route::post('/', 'StatisticSettingsController@store');
                    Route::get('/get-form', 'StatisticSettingsController@getForm');
                    Route::post('/storeItem', 'StatisticSettingsController@storeItem');
                    Route::get('/{id}/editItem', 'StatisticSettingsController@editItem');
                    Route::post('/{id}/updateItem', 'StatisticSettingsController@updateItem');
                    Route::get('/{id}/deleteItem', 'StatisticSettingsController@deleteItem');
                    Route::post('/sort', 'StatisticSettingsController@sort');
                });

                Route::get('/{name}', 'SettingsController@personalizationPage');
            });

            Route::group(['prefix' => 'update-app'], function () {
                Route::get('/', 'UpdateController@index');
                Route::post('/basic', 'UpdateController@basicUpdate');
                Route::post('/custom-update', 'UpdateController@customUpdate');
                Route::post('/database', 'UpdateController@databaseUpdate');
            });

            Route::get('/reset-users-login-count', 'SettingsController@resetUsersLoginCount');
            Route::get('/{page}', 'SettingsController@page');
            Route::post('/{name}', 'SettingsController@store');
            Route::post('/seo_metas/store', 'SettingsController@storeSeoMetas');
            Route::post('/notifications/store', 'SettingsController@notificationsMetas');

            /* Currency */
            Route::group(['prefix' => "/financial/currency"], function () {
                Route::post('/', 'SettingsController@financialCurrencyStore');
                Route::get('/{id}/edit', 'SettingsController@financialCurrencyEdit');
                Route::get('/{id}/delete', 'SettingsController@financialCurrencyDelete');
                Route::post('/order-items', 'SettingsController@financialCurrencyOrderItems');
            });

            /* Offline Banks */
            Route::group(['prefix' => "/financial/offline_banks"], function () {
                Route::get('/get-form', 'SettingsController@financialOfflineBankForm');
                Route::post('/store', 'SettingsController@financialOfflineBankStore');
                Route::get('/{id}/edit', 'SettingsController@financialOfflineBankEdit');
                Route::post('/{id}/update', 'SettingsController@financialOfflineBankUpdate');
                Route::get('/{id}/delete', 'SettingsController@financialOfflineBankDelete');
            });

            /* User Banks */
            Route::group(['prefix' => "/financial/user_banks"], function () {
                Route::get('/get-form', 'SettingsController@financialUserBankForm');
                Route::post('/store', 'SettingsController@financialUserBankStore');
                Route::get('/{id}/edit', 'SettingsController@financialUserBankEdit');
                Route::post('/{id}/update', 'SettingsController@financialUserBankUpdate');
                Route::get('/{id}/delete', 'SettingsController@financialUserBankDelete');
            });

            Route::group(['prefix' => '/socials'], function () {
                Route::post('/store', 'SettingsController@storeSocials');
                Route::get('/{key}/edit', 'SettingsController@editSocials');
                Route::get('/{key}/delete', 'SettingsController@deleteSocials');
            });

            Route::group(['prefix' => 'payment_channels'], function () {
                Route::get('/', 'PaymentChannelController@index');
                Route::get('/{id}/toggleStatus', 'PaymentChannelController@toggleStatus');
                Route::get('/{id}/edit', 'PaymentChannelController@edit');
                Route::post('/{id}/update', 'PaymentChannelController@update');
            });

            Route::post('/custom_css_js/store', 'SettingsController@storeCustomCssJs');
        });

        Route::group(['prefix' => 'testimonials'], function () {
            Route::get('/', 'TestimonialsController@index');
            Route::get('/create', 'TestimonialsController@create');
            Route::post('/store', 'TestimonialsController@store');
            Route::get('/{id}/edit', 'TestimonialsController@edit');
            Route::post('/{id}/update', 'TestimonialsController@update');
            Route::get('/{id}/delete', 'TestimonialsController@delete');
        });

        Route::group(['prefix' => 'contacts'], function () {
            Route::get('/', 'ContactController@index');
            Route::get('/{id}/reply', 'ContactController@reply');
            Route::post('/{id}/reply', 'ContactController@storeReply');
            Route::get('/{id}/delete', 'ContactController@delete');
        });

        Route::group(['prefix' => 'pages'], function () {
            Route::get('/', 'PagesController@index');
            Route::get('/create', 'PagesController@create');
            Route::post('/store', 'PagesController@store');
            Route::get('/{id}/edit', 'PagesController@edit');
            Route::post('/{id}/update', 'PagesController@update');
            Route::get('/{id}/delete', 'PagesController@delete');
            Route::get('/{id}/toggle', 'PagesController@statusTaggle');
        });

        Route::group(['prefix' => 'agora_history'], function () {
            Route::get('/', 'AgoraHistoryController@index');
            Route::get('/excel', 'AgoraHistoryController@exportExcel');
        });

        Route::group(['prefix' => 'regions'], function () {
            Route::get('/new', 'RegionController@create');
            Route::post('/store', 'RegionController@store');
            Route::get('/{id}/edit', 'RegionController@edit');
            Route::post('/{id}/update', 'RegionController@update');
            Route::get('/{id}/delete', 'RegionController@delete');
            Route::get('/provincesByCountry/{countryId}', 'RegionController@provincesByCountry');
            Route::get('/citiesByProvince/{provinceId}', 'RegionController@citiesByProvince');
            Route::get('/{pageType}', 'RegionController@index');
        });

        Route::group(['prefix' => 'rewards'], function () {
            Route::get('/', 'RewardController@index');
            Route::get('/items', 'RewardController@create');
            Route::post('/items', 'RewardController@store');
            Route::get('/items/{id}', 'RewardController@edit');
            Route::post('/items/{id}', 'RewardController@update');
            Route::get('/items/{id}/delete', 'RewardController@delete');
            Route::get('/settings', 'RewardController@settings');
            Route::post('/settings', 'RewardController@storeSettings');
        });

        Route::group(['prefix' => 'store', 'namespace' => 'Store'], function () {

            Route::group(['prefix' => 'in-house-products'], function () {
                Route::get('/', 'ProductsController@inHouseProducts');
            });

            Route::group(['prefix' => 'products'], function () {
                Route::get('/', 'ProductsController@index');
                Route::get('/create', 'ProductsController@create');
                Route::post('/store', 'ProductsController@store');
                Route::get('/{id}/edit', 'ProductsController@edit');
                Route::post('/{id}/update', 'ProductsController@update');
                Route::get('/{id}/delete', 'ProductsController@destroy');
                Route::post('/{id}/getContentItemByLocale', 'ProductsController@getContentItemByLocale');
                Route::post('/search', 'ProductsController@search');
                Route::get('/excel', 'ProductsController@exportExcel');

                Route::get('/{id}/approve', 'ProductsController@approve');
                Route::get('/{id}/reject', 'ProductsController@reject');
                Route::get('/{id}/unpublish', 'ProductsController@unpublish');

                Route::group(['prefix' => 'files'], function () {
                    Route::post('/store', 'ProductFileController@store');
                    Route::post('/{id}/edit', 'ProductFileController@edit');
                    Route::post('/{id}/update', 'ProductFileController@update');
                    Route::get('/{id}/delete', 'ProductFileController@destroy');
                });

                Route::group(['prefix' => 'specifications'], function () {
                    Route::get('/{id}/get', 'ProductSpecificationController@getItem');
                    Route::post('/store', 'ProductSpecificationController@store');
                    Route::post('/{id}/update', 'ProductSpecificationController@update');
                    Route::get('/{id}/delete', 'ProductSpecificationController@destroy');
                    //Route::post('/order-items', 'ProductSpecificationController@orderItems');
                    Route::post('/search', 'ProductSpecificationController@search');
                    Route::get('/get-by-category-id/{categoryId}', 'ProductSpecificationController@getByCategoryId');
                });

                Route::group(['prefix' => 'faqs'], function () {
                    Route::post('/store', 'ProductFaqController@store');
                    Route::post('/{id}/update', 'ProductFaqController@update');
                    Route::get('/{id}/delete', 'ProductFaqController@destroy');
                });

                Route::group(['prefix' => 'filters'], function () {
                    Route::get('/get-by-category-id/{categoryId}', 'ProductFilterController@getByCategoryId');
                });
            });

            Route::group(['prefix' => 'orders'], function () {
                Route::get('/', 'OrderController@index');
                Route::get('/{id}/refund', 'OrderController@refund');
                Route::get('/{id}/invoice', 'OrderController@invoice');
                Route::get('/export', 'OrderController@exportExcel');
                Route::get('/{id}/getProductOrder/{order_id}', 'OrderController@getProductOrder');
                Route::post('/{id}/productOrder/{order_id}/setTrackingCode', 'OrderController@setTrackingCode');
            });

            Route::group(['prefix' => 'in-house-orders'], function () {
                Route::get('/', 'OrderController@inHouseOrders');
            });

            Route::group(['prefix' => 'sellers'], function () {
                Route::get('/', 'SellersController@index');
            });

            Route::group(['prefix' => 'categories'], function () {
                Route::get('/', 'CategoryController@index');
                Route::get('/create', 'CategoryController@create');
                Route::post('/store', 'CategoryController@store');
                Route::get('/{id}/edit', 'CategoryController@edit');
                Route::post('/{id}/update', 'CategoryController@update');
                Route::get('/{id}/delete', 'CategoryController@destroy');
                Route::post('/search', 'CategoryController@search');
            });

            Route::group(['prefix' => 'filters'], function () {
                Route::get('/', 'FilterController@index');
                Route::get('/create', 'FilterController@create');
                Route::post('/store', 'FilterController@store');
                Route::get('/{id}/edit', 'FilterController@edit');
                Route::post('/{id}/update', 'FilterController@update');
                Route::get('/{id}/delete', 'FilterController@destroy');
            });

            Route::group(['prefix' => 'specifications'], function () {
                Route::get('/', 'SpecificationController@index');
                Route::get('/create', 'SpecificationController@create');
                Route::post('/store', 'SpecificationController@store');
                Route::get('/{id}/edit', 'SpecificationController@edit');
                Route::post('/{id}/update', 'SpecificationController@update');
                Route::get('/{id}/delete', 'SpecificationController@destroy');
            });

            Route::group(['prefix' => 'discounts'], function () {
                Route::get('/', 'DiscountController@index');
                Route::get('/create', 'DiscountController@create');
                Route::post('/store', 'DiscountController@store');
                Route::get('/{id}/edit', 'DiscountController@edit');
                Route::post('/{id}/update', 'DiscountController@update');
                Route::get('/{id}/delete', 'DiscountController@destroy');
            });

            Route::group(['prefix' => 'reviews'], function () {
                Route::get('/', 'ReviewsController@index');
                Route::get('/{id}/toggleStatus', 'ReviewsController@toggleStatus');
                Route::get('/{id}/reply', 'ReviewsController@reply');
                Route::get('/{id}/delete', 'ReviewsController@delete');
            });

            Route::group(['prefix' => 'settings'], function () {
                Route::get('/', 'ProductsController@settings');
                Route::post('/', 'ProductsController@storeSettings');
            });
        });

        Route::group(['prefix' => 'bundles'], function () {
            Route::get('/', 'BundleController@index');
            Route::get('/create', 'BundleController@create');
            Route::post('/store', 'BundleController@store');
            Route::get('/{id}/edit', 'BundleController@edit');
            Route::post('/{id}/update', 'BundleController@update');
            Route::get('/{id}/delete', 'BundleController@destroy');
            Route::post('/search', 'BundleController@search');
            Route::get('/excel', 'BundleController@exportExcel');

            Route::get('/{id}/approve', 'BundleController@approve');
            Route::get('/{id}/reject', 'BundleController@reject');
            Route::get('/{id}/unpublish', 'BundleController@unpublish');

            Route::get('/{id}/students', 'BundleController@studentsLists');
            Route::get('/{id}/sendNotification', 'BundleController@notificationToStudents');
            Route::post('/{id}/sendNotification', 'BundleController@sendNotificationToStudents');
        });

        Route::group(['prefix' => 'bundle-webinars'], function () {
            Route::post('/store', 'BundleWebinarsController@store');
            Route::post('/{id}/edit', 'BundleWebinarsController@edit');
            Route::post('/{id}/update', 'BundleWebinarsController@update');
            Route::get('/{id}/delete', 'BundleWebinarsController@destroy');
        });

        Route::group(['prefix' => 'forums'], function () {
            Route::get('/', 'ForumController@index');
            Route::get('/create', 'ForumController@create');
            Route::post('/store', 'ForumController@store');
            Route::get('/{id}/edit', 'ForumController@edit');
            Route::post('/{id}/update', 'ForumController@update');
            Route::get('/{id}/delete', 'ForumController@destroy');
            Route::post('/search', 'ForumController@search');

            Route::group(['prefix' => 'topics'], function () {
                Route::post('/search', 'ForumController@searchTopics');
                Route::get('/create', 'ForumTopicsController@create');
                Route::post('/store', 'ForumTopicsController@store');
            });

            Route::group(['prefix' => '{id}/topics'], function () {
                Route::get('/', 'ForumTopicsController@index');
                Route::get('/{topic_id}/edit', 'ForumTopicsController@edit');
                Route::post('/{topic_id}/update', 'ForumTopicsController@update');
                Route::post('/{topic_id}/closeToggle', 'ForumTopicsController@closeToggle');
                Route::get('/{topic_id}/close', 'ForumTopicsController@close');
                Route::get('/{topic_id}/open', 'ForumTopicsController@open');
                Route::get('/{topic_id}/delete', 'ForumTopicsController@delete');

                Route::group(['prefix' => '{topic_id}/posts'], function () {
                    Route::get('/', 'ForumTopicsController@posts');
                    Route::post('/', 'ForumTopicsController@storePost');
                    Route::get('/{post_id}/edit', 'ForumTopicsController@postEdit');
                    Route::post('/{post_id}/edit', 'ForumTopicsController@postUpdate');
                    Route::post('/{post_id}/un_pin', 'ForumTopicsController@postUnPin');
                    Route::post('/{post_id}/pin', 'ForumTopicsController@postPin');
                    Route::get('/{post_id}/delete', 'ForumTopicsController@postDelete');
                });
            });
        });

        Route::group(['prefix' => 'featured-topics'], function () {
            Route::get('/', 'FeaturedTopicsController@index');
            Route::get('/create', 'FeaturedTopicsController@create');
            Route::post('/store', 'FeaturedTopicsController@store');
            Route::get('/{id}/edit', 'FeaturedTopicsController@edit');
            Route::post('/{id}/update', 'FeaturedTopicsController@update');
            Route::get('/{id}/delete', 'FeaturedTopicsController@destroy');
        });

        Route::group(['prefix' => 'recommended-topics'], function () {
            Route::get('/', 'RecommendedTopicsController@index');
            Route::get('/create', 'RecommendedTopicsController@create');
            Route::post('/store', 'RecommendedTopicsController@store');
            Route::get('/{id}/edit', 'RecommendedTopicsController@edit');
            Route::post('/{id}/update', 'RecommendedTopicsController@update');
            Route::get('/{id}/delete', 'RecommendedTopicsController@destroy');
        });

        Route::group(['prefix' => 'advertising_modal'], function () {
            Route::get('/', 'AdvertisingModalController@index');
            Route::post('/', 'AdvertisingModalController@store');
        });

        Route::group(['prefix' => 'floating_bars'], function () {
            Route::get('/', 'FloatingBarController@index');
            Route::post('/', 'FloatingBarController@store');
        });

        Route::group(['prefix' => 'enrollments'], function () {
            Route::get('/history', 'EnrollmentController@history');
            Route::get('/add-student-to-class', 'EnrollmentController@addStudentToClass');
            Route::post('/store', 'EnrollmentController@store');
            Route::get('/{sale_id}/block-access', 'EnrollmentController@blockAccess');
            Route::get('/{sale_id}/enable-access', 'EnrollmentController@enableAccess');
            Route::get('/export', 'EnrollmentController@exportExcel');
        });


        Route::group(['prefix' => 'upcoming_courses'], function () {
            Route::get('/', 'UpcomingCoursesController@index');
            Route::get('/new', 'UpcomingCoursesController@create');
            Route::post('/store', 'UpcomingCoursesController@store');
            Route::get('/{id}/edit', 'UpcomingCoursesController@edit');
            Route::post('/{id}/update', 'UpcomingCoursesController@update');
            Route::get('/{id}/delete', 'UpcomingCoursesController@destroy');
            Route::get('/{id}/approve', 'UpcomingCoursesController@approve');
            Route::get('/{id}/reject', 'UpcomingCoursesController@reject');
            Route::get('/{id}/unpublish', 'UpcomingCoursesController@unpublish');
            Route::post('/search', 'UpcomingCoursesController@search');

            Route::group(['prefix' => '/{id}/followers'], function () {
                Route::get('/', 'UpcomingCoursesController@followers');
                Route::get('/{follow_id}/delete', 'UpcomingCoursesController@deleteFollow');
            });

            Route::post('/order-items', 'UpcomingCoursesController@orderItems');
            Route::get('/excel', 'UpcomingCoursesController@exportExcel');
        });

        Route::group(['prefix' => 'registration_bonus'], function () {
            Route::get('/history', 'RegistrationBonusController@index');
            Route::get('/export', 'RegistrationBonusController@exportExcel');

            Route::group(['prefix' => 'settings'], function () {
                Route::get('/', 'RegistrationBonusController@settings');
                Route::post('/', 'RegistrationBonusController@storeSettings');
            });
        });

        Route::group(['prefix' => 'cashback'], function () {

            Route::get('/history', 'CashbackTransactionsController@history');
            Route::get('/excel', 'CashbackTransactionsController@exportExcel');

            Route::group(['prefix' => 'history'], function () {
                Route::get('/', 'CashbackTransactionsController@history');
                Route::get('/excel', 'CashbackTransactionsController@historyExportExcel');
            });

            Route::group(['prefix' => 'transactions'], function () {
                Route::get('/', 'CashbackTransactionsController@index');
                Route::get('/excel', 'CashbackTransactionsController@exportExcel');
                Route::get('/{id}/refund', 'CashbackTransactionsController@refund');
            });

            Route::group(['prefix' => 'rules'], function () {
                Route::get('/', 'CashbackRuleController@index');
                Route::get('/new', 'CashbackRuleController@create');
                Route::post('/store', 'CashbackRuleController@store');
                Route::get('/{id}/edit', 'CashbackRuleController@edit');
                Route::post('/{id}/update', 'CashbackRuleController@update');
                Route::get('/{id}/delete', 'CashbackRuleController@delete');
                Route::get('/{id}/statusToggle', 'CashbackRuleController@statusToggle');
            });
        });


        Route::group(['prefix' => 'waitlists'], function () {
            Route::get('/', 'WaitlistController@index');
            Route::get('/export', 'WaitlistController@exportExcel');
            Route::get('/{id}/view_list', 'WaitlistController@viewList');
            Route::get('/{id}/clear_list', 'WaitlistController@clearList');
            Route::get('/{id}/disable', 'WaitlistController@disableWaitlist');
            Route::get('/{id}/export_list', 'WaitlistController@exportUsersList');

            Route::group(['prefix' => 'items'], function () {
                Route::get('/{id}/delete', 'WaitlistController@deleteWaitlistItems');
            });
        });


        Route::group(['prefix' => 'gifts'], function () {
            Route::get('/', 'GiftsController@index');
            Route::get('/{id}/send_reminder', 'GiftsController@sendReminder');
            Route::get('/{id}/cancel', 'GiftsController@cancel');
            Route::get('/excel', 'GiftsController@exportExcel');


            Route::group(['prefix' => 'settings'], function () {
                Route::get('/', 'GiftsController@settings');
                Route::post('/', 'GiftsController@storeSettings');
            });
        });


        /* Forms */
        Route::group(['prefix' => 'forms'], function () {
            Route::get('/', 'FormsController@index');
            Route::get('/create', 'FormsController@create');
            Route::post('/store', 'FormsController@store');
            Route::get('/{id}/edit', 'FormsController@edit');
            Route::post('/{id}/update', 'FormsController@update');
            Route::get('/{id}/delete', 'FormsController@delete');

            Route::group(['prefix' => '{form_id}/fields'], function () {
                Route::post('/orders', 'FormFieldsController@orders');
                Route::post('/store', 'FormFieldsController@store');
                Route::get('/{id}/edit', 'FormFieldsController@edit');
                Route::post('/{id}/update', 'FormFieldsController@update');
                Route::get('/{id}/delete', 'FormFieldsController@delete');

                Route::group(['prefix' => '/{field_id}/options'], function () {
                    Route::post('/orders', 'FormFieldsController@orderOptions');
                    Route::get('/{id}/delete', 'FormFieldsController@deleteOption');
                });
            });

            Route::group(['prefix' => 'submissions'], function () {
                Route::get('/', 'FormSubmissionsController@index');
                Route::get('/{id}/show', 'FormSubmissionsController@show');
                Route::get('/{id}/delete', 'FormSubmissionsController@delete');
            });

            Route::post("/{form_id}/submissions/{id}/update", 'FormSubmissionsController@update');

        });

        Route::group(['prefix' => 'ai-contents'], function () {
            Route::get('/lists', 'AIContentsController@index');
            Route::get('/{id}/delete', 'AIContentsController@delete');
            Route::post('/generate', 'AIContentsController@generate');
            Route::get('/settings', 'AIContentsController@settings');

            Route::group(['prefix' => 'templates'], function () {
                Route::get('/', 'AIContentTemplatesController@index');
                Route::get('/create', 'AIContentTemplatesController@create');
                Route::post('/store', 'AIContentTemplatesController@store');
                Route::get('/{id}/edit', 'AIContentTemplatesController@edit');
                Route::post('/{id}/update', 'AIContentTemplatesController@update');
                Route::get('/{id}/delete', 'AIContentTemplatesController@delete');
                Route::get('/{id}/statusToggle', 'AIContentTemplatesController@statusToggle');
            });

        });

        Route::group(['prefix' => 'purchase_notifications'], function () {
            Route::get('/', 'PurchaseNotificationsController@index');
            Route::get('/create', 'PurchaseNotificationsController@create');
            Route::post('/store', 'PurchaseNotificationsController@store');
            Route::get('/{id}/edit', 'PurchaseNotificationsController@edit');
            Route::post('/{id}/update', 'PurchaseNotificationsController@update');
            Route::get('/{id}/delete', 'PurchaseNotificationsController@delete');
            Route::post('search-contents', 'PurchaseNotificationsController@searchContents');
        });


        Route::group(['prefix' => 'content-delete-requests'], function () {
            Route::get('/', 'ContentDeleteRequestController@index');
            Route::get('/{id}/approve', 'ContentDeleteRequestController@approve');
            Route::get('/{id}/reject', 'ContentDeleteRequestController@reject');
        });


        Route::group(['prefix' => 'product-badges'], function () {
            Route::get('/', 'ProductBadgeController@index');
            Route::get('/create', 'ProductBadgeController@create');
            Route::post('/store', 'ProductBadgeController@store');
            Route::get('/{id}/edit', 'ProductBadgeController@edit');
            Route::post('/{id}/update', 'ProductBadgeController@update');
            Route::get('/{id}/delete', 'ProductBadgeController@delete');

            Route::group(['prefix' => "/{badge_id}/contents"], function () {
                Route::get("/get-form", "ProductBadgeContentsController@getForm");
                Route::post('/store', 'ProductBadgeContentsController@store');
                Route::get('/{id}/edit', 'ProductBadgeContentsController@edit');
                Route::post('/{id}/update', 'ProductBadgeContentsController@update');
                Route::get('/{id}/delete', 'ProductBadgeContentsController@delete');
            });
        });

        Route::group(['prefix' => 'cart_discount'], function () {
            Route::get('/', 'CartDiscountController@index');
            Route::post('/store', 'CartDiscountController@store');
        });

        /* Abandoned Cart Route */
        Route::group(['prefix' => 'abandoned-cart'], function () {

            Route::get('/settings', 'AbandonedCartController@settings');

            /* Rules */
            Route::group(['prefix' => 'rules'], function () {
                Route::get('/', 'AbandonedCartRulesController@index');
                Route::get('/create', 'AbandonedCartRulesController@create');
                Route::post('/store', 'AbandonedCartRulesController@store');
                Route::get('/{id}/edit', 'AbandonedCartRulesController@edit');
                Route::post('/{id}/update', 'AbandonedCartRulesController@update');
                Route::get('/{id}/delete', 'AbandonedCartRulesController@delete');
            });


            /* Users Carts */
            Route::group(['prefix' => 'users-carts'], function () {
                Route::get('/', 'AbandonedUsersCartController@index');
                Route::get('/{user_id}/send-reminder', 'AbandonedUsersCartController@sendReminder');
                Route::get('/{user_id}/view-items', 'AbandonedUsersCartController@viewItems');
                Route::get('/{user_id}/empty', 'AbandonedUsersCartController@empty');
                Route::get('/delete-by-id/{id}', 'AbandonedUsersCartController@deleteById');
            });
        });

        Route::group(['prefix' => "/translator"], function () {
            Route::get("/", "TranslatorController@index");
            Route::post("/translate", "TranslatorController@translate");
        });

        /* End Admin Middleware */
    });
});
