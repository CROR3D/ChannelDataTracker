<div id="video_settings_popup" class="popup-background">
    <div id="video-settings-form-wrapper">
        <div class="text-right pr-3 my-2">
            <a class="close-btn">Close</a>
        </div>
        <h3 id="popup-title" class="text-center my-2"></h3>
        <div class="p-3">
            <form id="videoSettingsForm" class="form mb-4 p-0" name="videoSettingsForm" method="post" action="{{ route('channels') }}">
                <div class="form-group text-left">
                    <label>Change title</label>
                    <input id="videoSettingsTitle" class="form-control app-form-input" name="videoSettingsTitle" type="text" value="">
                    <label id="videoTitleError" class="validation-error text-danger">Title is required!</label>
                    <label>Earning factor per 1000 views</label>
                    <div class="row">
                        <div class="col-md-8">
                            <input id="videoSettingsEarningFactor" class="form-control app-form-input" name="videoSettingsEarningFactor" type="text" value="">
                        </div>
                        <div class="col-md-4">
                            <select id="videoSettingsFactorCurrency" class="form-control" name="videoSettingsFactorCurrency">
                                <option>HRK</option>
                                <option>USD</option>
                                <option>EUR</option>
                            </select>
                        </div>
                    </div>
                    <label id="earningFactorError" class="validation-error text-danger">Earning factor is not valid!</label>
                    <label>Get notified when video exceeds view limit</label>
                    <input id="videoSettingsTreshold" class="form-control app-form-input" name="videoSettingsTreshold" type="text" value="">
                    <label id="videoTresholdError" class="validation-error text-danger">Treshold is not defined properly!</label>
                    <label>Message related to this video</label>
                    <input id="videoSettingsNote" class="form-control mb-5" name="videoSettingsNote" type="text" value="">
                    <div class="row">
                        <div class="col">
                            <button id="videoSettingsAddOrUpdate" class="btn-custom btn-custom-secondary btn-block" name="formName" type="submit" value=""></button>
                        </div>
                        <div class="col">
                            <button id="videoSettingsDelete" class="btn-custom btn-custom-danger btn-block" name="formName" type="submit" value="DeleteVideoForm">Delete Video</button>
                        </div>
                    </div>
                    <input id="videoSettingsChannelId" name="videoSettingsChannelId" value="" type="hidden">
                </div>
                <input name="_token" value="{{ csrf_token() }}" type="hidden">
                <input id="videoSettingsVideoId" name="videoSettingsVideoId" value="" type="hidden">
            </form>
        </div>
    </div>
</div>
