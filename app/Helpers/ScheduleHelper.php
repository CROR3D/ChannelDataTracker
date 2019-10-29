<?php

namespace App\Helpers;

use Config;
use GuzzleHttp\Client;
use App\Channel;

class ScheduleHelper
{
    public function getCurrentChannelsData()
    {
        $channels = Channel::all();
        $data = [];

        foreach ($channels as $channel) {
            $client = new Client();
            $response = $client->request('GET',
                'https://www.googleapis.com/youtube/v3/channels',
                [
                    'headers' => [
                        'Accept' => 'application/json','Content-type' => 'application/json'
                    ],
                    'query' => [
                        'part' => 'snippet,contentDetails,statistics,topicDetails,status',
                        'id' => $channel->id,
                        'key' => Config::get('values.apiKey')
                    ],
                ])->getBody();

            array_push($data, json_decode($response));
        }

        return $data;
    }
}
