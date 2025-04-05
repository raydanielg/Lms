<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RestrictionController extends Controller
{
    public function index()
    {
        $data = [
            'pageTitle' => trans('update.restriction'),
            'pageRobot' => getPageRobotNoIndex()
        ];

        return view('web.default.restriction.index', $data);
    }
}
