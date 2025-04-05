<?php

namespace App\Providers;

use App\CustomStorage\CustomMinioAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;

class MinioStorageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Storage::extend('minio', function ($app, $config) {
            $client = new S3Client([
                'credentials' => [
                    'key' => $config["key"],
                    'secret' => $config["secret"]
                ],
                'region' => $config["region"],
                'version' => "latest",
                'bucket_endpoint' => false,
                'use_path_style_endpoint' => true,
                'endpoint' => $config["endpoint"],
            ]);

            $options = [
                'override_visibility_on_copy' => true,
                'CURLOPT_SSL_VERIFYPEER' => false
            ];

            //$adapter = new AwsS3V3Adapter($client, $config["bucket"], '', null, null, $options);
            $adapter = new CustomMinioAdapter($client, $config["bucket"], '', null, null, $options);

            $filesystem = new \League\Flysystem\Filesystem($adapter);

            return new \Illuminate\Filesystem\FilesystemAdapter($filesystem, $adapter);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
