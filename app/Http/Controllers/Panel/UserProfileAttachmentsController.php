<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Translation\UserProfileAttachmentTranslation;
use App\Models\UserProfileAttachment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserProfileAttachmentsController extends Controller
{

    public function getForm(Request $request)
    {
        $data = $request->all();
        $html = (string)view()->make('design_1.panel.settings.includes.attachments_modal', $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required',
            'file_type' => 'required',
            'attachment' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['user_id'])) {
            $organization = auth()->user();
            $user = User::where('id', $data['user_id'])
                ->where('organ_id', $organization->id)
                ->first();
        } else {
            $user = auth()->user();
        }

        if (!empty($user) and !empty($request->file('attachment'))) {
            $attachment = UserProfileAttachment::query()->create([
                'user_id' => $user->id,
                'file_type' => $data['file_type'],
                'attachment' => null,
                'created_at' => time(),
            ]);

            $this->handleExtraData($request, $attachment, $user);

            return response()->json([
                'code' => 200,
                'title' => trans('public.request_success'),
                'msg' => trans('update.attachment_stored_successfully')
            ]);
        }

        return response([
            'code' => 422,
            'errors' => $validator->errors(),
        ], 422);
    }

    public function edit(Request $request, $id)
    {
        $attachment = UserProfileAttachment::query()->findOrFail($id);

        $data = [
            'attachment' => $attachment,
            'user_id' => $request->get('user_id')
        ];

        $html = (string)view()->make('design_1.panel.settings.includes.attachments_modal', $data);

        return response()->json([
            'code' => 200,
            'html' => $html
        ]);
    }

    public function update(Request $request, $id)
    {
        $attachment = UserProfileAttachment::query()->findOrFail($id);
        $data = $request->all();

        $validator = Validator::make($data, [
            'title' => 'required',
            'file_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        if (!empty($data['user_id'])) {
            $organization = auth()->user();
            $user = User::where('id', $data['user_id'])
                ->where('organ_id', $organization->id)
                ->first();
        } else {
            $user = auth()->user();
        }

        if (!empty($user) and $attachment->user_id == $user->id) {
            $attachment->update([
                'user_id' => $user->id,
                'file_type' => $data['file_type'],
                'created_at' => time(),
            ]);

            $this->handleExtraData($request, $attachment, $user);

            return response()->json([
                'code' => 200,
                'title' => trans('public.request_success'),
                'msg' => trans('update.attachment_updated_successfully')
            ]);
        }

        return response([
            'code' => 422,
            'errors' => $validator->errors(),
        ], 422);
    }

    public function delete($id)
    {
        $user = auth()->user();

        $attachment = UserProfileAttachment::query()
            ->where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('user_id', $user->id);
                $query->orWhereHas('user', function ($query) use ($user) {
                    $query->where('organ_id', $user->id);
                });
            })->first();

        if (!empty($attachment)) {
            $this->removeFile($attachment->attachment);

            $attachment->delete();

            return response()->json([
                'code' => 200,
                'title' => trans('public.request_success'),
                'msg' => trans('update.attachment_deleted_successfully')
            ]);
        }

        return response()->json([], 422);
    }

    private function handleExtraData(Request $request, $attachment, $user)
    {
        $data = $request->all();

        UserProfileAttachmentTranslation::query()->updateOrCreate([
            'user_profile_attachment_id' => $attachment->id,
            'locale' => mb_strtolower($data['locale'])
        ], [
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
        ]);


        // Upload File
        $path = $attachment->attachment ?? null;
        $destination = "setting/attachments/{$attachment->id}";

        if (!empty($request->file('attachment'))) {
            if (!empty($path)) {
                $this->removeFile($path);
            }

            $path = $this->uploadFile($request->file('attachment'), $destination, null, $user->id);
        }

        $attachment->update([
            'attachment' => $path
        ]);
    }

}
