<?php

namespace App\Policies;

use App\Models\Webinar;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class WebinarPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\User $user
     * @param \App\Models\Api\Webinar $webinar
     * @return mixed
     */
    public function view(User $user, Webinar $webinar)
    {
        $access = false;
        if ($webinar->checkUserHasBought($user)) {
            $isPrivate = $webinar->private;
            if (!empty($user) and ($user->id == $webinar->creator_id or $user->organ_id == $webinar->creator_id or $user->isAdmin())) {
                $isPrivate = false;
            }
            $access = true;
            if ($isPrivate) {
                $access = false;
            }
        }
        return $access;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\User $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\User $user
     * @param \App\Models\Api\Webinar $webinar
     * @return mixed
     */
    public function update(User $user, Webinar $webinar)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\User $user
     * @param \App\Models\Api\Webinar $webinar
     * @return mixed
     */
    public function delete(User $user, Webinar $webinar)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\User $user
     * @param \App\Models\Api\Webinar $webinar
     * @return mixed
     */
    public function restore(User $user, Webinar $webinar)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\User $user
     * @param \App\Models\Api\Webinar $webinar
     * @return mixed
     */
    public function forceDelete(User $user, Webinar $webinar)
    {
        //
    }
}
