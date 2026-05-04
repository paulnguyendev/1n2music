<?php

namespace App\Helpers;
use Alaouy\Youtube\Facades\Youtube;
use Illuminate\Support\Facades\Log;


class YoutubeService{
    public static function getYoutubeVideoDetailsFromUrl($url)
    {
        $videoId = self::extractYoutubeVideoId($url);
        if (!$videoId) {
            return [
                'status' => false,
                'message'=>'Invalid Youtube URL'
            ];
        }
        $video = Youtube::getVideoInfo($videoId);
        $snippet = $video->snippet??null;
        $title = $snippet->title??null;
        $description = $snippet->description??null;
        $metaData = $video??null;
        $status = true;
        if (!$video) {
            $status = false;
        }
        return [
            'status' => $status,
            'videoId' => $videoId,
            'title' => $title,
            'description' => $description,
            'metaData' => $metaData,
        ];
    }
    public static function getYoutubeVideoDetailsById($id='')
    {
        $video = Youtube::getVideoInfo($id);
        $snippet = $video->snippet??null;
        $title = $snippet->title??null;
        $description = $snippet->description??null;
        $tags = $snippet->tags??[];
        $metaData = $video??null;
        $status = true;
        if (!$video) {
            $status = false;
        }
        return [
            'status' => $status,
            'videoId' => $id,
            'title' => $title,
            'description' => $description,
            'tags' => $tags,
            'metaData' => $metaData,
        ];
    }
    public static function extractYoutubeVideoId($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }
        if (!preg_match('/(youtube\.com|youtu\.be)/', $url)) {
            return null;
        }
        try {
            return Youtube::parseVidFromURL($url);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return null;
        }
    }

}
