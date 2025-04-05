<?php
namespace App\Models\Api ;

use App\Models\Support as Model ;

class Support extends Model{

    public function department()
    {
        return $this->belongsTo('App\Models\SupportDepartment', 'department_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Api\User', 'user_id', 'id');
    }

    public function webinar()
    {
        return $this->belongsTo('App\Models\Api\Webinar', 'webinar_id', 'id');
    }

    public function conversations()
    {
        return $this->hasMany('App\Models\Api\SupportConversation', 'support_id', 'id');
    }

    public function getDetailsAttribute(){
        
        return [
            'id'=>$this->id ,
            'department'=>$this->department->title??null ,
            'status'=>$this->status ,
            'type' => ($this->webinar_id) ? 'course_support' : 'platform_support',
            'title'=>$this->title ,
            'webinar'=>$this->webinar->brief??null ,
            'user'=>$this->user->brief ,
            'conversations'=>$this->conversations->map(function($conversation){
                return $conversation->brief ;
            }) ,
            'created_at'=>$this->created_at ,
            'updated_at'=>$this->updated_at ,

        ] ;

    }

    public function scopeHandleFilters($query, $userWebinarsIds = [])
    {
        $request=request() ;
        $from = $request->get('from');
        $to = $request->get('to');
        $role = $request->get('role');
        $student_id = $request->get('student');
        $teacher_id = $request->get('teacher');
        $webinar_id = $request->get('webinar');
        $department = $request->get('department');
        $status = $request->get('status');

        $query = fromAndToDateFilter($from, $to, $query, 'created_at');

        if (!empty($role) and $role == 'student' and (empty($student_id) or $student_id == 'all')) {
            $studentsIds = Sale::whereIn('webinar_id', $userWebinarsIds)
                ->whereNull('refund_at')
                ->pluck('buyer_id')
                ->toArray();

            $query->whereIn('user_id', $studentsIds);
        }

        if (!empty($student_id) and $student_id != 'all') {
            $query->where('user_id', $student_id);
        }

        if (!empty($teacher_id) and $teacher_id != 'all') {
            $teacher = User::where('id', $teacher_id)
                ->where('status', 'active')
                ->first();

            $teacherWebinarIds = $teacher->webinars->pluck('id')->toArray();

            $query->whereIn('webinar_id', $teacherWebinarIds);
        }

        if (!empty($webinar_id) and $webinar_id != 'all') {
            $query->where('webinar_id', $webinar_id);
        }

        if (!empty($status) and $status != 'all') {
            $query->where('status', $status);
        }


        if (!empty($department) and $department != 'all') {
            $query->where('department_id', $department);
        }

        return $query;
    }
}
