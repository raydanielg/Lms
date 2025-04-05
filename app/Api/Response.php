<?php

namespace App\Api;

class Response
{
    private $status;
    private $data;
    private $msg;
    private $success;

    public function apiResponse($success, $msg = null, $data = null, $http_status = null)
    {
        $this->success = $success;
        $this->msg = $msg;

        $this->data = $data;
        $json = ['message' => $msg, 'success' => $this->getSuccess()];
        if (isset($data)) {
            $json['data'] = $data;
        }

        return response()->json($json);
    }

    public function apiResponse2($success, $status, $msg, $data = null,$title=null)
    {
        $this->success = $success;
        $this->msg = $msg;
        $this->status = $status;

        $this->data = $data;
        $json = ['success' => $this->getSuccess(), 'status' => $this->getStatus(), 'message' => $this->getMsg()];

        if (isset($title)) {
            $json['title'] = $title;
        }

        if (isset($data)) {
            $json['data'] = $data;
        }

        return response()->json($json,200,[],JSON_INVALID_UTF8_IGNORE|JSON_INVALID_UTF8_SUBSTITUTE);
    }


    public function returnData($data)
    {
        return ['count' => $data->count(), 'data' => $data];
    }

    public function getStatus()
    {
        switch ($this->status) {
            case 'list':
                return 'list';
            case 'passed' :
                return 'passed';
            case 'max-attempt' :
                return 'max-attempt';
            case 'validation_error' :
                return 'validation_error' ;
            case "no_meeting":
                return 'no_meeting' ;
            case "disabled":
                return 'disabled' ;
            case "invalid":
                return 'invalid' ;
            default:
                return $this->status ;


        }
    }


    public function getSuccess()
    {
        switch ($this->success) {
            case 1:
                return true;
            case 0 :
                return false;
        }
    }

    public function getMsg()
    {
        if (!$this->msg) {
            switch ($this->getSuccess()) {
                case 'list':
                    return 'data reitrived successfully';
                case 0 :
                    return 'failed';
            }
        }
        return $this->msg;

    }
}
