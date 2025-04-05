<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\WebinarReport ;

class ReportsController extends Controller
{
   
   public function index(){

    $reasons=getReportReasons() ;
    return apiResponse2(1, 'retrieved', trans('api.public.retrieved'),$reasons);

   }


}
