<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="https://bootswatch.com/4/slate/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/effects.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ URL::asset('css/styles.css') }}">

    <title>Youtube Statistics</title>
  </head>

  <body>
      <div class="container-wrapper">
        <div class="container">
            <div class="jumbotron pb-0">
                <h1 class="display-4">Youtube Statistics</h1>
                <p class="lead">Trace your channel data</p>
                <hr class="my-4">
                <div id="search-form-wrapper" class="mb-4">
                    <form id="search-form" class="input-group" method="post" action="{{ route('channels') }}">
                        <input id="search" name="search" type="text" class="form-control" aria-label="Text input with dropdown button" autocomplete="off">
                        <div class="input-group-append mr-4 results">
                            <button id="results" class="btn-custom btn-custom-secondary dropdown-toggle" name="results" type="button"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                5 Results
                            </button>
                            <div class="dropdown-menu text-center">
                                <a class="dropdown-item result-count" value="5" href="#">5</a>
                                <a class="dropdown-item result-count" value="10" href="#">10</a>
                                <a class="dropdown-item result-count" value="20" href="#">20</a>
                                <a class="dropdown-item result-count" value="30" href="#">30</a>
                            </div>
                        </div>
                        <input id="maxResults" name="maxResults" value="5" type="hidden">
                        <button id="searchChannel" class="btn-custom btn-custom-secondary" name="formName" type="submit" value="SearchForm">Search</button>
                        <input name="_token" value="{{ csrf_token() }}" type="hidden">
                    </form>
                </div>
                <p class="lead mb-3"><a id="popupSearchId" class="link" href="#">Add channel by ID <span class="plus">+</span></a></p>

                @include('partials.popups.add_channel')
                @include('partials.popups.channel_settings')
                @include('partials.popups.video_info')
                @include('partials.popups.video_settings')

                <div class="container-fluid">
                    <div id="search-result" class="row mt-2 p-3">
                        @if(!is_null($searchData))
                            @for($i = 0; $i < count($searchData->items); $i++)
                                <div class="text-center search-element p-3">
                                    <form method="post" action="{{ route('channels') }}">
                                        <img class="mb-2 channel-img" src="{{ $searchData->items[$i]->snippet->thumbnails->default->url }}" />
                                        <p class="mb-2">{{ str_limit($searchData->items[$i]->snippet->title, $limit = 15, $end = ' ...') }}</p>
                                        <button class="btn-custom btn-custom-secondary search-channel" type="submit" name="formName" value="AddChannelForm">
                                            Add Channel
                                        </button>
                                        <input name="_token" value="{{ csrf_token() }}" type="hidden">
                                        <input name="addChannelID" value="{{ $searchData->items[$i]->id->channelId }}" type="hidden">
                                    </form>
                                </div>
                            @endfor
                        @endif
                    </div>
                </div>

            </div>
            <div id="display-channel" class="jumbotron">
                <div class="container-fluid">
                    @include('notification')
                    <div class="row py-3 text-center fav-header">
                        <div class="col">CHANNEL<span class="plural">S</span></div>
                        <div class="col">SUBSCRIBERS</div>
                        <div class="col">VIEWS</div>
                        <div class="col">REACHED</div>
                    </div>
                    <div id="list-channels" class="container mt-2 p-0">
                        @if(count($data) > 0)
                            @foreach($data as $channel)
                                <?php $tracking = $channel['tracking'] ?>
                                <div class="row channel text-center py-3">
                                    <div class="col hvr-wobble-horizontal channel-title">{{ str_limit($channel['name'], $limit = 37, $end = ' ...') }}</div>
                                    <div class="col">
                                        <p class="col-description">SUBS: </p>
                                        @switch($tracking)
                                            @case('daily')
                                                <span>{{ $channel['channel_data']['daily']['yesterday']['subs'] }}</span>
                                                <span class="text-success">/</span>
                                                <span>{{ $channel['channel_data']['daily']['today']['subs'] }}</span>
                                                @break
                                            @case('average')
                                                <span>{{ $channel['channel_data']['average']['monthly']['subs'] }}</span>
                                                <span class="text-success">/</span>
                                                <span>{{ $channel['channel_data']['average']['yearly']['subs'] }}</span>
                                                @break
                                            @default
                                                {{ $channel['channel_data']['total']['subs'] }}
                                        @endswitch
                                    </div>
                                    <div class="col">
                                        <p class="col-description">VIEWS: </p>
                                        @switch($tracking)
                                            @case('daily')
                                                <span>{{ $channel['channel_data']['daily']['yesterday']['views'] }}</span>
                                                <span class="text-success">/</span>
                                                <span>{{ $channel['channel_data']['daily']['today']['views'] }}</span>
                                                @break
                                            @case('average')
                                                <span>{{ $channel['channel_data']['average']['monthly']['views'] }}</span>
                                                <span class="text-success">/</span>
                                                <span>{{ $channel['channel_data']['average']['yearly']['views'] }}</span>
                                                @break
                                            @default
                                                {{ $channel['channel_data']['total']['views'] }}
                                        @endswitch
                                    </div>
                                    <div class="col">
                                        <p class="col-description">REACHED: </p>
                                        <span>0</span>
                                        /
                                        <span>0</span>
                                    </div>
                                </div>
                                <div class="row channel-data px-0">
                                    <div class="element-border my-2">
                                        <div class="element-group">
                                            <input class="video-id p-2 mr-3" name="video_id" type="text" autocomplete="off">
                                            <button class="btn-custom btn-custom-secondary add-video" name="add_video_popup" value="{{ $channel['id'] }}">Add <span>Video</span></button>
                                            <button class="btn-custom btn-custom-secondary edit-channel" name="edit-channel" value="{{ $channel['id'] }}" data-channel="{{ json_encode($channel) }}">Channel <span>Settings</span></button>
                                        </div>
                                    </div>
                                    @if(count($channel['channel_videos']) > 0)
                                    <div class="container-fluid element-border my-2 video-data">
                                        <div class="row video-header text-center p-3">
                                            <div class="col">VIDEO<span class="plural">S</span></div>
                                            <div class="col">
                                                @switch($tracking)
                                                    @case('daily')
                                                        VIEWS YESTERDAY
                                                        @break
                                                    @case('average')
                                                        VIEWS (avg last MONTH)
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
                                                        <span>{{ $channel['channel_calculation']['daily']['calculatedYesterday']['views'] }}</span>
                                                        <span class="text-success earning-border">{{ $channel['channel_calculation']['daily']['calculatedYesterday']['earning'] }}</span>
                                                        @break
                                                    @case('average')
                                                        <span>{{ $channel['channel_calculation']['average']['calculatedMonthViews']['views'] }}</span>
                                                        <span class="text-success earning-border">{{ $channel['channel_calculation']['average']['calculatedMonthViews']['earning'] }}</span>
                                                        @break
                                                    @default
                                                        <span>{{ $channel['channel_calculation']['total']['calculatedViews']['views'] }}</span>
                                                        <span class="text-success earning-border">{{ $channel['channel_calculation']['total']['calculatedViews']['earning'] }}</span>
                                                @endswitch
                                            </div>
                                            <div class="col">
                                                @switch($tracking)
                                                    @case('daily')
                                                        <span>{{ $channel['channel_calculation']['daily']['calculatedToday']['views'] }}</span>
                                                        <span class="text-success earning-border">{{ $channel['channel_calculation']['daily']['calculatedToday']['earning'] }}</span>
                                                        @break
                                                    @case('average')
                                                        <span>{{ $channel['channel_calculation']['average']['calculatedYearViews']['views'] }}</span>
                                                        <span class="text-success earning-border">{{ $channel['channel_calculation']['average']['calculatedYearViews']['earning'] }}</span>
                                                        @break
                                                    @default
                                                        <span>{{ $channel['channel_calculation']['total']['calculatedMonthlyViews']['monthlyViews'] }}</span>
                                                        <span class="text-success earning-border">{{ $channel['channel_calculation']['total']['calculatedMonthlyViews']['earning'] }}</span>
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
                                                                    VIEWS (avg last MONTH):
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
                                                        <span class="{{ ($video['video_data']['total']['calculatedViews']['views'] - $video['tracked_zero'] < $video['treshold']) ? 'text-danger' : 'text-success' }}">{{ $video['video_data']['total']['calculatedViews']['views'] - $video['tracked_zero'] }}</span>
                                                        <p class="slash">/</p>
                                                        <span>{{ $video['treshold'] }}</span>
                                                    </div>
                                                    <div class="col last-row">
                                                        <button class="btn-custom btn-custom-secondary info-hover {{ ($video['note'] !== null) ? 'text-custom-success' : '' }}" data-toggle="tooltip" data-history="{{ $video['history'] }}" data-placement="bottom" title="{{ $video['note'] }}">
                                                            Info
                                                        </button>
                                                        <button class="btn-custom btn-custom-secondary video-settings">Settings</button>
                                                    </div>
                                                </div>
                                            @endforeach
                                    @endif
                                    </div>
                                </div>
                            @endforeach
                        @else

                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
    <script src="{{ URL::asset('js/ui.js') }}"></script>
    <script src="{{ URL::asset('js/main.js') }}"></script>
  </body>
</html>
