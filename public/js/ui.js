$(document).ready(function() {
    removeNotifications();
    popUpChannelForm();
    popUpChannelSettingsForm();
    popUpVideoHistory();
    popUpVideoForm();
    closePopUp();
    changeResultCount();
    dropdownChannel();
    caclulateReachedTresholds();
    enableTooltips();
    trackingButtons();
    resetButton();
    updateValidation();
    deleteSafetyCountdown();
});

function removeNotifications() {
    setTimeout(function(){
            $('.notification').remove();
    }, 5000);
}

function enableTooltips() {
    $('.video-link').tooltip();
    $('.info-hover').tooltip();
}

function clearChannels() {
    document.getElementById('list-channels').innerHTML = ``;
}

function updateValidation() {
    document.getElementById('channelSettingsUpdate').addEventListener('click', validateChannelForm);
    document.getElementById('videoSettingsAddOrUpdate').addEventListener('click', validateVideoForm);
}

function deleteSafetyCountdown() {
    document.getElementById('videoSettingsDelete').addEventListener('click', deleteCountdown);
    document.getElementById('channelSettingsDelete').addEventListener('click', deleteCountdown);
}

function popUpChannelForm() {
    let elements = document.querySelectorAll('.channel-title');

    $(document).on('click', '#popupSearchId', function() {
        [].forEach.call(elements, function(el) {
            el.classList.remove('hvr-wobble-horizontal');
        });
        $('#add_channel_popup').show();
        $('.container-wrapper').css('overflowY','hidden');
    });
}

function popUpChannelSettingsForm() {
    let elements = document.querySelectorAll('.channel-title');

    $(document).on('click', '.edit-channel', function(e) {
        let channelId = e.target.value;
        channelDataUpdate(e.target);
        $('#channelSettingsChannelId').attr('value', channelId);
        [].forEach.call(elements, function(el) {
            el.classList.remove('hvr-wobble-horizontal');
        });
        $('#channel_settings_popup').show();
        $('.container-wrapper').css('overflowY','hidden');
    });
}

function popUpVideoHistory() {
    let elements = document.querySelectorAll('.channel-title');

    $(document).on('click', '.info-hover', function(e) {
        let history = null;
        [].forEach.call(elements, function(el) {
            el.classList.remove('hvr-wobble-horizontal');
        });

        if(e.target.dataset.history) {
            history = JSON.parse(e.target.dataset.history);
        }

        displayHistory(history);
        $('#video_info_popup').show();
        $('.container-wrapper').css('overflowY','hidden');
    });
}

function popUpVideoForm() {
    const elements = document.querySelectorAll('.channel-title'),
          title = document.getElementById('popup-title'),
          formButton = document.getElementById('videoSettingsAddOrUpdate'),
          formButtonDelete = document.getElementById('videoSettingsDelete'),
          hiddenVideoId = document.getElementById('videoSettingsVideoId'),
          hiddenChannelId = document.getElementById('videoSettingsChannelId');

    $(document).on('click', '.add-video', function(e) {
        let videoSearch = e.target.previousElementSibling,
            channelId = e.target.value;

        if(videoSearch.value !== '') {
            $(hiddenVideoId).attr('value', videoSearch.value);
            $(formButton).attr('value', 'AddVideoForm');
            $('#videoSettingsDelete').hide();
            $(hiddenChannelId).attr('value', channelId);
            title.textContent = 'Add Video';
            formButton.textContent = 'Add Video';
            [].forEach.call(elements, function(el) {
                el.classList.remove('hvr-wobble-horizontal');
            });
            $('#video_settings_popup').show();
            $('.container-wrapper').css('overflowY','hidden');
        } else {
            $(videoSearch).attr('placeholder', 'Enter video ID');
        }
    });

    $(document).on('click', '.video-id', function(e) {
        $(e.target).removeAttr('placeholder');
    });

    $(document).on('click', '.video-settings', function(e) {
        let parent = e.target.parentElement.parentElement,
            videoData = JSON.parse(parent.dataset.video);

        $('#videoSettingsDelete').show();
        $(hiddenVideoId).attr('value', videoData.id);
        let videoRow = e.target.parentElement.parentElement;
        videoDataUpdate(videoRow);
        $(formButton).attr('value', 'UpdateVideoForm');
        title.textContent = 'Update Video';
        formButton.textContent = 'Update Video';
        [].forEach.call(elements, function(el) {
            el.classList.remove('hvr-wobble-horizontal');
        });
        $('#video_settings_popup').show();
        $('.container-wrapper').css('overflowY','hidden');
    });
}

