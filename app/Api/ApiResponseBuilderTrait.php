<?php

namespace App\Api;

trait ApiResponseBuilderTrait
{
    public $retrievedStatus = 'retrieved';
    public $updatedStatus = 'updated';
    public $deletedStatus = 'deleted';
    public $storedStatus = 'stored';
    public $notFoundStatus = 'not_found';
    public $validationError = 'validation_error';
    public $invalidContentType = 'invalid_content_type';

    public function success($message, $data = [], $status_code = 200)
    {
        return response()->json([
            'success' => true,
            'status' => $status_code,
            'message' => $message,
            'data' => $data
        ], $status_code);
    }


    public function failure($message, $status_code = 401, $custom_status = null)
    {
        return response()->json([
            'success' => false,
            'status' => $custom_status ?: $status_code,
            'message' => $message,
        ], $status_code);
    }

    public function notFound()
    {
        return $this->failure(trans('http_status.404'), 404);
    }

    public function forbidden()
    {
        return $this->failure(trans('http_status.403'), 403);
    }

    public function failureWithErrors($message, $status, $data, $status_code = 401)
    {
        return response()->json([
            'success' => false,
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $status_code);
    }
}
