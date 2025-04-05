<?php

namespace App\Http\Controllers\Panel\Traits;

use App\Mixins\BunnyCDN\BunnyVideoStream;
use Illuminate\Http\Request;

trait VideoDemoTrait
{
    private function handleVideoDemoData(Request $request, $data, $name)
    {
        // Upload to Bunny
        if (!empty($data['video_demo_source']) and $data['video_demo_source'] == "secure_host") {

            if (!empty($request->file('video_demo_secure_host_file'))) {
                try {
                    $bunnyVideoStream = new BunnyVideoStream();

                    $file = $request->file('video_demo_secure_host_file');

                    $collectionId = $bunnyVideoStream->createCollection($name);

                    if ($collectionId) {

                        $videoUrl = $bunnyVideoStream->uploadVideo($file->getClientOriginalName(), $collectionId, $file);

                        $data['video_demo'] = $videoUrl;
                    }
                } catch (\Exception $ex) {
                    dd($ex);
                }
            }
        } else {

            if (!empty($data['video_demo_source']) and !in_array($data['video_demo_source'], ['upload', 'youtube', 'vimeo', 'external_link'])) {
                $data['video_demo_source'] = 'upload';
            }
        }

        return $data;
    }

}
