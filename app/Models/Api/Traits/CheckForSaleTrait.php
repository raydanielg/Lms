<?php

namespace App\Models\Api\Traits;

trait CheckForSaleTrait
{
    public function checkWebinarForSale($user)
    {
        if (!$this->canSale()) {
            return apiResponse2(0, 'no_capacity', trans('cart.course_not_capacity'));
        }

        if ($this->creator_id == $user->id or $this->teacher_id == $user->id) {
            return apiResponse2(0, 'same_user', trans('cart.cant_purchase_your_course'));
        }

        if ($this->checkUserHasBought($user)) {
            return apiResponse2(0, 'already_bought', trans('site.you_bought_webinar'));
        }


        if ($this->notPassedRequiredPrerequisite2($user)) {
            return apiResponse2(0, 'required_prerequisites', trans('cart.this_course_has_required_prerequisite'));
        }
        return 'ok';

    }

    public function checkWebinarForAccess($user)
    {
        $access = false;
        if ($this->checkUserHasBought($user)) {
            $isPrivate = $this->private;
            if (!empty($user) and ($user->id == $this->creator_id or $user->organ_id == $this->creator_id or $user->isAdmin())) {
                $isPrivate = false;
            }
            $access = true;
            if ($isPrivate) {
                $access = false;
            }
        }
        return $access;

    }

    public function notPassedRequiredPrerequisite2($user)
    {
        $isRequiredPrerequisite = false;
        $prerequisites = $this->prerequisites;
        if (!empty($prerequisites)) {
            foreach ($prerequisites as $prerequisite) {
                $prerequisiteWebinar = $prerequisite->prerequisiteWebinar;

                if ($prerequisite->required and !empty($prerequisiteWebinar) and !$prerequisiteWebinar->checkUserHasBought($user)) {
                    $isRequiredPrerequisite = true;
                }
            }
        }

        return $isRequiredPrerequisite;

    }
}
