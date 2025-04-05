<?php
namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\Api\SupportDepartment ;

class SupportDepartmentsController extends Controller {

    public function index(){

        return SupportDepartment::all()->map(function($department){
            return $department->details ;
        }) ;
    }
}