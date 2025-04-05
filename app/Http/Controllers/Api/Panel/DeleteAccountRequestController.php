<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Controller;
use App\Models\DeleteAccountRequest;
use Illuminate\Http\Request;

class DeleteAccountRequestController extends Controller
{
    public function store()
    {
        DeleteAccountRequest::updateOrCreate([
            'user_id' => apiAuth()->id,
        ], [
            'created_at' => time()
        ]);

        return apiResponse2(1, 'stored', trans('update.delete_account_request_stored_msg'));

    }
}
