<?php

namespace App\CustomStorage;

use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

class CustomMinioAdapter extends AwsS3V3Adapter
{

    public function getUrl($path)
    {
        $endpoint = env('MINIO_ENDPOINT', env('app_url'));
        $bucket = env('MINIO_BUCKET');

        return "{$endpoint}/{$bucket}/{$path}";
    }

}