function deleteCountdown(e) {
    if(e.target.classList.contains('safety-countdown')) {
        return true;
    } else {
        e.preventDefault();
        let innerText = $(e.target).text();
        $(e.target).prop('disabled', true);
        let timeleft = 4;
        window.downloadTimer = setInterval(function(){
            timeleft--;
            $(e.target).text(timeleft);
            if(timeleft < 0) {
                clearInterval(window.downloadTimer);
                $(e.target).text('Are you sure?');
                e.target.classList.add('safety-countdown');
                $(e.target).prop('disabled', false);
                setTimeout(function() {
                    e.target.classList.remove('safety-countdown');
                    $(e.target).text(innerText);
                }, 3000);
            }
        }, 1000);
        return false;
    }
}

function closePopUp() {
    let elements = document.querySelectorAll('.channel-title'),
        channelDelete = document.getElementById('channelSettingsDelete'),
        videoDelete = document.getElementById('videoSettingsDelete'),
        resetTresholds = document.getElementById('channelSettingsResetTresholds'),
        resetTresholdsHidden = document.getElementById('channelSettingsResetAllTresholds');

    $(document).on('click', '.close-btn', function(e) {
        const parent = e.target.parentNode.parentNode.parentNode;
        [].forEach.call(elements, function(el) {
            el.classList.add('hvr-wobble-horizontal');
        });
        $(parent).hide();
        resetTresholds.value = 'false';
        resetTresholds.style.color = '#212529';
        resetTresholdsHidden.value = 'false';
        $('.container-wrapper').css('overflowY','auto');
        clearVideoData();
        let isInCountdown = channelDelete.classList.contains('safety-countdown') || videoDelete.classList.contains('safety-countdown'),
            isDisabled = $(channelDelete).prop('disabled') || $(videoDelete).prop('disabled');
        if(isInCountdown || isDisabled) {
            clearInterval(window.downloadTimer);
            $(channelDelete).prop('disabled', false);
            channelDelete.classList.remove('safety-countdown');
            $(channelDelete).text('Delete Channel');
            $(videoDelete).prop('disabled', false);
            videoDelete.classList.remove('safety-countdown');
            $(videoDelete).text('Delete Video');
        }

        clearErrors('quick');
    });
}

function changeResultCount() {
    $('.result-count').click(function(e) {
        let count = e.target.getAttribute('value');
        $('#maxResults').attr('value', count);
        $('#results').text(count + ' Results');
    });
}

function dropdownChannel() {
    $(document).on('click', '.channel', function(e) {
        $(this).next('.channel-data').slideToggle('slow');
    });
}

function caclulateReachedTresholds() {
    let channels = document.getElementsByClassName('channel'),
        channelEditData = document.getElementsByClassName('edit-channel');

    for(let i = 0; i < channels.length; i++) {
        let reached = channels[i].lastElementChild.getElementsByTagName('span'),
            current = reached[0],
            total = reached[1],
            channelData = channels[i].nextElementSibling.lastElementChild,
            videoData = channelData.children,
            currentTreshold = 0,
            totalTreshold = 0,
            currentTresholdViews = 0,
            totalTresholdViews = 0,
            mode = null;

        for(let x = 0; x < videoData.length; x++) {
            if(videoData[x].classList.contains('video-row')) {
                let currentVideoData = JSON.parse(videoData[x].dataset.video);

                let videoChannelId = currentVideoData.channel_id;

                for(let y = 0; y < channelEditData.length; y++) {
                    if(mode) continue;

                    let channelParsedData = JSON.parse(channelEditData[y].dataset.channel);

                    if(videoChannelId == channelParsedData.id) mode = channelParsedData.mode;
                }

                if(mode != 'all_views') {
                    currentTresholdViews = parseFloat(videoData[x].children[3].getElementsByTagName('span')[0].innerText);
                    totalTresholdViews = parseFloat(videoData[x].children[3].getElementsByTagName('span')[1].innerText);
                } else {
                    currentTresholdViews = parseFloat(videoData[x].children[1].getElementsByTagName('span')[0].innerText);
                    totalTresholdViews = parseFloat(videoData[x].children[3].getElementsByTagName('span')[0].innerText);
                }

                totalTreshold++;

                if(currentTresholdViews >= totalTresholdViews) currentTreshold++;
            }
        }

        current.className = "";

        if(currentTreshold >= totalTreshold && currentTreshold != 0) {
            current.classList.add('text-success');
        } else if (currentTreshold < totalTreshold) {
            current.classList.add('text-danger');
        }

        current.innerText = currentTreshold;
        total.innerText = totalTreshold;
    }
}

