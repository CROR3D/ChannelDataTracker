<div id="add_channel_popup" class="popup-background">
    <div id="channel-form-wrapper">
        <div class="text-right pr-3 my-2">
            <a class="close-btn">Close</a>
        </div>
        <div class="p-3">
            <form id="channelAddByIdForm" class="form-inline mb-4 p-0" method="post" action="{{ route('forms') }}">
                <div class="form-group">
                    <input id="id" class="form-control mr-3" name="addChannelID" type="text" placeholder="Type ID">
                    <button id="addChannel" class="btn-custom btn-custom-secondary btn-block text-center my-2" name="formName" type="submit" value="AddChannelForm">Add Channel</button>
                </div>
                <input name="_token" value="{{ csrf_token() }}" type="hidden">
            </form>
        </div>
    </div>
</div>
