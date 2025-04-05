<?php

namespace App\Enums;

class UploadSource
{
    const YOUTUBE = 'youtube';
    const VIMEO = 'vimeo';
    const UPLOAD = 'upload';
    const S3 = 's3';
    const EXTERNAL = 'external';
    const IFRAME = 'iframe';

    const allSources = [
        self::YOUTUBE,
        self::VIMEO,
        self::UPLOAD,
        self::S3,
        self::EXTERNAL,
        self::IFRAME
    ];

    const uploadItems = [self::UPLOAD, self::S3];
    const urlPathItems = [
        self::YOUTUBE,
        self::VIMEO,
        self::EXTERNAL,
        self::IFRAME,
    ];
}