function trackingButtons() {
    $(document).on('click', '.tracking-btn', function(e) {
        e.preventDefault();
        let trackingHidden = document.getElementById('channelSettingsTracking');
        $('.tracking-btn').removeClass("tracking-selected");
        e.target.classList.add('tracking-selected');
        trackingHidden.value = e.target.value;
    });
}

function resetButton() {
    $(document).on('click', '.reset-btn', function(e) {
        e.preventDefault();
        let resetTresholds = document.getElementById('channelSettingsResetTresholds'),
            resetTresholdsHidden = document.getElementById('channelSettingsResetAllTresholds');

        if(resetTresholds.value == 'false') {
            resetTresholds.value = 'true';
            resetTresholds.style.color = '#dc3545';
            resetTresholdsHidden.value = 'true';
        } else {
            resetTresholds.value = 'false';
            resetTresholds.style.color = '#212529';
            resetTresholdsHidden.value = 'false';
        }
    });
}

function channelDataUpdate(button) {
    let data = JSON.parse(button.dataset.channel),
        title = document.getElementById('channelSettingsTitle'),
        trackingButtons = document.getElementsByClassName('tracking-btn'),
        trackingMode = document.getElementById('channelSettingsTrackingMode'),
        trackingHidden = document.getElementById('channelSettingsTracking');

    $('.tracking-btn').removeClass("tracking-selected");
    title.value = data.name;

    for(var i = 0; i < trackingButtons.length; i++) {
        if((trackingButtons[i].innerText.charAt(0).toLowerCase() + trackingButtons[i].innerText.slice(1)) === data.tracking) {
            trackingButtons[i].classList.add('tracking-selected');
        }
    }

    for(let i = 0; i < trackingMode.length; i++) {
        if(trackingMode.options[i].value === data.mode) trackingMode.options[i].selected = true;
    }

    trackingHidden.value = data.tracking;
}

function videoDataUpdate(button) {
    let data = JSON.parse(button.dataset.video),
        title = document.getElementById('videoSettingsTitle'),
        earningFactor = document.getElementById('videoSettingsEarningFactor'),
        factorCurrency = document.getElementById('videoSettingsFactorCurrency'),
        treshold = document.getElementById('videoSettingsTreshold'),
        note = document.getElementById('videoSettingsNote'),
        hiddenId = document.getElementById('videoSettingsChannelId');

    title.value = data.name;
    earningFactor.value = data.earning_factor;

    for(let i = 0; i < factorCurrency.length; i++) {
        if(factorCurrency.options[i].text === data.factor_currency) factorCurrency.options[i].selected = true;
    }

    treshold.value = data.treshold;
    note.value = data.note;
    hiddenId.value = data.channel_id;
}

function clearVideoData() {
    let title = document.getElementById('videoSettingsTitle'),
        earningFactor = document.getElementById('videoSettingsEarningFactor'),
        treshold = document.getElementById('videoSettingsTreshold'),
        note = document.getElementById('videoSettingsNote'),
        hiddenId = document.getElementById('videoSettingsChannelId');

    title.value = '';
    earningFactor.value = '';
    treshold.value = '';
    note.value = '';
    hiddenId.value = '';
}

function validateChannelForm(e) {
    let channelTitle = document.forms['channelSettingsForm']['channelSettingsTitle'].value,
        isValid = true;

    if (channelTitle === "" || typeof channelTitle !== 'string') {
        let titleError = document.getElementById('channelTitleError');
        $(titleError).css({ 'opacity': 1 });
        $('#channelSettingsTitle').css({ "border": "1px solid #a94442" });
        clearErrors();
        isValid = false;
    }

    if(!isValid) e.preventDefault();

    return isValid;
}

