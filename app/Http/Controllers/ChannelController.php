<?php

namespace App\Http\Controllers;

use Config;
use Carbon\Carbon;
use App\Channel;
use App\Video;
use App\History;
use App\DailyTracker;
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
            case $request->has('addSearchedChannel'):

                $id = ($request->id) ? $request->id : $request->addSearchedChannel;
                $data = $this->addChannel($id);
                $this->storeChannel($data->items[0]);
                break;

            case $request->has('channelSettingsUpdate'):

                $channelId = $request->channelSettingsChannelId;
                $channelData = [
                    'title' => $request->channelSettingsTitle,
                    'tracking' => 'total',
                    'earning_factor' => $request->channelSettingsEarningFactor,
                    'factor_currency' => $request->channelSettingsFactorCurrency
                ];
                $this->updateChannel($channelId, $channelData);
                break;
            case $request->has('channelSettingsDelete'):

                $channelId = $request->channelSettingsChannelId;
                $this->deleteChannel($channelId);
                break;
            case $request->has('videoSettingsAdd'):

                $videoId = $request->videoSettingsAdd;
                $channelId = $request->videoSettingsChannelId;
                $videoExists = Video::where('id', $videoId)->exists();

                if($videoExists) {
                    session()->flash('error', 'Video is already tracked!');
                    return redirect()->route('index');
                }

                $video = $this->addVideo($videoId);
                $videoChannelId = $video->items[0]->snippet->channelId;

                if($video === null) {
                    session()->flash('error', 'Video not found!');
                    return redirect()->route('index');
                }

                if($videoChannelId !== $channelId) {
                    session()->flash('error', 'Video doesn\'t belong to the channel you want to add it to!');
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

            case $request->has('videoSettingsUpdate'):

                $videoData = array(
                    'name' => $request->videoSettingsTitle,
                    'earning_factor' => $request->videoSettingsEarningFactor,
                    'factor_currency' => $request->videoSettingsFactorCurrency,
                    'treshold' => $request->videoSettingsTreshold,
                    'note' => $request->videoSettingsNote
                );

                $this->updateTrackedVideo($request->videoSettingsUpdate, $videoData);
                break;
            case $request->has('videoSettingsDelete'):

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
        $channelId = $data->id;
        $today = Carbon::now();
        $day = $today->day;

        $channel = [
            'id' => $channelId,
            'name' => $data->snippet->title,
            'subs' => $data->statistics->subscriberCount,
            'videos' => $data->statistics->videoCount,
            'views' => $data->statistics->viewCount
        ];

        $dailyData = [
            'channel_id' => $channelId
        ];

        $dailyData['day' . $day] = [
            'subs' => $channel['subs'],
            'videos' => $channel['videos'],
            'views' => $channel['views']
        ];

        $dailyData['day3'] = [
            'subs' => $channel['subs'] + 200,
            'videos' => $channel['videos'] - 15,
            'views' => $channel['views'] - 3500
        ];

        $newChannel = new Channel;
        $newChannel->saveChannel($channel);

        $newDailyTracker = new DailyTracker;
        $newDailyTracker->saveDailyTracker($dailyData);
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
            'note' => $definedData['videoSettingsNote'],
            'privacy' => $data->status->privacyStatus
        );

        $newVideo = new Video;
        $newVideo->saveVideo($video);
    }

    private function updateTrackedVideo($id, $videoData)
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
            $dailyData = $this->getDailyData($channelId);

            $channelData = array(
                $channelId => [
                    'id' => $channelId,
                    'name' => $channel->name,
                    'tracking' => $channel->tracking,
                    'total' => [
                        'subs' => $channel->subs,
                        'videos' => $channel->videos,
                        'views' => $channel->views,
                    ],
                    'daily' => [
                        'subs' => $dailyData['subs'],
                        'videos' => $dailyData['videos'],
                        'views' => $dailyData['views'],
                    ],
                    'average' => [
                        'subs' => 16,
                        'videos' => 7,
                        'views' => 17,
                    ],
                    'channel_videos' => [],
                ]
            );

            $videos = Video::where('channel_id', $channelId)->get();

            foreach ($videos as $video) {
                $channelVideo = [];
                array_push($channelVideo, $video);

                $currencyExchange = $this->searchCurrencyExchangeValues();

                $earningsOnViewsInDollars = ($video->views / 1000) * $video->earning_factor;
                $earningsOnMonthlyViewsInDollars = ($video->monthly_views / 1000) * $video->earning_factor;

                $basedOnViews = $this->exchangeCurrency($video->factor_currency, $currencyExchange, $earningsOnViewsInDollars);
                $basedOnMonthlyViews = $this->exchangeCurrency($video->factor_currency, $currencyExchange, $earningsOnMonthlyViewsInDollars);

                $videoEarningsInDollars = [
                    'basedOnViews' => $basedOnViews,
                    'basedOnMonthlyViews' => $basedOnMonthlyViews,
                ];

                array_push($channelVideo, $videoEarningsInDollars);

                $history = History::where('video_id', $video->id)->first();
                array_push($channelVideo, $history);

                array_push($channelData[$channelId]['channel_videos'], $channelVideo);
            }

            $data = array_merge($data, $channelData);
        }

        return $data;
    }

    private function searchCurrencyExchangeValues()
    {
        $client = new Client();
        $response = $client->request('GET',
            'https://api.exchangeratesapi.io/latest',
            [
                'headers' => [
                    'Accept' => 'application/json','Content-type' => 'application/json'
                ],
                'query' => [
                    'base' => 'USD',
                    'symbols' => 'EUR,HRK'
                ],
            ])->getBody();

        return json_decode($response);
    }

    private function exchangeCurrency($currency, $currencyExchange, $value)
    {
        switch($currency) {
            case 'HRK':
                $currentState = $currencyExchange->rates->HRK;
                return strval(number_format($value * $currentState, 2) . 'kn');
                break;
            case 'EUR':
                $currentState = $currencyExchange->rates->EUR;
                return strval(number_format($value * $currentState, 2) . '€');
                break;
            default:
                return strval(number_format($value, 2) . '$');
                break;
        }
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

    private function getDailyData($id)
    {
        $today = Carbon::now();
        $day = $today->day;
        $dailyTracking = DailyTracker::where('channel_id', $id)->first();
        $todayData = $dailyTracking->{'day' . $day};
        $yesterdayData = $dailyTracking->{'day' . ($day - 1)};

        return [
            'subs' => $todayData['subs'] - $yesterdayData['subs'],
            'videos' => $todayData['videos'] - $yesterdayData['videos'],
            'views' => $todayData['views'] - $yesterdayData['views']
        ];
    }

    // FUNCTION FOR TASK SCHEDULING (SAVING MONTH HISTORY 1st DAY OF THE MONTH AT 00:00)
    public function saveMonthHistory($videoId, $monthHistory)
    {
        $history = History::where('video_id', $videoId)->first();
        $history->update($monthHistory);
    }

    // FUNCTION FOR TASK SCHEDULING (SAVING DAILY DATA EVERY DAY AT 00:00)
}
