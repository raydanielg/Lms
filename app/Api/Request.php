<?php

namespace App\Api;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Api\Response;

class Request
{
    public function validateParam($request_input, $rules, $somethingElseIsInvalid = null)
    {

        $validator = Validator::make($request_input, $rules);

        $validator->after(function ($validator) use ($somethingElseIsInvalid) {
            if ($somethingElseIsInvalid) {
                foreach ($somethingElseIsInvalid as $err) {
                    $validator->errors()->add(
                        $err[0], $err[1]
                    );
                }

            }
        });

        if ($validator->fails()) {
            $errors['errors'] = json_decode($validator->errors(), true);
        //    throw new ValidationException($validator, apiResponse(0, 'request validation error', $errors));
            throw new ValidationException($validator, apiResponse2(0,'validation_error', 'request validation error', $errors));

        }
    }
}
