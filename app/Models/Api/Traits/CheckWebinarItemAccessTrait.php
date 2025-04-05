<?php

namespace App\Models\Api\Traits;

trait CheckWebinarItemAccessTrait
{
    public function canViewError()
    {
        $error = null;
        $user = apiAuth();
        if (!$user) {
            $error = trans('public.not_login_toast_msg_lang');
        } elseif (!$this->webinar->checkUserHasBought($user)) {
            $error = trans('public.not_access_to_this_content');
        } elseif ($checkSequenceContent = $this->checkSequenceContent($user)) {
            $errors = [];
            if (is_array($checkSequenceContent)) {
                foreach ($checkSequenceContent as $key => $value) {
                    if ($value) {
                        $errors[] = $value;
                    }
                }
            }
            $error = (count($errors) > 0) ? implode(' ', $errors) : null;
        } elseif (!$this->user_has_access) {
            $error = trans('public.not_access_to_this_content');
        }

        return $error;
    }

}
