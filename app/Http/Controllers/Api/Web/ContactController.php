<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{

    public function store(Request $request)
    {
        validateParam($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email',
            'phone' => 'required|numeric',
            'subject' => 'required|string',
            'message' => 'required|string',
        //    'captcha' => 'required|captcha',
        ]);

        $data = $request->all();
        unset($data['_token']);
        $data['created_at'] = time();

        Contact::create($data);

        $notifyOptions = [
            '[c.u.title]' => $data['subject'],
            '[u.name]' => $data['name']
        ];
        sendNotification('new_contact_message', $notifyOptions, 1);

        return apiResponse(1, 'user sent message successfully.');
        //return back()->with(['msg' => trans('site.contact_store_success')]);
    }
}
