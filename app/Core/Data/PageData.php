<?php

namespace App\Core\Data;

use Sentinel;
use APIManager;
use Carbon\Carbon;
use App\Core\Data\Daily\ChannelDailyData;
use App\Core\Data\Daily\VideoDailyData;
use App\Core\Data\ConvertData;
use App\Models\User;
use App\Models\Channel;
use App\Models\Video;
use App\Models\History;

class PageData
{
    public static function get()
    {
        if($user = Sentinel::check())
        {
            $userId = $user->id;
        }
        else
        {
            $userId = null;
        }

        $channels = Channel::where('user_id', $userId)->get();
        //dd(User::find(1)->channels);
        $day = Carbon::now()->day;
        $data = $channelVideos = [];
        $error = false;

        foreach ($channels as $dbChannel)
        {
            $channelId = $dbChannel->id;

            $totalCalculatedViews = $totalCalculatedMonthlyViews = 0;
            $dailyCalculatedYesterdayViews = $dailyCalculatedTodayViews = 0;
            $avgCalculatedMonthViews = $avgCalculatedYearViews = 0;

            $totalCalculatedViewsEarning = $totalCalculatedMonthlyViewsEarning = 0;
            $dailyCalculatedYesterdayEarning = $dailyCalculatedTodayEarning = 0;
            $avgCalculatedMonthEarning = $avgCalculatedYearEarning = 0;

            $channelCurrentData = APIManager::getChannelData($channelId);

            if(!$channelCurrentData)
            {
                $error = true;
                break;
            }

            $channel = [
                'subs' => $channelCurrentData->items[0]->statistics->subscriberCount,
                'videos' => $channelCurrentData->items[0]->statistics->videoCount,
                'views' => $channelCurrentData->items[0]->statistics->viewCount
            ];

            $channelMode = $dbChannel->mode;

            $channelDailyData = new ChannelDailyData($channelId, $userId);
            $channelDaily = $channelDailyData->get();

            $videos = Video::where('channel_id', $channelId)->where('user_id', $userId)->get();
            $videoCount = count($videos);
            $calculationCurrency = null;

            foreach ($videos as $dbVideo)
            {
                $videoCurrentData = APIManager::getVideoData($dbVideo->id);
                $videoViews =  $videoCurrentData->items[0]->statistics->viewCount;

                $videoDailyData = new VideoDailyData($dbVideo->id, $userId);
                $videoDaily = $videoDailyData->get();

                $calculatedVideoDailyData = [
                    'yesterdayViews' => $videoDaily['yesterday']['views'],
                    'todayViews' => $videoDaily['today']['views']
                ];

                if(!$calculationCurrency) $calculationCurrency = $dbVideo->factor_currency;

                $currencyExchange = APIManager::searchCurrencyExchangeValues();

                $channelVideoFactorCurrency = $dbVideo->factor_currency;
                $channelVideoEarningFactor = $dbVideo->earning_factor;

                if($channelMode == 'all_views')
                {
                    $videoViewsBasedOnMode = $videoViews;
                }
                else
                {
                    $videoViewsBasedOnMode = $videoViews - $dbVideo->tracked_zero;
                }

                $totalEarningsOnViewsInDollars = ($videoViewsBasedOnMode / 1000) * $channelVideoEarningFactor;
                $totalEarningsOnMonthlyViewsInDollars = (($videoViews - $dbVideo->month_zero) / 1000) * $channelVideoEarningFactor;
                $totalBasedOnViews = ConvertData::exchangeCurrency($channelVideoFactorCurrency, $currencyExchange, $totalEarningsOnViewsInDollars);
                $totalBasedOnMonthlyViews = ConvertData::exchangeCurrency($channelVideoFactorCurrency, $currencyExchange, $totalEarningsOnMonthlyViewsInDollars);

                $dailyYesterdayEarningsInDollars = ($calculatedVideoDailyData['yesterdayViews'] / 1000) * $channelVideoEarningFactor;
                $dailyTodayEarningsInDollars = ($calculatedVideoDailyData['todayViews'] / 1000) * $channelVideoEarningFactor;
                $dailyYesterday = ConvertData::exchangeCurrency($channelVideoFactorCurrency, $currencyExchange, $dailyYesterdayEarningsInDollars);
                $dailyToday = ConvertData::exchangeCurrency($channelVideoFactorCurrency, $currencyExchange, $dailyTodayEarningsInDollars);

                $videoMonthData = $videoDailyData->getMonthData();
                $videoMonthData = $videoMonthData->getAttributes();
                $videoMonthData = array_slice($videoMonthData, 4, -2);
                $videoYearData = $videoDailyData->getYearData();
                $videoYearData = $videoYearData->getAttributes();
                $videoYearData = array_slice($videoYearData, 4, -2);

                $avgMonth = ConvertData::exchangeCurrency($channelVideoFactorCurrency, $currencyExchange, ConvertData::calculateAverage($videoMonthData, 'earned'), $channelVideoFactorCurrency);
                $avgYear = ConvertData::exchangeCurrency($channelVideoFactorCurrency, $currencyExchange, ConvertData::calculateAverage($videoYearData, 'earned'), $channelVideoFactorCurrency);

                $history = History::where('video_id', $dbVideo->id)->first();

                $channelVideo = [
                    'db_id' => $dbVideo->db_id,
                    'id' => $dbVideo->id,
                    'channel_db_id' => $dbVideo->channel_db_id,
                    'channel_id' => $dbVideo->channel_id,
                    'name' => $dbVideo->name,
                    'tracked_zero' => $dbVideo->tracked_zero,
                    'month_zero' => $dbVideo->month_zero,
                    'treshold_zero' => $dbVideo->treshold_zero,
                    'earning_factor' => $channelVideoEarningFactor,
                    'factor_currency' => $channelVideoFactorCurrency,
                    'treshold' => $dbVideo->treshold,
                    'note' => $dbVideo->note,
                    'history' => $history,
                    'video_data' => [
                        'total' => [
                            'calculatedViews' => [
                                'views' => ($channelMode == 'mixed' ) ? $videoViews : $videoViewsBasedOnMode,
                                'monthlyViews' => $videoViews - $dbVideo->month_zero
                            ],
                            'calculatedEarnings' => [
                                'views' => ConvertData::addCurrency($totalBasedOnViews, $channelVideoFactorCurrency),
                                'monthlyViews' => ConvertData::addCurrency($totalBasedOnMonthlyViews, $channelVideoFactorCurrency)
                            ]
                        ],
                        'daily' => [
                            'calculatedViews' => [
                                'yesterdayViews' => $calculatedVideoDailyData['yesterdayViews'],
                                'todayViews' => $calculatedVideoDailyData['todayViews']
                            ],
                            'calculatedEarnings' => [
                                'yesterdayViews' => ConvertData::addCurrency($dailyYesterday, $channelVideoFactorCurrency),
                                'todayViews' => ConvertData::addCurrency($dailyToday, $channelVideoFactorCurrency)
                            ]
                        ],
                        'average' => [
                            'calculatedViews' => [
                                'lastMonthViews' => number_format(ConvertData::calculateAverage($videoMonthData, 'views'), 2),
                                'lastYearViews' => number_format(ConvertData::calculateAverage($videoYearData, 'views'), 2)
                            ],
                            'calculatedEarnings' => [
                                'lastMonthViews' => ConvertData::addCurrency($avgMonth, $channelVideoFactorCurrency),
                                'lastYearViews' => ConvertData::addCurrency($avgYear, $channelVideoFactorCurrency)
                            ]
                        ]
                    ]
                ];

                array_push($channelVideos, $channelVideo);

                // IF VIDEO COUNT IS MORE THAN 1, CALCULATE TOTAL SUM OF ALL VIEWS AND EARNINGS
                if($videoCount > 1)
                {
                    // CONVERT VALUES IF VIDEO HAS SET DIFFERENT CURRENCY THAN OTHER VIDEOS FROM CURRENT CHANNEL
                    if($calculationCurrency !== $channelVideo['factor_currency'])
                    {
                        $totalBasedOnViewsExchanged = ConvertData::exchangeCurrency($calculationCurrency, $currencyExchange, $totalEarningsOnViewsInDollars);
                        $totalBasedOnMonthlyViewsExchanged = ConvertData::exchangeCurrency($calculationCurrency, $currencyExchange, $totalEarningsOnMonthlyViewsInDollars);
                        $totalCalculatedViewsEarning += $totalBasedOnViewsExchanged;
                        $totalCalculatedMonthlyViewsEarning += $totalBasedOnMonthlyViewsExchanged;

                        $dailyYesterdayExchanged = ConvertData::exchangeCurrency($calculationCurrency, $currencyExchange, $dailyYesterdayEarningsInDollars);
                        $dailyTodayExchanged = ConvertData::exchangeCurrency($calculationCurrency, $currencyExchange, $dailyTodayEarningsInDollars);
                        $dailyCalculatedYesterdayEarning += $dailyYesterdayExchanged;
                        $dailyCalculatedTodayEarning += $dailyTodayExchanged;

                        $avgMonthExchanged = ConvertData::exchangeCurrency($calculationCurrency, $currencyExchange, $avgMonth);
                        $avgYearExchanged = ConvertData::exchangeCurrency($calculationCurrency, $currencyExchange, $avgYear);

                        $avgCalculatedMonthEarning += $avgMonthExchanged;
                        $avgCalculatedYearEarning += $avgYearExchanged;
                    }
                    else
                    {
                        $totalCalculatedViewsEarning += $totalBasedOnViews;
                        $totalCalculatedMonthlyViewsEarning += $totalBasedOnMonthlyViews;

                        $dailyCalculatedYesterdayEarning += $dailyYesterday;
                        $dailyCalculatedTodayEarning += $dailyToday;

                        $avgCalculatedMonthEarning += ConvertData::exchangeCurrency($channelVideoFactorCurrency, $currencyExchange, ConvertData::calculateAverage($videoMonthData, 'earnings'));
                        $avgCalculatedYearEarning += ConvertData::exchangeCurrency($channelVideoFactorCurrency, $currencyExchange, ConvertData::calculateAverage($videoYearData, 'earnings'));
                    }

                    if($channelMode == 'mixed')
                    {
                        $totalCalculatedViews += $videoViews;
                    }
                    else
                    {
                        $totalCalculatedViews += $videoViewsBasedOnMode;
                    }

                    $totalCalculatedMonthlyViews = ($totalCalculatedMonthlyViews + $videoViews) - $dbVideo->month_zero;

                    $dailyCalculatedYesterdayViews += $calculatedVideoDailyData['yesterdayViews'];
                    $dailyCalculatedTodayViews += $calculatedVideoDailyData['todayViews'];

                    $avgCalculatedMonthViews += $channelVideo['video_data']['average']['calculatedViews']['lastMonthViews'];
                    $avgCalculatedYearViews += $channelVideo['video_data']['average']['calculatedViews']['lastYearViews'];
                }
            }

            $totalCalculatedViewsEarning = ConvertData::addCurrency(number_format($totalCalculatedViewsEarning, 2), $calculationCurrency);
            $totalCalculatedMonthlyViewsEarning = ConvertData::addCurrency(number_format($totalCalculatedMonthlyViewsEarning, 2), $calculationCurrency);
            $dailyCalculatedYesterdayEarning = ConvertData::addCurrency(number_format($dailyCalculatedYesterdayEarning, 2), $calculationCurrency);
            $dailyCalculatedTodayEarning = ConvertData::addCurrency(number_format($dailyCalculatedTodayEarning, 2), $calculationCurrency);
            $avgCalculatedMonthEarning = ConvertData::addCurrency(number_format($avgCalculatedMonthEarning, 2), $calculationCurrency);
            $avgCalculatedYearEarning = ConvertData::addCurrency(number_format($avgCalculatedYearEarning, 2), $calculationCurrency);

            $channelData = array(
                $channelId => [
                    'db_id' => $dbChannel->db_id,
                    'id' => $channelId,
                    'name' => $dbChannel->name,
                    'tracking' => $dbChannel->tracking,
                    'mode' => $dbChannel->mode,
                    'channel_videos' => $channelVideos,
                    'channel_data' => [
                        'total' => [
                            'subs' => $channel['subs'],
                            'views' => $channel['views']
                        ],
                        'daily' => [
                            'yesterday' => [
                                'subs' => $channelDaily['yesterday']['subs'],
                                'views' => $channelDaily['yesterday']['views']
                            ],
                            'today' => [
                                'subs' => $channel['subs'] - $channelDaily['today']['currentSubs'],
                                'views' => $channel['views'] - $channelDaily['today']['currentViews']
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
                    'all_videos_sum' => [
                        'total' => [
                            'calculatedViews' => [
                                'views' => $totalCalculatedViews,
                                'earning' => $totalCalculatedViewsEarning
                            ],
                            'calculatedMonthlyViews' => [
                                'monthlyViews' => $totalCalculatedMonthlyViews,
                                'earning' => $totalCalculatedMonthlyViewsEarning
                            ]
                        ],
                        'daily' => [
                            'calculatedYesterday' => [
                                'views' => $dailyCalculatedYesterdayViews,
                                'earning' => $dailyCalculatedYesterdayEarning
                            ],
                            'calculatedToday' => [
                                'views' => $dailyCalculatedTodayViews,
                                'earning' => $dailyCalculatedTodayEarning
                            ]
                        ],
                        'average' => [
                            'calculatedMonthViews' => [
                                'views' => $avgCalculatedMonthViews,
                                'earning' => $avgCalculatedMonthEarning
                            ],
                            'calculatedYearViews' => [
                                'views' => $avgCalculatedYearViews,
                                'earning' => $avgCalculatedYearEarning
                            ]
                        ]
                    ]
                ]
            );

            $data = array_merge($data, $channelData);
        }

        if($error) return false;

        return $data;
    }
}
