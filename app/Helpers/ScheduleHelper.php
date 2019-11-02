<?php

namespace App\Helpers;

use Config;
use GuzzleHttp\Client;
use App\Models\Channel;
use App\Models\Video;

class ScheduleHelper
{
    public function getChannelsData()
    {
        $channels = Channel::all();
        $data = [];

        foreach ($channels as $channel) {
            array_push($data, $this->getChannelData($channel->id));
        }

        return $data;
    }

    public function getVideosData()
    {
        $videos = Video::all();
        $data = [];

        foreach ($videos as $video) {
            array_push($data, $this->getVideoData($video->id));
        }

        return $data;
    }

    public function getChannelData($id)
    {
        $client = new Client();
        $response = $client->request('GET',
            'https://www.googleapis.com/youtube/v3/channels',
            [
                'headers' => [
                    'Accept' => 'application/json','Content-type' => 'application/json'
                ],
                'query' => [
                    'part' => 'snippet,contentDetails,statistics,topicDetails,status',
                    'id' => $id,
                    'key' => Config::get('values.apiKey')
                ],
            ])->getBody();

        return json_decode($response);
    }

    public function getVideoData($id)
    {
        $client = new Client();
        $response = $client->request('GET',
            'https://www.googleapis.com/youtube/v3/videos',
            [
                'headers' => [
                    'Accept' => 'application/json','Content-type' => 'application/json'
                ],
                'query' => [
                    'part' => 'snippet,contentDetails,statistics,status',
                    'id' => $id,
                    'key' => Config::get('values.apiKey')
                ],
            ])->getBody();

        return json_decode($response);
    }
}
