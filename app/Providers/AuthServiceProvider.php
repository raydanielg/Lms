<?php

namespace App\Providers;

use App\Models\Api\CourseForumAnswer;
use App\Models\Webinar;
use App\Models\CourseForum;
use App\Models\Section;
use App\Policies\CourseForumAnswerPolicy;
use App\Policies\CourseForumPolicy;
use App\Policies\WebinarPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        CourseForum::class => CourseForumPolicy::class,
        CourseForumAnswer::class => CourseForumAnswerPolicy::class ,
        Webinar::class => WebinarPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {

        $this->registerPolicies();

        $minutes = 60 * 60; // 1 hour
        $sections = Cache::remember('sections', $minutes, function () {
            return Section::all();
        });

        $scopes = [];
        foreach ($sections as $section) {
            $scopes[$section->name] = $section->caption;
            Gate::define($section->name, function ($user) use ($section) {
                return $user->hasPermission($section->name);
            });
        }


        //
    }
}
