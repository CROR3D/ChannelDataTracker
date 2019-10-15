<div id="channel_settings_popup" class="popup-background">
    <div id="channel-settings-form-wrapper">
        <div class="text-right pr-3 my-2">
            <a class="close-btn">Close</a>
        </div>
        <h3 id="popup-channel-title" class="text-center my-2">Update Channel</h3>
        <div class="p-3">
            <form id="channelSettingsForm" class="form mb-4 p-0" method="post" action="{{ route('channels') }}">
                <div class="form-group text-left">
                    <label>Change title</label>
                    <input id="channelSettingsTitle" class="form-control mb-3" name="channelSettingsTitle" type="text">
                    <label>What kind of tracking do you prefer</label>
                    <div class="row mb-4">
                        <div class="col tracking-btn tracking-selected">
                            Total
                        </div>
                        <div class="col tracking-btn">
                            Daily
                        </div>
                        <div class="col tracking-btn">
                            Average
                        </div>
                    </div>
                    <label>Earning factor per 1000 views for all channel videos</label>
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <input id="channelSettingsEarningFactor" class="form-control mb-3" name="channelSettingsEarningFactor" type="text">
                        </div>
                        <div class="col-md-4">
                            <select id="channelSettingsFactorCurrency" class="form-control" name="channelSettingsFactorCurrency">
                                <option>HRK</option>
                                <option>USD</option>
                                <option>EUR</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <button id="channelSettingsUpdate" class="btn-custom btn-custom-secondary btn-block" name="channelSettingsUpdate" type="submit" value="">Update Channel</button>
                        </div>
                        <div class="col">
                            <button id="channelSettingsDelete" class="btn-custom btn-custom-danger btn-block" name="channelSettingsDelete" type="submit" value="">Delete Channel</button>
                        </div>
                    </div>
                    <input id="channelSettingsChannelId" name="channelSettingsChannelId" value="" type="hidden">
                </div>
                <input name="_token" value="{{ csrf_token() }}" type="hidden">
            </form>
        </div>
    </div>
</div>
