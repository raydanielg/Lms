<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MobileAppController extends Controller
{
    public function index()
    {
        /*if (empty(getFeaturesSettings('mobile_app_status')) or !getFeaturesSettings('mobile_app_status')) {
            return redirect('/');
        }*/


        $data = [
            'pageTitle' => trans('update.download_mobile_app_and_enjoy'),
            'pageRobot' => getPageRobotNoIndex()
        ];

        return view('web.default.mobile_app.index', $data);
    }
}
