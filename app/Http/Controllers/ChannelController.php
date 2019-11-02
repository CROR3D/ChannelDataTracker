<?php

namespace App\Http\Controllers;

use Config;
use ScheduleHelper;
use Carbon\Carbon;
use App\Models\Channel;
use App\Models\Video;
use App\Models\History;
use App\Models\ChannelDailyTracker;
use App\Models\VideoDailyTracker;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Validation\Rule;

class ChannelController extends Controller
{
    public function index()
    {
        $data = $this->getData();

        return view('index')->with(['searchData' => null, 'data' => $data]);
    }

    public function manageForms(Request $request)
    {
        switch(true) {
            case $request->has('searchBtn'):
                if($request->search === null) return redirect()->route('index');
                $searchData = $this->searchChannels($request->maxResults, $request->search);
                $data = $this->getData();

                return view('index')->with(['searchData' => $searchData, 'data' => $data]);
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
                    'tracking' => $request->channelSettingsTracking
                ];

                if($request->channelSettingsEarningFactor) {
                    $allVideoData = [
                        'earning_factor' => $request->channelSettingsEarningFactor,
                        'factor_currency' => $request->channelSettingsFactorCurrency
                    ];

                    $allChannelVideos = Video::where('channel_id', $channelId)->get();

                    foreach ($allChannelVideos as $video) {
                        $this->updateTrackedVideo($video->id, $allVideoData);
                    }
                }

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

                if($video === null || empty($video->items)) {
                    session()->flash('error', 'Video not found!');
                    return redirect()->route('index');
                }

                $videoChannelId = $video->items[0]->snippet->channelId;

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
                $videoId = $request->videoSettingsDelete;
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
            'name' => $data->snippet->title
        ];

        $dailyData = [
            'channel_id' => $channelId
        ];

        $dailyData['day' . $day] = [
            'subs' => $data->statistics->subscriberCount,
            'videos' => $data->statistics->videoCount,
            'views' => $data->statistics->viewCount
        ];

        $newChannel = new Channel;
        $newChannel->saveChannel($channel);

        $newChannelDailyTracker = new ChannelDailyTracker;
        $newChannelDailyTracker->saveChannelDailyTracker($dailyData);
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
        $today = Carbon::now();
        $day = $today->day;

        $video = array(
            'id' => $data->id,
            'channel_id' => $data->snippet->channelId,
            'name' => ($definedData['title'] === null) ? $data->snippet->title : $definedData['title'],
            'tracked_zero' => $data->statistics->viewCount,
            'month_zero' => $data->statistics->viewCount,
            'earning_factor' => $definedData['videoSettingsEarningFactor'],
            'factor_currency' => $definedData['videoSettingsFactorCurrency'],
            'treshold' => $definedData['videoSettingsTreshold'],
            'note' => $definedData['videoSettingsNote']
        );

        $dailyData = [
            'video_id' => $data->id
        ];

        $dailyData['day' . $day] = [
            'views' => $video['tracked_zero'],
            'earned' => 0
        ];

        $historyData = [
            'video_id' => $data->id
        ];

        $newVideo = new Video;
        $newVideo->saveVideo($video);

        $newVideoDailyTracker = new VideoDailyTracker;
        $newVideoDailyTracker->saveVideoDailyTracker($dailyData);

