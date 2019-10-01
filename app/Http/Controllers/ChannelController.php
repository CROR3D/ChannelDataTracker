<?php

namespace App\Http\Controllers;

use Config;
use App\Channel;
use App\Video;
use App\History;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Validation\Rule;

class ChannelController extends Controller
{
    public function index()
    {
        $data = $this->getData();

        return view('index')->with('data', $data);
    }

    public function manageForms(Request $request)
    {
        switch(true) {
            case $request->has('searchBtn'):

                if($request->search === null) return redirect()->route('index');
                $searchData = $this->searchChannels($request->maxResults, $request->search);
                $data = Channel::all();
                $videos = Video::all();

                return view('index')->with(['searchData' => $searchData, 'data' => $data, 'videos' => $videos]);
                break;

            case $request->has('addChannel'):
            case $request->has('add_search_form'):

                $id = ($request->id) ? $request->id : $request->add_search_form;
                $data = $this->addChannel($id);
                $this->storeChannel($data->items[0]);
                break;

            case $request->has('update_channel_form'):

                $channelId = $request->channelSettingsChannelId;
                $channelData = [
                    'title' => $request->channelSettingsTitle,
                    'tracking' => 'total',
                    'earning_factor' => $request->channelSettingsEarningFactor,
                    'factor_currency' => $request->channelSettingsFactorCurrency
                ];
                $this->updateChannel($channelId, $channelData);
                break;

            case $request->has('videoSettingsAdd'):

                $videoId = $request->videoSettingsAdd;
                $channelId = $request->videoSettingsChannelId;
                $videoExists = Video::where('id', $videoId)->exists();

                if($videoExists) return redirect()->route('index');

                $validatedData = $request->validate([
                    'videoSettingsTitle' => 'string|nullable',
                    'videoSettingsEarningFactor' => 'required|numeric',
                    'videoSettingsFactorCurrency' => [
                        'required',
                        Rule::in(['HRK', 'USD', 'EUR']),
                    ],
                    'videoSettingsTreshold' => 'numeric',
                    'videoSettingsNote' => 'string|nullable',
                    'videoSettingsAddOrUpdate' => 'required',
                ]);

                $video = $this->addVideo($videoId);
                $videoChannelId = $video->items[0]->snippet->channelId;

                if($videoChannelId !== $channelId) {
                    session()->flash('error', 'Video not found!');
                    return redirect()->route('index');
                }

                $videoData = [
                    'title' => $request->videoSettingsTitle,
                    'videoSettingsEarningFactor' => $request->videoSettingsEarningFactor,
                    'videoSettingsFactorCurrency' => $request->videoSettingsFactorCurrency,
                    'videoSettingsTreshold' => $request->videoSettingsTreshold,
                    'videoSettingsNote' => $request->videoSettingsNote
                ];

                $this->storeVideo($video->items[0], $videoData);
                break;

            case $request->has('update_video_form'):

                $videoData = array(
                    'name' => $request->videoSettingsTitle,
                    'earning_factor' => $request->videoSettingsEarningFactor,
                    'factor_currency' => $request->videoSettingsFactorCurrency,
                    'treshold' => $request->videoSettingsTreshold,
                    'note' => $request->videoSettingsNote
                );

                $this->updateVideo($request->video_id_form, $videoData);
                break;
            case $request->has('channelSettingsDelete'):

                $channelId = $request->channelSettingsChannelId;
                $this->deleteChannel($channelId);
                break;
            case $request->has('video_settings_delete'):

                $videoId = $request->videoSettingsChannelId;
                $this->deleteVideo($videoId);
                break;
        }

        return redirect()->route('index');
    }

    private function searchChannels($maxResults, $search)
    {
        $client = new Client();
        $response = $client->request('GET',
            'https://www.googleapis.com/youtube/v3/search',
            [
                'headers' => [
                    'Accept' => 'application/json','Content-type' => 'application/json'
                ],
                'query' => [
                    'part' => 'snippet',
                    'maxResults' => $maxResults,
                    'q' => $search,
                    'type' => 'channel',
                    'key' => Config::get('values.apiKey')
                ],
            ])->getBody();

        return json_decode($response);
    }

    private function addChannel($id)
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

    private function storeChannel($data)
    {
        $channel = array(
            'id' => $data->id,
            'name' => $data->snippet->title,
            'subs' => $data->statistics->subscriberCount,
            'videos' => $data->statistics->videoCount,
            'views' => $data->statistics->viewCount
        );

        $new_channel = new Channel;
        $new_channel->saveChannel($channel);
    }

    private function updateChannel($id, $channelData)
    {
        $channel = Channel::find($id);
        $channel->updateChannel($channelData);
    }

    private function addVideo($id)
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

    private function storeVideo($data, $definedData)
    {
        $video = array(
            'id' => $data->id,
            'channel_id' => $data->snippet->channelId,
            'name' => ($definedData['title'] === null) ? $data->snippet->title : $definedData['title'],
            'views' => $data->statistics->viewCount,
            'earning_factor' => $definedData['videoSettingsEarningFactor'],
            'factor_currency' => $definedData['videoSettingsFactorCurrency'],
            'monthly_views' => 0,
            'treshold_views' => $data->statistics->viewCount,
            'treshold' => $definedData['videoSettingsTreshold'],
            'likes' => $data->statistics->likeCount,
            'dislikes' => $data->statistics->dislikeCount,
            'comments' => $data->statistics->commentCount,
            'note' => $definedData['note'],
            'privacy' => $data->status->privacyStatus
        );

        $new_video = new Video;
        $new_video->saveVideo($video);
    }

    private function updateVideo($id, $videoData)
    {
        $video = Video::find($id);
        $video->updateVideo($videoData);
    }

    private function getData()
    {
        $channels = Channel::all();
        $data = [];

        foreach ($channels as $channel) {
            $channelId = $channel->id;
            $channelData = array(
                $channelId => [
                    'id' => $channelId,
                    'name' => $channel->name,
                    'tracking' => $channel->tracking,
                    'subs' => $channel->subs,
                    'videos' => $channel->videos,
                    'views' => $channel->views,
                    'channel_videos' => [],
                ]
            );

            $videos = Video::where('channel_id', $channelId)->get();

            foreach ($videos as $video) {
                $channelVideo = [];
                array_push($channelVideo, $video);
                $history = History::where('video_id', $video->id)->first();
                array_push($channelVideo, $history);
                array_push($channelData[$channelId]['channel_videos'], $channelVideo);
            }

            $data = array_merge($data, $channelData);
        }

        return $data;
    }

    private function deleteChannel($id)
    {
        $channel = Channel::find($id);
        $channel->delete();
    }

    private function deleteVideo($id)
    {
        $video = Video::find($id);
        $video->delete();
    }

    // FUNCTION FOR TASK SCHEDULING
    public function saveMonthHistory($videoId, $monthHistory)
    {
        $history = History::where('video_id', $videoId)->first();
        $history->update($monthHistory);
    }
}
