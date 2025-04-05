<?php

namespace App\Policies;

use App\Models\CourseForum;
use App\Models\Api\CourseForumAnswer;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CourseForumAnswerPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function pin(User $user, CourseForumAnswer $courseForumAnswer)
    {
        return $courseForumAnswer->course_forum->webinar->isOwner($user->id);
    }

    public function resolve(User $user, CourseForumAnswer $courseForumAnswer)
    {
        return ($courseForumAnswer->course_forum->webinar->isOwner($user->id)  or $courseForumAnswer->course_forum->user_id==$user->id );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User $user
     * @param \App\Models\CourseForum $courseForum
     * @return mixed
     */
    public function update(User $user,CourseForumAnswer $courseForumAnswer)
    {

        return  $courseForumAnswer->user_id==$user->id ;
    }



}
