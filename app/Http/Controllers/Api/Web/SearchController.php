<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\Objects\UserObj;
use App\Http\Controllers\Api\Objects\WebinarObj;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Api\Webinar;
use App\Models\Api\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function list(Request $request)
    {
        $data = [];
        $search = $request->get('search', null);

        if(strlen($search) < 3){
         //   return apiResponse2(1, 'too_short', trans('api.search.too_short'));

        }

        $webinars=[] ;
        $users=[] ;
        $teachers=[] ;
        $organizations=[] ;

        if (!empty($search) and strlen($search) >= 3) {

            $webinars = Webinar::where('status', 'active')
                ->where('private', false)
                ->whereTranslationLike('title', "%$search%")
                ->get()->map(function($webinar){
                    return $webinar->brief ;
                });


            $all_users = User::where('status', 'active')->where(function($query) use($search){
                $query->orWhere('full_name', 'like', "%$search%");
                $query->orWhere('email', 'like', "%$search%");
                $query->orWhere('mobile', 'like', "%$search%") ;
            });


            $users = $all_users->get()->map(function($user){
                return $user->brief ;
            });


            $teachers = $all_users->where('role_name', Role::$teacher)->get()
            ->map(function($teacher){

                return $teacher->brief ;
            }) ;
            ;


            $organizations = $all_users->where('role_name', Role::$organization)->get()
            ->map(function($organization){
                  return $organization->brief ;
            })
            ;



        }
        $data = [

            'webinars' =>
        [ 'webinars'=>$webinars ,
           'count'=>count($webinars)

        ]
        ,

        'users' =>
        [ 'users'=>$users ,
           'count'=>count($users)

        ]
        ,

        'teachers' =>
        [ 'teachers'=>$teachers ,
           'count'=>count($teachers)

        ]
        ,

        'organizations' =>
        [ 'organizations'=>$organizations ,
           'count'=>count($organizations)
        ]
        ,


        ];

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $data);

    }

}
