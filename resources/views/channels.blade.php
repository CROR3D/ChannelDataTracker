@extends('layout')

@section('content')
    @include('partials.header', ['connectionStatus' => $connectionStatus])
    @include('partials.search', ['searchData' => $searchData])

    @include('partials.popups.add_channel')
    @include('partials.popups.channel_settings')
    @include('partials.popups.video_info')
    @include('partials.popups.video_settings')

    <div id="display-channel" class="jumbotron">
        <div class="container-fluid">
            @include('notification')
            <div class="row py-3 text-center fav-header">
                <div class="col">CHANNEL<span class="plural">S</span></div>
                <div class="col">SUBSCRIBERS</div>
                <div class="col">VIEWS</div>
                <div class="col">REACHED</div>
            </div>
            <div id="list-channels" class="mt-2 p-0">
                @if($connectionStatus == 'ACTIVE')
                    @if(count($data) > 0)
                        @foreach($data as $channel)
                            <?php
                                $tracking = $channel['tracking'];
                                $mode = $channel['mode'];
                            ?>
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
                            <div class="channel-data px-0">
                                <div class="element-border my-2">
                                    <div class="element-group">
                                        <input class="video-id p-2 mr-3" name="video_id" type="text" autocomplete="off">
                                        <button class="btn-custom btn-custom-secondary add-video" name="add_video_popup" value="{{ $channel['id'] }}">Add <span>Video</span></button>
                                        <button class="btn-custom btn-custom-secondary edit-channel" name="edit-channel" value="{{ $channel['id'] }}" data-channel="{{ json_encode($channel) }}">Channel <span>Settings</span></button>
                                    </div>
                                </div>

                                @include('partials.videos', [
                                    'tracking' => $tracking,
                                    'channel' => $channel,
                                ])

                            </div>
                        @endforeach
                    @else
                        <p class="error-status text-center mt-4">No channels currently tracked</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ URL::asset('js/ui.js') }}"></script>
<script src="{{ URL::asset('js/main.js') }}"></script>
@endpush
