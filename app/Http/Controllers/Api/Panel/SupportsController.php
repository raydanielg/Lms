<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Http\Controllers\Api\Objects\SupportObj;
use App\Models\Sale;
use App\Models\Api\Support;
use App\Models\SupportConversation;
use App\Models\Webinar;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\UploadFileManager ;


class SupportsController extends Controller
{

    public function show(Request $request ,$id){

        $support = Support::where('id', $id)->first();
        if (!$support) {
            abort(404);
        }
        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $support->details );


    }

    public function index(Request $request){
        $data = [
            'class_support' => $this->classSupport($request)->map(function ($support) {
                return $support->details ;

            }),
            'my_class_support' => $this->myClassSupport($request)->map(function ($support) {
                return $support->details ;
            }),
            'tickets' => $this->platformSupport($request)->map(function ($support) {
                return $support->details ;
            }),
        ];

        return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $data);

    }



    public function myClassSupport(Request $request)
    {
        $user = apiAuth();
        $userWebinarsIds = $user->webinars->pluck('id')->toArray();

        $supports = Support::whereNull('department_id')
       ->WhereIn('webinar_id', $userWebinarsIds)
        ->handleFilters()->orderBy('created_at', 'desc')
            ->orderBy('status', 'asc')
            ->get()
            ->map(function ($support) {
                return $support->details ;
            });

            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $supports);

    }

    public function classSupport(Request $request){

        $user = apiAuth();
        $supports = Support::whereNull('department_id')
            ->where('user_id', $user->id)
            ->handleFilters()->orderBy('created_at', 'desc')
            ->orderBy('status', 'asc')
            ->get()->map(function ($support) {
                return $support->details ;

            })  ;

            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $supports);

    }


    public function platformSupport(Request $request)
    {
        $user = apiAuth();
        $supports = Support::whereNotNull('department_id')
            ->where('user_id', $user->id)->handleFilters()
            ->orderBy('created_at', 'desc')
            ->orderBy('status', 'asc')
            ->get()
            ->map(function ($support) {
                return $support->details ;
            });
            return apiResponse2(1, 'retrieved', trans('api.public.retrieved'), $supports);


    }

    public function store(Request $request)
    {
        $user = apiAuth();
        validateParam($request->all(), [
            'title' => 'required|min:2',
            'type' => 'required|in:course_support,platform_support',
            'department_id' => 'required_if:type,platform_support|nullable|exists:support_departments,id',
            'webinar_id' => 'required_if:type,course_support|nullable|exists:webinars,id',
            'message' => 'required|min:2',
           // 'attach' => 'nullable|string',
        ]);

        $attach=null ;
        if( $request->file('attach',null)){
            $storage=new UploadFileManager($request->file('attach')) ;
            $attach=$storage->storage_path ;
        }




      //  $data = $request->all();
     //   unset($data['type']);
        //     $request->input('department_id')
        $support = Support::create([
            'user_id' => $user->id,
            'department_id' => !empty($request->input('department_id')
            && $request->input('type')=='platform_support'
            ) ? $request->input('department_id') : null,
            'webinar_id' => !empty($request->input('webinar_id')
            && $request->input('type')=='course_support'
            ) ? $request->input('webinar_id') : null,
            'title' => $request->input('title'),
            'status' => 'open',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        SupportConversation::create([
            'support_id' => $support->id,
            'sender_id' => $user->id,
            'message' => $request->input('message'),
            'attach' => $attach,
            'created_at' => time(),
        ]);

        if ($request->input('webinar_id')) {
            $webinar = Webinar::findOrFail($request->input('webinar_id'));

            $notifyOptions = [
                '[c.title]' => $webinar->title,
                '[u.name]' => $user->full_name
            ];
            sendNotification('support_message', $notifyOptions, $webinar->teacher_id);
        }

        if ($request->input('department_id')) {
            $notifyOptions = [
                '[s.t.title]' => $support->title,
            ];
            sendNotification('support_message_admin', $notifyOptions, 1); // for admin
        }

        return apiResponse2(1, 'stored', trans('api.public.stored'),[
            'attach'=>url($attach)
        ]);
    }

    public function storeConversations(Request $request, $id)
    {
        validateParam($request->all(), [
            'message' => 'required|string|min:2',
        ]);

        $data = $request->all();
        $user = apiAuth();

        $userWebinarsIds = $user->webinars->pluck('id')->toArray();

        $support = Support::where('id', $id)
            ->where(function ($query) use ($user, $userWebinarsIds) {
                $query->where('user_id', $user->id)
                    ->orWhereIn('webinar_id', $userWebinarsIds);
            })->first();

        if (empty($support)) {
            abort(404);
        }


        $support->update([
            'status' => ($support->user_id == $user->id) ? 'open' : 'replied',
            'updated_at' => time()
        ]);

        $attach=null ;
        if( $request->file('attachment',null)){
            $storage=new UploadFileManager($request->file('attachment')) ;
            $attach=$storage->storage_path ;
        }


        SupportConversation::create([
            'support_id' => $support->id,
            'sender_id' => $user->id,
            'message' => $request->input('message'),
            'attach' => $attach,  //  $request->input('attach')
            'created_at' => time(),
        ]);

        if (!empty($support->webinar_id)) {
            $webinar = Webinar::findOrFail($support->webinar_id);

            $notifyOptions = [
                '[c.title]' => $webinar->title,
            ];
            sendNotification('support_message_replied', $notifyOptions, ($support->user_id == $user->id) ? $webinar->teacher_id : $user->id);
        }

        if (!empty($support->department_id)) {
            $notifyOptions = [
                '[s.t.title]' => $support->title,
            ];
            sendNotification('support_message_replied_admin', $notifyOptions, 1); // for admin
        }

        return apiResponse2(1, 'stored', trans('api.public.stored'),[
            'attach'=>url($attach)
        ]);
    }

    public function close($id)
    {
      //  dd('ff') ;
        $user = apiAuth();
        $userWebinarsIds = $user->webinars->pluck('id')->toArray();

        $support = Support::where('id', $id)
            ->where(function ($query) use ($user, $userWebinarsIds) {
                $query->where('user_id', $user->id)
                    ->orWhereIn('webinar_id', $userWebinarsIds);
            })->first();

        if (empty($support)) {
            abort(404);
        }

        $support->update([
            'status' => 'close',
            'updated_at' => time()
        ]);

        return apiResponse2(1, 'closed', trans('api.support.closed') );

    }
}
