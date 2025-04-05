@extends('web.default.layouts.email')

@section('body')
    <!-- content -->
    <td valign="top" class="bodyContent" mc:edit="body_content">
        <h1 class="h1">{{ $notification['title'] }}</h1>
        <p>{!! nl2br($notification['message']) !!}</p>

        <p>{{ trans('notification.email_ignore_msg') }}</p>
    </td>
@endsection