        $newVideoHistory = new History;
        $newVideoHistory->saveHistory($historyData);
    }

    private function updateTrackedVideo($id, $videoData)
    {
        $video = Video::find($id);
        $video->updateVideo($videoData);
    }

    private function getData()
    {
        $channels = Channel::all();
        $today = Carbon::now();
        $day = $today->day;
        $data = [];

        foreach ($channels as $dbChannel) {
            $channelId = $dbChannel->id;
            $channelCurrentData = ScheduleHelper::getChannelData($channelId);

            $channel = [
                'subs' => $channelCurrentData->items[0]->statistics->subscriberCount,
                'videos' => $channelCurrentData->items[0]->statistics->videoCount,
                'views' => $channelCurrentData->items[0]->statistics->viewCount
            ];

            $dailyChannelData = $this->getDailyData($channelId, 'channel');

            $channelData = array(
                $channelId => [
                    'id' => $channelId,
                    'name' => $dbChannel->name,
                    'tracking' => $dbChannel->tracking,
                    'channel_videos' => [],
                    'channel_data' => [
                        'total' => [
                            'subs' => $channel['subs'],
                            'views' => $channel['views']
                        ],
                        'daily' => [
                            'yesterday' => [
                                'subs' => $dailyChannelData['today']['subs'] - $dailyChannelData['yesterday']['subs'],
                                'views' => $dailyChannelData['today']['views'] - $dailyChannelData['yesterday']['views']
                            ],
                            'today' => [
                                'subs' => $channel['subs'] - $dailyChannelData['today']['subs'],
                                'views' => $channel['views'] - $dailyChannelData['today']['views']
                            ]
                        ],
                        'average' => [
                            'monthly' => [
                                'subs' => 0,
                                'views' => 0
                            ],
                            'yearly' => [
                                'subs' => 0,
                                'views' => 0
                            ]
                        ]
                    ],
                    'channel_calculation' => [
                        'total' => [
                            'calculatedViews' => [
                                'views' => 0,
                                'earning' => 0
                            ],
                            'calculatedMonthlyViews' => [
                                'monthlyViews' => 0,
                                'earning' => 0
                            ]
                        ],
                        'daily' => [
                            'calculatedYesterday' => [
                                'views' => 0,
                                'earning' => 0
                            ],
                            'calculatedToday' => [
                                'views' => 0,
                                'earning' => 0
                            ]
                        ],
                        'average' => [
                            'calculatedMonthViews' => [
                                'views' => 0,
                                'earning' => 0
                            ],
                            'calculatedYearViews' => [
                                'views' => 0,
                                'earning' => 0
                            ]
                        ]
                    ]
                ]
            );

            $videos = Video::where('channel_id', $channelId)->get();
            $videoCount = count($videos);
            $calculationCurrency = null;

            foreach ($videos as $dbVideo) {
                $videoCurrentData = ScheduleHelper::getVideoData($dbVideo->id);
                $videoViews =  $videoCurrentData->items[0]->statistics->viewCount;

                $dailyVideoData = $this->getDailyData($dbVideo->id, 'video');

                $calculatedVideoDailyData = [
                    'yesterdayViews' => $dailyVideoData['today']['views'] - $dailyVideoData['yesterday']['views'],
                    'todayViews' => $videoViews - $dailyVideoData['today']['views']
                ];

                if(!$calculationCurrency) $calculationCurrency = $dbVideo->factor_currency;
                $channelVideo = [
                    'id' => $dbVideo->id,
                    'channel_id' => $dbVideo->channel_id,
                    'name' => $dbVideo->name,
                    'tracked_zero' => $dbVideo->tracked_zero,
                    'month_zero' => $dbVideo->month_zero,
                    'earning_factor' => $dbVideo->earning_factor,
                    'factor_currency' => $dbVideo->factor_currency,
                    'treshold' => $dbVideo->treshold,
                    'note' => $dbVideo->note,
                    'video_data' => [
                        'total' => [
                            'calculatedViews' => [
                                'views' => $videoViews,
                                'monthlyViews' => $videoViews - $dbVideo->month_zero
                            ]
                        ]
                    ]
                ];

                $currencyExchange = $this->searchCurrencyExchangeValues();

                $earningsOnViewsInDollars = (($videoViews - $channelVideo['tracked_zero']) / 1000) * $channelVideo['earning_factor'];
                $earningsOnMonthlyViewsInDollars = (($videoViews - $channelVideo['month_zero']) / 1000) * $channelVideo['earning_factor'];

                $basedOnViews = $this->exchangeCurrency(
                                    $channelVideo['factor_currency'],
                                    $currencyExchange,
                                    $earningsOnViewsInDollars
                                );

                $basedOnMonthlyViews = $this->exchangeCurrency(
                                           $channelVideo['factor_currency'],
                                           $currencyExchange,
                                           $earningsOnMonthlyViewsInDollars
                                       );

                $channelVideo['video_data']['total']['calculatedEarnings']['views'] = $this->addCurrency(
                                                                                          $basedOnViews,
                                                                                          $channelVideo['factor_currency']
                                                                                      );

                $channelVideo['video_data']['total']['calculatedEarnings']['monthlyViews'] = $this->addCurrency(
                                                                                                $basedOnMonthlyViews,
                                                                                                $channelVideo['factor_currency']
                                                                                             );

                $channelVideo['video_data']['daily']['calculatedViews']['yesterdayViews'] = $calculatedVideoDailyData['yesterdayViews'];
                $channelVideo['video_data']['daily']['calculatedViews']['todayViews'] = $calculatedVideoDailyData['todayViews'];

                $channelVideo['video_data']['daily']['calculatedEarnings']['yesterdayViews'] = $this->addCurrency(
                                                                                                    $this->exchangeCurrency($channelVideo['factor_currency'], $currencyExchange, $dailyVideoData['yesterday']['earned']),
                                                                                                    $channelVideo['factor_currency']
                                                                                                );

                $channelVideo['video_data']['daily']['calculatedEarnings']['todayViews'] = $this->addCurrency(
                                                                                                $this->exchangeCurrency($channelVideo['factor_currency'], $currencyExchange, $dailyVideoData['today']['earned']),
                                                                                                $channelVideo['factor_currency']
                                                                                            );

                $videoMonthData = $this->getMonthData($dbVideo->id, 'video');
                $videoMonthData = $videoMonthData->getAttributes();
                $videoMonthData = array_slice($videoMonthData, 1, -2);

                $videoYearData = $this->getVideoYearData($dbVideo->id);
                $videoYearData = $videoYearData->getAttributes();
                $videoYearData = array_slice($videoYearData, 2, -2);

                $channelVideo['video_data']['average']['calculatedViews']['lastMonthViews'] = $this->calculateAverage($videoMonthData, $channelVideo['tracked_zero'], 'views');
                $channelVideo['video_data']['average']['calculatedViews']['lastYearViews'] = $this->calculateAverage($videoYearData, $channelVideo['tracked_zero'], 'views');
                $channelVideo['video_data']['average']['calculatedEarnings']['lastMonthViews'] = $this->addCurrency(
                                                                                                    $this->exchangeCurrency($channelVideo['factor_currency'], $currencyExchange, $this->calculateAverage($videoMonthData, null, 'earnings')),
                                                                                                    $channelVideo['factor_currency']
                                                                                                 );
                $channelVideo['video_data']['average']['calculatedEarnings']['lastYearViews'] = $this->addCurrency(
                                                                                                    $this->exchangeCurrency($channelVideo['factor_currency'], $currencyExchange, $this->calculateAverage($videoYearData, null, 'earnings')),
                                                                                                    $channelVideo['factor_currency']
                                                                                                 );

                $history = History::where('video_id', $channelVideo['id'])->first();
                $channelVideo['history'] = $history;

                array_push($channelData[$channelId]['channel_videos'], $channelVideo);

                if($videoCount > 1) {
                    if($calculationCurrency !== $channelVideo['factor_currency']) {
                        $basedOnViewsExchanged = $this->exchangeCurrency(
                                                    $calculationCurrency,
                                                    $currencyExchange,
                                                    $earningsOnViewsInDollars
                                                );
                        $basedOnMonthlyViewsExchanged = $this->exchangeCurrency(
                                                            $calculationCurrency,
                                                            $currencyExchange,
                                                            $earningsOnMonthlyViewsInDollars
                                                        );

                        $channelData[$channelId]['channel_calculation']['total']['calculatedViews']['earning'] =
                            $channelData[$channelId]['channel_calculation']['total']['calculatedViews']['earning'] + $basedOnViewsExchanged;

                        $channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['earning'] =
                            $channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['earning'] + $basedOnMonthlyViewsExchanged;
                    } else {
                        $channelData[$channelId]['channel_calculation']['total']['calculatedViews']['earning'] =
                            $channelData[$channelId]['channel_calculation']['total']['calculatedViews']['earning'] + $basedOnViews;

                        $channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['earning'] =
                            $channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['earning'] + $basedOnMonthlyViews;
                    }

                    $channelData[$channelId]['channel_calculation']['total']['calculatedViews']['views'] =
                        $channelData[$channelId]['channel_calculation']['total']['calculatedViews']['views'] + $videoViews;

                    $channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['monthlyViews'] =
                        $channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['monthlyViews'] + $videoViews - $dbVideo->month_zero;
                }
            }

            $channelData[$channelId]['channel_calculation']['total']['calculatedViews']['earning'] =
                $this->addCurrency(number_format($channelData[$channelId]['channel_calculation']['total']['calculatedViews']['earning'], 2), $calculationCurrency);

            $channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['earning'] =
                $this->addCurrency(number_format($channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['earning'], 2), $calculationCurrency);

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
                return number_format($value * $currentState, 2);
                break;
            case 'EUR':
                $currentState = $currencyExchange->rates->EUR;
                return number_format($value * $currentState, 2);
                break;
            default:
                return number_format($value, 2);
                break;
        }
    }

    private function addCurrency($value, $currency)
    {
        switch($currency) {
            case 'HRK':
                return $value . 'kn';
                break;
            case 'EUR':
                return $value . '€';
                break;
            default:
                return $value . '$';
                break;
        }
    }

    private function deleteChannel($id)
    {
        $channel = Channel::find($id);
        $videos = Video::where('channel_id', $id)->get();
        foreach($videos as $video) {
            $videoDailyData = VideoDailyTracker::where('video_id', $video->id)->first();
            $videoHistory = History::where('video_id', $video->id)->first();
            $video->delete();
            $videoDailyData->delete();
            $videoHistory->delete();
        }
        $channel->delete();
    }

    private function deleteVideo($id)
    {
        $video = Video::find($id);
        $video->delete();
    }

    private function getDailyData($id, $type)
    {
        $today = Carbon::now();
        $day = $today->day;

        if($type === 'channel') {
            $dailyTracking = ChannelDailyTracker::where('channel_id', $id)->first();
        } else {
            $dailyTracking = VideoDailyTracker::where('video_id', $id)->first();
        }

        $todayData = $dailyTracking->{'day' . $day};

        if($day === 1) {
            $lastDayOfPreviousMonth = $today->startOfMonth()->subSeconds(1)->day;
            $yesterdayData = $dailyTracking->{'day' . $lastDayOfPreviousMonth};
        } else {
            $yesterdayData = $dailyTracking->{'day' . ($day - 1)};
        }

        if($type === 'channel') {
            return [
                'yesterday' => [
                    'subs' => ($yesterdayData['subs']) ? $yesterdayData['subs'] : $todayData['subs'],
                    'views' => ($yesterdayData['views']) ? $yesterdayData['views'] : $todayData['views'],
                ],
                'today' => [
                    'subs' => $todayData['subs'],
                    'views' => $todayData['views']
                ]
            ];
        } else {
            return [
                'yesterday' => [
                    'views' => ($yesterdayData['views']) ? $yesterdayData['views'] : $todayData['views'],
                    'earned' => ($yesterdayData['earned']) ? $yesterdayData['earned'] : $todayData['earned']
                ],
                'today' => [
                    'views' => $todayData['views'],
                    'earned' => $todayData['earned']
                ]
            ];
        }
    }

    private function getMonthData($id, $type)
    {
        if($type === 'channel') {
            return ChannelDailyTracker::where('channel_id', $id)->first();
        } else {
            return VideoDailyTracker::where('video_id', $id)->first();
        }
    }

    private function getVideoYearData($id)
    {
        return History::where('video_id', $id)->first();
    }

    private function calculateAverage($array, $trackedZero, $type)
    {
        $sum = 0;
        $count = 0;
        $areViews = $type === 'views';

        foreach ($array as $value) {
            if(gettype($value) === 'string') {
                $value = json_decode($value);
                $arrayViews = $value->views;
                $arrayEarned = $value->earned;
            } else {
                $arrayViews = $value['views'];
                $arrayEarned = $value['earned'];
            }

            if($areViews && !is_null($value) && $trackedZero) {
                $number = $arrayViews - $trackedZero;
                $count++;
            } elseif(!$areViews && !is_null($value)) {
                $number = $arrayEarned;
                $count++;
            } else {
                $number = 0;
            }
        }

        $sum += $number;

        if($count === 0) {
            if($areViews) {
                return 0;
            } else {
                return number_format(0, 2);
            }
        }

        if($areViews) {
            return $sum / $count;
        } else {
            return number_format($sum / $count, 2);
        }
    }
}