function validateVideoForm(e) {
    let videoTitle = document.forms['videoSettingsForm']['videoSettingsTitle'].value,
        videoSettingsEarningFactor = parseFloat(document.forms['videoSettingsForm']['videoSettingsEarningFactor'].value),
        videoSettingsFactorCurrency = document.forms['videoSettingsForm']['videoSettingsFactorCurrency'].value,
        videoSettingsTreshold = parseInt(document.forms['videoSettingsForm']['videoSettingsTreshold'].value);

    let isValid = true,
        formType = $('#videoSettingsAddOrUpdate').attr('name');

    if(formType === 'videoSettingsUpdate') {
        if (videoTitle === "" || typeof videoTitle !== 'string') {
            let titleError = document.getElementById('videoTitleError');
            $(titleError).css({ 'opacity': 1 });
            $('#videoSettingsTitle').css({ "border": "1px solid #a94442" });
            clearErrors();
            isValid = false;
        }
    }

    if (isNaN(videoSettingsEarningFactor) || videoSettingsEarningFactor <= 0 || typeof videoSettingsEarningFactor !== 'number') {
        let earningFactorError = document.getElementById('earningFactorError');
        $(earningFactorError).css({ 'opacity': 1 });
        $('#videoSettingsEarningFactor').css({ "border": "1px solid #a94442" });
        clearErrors();
        isValid = false;
    }

    if (isNaN(videoSettingsTreshold) || videoSettingsTreshold < 0 || typeof videoSettingsTreshold !== 'number') {
        let videoTresholdError = document.getElementById('videoTresholdError');
        $(videoTresholdError).css({ 'opacity': 1 });
        $('#videoSettingsTreshold').css({ "border": "1px solid #a94442" });
        clearErrors();
        isValid = false;
    }

    if(!isValid) e.preventDefault();

    return isValid;
}

function clearErrors(clearMode) {
    if(clearMode === 'quick') {
        $('.validation-error').css({ 'opacity': 0 });
        $('.app-form-input').css({ "border": "1px solid #ced4da" });
    } else {
        setTimeout(function() {
            $('.validation-error').css({ 'opacity': 0 });
            $('.app-form-input').css({ "border": "1px solid #ced4da" });
        }, 5000);
    }
}

function displayHistory(history) {
    const videoHistory = document.getElementById('videoHistory'),
          videoTresholdsReached = document.getElementById('tresholdsReached'),
          videoTrackingZero = document.getElementById('trackingZero');

    let historyArray = Object.keys(history).map(function(m) {
        return history[m];
    });

    videoTrackingZero.innerHTML = `
        <h4><span class="text-success">Tracked since:</span> ${history.created_at}</h4>
    `;

    historyArray = historyArray.slice(2, -2);

    let isEmpty = historyArray.find(function(m) {
        return m != null;
    });

    if(isEmpty == null) {
        videoHistory.innerHTML = `No History For This Video`;
        return;
    }

    let today = new Date(),
        day = today.getDate(),
        month = today.getMonth(),
        months = ['january', 'february', 'march', 'april', 'may', 'june', 'july', 'august', 'september', 'october', 'november', 'december'],
        lastMonthIndex = (month === 0) ? 11 : month - 1,
        lastMonthName = months[lastMonthIndex];

    videoTresholdsReached.innerHTML = `
        <h4>Tresholds Reached: ${history.tresholds_reached}</h4>
    `;

    videoHistory.innerHTML = `
        <h4><span class="text-danger">${lastMonthName}: </span>
            <span>${history[lastMonthName].views} views</span>
            <span class="text-success">${history[lastMonthName].earned}$ earned</span>
        </h4>
    `;

    for(let i = lastMonthIndex - 1; i >= 0; i--) {
        let monthDataBefore = history[months[i]];
        if(monthDataBefore) {
            videoHistory.innerHTML += `
                <h4>${months[i]}: ${monthDataBefore}</h4>
            `;
        }
    }

    for(let x = months.length - 1; x > lastMonthIndex; x--) {
        let monthDataAfter = history[months[x]];
        if(monthDataAfter) {
            videoHistory.innerHTML += `
                <h4>${months[x]}: ${monthDataAfter}</h4>
            `;
        }
    }
}
