<div class="jumbotron pb-0">
    <h1 class="display-4">Youtube Statistics</h1>
    <p class="lead">Trace your channel data</p>
    <hr class="my-4">
    <div id="search-form-wrapper" class="mb-4">
        <form id="search-form" class="input-group" method="post" action="{{ route('forms') }}">
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

    <div class="container-fluid">
        <div id="search-result" class="row mt-2 p-3">
            @if(!is_null($searchData))
                @for($i = 0; $i < count($searchData->items); $i++)
                    <div class="text-center search-element p-3">
                        <form method="post" action="{{ route('forms') }}">
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
