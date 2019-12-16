@if(count($channel['channel_videos']) > 0)
<div class="element-border my-2 video-data">
    <div class="row video-header text-center p-3">
        <div class="col">VIDEO<span class="plural">S</span></div>
        <div class="col">
            @switch($tracking)
                @case('daily')
                    VIEWS YESTERDAY
                    @break
                @case('average')
                    VIEWS (avg this MONTH)
                    @break
                @default
                    VIEWS
            @endswitch
        </div>
        <div class="col">
            @switch($tracking)
                @case('daily')
                    VIEWS TODAY
                    @break
                @case('average')
                    VIEWS (avg last YEAR)
                    @break
                @default
                    VIEWS/MONTH
            @endswitch
        </div>
        <div class="col">TRESHOLD</div>
        <div class="col"></div>
    </div>
    @if(count($channel['channel_videos']) > 1)
    <div class="row video-header text-center p-3 monthly-highlight">
        <div class="col"></div>
        <div class="col">
            @switch($tracking)
                @case('daily')
                    <span>{{ $channel['all_videos_sum']['daily']['calculatedYesterday']['views'] }}</span>
                    <span class="text-success earning-border">{{ $channel['all_videos_sum']['daily']['calculatedYesterday']['earning'] }}</span>
                    @break
                @case('average')
                    <span>{{ $channel['all_videos_sum']['average']['calculatedMonthViews']['views'] }}</span>
                    <span class="text-success earning-border">{{ $channel['all_videos_sum']['average']['calculatedMonthViews']['earning'] }}</span>
                    @break
                @default
                    <span>{{ $channel['all_videos_sum']['total']['calculatedViews']['views'] }}</span>
                    <span class="text-success earning-border">{{ $channel['all_videos_sum']['total']['calculatedViews']['earning'] }}</span>
            @endswitch
        </div>
        <div class="col">
            @switch($tracking)
                @case('daily')
                    <span>{{ $channel['all_videos_sum']['daily']['calculatedToday']['views'] }}</span>
                    <span class="text-success earning-border">{{ $channel['all_videos_sum']['daily']['calculatedToday']['earning'] }}</span>
                    @break
                @case('average')
                    <span>{{ $channel['all_videos_sum']['average']['calculatedYearViews']['views'] }}</span>
                    <span class="text-success earning-border">{{ $channel['all_videos_sum']['average']['calculatedYearViews']['earning'] }}</span>
                    @break
                @default
                    <span>{{ $channel['all_videos_sum']['total']['calculatedMonthlyViews']['monthlyViews'] }}</span>
                    <span class="text-success earning-border">{{ $channel['all_videos_sum']['total']['calculatedMonthlyViews']['earning'] }}</span>
            @endswitch
        </div>
        <div class="col"></div>
        <div class="col"></div>
    </div>
    @endif
    @foreach($channel['channel_videos'] as $video)
    <div class="row video-row text-center p-3" data-video="{{ json_encode($video) }}">
            <div class="col"><a class="video-link" href="https://www.youtube.com/watch?v={{ $video['id'] }}" target="_blank" data-toggle="tooltip" data-placement="bottom" title="{{ $video['name'] }}">{{ str_limit($video['name'], $limit = 18, $end = ' ...') }}</a></div>
            <div class="col">
                <p class="col-description">
                    @switch($tracking)
                        @case('daily')
                            VIEWS YESTERDAY:
                            @break
                        @case('average')
                            VIEWS (avg this MONTH):
                            @break
                        @default
                            VIEWS:
                    @endswitch
                </p>
                @switch($tracking)
                    @case('daily')
                        <span>{{ $video['video_data']['daily']['calculatedViews']['yesterdayViews'] }} </span>
                        <span class="text-success earning-border">{{ $video['video_data']['daily']['calculatedEarnings']['yesterdayViews'] }}</span>
                        @break
                    @case('average')
                        <span>{{ $video['video_data']['average']['calculatedViews']['lastMonthViews'] }} </span>
                        <span class="text-success earning-border">{{ $video['video_data']['average']['calculatedEarnings']['lastMonthViews'] }}</span>
                        @break
                    @default
                        <span>{{ $video['video_data']['total']['calculatedViews']['views'] }} </span>
                        <span class="text-success earning-border">{{ $video['video_data']['total']['calculatedEarnings']['views'] }}</span>
                @endswitch
            </div>
            <div class="col">
                <p class="col-description">
                    @switch($tracking)
                        @case('daily')
                            VIEWS TODAY:
                            @break
                        @case('average')
                            VIEWS (avg last YEAR):
                            @break
                        @default
                            VIEWS/MONTH:
                    @endswitch
                </p>
                @switch($tracking)
                    @case('daily')
                        <span>{{ $video['video_data']['daily']['calculatedViews']['todayViews'] }} </span>
                        <span class="text-success earning-border">{{ $video['video_data']['daily']['calculatedEarnings']['todayViews'] }}</span>
                        @break
                    @case('average')
                        <span>{{ $video['video_data']['average']['calculatedViews']['lastYearViews'] }} </span>
                        <span class="text-success earning-border">{{ $video['video_data']['average']['calculatedEarnings']['lastYearViews'] }}</span>
                        @break
                    @default
                        <span>{{ $video['video_data']['total']['calculatedViews']['monthlyViews'] }} </span>
                        <span class="text-success earning-border">{{ $video['video_data']['total']['calculatedEarnings']['monthlyViews'] }}</span>
                @endswitch
            </div>
            <div class="col">
                <p class="col-description">TRESHOLD: </p>
                @if($mode != 'all_views')
                        @if($mode == 'tracking_zero')
                            <span class="{{ ($video['video_data']['total']['calculatedViews']['views'] < $video['treshold']) ? 'text-danger' : 'text-success' }}">
                                {{ $video['video_data']['total']['calculatedViews']['views'] }}
                            </span>
                        @else
                            <span class="{{ ($video['video_data']['total']['calculatedViews']['views'] - $video['tracked_zero'] < $video['treshold']) ? 'text-danger' : 'text-success' }}">
                                {{ $video['video_data']['total']['calculatedViews']['views'] - $video['tracked_zero'] }}
                            </span>
                        @endif
                    <p class="slash">/</p>
                @endif
                @if($mode != 'all_views')
                    <span>{{ $video['treshold'] }}</span>
                @else
                    <span class="single-treshold {{ ($mode == 'all_views' && ($video['video_data']['total']['calculatedViews']['views'] >= $video['treshold'])) ? 'text-success' : 'text-danger' }}">{{ $video['treshold'] }}</span>
                @endif
            </div>
            <div class="col last-row">
                <button class="btn-custom btn-custom-secondary info-hover {{ ($video['note'] !== null) ? 'text-custom-success' : '' }}" data-toggle="tooltip" data-history="{{ $video['history'] }}" data-placement="bottom" title="{{ $video['note'] }}">
                    Info
                </button>
                <button class="btn-custom btn-custom-secondary video-settings">Settings</button>
            </div>
        </div>
    @endforeach
</div>
@endif
