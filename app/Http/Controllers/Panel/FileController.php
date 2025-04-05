<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Mixins\BunnyCDN\BunnyVideoStream;
use App\Models\File;
use App\Models\Translation\FileTranslation;
use App\Models\Webinar;
use App\Models\WebinarChapterItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Validator;

class FileController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $s3FileInput = $request->file('s3_file');
        $data = $request->get('ajax')['new'];
        $data['s3_file'] = $s3FileInput;

        if (empty($data['storage'])) {
            $data['storage'] = 'upload';
        }

        if (!empty($data['file_path']) and is_array($data['file_path'])) {
            $data['file_path'] = $data['file_path'][0];
        }

        $sourceRequiredFileType = ['external_link', 's3', 'google_drive', 'upload'];
        $sourceRequiredFileVolume = ['external_link', 'google_drive'];
        $sourceDefaultFileTypeAndVolume = ['youtube', 'vimeo', 'iframe', 'secure_host'];

        if (in_array($data['storage'], $sourceDefaultFileTypeAndVolume)) {
            $data['file_type'] = 'video';
            $data['volume'] = !empty($data['volume']) ? $data['volume'] : 0;
        }

        $rules = [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required|max:255',
            'accessibility' => 'required|' . Rule::in(File::$accessibility),
            'file_path' => 'required',
            'file_type' => Rule::requiredIf(in_array($data['storage'], $sourceRequiredFileType)),
            'volume' => Rule::requiredIf(in_array($data['storage'], $sourceRequiredFileVolume)),
            'description' => 'nullable',
        ];

        if ($data['storage'] == 'upload_archive') {
            $rules['interactive_type'] = 'required';
            $rules['interactive_file_name'] = Rule::requiredIf($data['interactive_type'] == 'custom');
        }

        if ($data['storage'] == 's3') {
            $rules['file_path'] = 'nullable';
            $rules['s3_file'] = 'required';
        }

        if ($data['storage'] == 'secure_host') {
            $rules['file_path'] = 'nullable';
            $rules['s3_file'] = 'required';

            if ($data['secure_host_upload_type'] == "manual") {
                $rules['s3_file'] = 'nullable';
                $rules['secure_host_file_path'] = 'required';
                $rules['volume'] = 'required';
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data['downloadable'] = !empty($data['downloadable']);
        if (in_array($data['storage'], ['youtube', 'vimeo', 'iframe', 'google_drive', 'upload_archive'])) {
            $data['downloadable'] = false;
        } elseif (in_array($data['storage'], ['external_link', 's3']) and $data['file_type'] != 'video') {
            $data['downloadable'] = true;
        }

        if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
            $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
            $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
        } else {
            $data['check_previous_parts'] = false;
            $data['access_after_day'] = null;
        }

        $webinar = Webinar::find($data['webinar_id']);

        if (!empty($webinar) and $webinar->canAccess($user)) {
            $volume = 0;
            $fileInfos = null;

            if ($data['storage'] == 'upload_archive') {
                $fileInfos = $this->fileInfo($data['file_path']);

                if (empty($fileInfos) or $fileInfos['extension'] != 'zip') {
                    return response([
                        'code' => 422,
                        'errors' => [
                            'file_path' => [trans('validation.mimes', ['attribute' => 'file', 'values' => 'zip'])]
                        ],
                    ], 422);
                }

                $volume = convertToMB($fileInfos['size'] ?? 0);
                $fileInfos['extension'] = 'archive';
                $data['interactive_file_path'] = $this->handleUnZipFile($data);

            } elseif ($data['storage'] == 'upload') {
                $uploadFile = $this->fileInfo($data['file_path']);
                $volume = convertToMB($uploadFile['size'] ?? 0);
            } elseif (in_array($data['storage'], ['s3', 'secure_host'])) {

                if ($data['storage'] == 's3') {
                    $data['volume'] = $request->file('s3_file')->getSize();
                    $result = $this->uploadFileToS3($data['s3_file']);
                } else {
                    if ($data['secure_host_upload_type'] == "direct") {
                        $data['volume'] = $request->file('s3_file')->getSize();
                        $result = $this->uploadFileToBunny($webinar, $data['s3_file']);
                    } else {
                        $result['status'] = true;
                        $result['path'] = $data['secure_host_file_path'];
                    }
                }

                if (!$result['status']) {
                    return $result['path'];
                }

                $data['file_path'] = $result['path'];
                $fileInfos['extension'] = $data['file_type'];
                $fileInfos['size'] = $data['volume'];

                if ($data['storage'] == 'secure_host' and $data['secure_host_upload_type'] == "manual") {
                    $volume = $data['volume'];
                } else {
                    $volume = convertToMB(($data['volume'] ?? 0));
                }

            } else {
                $volume = !empty($data['volume']) ? $data['volume'] : 0; // input is MB
            }

            $file = File::create([
                'creator_id' => $user->id,
                'webinar_id' => $data['webinar_id'],
                'chapter_id' => $data['chapter_id'],
                'file' => $data['file_path'],
                'volume' => $volume,
                'file_type' => !empty($fileInfos) ? $fileInfos['extension'] : $data['file_type'],
                'accessibility' => $data['accessibility'],
                'storage' => $data['storage'],
                'secure_host_upload_type' => $data['secure_host_upload_type'] ?? null,
                'interactive_type' => $data['interactive_type'] ?? null,
                'interactive_file_name' => $data['interactive_file_name'] ?? null,
                'interactive_file_path' => $data['interactive_file_path'] ?? null,
                'online_viewer' => (!empty($data['online_viewer']) and $data['online_viewer'] == 'on'),
                'downloadable' => $data['downloadable'],
                'check_previous_parts' => $data['check_previous_parts'],
                'access_after_day' => $data['access_after_day'],
                'status' => (!empty($data['status']) and $data['status'] == 'on') ? File::$Active : File::$Inactive,
                'created_at' => time()
            ]);

            if (!empty($file)) {
                FileTranslation::updateOrCreate([
                    'file_id' => $file->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);

                WebinarChapterItem::makeItem($file->creator_id, $file->chapter_id, $file->id, WebinarChapterItem::$chapterFile);
            }

            return response()->json([
                'code' => 200,
            ], 200);
        }

        abort(403);
    }

    private function handleUnZipFile($data)
    {
        $path = $data['file_path'];
        $interactiveType = $data['interactive_type'] ?? null;
        $interactiveFileName = $data['interactive_file_name'] ?? null;

        $storage = Storage::disk('public');
        $user = auth()->user();

        $fileInfo = $this->fileInfo($path);

        $extractPath = $user->id . '/' . $fileInfo['name'];
        $storageExtractPath = $storage->url($extractPath);

        if (!$storage->exists($extractPath)) {
            $storage->makeDirectory($extractPath);

            $filePath = public_path($path);

            $zip = new \ZipArchive();
            $res = $zip->open($filePath);

            if ($res) {
                $zip->extractTo(public_path($storageExtractPath));

                $zip->close();
            }
        }

        $fileName = 'index.html';

        if ($interactiveType == 'i_spring') {
            $fileName = 'story.html';
        } elseif ($interactiveType == 'custom') {
            $fileName = $interactiveFileName;
        }

        return $storageExtractPath . '/' . $fileName;
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $s3FileInput = $request->file('s3_file');
        $data = $request->get('ajax')[$id];
        $data['s3_file'] = $s3FileInput;

        $sourceRequiredFileType = ['external_link', 's3', 'google_drive', 'upload'];
        $sourceRequiredFileVolume = ['external_link', 'google_drive'];
        $sourceDefaultFileTypeAndVolume = ['youtube', 'vimeo', 'iframe', 'secure_host'];

        if (in_array($data['storage'], $sourceDefaultFileTypeAndVolume)) {
            $data['file_type'] = 'video';
            $data['volume'] = 0;
        }

        if (empty($data['storage'])) {
            $data['storage'] = 'upload';
        }

        if (!empty($data['file_path']) and is_array($data['file_path'])) {
            $data['file_path'] = $data['file_path'][0];
        }

        $sourceRequiredFileType = ['external_link', 's3', 'google_drive', 'upload'];
        $sourceRequiredFileVolume = ['external_link', 'google_drive'];
        $sourceDefaultFileTypeAndVolume = ['youtube', 'vimeo', 'iframe', 'secure_host'];

        if (in_array($data['storage'], $sourceDefaultFileTypeAndVolume)) {
            $data['file_type'] = 'video';
            $data['volume'] = !empty($data['volume']) ? $data['volume'] : 0;
        }

        $rules = [
            'webinar_id' => 'required',
            'chapter_id' => 'required',
            'title' => 'required|max:255',
            'accessibility' => 'required|' . Rule::in(File::$accessibility),
            'file_path' => 'required',
            'file_type' => Rule::requiredIf(in_array($data['storage'], $sourceRequiredFileType)),
            'volume' => Rule::requiredIf(in_array($data['storage'], $sourceRequiredFileVolume)),
            'description' => 'nullable',
        ];

        if ($data['storage'] == 'upload_archive') {
            $rules['interactive_type'] = 'required';
            $rules['interactive_file_name'] = Rule::requiredIf($data['interactive_type'] == 'custom');
        }

        if ($data['storage'] == 's3') {
            $rules ['file_path'] = 'nullable';
            $rules ['s3_file'] = 'nullable';
        }

        if ($data['storage'] == 'secure_host') {
            $rules['file_path'] = 'nullable';
            $rules['s3_file'] = 'nullable';

            if ($data['secure_host_upload_type'] == "manual") {
                $rules['secure_host_file_path'] = 'required';
                $rules['volume'] = 'required';
            }
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return response([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data['downloadable'] = !empty($data['downloadable']);
        if (in_array($data['storage'], ['youtube', 'vimeo', 'iframe', 'google_drive', 'upload_archive'])) {
            $data['downloadable'] = false;
        } elseif (in_array($data['storage'], ['external_link', 's3']) and $data['file_type'] != 'video') {
            $data['downloadable'] = true;
        }

        if (!empty($data['sequence_content']) and $data['sequence_content'] == 'on') {
            $data['check_previous_parts'] = (!empty($data['check_previous_parts']) and $data['check_previous_parts'] == 'on');
            $data['access_after_day'] = !empty($data['access_after_day']) ? $data['access_after_day'] : null;
        } else {
            $data['check_previous_parts'] = false;
            $data['access_after_day'] = null;
        }

        $webinar = Webinar::find($data['webinar_id']);

        if (!empty($webinar) and $webinar->canAccess($user)) {
            $volume = 0;
            $fileInfos = null;

            if ($data['storage'] == 'upload_archive') {
                $fileInfos = $this->fileInfo($data['file_path']);

                if (empty($fileInfos) or $fileInfos['extension'] != 'zip') {
                    return response([
                        'code' => 422,
                        'errors' => [
                            'file_path' => [trans('validation.mimes', ['attribute' => 'file', 'values' => 'zip'])]
                        ],
                    ], 422);
                }

                $volume = convertToMB($fileInfos['size'] ?? 0);
                $fileInfos['extension'] = 'archive';
                $data['interactive_file_path'] = $this->handleUnZipFile($data);

            } elseif ($data['storage'] == 'upload') {
                $uploadFile = $this->fileInfo($data['file_path']);
                $volume = convertToMB($uploadFile['size'] ?? 0);
            } elseif (in_array($data['storage'], ['s3', 'secure_host'])) {
                if ($data['storage'] == 's3') {
                    $data['volume'] = $request->file('s3_file')->getSize();
                    $result = $this->uploadFileToS3($data['s3_file']);
                } else {
                    if ($data['secure_host_upload_type'] == "direct") {
                        $data['volume'] = $request->file('s3_file')->getSize();
                        $result = $this->uploadFileToBunny($webinar, $data['s3_file']);
                    } else {
                        $result['status'] = true;
                        $result['path'] = $data['secure_host_file_path'];
                    }
                }

                if (!$result['status']) {
                    return $result['path'];
                }

                $data['file_path'] = $result['path'];
                $fileInfos['extension'] = $data['file_type'];
                $fileInfos['size'] = $data['volume'];

                if ($data['storage'] == 'secure_host' and $data['secure_host_upload_type'] == "manual") {
                    $volume = $data['volume'];
                } else {
                    $volume = convertToMB(($data['volume'] ?? 0));
                }

            } else {
                $volume = !empty($data['volume']) ? $data['volume'] : 0; // input is MB
            }

            $file = File::where('id', $id)
                ->where(function ($query) use ($user, $webinar) {
                    $query->where('creator_id', $user->id);
                    $query->orWhere('webinar_id', $webinar->id);
                })
                ->first();

            if (!empty($file)) {

                $changeChapter = ($data['chapter_id'] != $file->chapter_id);
                $oldChapterId = $file->chapter_id;

                $file->update([
                    'chapter_id' => $data['chapter_id'],
                    'file' => $data['file_path'],
                    'volume' => $volume,
                    'file_type' => !empty($fileInfos) ? $fileInfos['extension'] : $data['file_type'],
                    'accessibility' => $data['accessibility'],
                    'storage' => $data['storage'],
                    'secure_host_upload_type' => $data['secure_host_upload_type'] ?? null,
                    'interactive_type' => $data['interactive_type'] ?? null,
                    'interactive_file_name' => $data['interactive_file_name'] ?? null,
                    'interactive_file_path' => $data['interactive_file_path'] ?? null,
                    'online_viewer' => (!empty($data['online_viewer']) and $data['online_viewer'] == 'on'),
                    'downloadable' => $data['downloadable'],
                    'check_previous_parts' => $data['check_previous_parts'],
                    'access_after_day' => $data['access_after_day'],
                    'status' => (!empty($data['status']) and $data['status'] == 'on') ? File::$Active : File::$Inactive,
                    'updated_at' => time()
                ]);

                if ($changeChapter) {
                    WebinarChapterItem::changeChapter($file->creator_id, $oldChapterId, $file->chapter_id, $file->id, WebinarChapterItem::$chapterFile);
                }

                FileTranslation::updateOrCreate([
                    'file_id' => $file->id,
                    'locale' => mb_strtolower($data['locale']),
                ], [
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);

                return response()->json([
                    'code' => 200,
                ], 200);
            }

        }

        abort(403);
    }

    public function fileInfo($path)
    {
        $file = array();

        $file_path = public_path($path);

        $filePath = pathinfo($file_path);

        $file['name'] = $filePath['filename'];
        $file['extension'] = $filePath['extension'];
        $file['size'] = filesize($file_path);

        return $file;
    }

    private function uploadFileToS3($file)
    {
        $user = auth()->user();

        $path = '/store/' . $user->id;

        $result = [
            'path' => null,
            'status' => true
        ];

        try {
            $fileName = time() . $file->getClientOriginalName();

            $storage = Storage::disk('minio');

            if (!$storage->exists($path)) {
                $storage->makeDirectory($path);
            }

            $path = $storage->put($path, $file, $fileName);
            $result['path'] = $storage->url($path);
        } catch (\Exception $ex) {

            $result = [
                'path' => response([
                    'code' => 500,
                    'message' => $ex->getMessage(),
                    'traces' => $ex->getTrace(),
                ], 500),
                'status' => false
            ];
        }

        return $result;
    }

    private function uploadFileToBunny($webinar, $file)
    {
        $result = [
            'path' => null,
            'status' => true
        ];

        try {
            $bunnyVideoStream = new BunnyVideoStream();

            $collectionId = $bunnyVideoStream->createCollection("course {$webinar->id}", true);

            if ($collectionId) {

                $videoUrl = $bunnyVideoStream->uploadVideo($file->getClientOriginalName(), $collectionId, $file);

                $result['path'] = $videoUrl;
            }
        } catch (\Exception $ex) {

            $result = [
                'path' => response([
                    'code' => 500,
                    'message' => $ex->getMessage(),
                    'traces' => $ex->getTrace(),
                ], 500),
                'status' => false
            ];
        }

        return $result;
    }

    public function destroy(Request $request, $id)
    {
        $user = auth()->user();
        $file = File::where('id', $id)->first();


        if (!empty($file)) {
            $webinar = Webinar::query()->find($file->webinar_id);

            if ($file->creator_id == $user->id or (!empty($webinar) and $webinar->canAccess($user))) {

                if ($file->storage == "secure_host") {
                    $bunnyVideoStream = new BunnyVideoStream();
                    $bunnyVideoStream->deleteVideo($file->file);
                }


                WebinarChapterItem::where('user_id', $file->creator_id)
                    ->where('item_id', $file->id)
                    ->where('type', WebinarChapterItem::$chapterFile)
                    ->delete();

                $file->delete();
            }
        }

        return response()->json([
            'code' => 200
        ], 200);
    }
}
