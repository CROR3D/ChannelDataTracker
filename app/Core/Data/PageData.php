<?php

namespace App\Core\Data;

use APIManager;
use Carbon\Carbon;
use App\Core\Data\Daily\ChannelDailyData;
use App\Core\Data\Daily\VideoDailyData;
use App\Core\Data\ConvertData;
use App\Models\Channel;
use App\Models\Video;
use App\Models\History;

class PageData
{
    public static function get()
    {
        $channels = Channel::all();
        $today = Carbon::now();
        $day = $today->day;
        $data = [];

        foreach ($channels as $dbChannel) {
            $channelId = $dbChannel->id;
            $channelCurrentData = APIManager::getChannelData($channelId);

            $channel = [
                'subs' => $channelCurrentData->items[0]->statistics->subscriberCount,
                'videos' => $channelCurrentData->items[0]->statistics->videoCount,
                'views' => $channelCurrentData->items[0]->statistics->viewCount
            ];

            $channelDailyData = new ChannelDailyData($channelId);
            $channelDaily = $channelDailyData->get();

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
                                'subs' => $channelDaily['today']['subs'] - $channelDaily['yesterday']['subs'],
                                'views' => $channelDaily['today']['views'] - $channelDaily['yesterday']['views']
                            ],
                            'today' => [
                                'subs' => $channel['subs'] - $channelDaily['today']['subs'],
                                'views' => $channel['views'] - $channelDaily['today']['views']
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
                $videoCurrentData = APIManager::getVideoData($dbVideo->id);
                $videoViews =  $videoCurrentData->items[0]->statistics->viewCount;

                $videoDailyData = new VideoDailyData($dbVideo->id);
                $videoDaily = $videoDailyData->get();

                $calculatedVideoDailyData = [
                    'yesterdayViews' => $videoDaily['today']['views'] - $videoDaily['yesterday']['views'],
                    'todayViews' => $videoViews - $videoDaily['today']['views']
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

                $currencyExchange = APIManager::searchCurrencyExchangeValues();

                $earningsOnViewsInDollars = (($videoViews - $channelVideo['tracked_zero']) / 1000) * $channelVideo['earning_factor'];
                $earningsOnMonthlyViewsInDollars = (($videoViews - $channelVideo['month_zero']) / 1000) * $channelVideo['earning_factor'];

                $basedOnViews = ConvertData::exchangeCurrency(
                                    $channelVideo['factor_currency'],
                                    $currencyExchange,
                                    $earningsOnViewsInDollars
                                );

                $basedOnMonthlyViews = ConvertData::exchangeCurrency(
                                           $channelVideo['factor_currency'],
                                           $currencyExchange,
                                           $earningsOnMonthlyViewsInDollars
                                       );

                $channelVideo['video_data']['total']['calculatedEarnings']['views'] = ConvertData::addCurrency(
                                                                                          $basedOnViews,
                                                                                          $channelVideo['factor_currency']
                                                                                      );

                $channelVideo['video_data']['total']['calculatedEarnings']['monthlyViews'] = ConvertData::addCurrency(
                                                                                                $basedOnMonthlyViews,
                                                                                                $channelVideo['factor_currency']
                                                                                             );

                $channelVideo['video_data']['daily']['calculatedViews']['yesterdayViews'] = $calculatedVideoDailyData['yesterdayViews'];
                $channelVideo['video_data']['daily']['calculatedViews']['todayViews'] = $calculatedVideoDailyData['todayViews'];

                $channelVideo['video_data']['daily']['calculatedEarnings']['yesterdayViews'] = ConvertData::addCurrency(
                                                                                                    ConvertData::exchangeCurrency($channelVideo['factor_currency'], $currencyExchange, $videoDaily['yesterday']['earned']),
                                                                                                    $channelVideo['factor_currency']
                                                                                                );

                $channelVideo['video_data']['daily']['calculatedEarnings']['todayViews'] = ConvertData::addCurrency(
                                                                                                ConvertData::exchangeCurrency($channelVideo['factor_currency'], $currencyExchange, $videoDaily['today']['earned']),
                                                                                                $channelVideo['factor_currency']
                                                                                            );

                $videoMonthData = $videoDailyData->getMonthData();
                $videoMonthData = $videoMonthData->getAttributes();
                $videoMonthData = array_slice($videoMonthData, 1, -2);

                $videoYearData = $videoDailyData->getYearData();
                $videoYearData = $videoYearData->getAttributes();
                $videoYearData = array_slice($videoYearData, 2, -2);

                $channelVideo['video_data']['average']['calculatedViews']['lastMonthViews'] = ConvertData::calculateAverage($videoMonthData, $channelVideo['tracked_zero'], 'views');
                $channelVideo['video_data']['average']['calculatedViews']['lastYearViews'] = ConvertData::calculateAverage($videoYearData, $channelVideo['tracked_zero'], 'views');
                $channelVideo['video_data']['average']['calculatedEarnings']['lastMonthViews'] = ConvertData::addCurrency(
                                                                                                    ConvertData::exchangeCurrency($channelVideo['factor_currency'], $currencyExchange, ConvertData::calculateAverage($videoMonthData, null, 'earnings')),
                                                                                                    $channelVideo['factor_currency']
                                                                                                 );
                $channelVideo['video_data']['average']['calculatedEarnings']['lastYearViews'] = ConvertData::addCurrency(
                                                                                                    ConvertData::exchangeCurrency($channelVideo['factor_currency'], $currencyExchange, ConvertData::calculateAverage($videoYearData, null, 'earnings')),
                                                                                                    $channelVideo['factor_currency']
                                                                                                 );

                $history = History::where('video_id', $channelVideo['id'])->first();
                $channelVideo['history'] = $history;

                array_push($channelData[$channelId]['channel_videos'], $channelVideo);

                if($videoCount > 1) {
                    if($calculationCurrency !== $channelVideo['factor_currency']) {
                        $basedOnViewsExchanged = ConvertData::exchangeCurrency(
                                                    $calculationCurrency,
                                                    $currencyExchange,
                                                    $earningsOnViewsInDollars
                                                );
                        $basedOnMonthlyViewsExchanged = ConvertData::exchangeCurrency(
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
                ConvertData::addCurrency(number_format($channelData[$channelId]['channel_calculation']['total']['calculatedViews']['earning'], 2), $calculationCurrency);

            $channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['earning'] =
                ConvertData::addCurrency(number_format($channelData[$channelId]['channel_calculation']['total']['calculatedMonthlyViews']['earning'], 2), $calculationCurrency);

            $data = array_merge($data, $channelData);
        }

        return $data;
    }
}
