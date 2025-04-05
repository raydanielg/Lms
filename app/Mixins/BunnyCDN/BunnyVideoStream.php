<?php

namespace App\Mixins\BunnyCDN;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use ToshY\BunnyNet\VideoStreamRequest;

class BunnyVideoStream
{
    protected $client;
    protected $bunnyStream;
    protected $libraryId;
    protected $hostname;
    protected $accessKey;
    protected $tokenAuthenticationKey;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client();

        $settings = getFeaturesSettings('bunny_configs');

        $this->libraryId = (!empty($settings) and !empty($settings['library_id'])) ? $settings['library_id'] : null;
        $this->hostname = (!empty($settings) and !empty($settings['hostname'])) ? $settings['hostname'] : null;
        $this->accessKey = (!empty($settings) and !empty($settings['access_key'])) ? $settings['access_key'] : null;
        $this->tokenAuthenticationKey = (!empty($settings) and !empty($settings['token_authentication_key'])) ? $settings['token_authentication_key'] : null;

        $this->bunnyStream = new VideoStreamRequest($this->accessKey);
    }

    /**
     * @throws GuzzleException
     */
    public function createCollection($name, $checkDuplicate = false)
    {
        if ($checkDuplicate) {
            $response = $this->bunnyStream->getCollectionList($this->libraryId, [
                'page' => 1,
                'itemsPerPage' => 100,
                'search' => $name,
                'orderBy' => 'date',
            ]);

            $oldCollectionId = null;

            if (!empty($response['content']) and !empty($response['content']['items'])) {
                foreach ($response['content']['items'] as $item) {
                    if ($item and $item['name'] == $name) {
                        $oldCollectionId = $item['guid'];
                    }
                }
            }

            if (!empty($oldCollectionId)) {
                return $oldCollectionId;
            }
        }


        $response = $this->client->request('POST', "https://video.bunnycdn.com/library/{$this->libraryId}/collections", [
            'body' => '{"name": "' . $name . '"}',
            'headers' => [
                'AccessKey' => "{$this->accessKey}",
                'accept' => 'application/json',
                'content-type' => 'application/*+json',
            ],
        ]);

        if ($response->getStatusCode() == 200) {
            $body = json_decode($response->getBody(), true);

            return $body['guid'];
        }

        return null;
    }

    public function uploadVideo($title, $collectionId, UploadedFile $file)
    {

        $videoId = $this->createVideo($title, $collectionId);

        if ($videoId) {

            // create tmp directory
            Storage::disk('public')->makeDirectory('bunny_tmp');

            // upload to tmp directory
            $filename = time() . $file->getClientOriginalName();
            $fileLocation = Storage::disk('public')->putFileAs(
                'bunny_tmp',
                $file,
                $filename
            );

            $filePath = public_path("/store/$fileLocation");

            $body = $this->bunnyStream->uploadVideo($this->libraryId, $videoId, $filePath);

            $url = null;

            if (!empty($body['status']) and $body['status']['code'] == 200) {
                //$url = $body['status']["info"]['url'];
                $url = "https://iframe.mediadelivery.net/embed/{$this->libraryId}/{$videoId}";

                $sha256 = Hash::make("{$this->tokenAuthenticationKey}" . "$videoId");
                $url .= "?token=" . $sha256;
            }

            // remove tmp directory
            Storage::disk('public')->deleteDirectory('bunny_tmp');

            return $url;
        }
    }

    private function createVideo($title, $collectionId = null)
    {
        $response = $this->bunnyStream->createVideo($this->libraryId, [
            'title' => $title,
            'collectionId' => $collectionId
        ]);


        if ($response and $response['status']['code'] == 200) {
            return $response['content']['guid'];
        }

        return false;
    }

    private function getLibraryIdAndVideoIdFromPath($path)
    {
        if (empty($path)) {
            return false;
        }

        $path = str_replace('https://iframe.mediadelivery.net/embed/', '', $path);
        $path = explode('?', $path)[0];
        $path = explode('/', $path);

        if (count($path) < 2) {
            return false;
        }

        $libraryId = $path[0];
        $videoId = $path[1];

        return [
            'libraryId' => $libraryId,
            'videoId' => $videoId,
        ];
    }

    public function deleteVideo($path)
    {
        $getLibraryIdAndVideoIdFromPath = $this->getLibraryIdAndVideoIdFromPath($path);

        if ($getLibraryIdAndVideoIdFromPath) {
            $libraryId = $getLibraryIdAndVideoIdFromPath['libraryId'];
            $videoId = $getLibraryIdAndVideoIdFromPath['videoId'];

            $this->bunnyStream->deleteVideo($libraryId, $videoId);
        }

    }
}
