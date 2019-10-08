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
    calculateViewsAndEarnings();
    enableTooltips();
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

function popUpChannelForm() {
    let elements = document.querySelectorAll('.channel-title');

    $(document).on('click', '#popupSearchId', function() {
        [].forEach.call(elements, function(el) {
            el.classList.remove('hvr-wobble-horizontal');
        });
        $('#add_channel_popup').show();
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
    });
}

function popUpVideoForm() {
    const elements = document.querySelectorAll('.channel-title'),
          title = document.getElementById('popup-title'),
          formButton = document.getElementById('videoSettingsAddOrUpdate'),
          hiddenId = document.getElementById('videoSettingsChannelId');

    $(document).on('click', '.add-video', function(e) {
        let videoSearch = e.target.previousElementSibling,
            channelId = e.target.value;

        if(videoSearch.value !== '') {
            $(formButton).attr('value', videoSearch.value);
            $(formButton).attr('name', 'videoSettingsAdd');
            $('#videoSettingsDelete').hide();
            $(hiddenId).attr('value', channelId);
            title.textContent = 'Add Video';
            formButton.textContent = 'Add Video';
            [].forEach.call(elements, function(el) {
                el.classList.remove('hvr-wobble-horizontal');
            });
            $('#video_settings_popup').show();
        } else {
            $(videoSearch).attr('placeholder', 'Enter video ID');
        }
    });

    $(document).on('click', '.video-settings', function(e) {
        let parent = e.target.parentElement.parentElement,
            videoData = JSON.parse(parent.dataset.video);

        $('#videoSettingsDelete').show();
        let videoRow = e.target.parentElement.parentElement;
        videoDataUpdate(videoRow);
        $(formButton).attr('value', videoData.id);
        $(formButton).attr('name', 'videoSettingsUpdate');
        title.textContent = 'Update Video';
        formButton.textContent = 'Update Video';
        [].forEach.call(elements, function(el) {
            el.classList.remove('hvr-wobble-horizontal');
        });
        $('#video_settings_popup').show();
    });
}

function closePopUp() {
    let elements = document.querySelectorAll('.channel-title');
    $(document).on('click', '.close-btn', function(e) {
        const parent = e.target.parentNode.parentNode.parentNode;
        [].forEach.call(elements, function(el) {
            el.classList.add('hvr-wobble-horizontal');
        });
        $(parent).hide();
        clearVideoData();
        // ADD DATA AGAIN AFTER CLEARING
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
    $(document).on('click', '.channel', function() {
        $(this).next('.channel-data').slideToggle('slow');
    });
}

function caclulateReachedTresholds() {
    let channels = document.getElementsByClassName('channel');

    for(let i = 0; i < channels.length; i++) {
        let reached = channels[i].lastElementChild,
            current = reached.firstElementChild,
            total = reached.lastElementChild,
            channelData = channels[i].nextElementSibling.lastElementChild,
            videoData = channelData.children,
            currentTreshold = 0,
            totalTreshold = 0;

            for(let x = 0; x < videoData.length; x++) {
                if(videoData[x].classList.contains('video-row')) {
                    let currentTresholdViews = parseFloat(videoData[x].children[3].firstElementChild.innerText),
                        totalTresholdViews = parseFloat(videoData[x].children[3].lastElementChild.innerText);

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

function calculateViewsAndEarnings() {
    let channelsVideoData = document.getElementsByClassName('video-data');

    for(let i = 0; i < channelsVideoData.length; i++) {
        let video = channelsVideoData[i].children[1],
            totalTrackedViews = video.children[1].firstElementChild,
            totalTrackedViewsTreshold = video.children[1].lastElementChild,
            totalMonthlyViews = video.children[2].firstElementChild,
            totalMonthlyViewsTreshold = video.children[2].lastElementChild;

        let countViews = 0,
            countMonthlyViews = 0,
            countViewsTreshold = 0,
            countMonthlyViewsTreshold = 0,
            countRowsInChannel = channelsVideoData[i].children.length,
            x = 0
            currency = 'HRK';

        if(countRowsInChannel > 2) {
            x = 2;
        } else {
            x = 1;
        }

        for(x; x < countRowsInChannel; x++) {
            let videoData = channelsVideoData[i].children[x],
                trackedViews = videoData.children[1].firstElementChild,
                trackedViewsTreshold = videoData.children[1].lastElementChild,
                monthlyViews = videoData.children[2].firstElementChild,
                monthlyViewsTreshold = videoData.children[2].lastElementChild;

            videoData = JSON.parse(videoData.dataset.video);
            currency = videoData.factor_currency;
            currency = currencySign(currency);

            countViews += parseFloat(trackedViews.innerText);
            countMonthlyViews += parseFloat(monthlyViews.innerText);
            countViewsTreshold += parseFloat(trackedViewsTreshold.innerText);
            countMonthlyViewsTreshold += parseFloat(monthlyViewsTreshold.innerText);

            trackedViewsTreshold.innerText += currency;
            monthlyViewsTreshold.innerText += currency;
        }

        totalTrackedViews.innerText = countViews;
        totalMonthlyViews.innerText = countMonthlyViews;
        totalTrackedViewsTreshold.innerText = countViewsTreshold + currency;
        totalMonthlyViewsTreshold.innerText = countMonthlyViewsTreshold + currency;
    }
}

function channelDataUpdate(button) {
    let data = JSON.parse(button.dataset.channel);
}

function videoDataUpdate(button) {
    let data = JSON.parse(button.dataset.video),
        title = document.getElementById('videoSettingsTitle'),
        earningFactor = document.getElementById('videoSettingsEarningFactor'),
        factorCurrency = document.getElementById('videoSettingsFactorCurrency'),
        treshold = document.getElementById('videoSettingsTreshold'),
        note = document.getElementById('videoSettingsNote'),
        hiddenId = document.getElementById('videoSettingsChannelId');

    $(title).attr('value', data.name);
    $(earningFactor).attr('value', data.earning_factor);

    for(let i = 0; i < factorCurrency.length; i++) {
        if(factorCurrency.options[i].text === data.factor_currency) factorCurrency.options[i].selected = true;
    }

    $(treshold).attr('value', data.treshold);
    $(note).attr('value', data.note);
    $(hiddenId).attr('value', data.channel_id);
}

function clearVideoData() {
    let title = document.getElementById('videoSettingsTitle'),
        earningFactor = document.getElementById('videoSettingsEarningFactor'),
        treshold = document.getElementById('videoSettingsTreshold'),
        note = document.getElementById('videoSettingsNote'),
        hiddenId = document.getElementById('videoSettingsChannelId');

    $(title).attr('value', '');
    $(earningFactor).attr('value', '');
    $(treshold).attr('value', '');
    $(note).attr('value', '');
    $(hiddenId).attr('value', '');
}

function currencySign(currency) {
    switch (currency) {
        case 'USD':
            return '$';
        case 'EUR':
            return '€';
        default:
            return 'kn';
    }
}

function currencyConverter(current, to) {

}

function validateVideoForm() {
    let videoForm = document.getElementById('videoSettingsForm'),
        videoTitle = document.forms['videoSettingsForm']['videoSettingsTitle'].value,
        videoSettingsEarningFactor = document.forms['videoSettingsForm']['videoSettingsEarningFactor'].value,
        videoSettingsFactorCurrency = document.forms['videoSettingsForm']['videoSettingsFactorCurrency'].value,
        videoSettingsTreshold = document.forms['videoSettingsForm']['videoSettingsTreshold'].value;

    let isValid = true;

    if (videoTitle === "" || typeof videoTitle !== 'string') {
        let titleError = document.getElementById('videoTitleError');
        titleError.textContent = '- Name is required!';
        setTimeout(function(){
            titleError.textContent = '';
        }, 5000);
        isValid = false;
    }

    if (videoSettingsEarningFactor < 0 || typeof videoSettingsEarningFactor !== 'number') {
        let earningFactorError = document.getElementById('earningFactorError');
        earningFactorError.textContent = '- Earning factor is not valid!';
        setTimeout(function(){
            earningFactorError.textContent = '';
        }, 5000);
        isValid = false;
    }

    if (['HRK', 'USD', 'EUR'].includes(videoSettingsFactorCurrency)) {
        earningFactorError.textContent = '- Factor currency is not valid!';
        setTimeout(function(){
            earningFactorError.textContent = '';
        }, 5000);
        isValid = false;
    }

    if (videoSettingsTreshold < 0 || typeof videoSettingsTreshold !== 'number') {
        let videoTresholdError = document.getElementById('videoTresholdError');
        videoTresholdError.textContent = '- Treshold is not defined properly!';
        setTimeout(function(){
            videoTresholdError.textContent = '';
        }, 5000);
        isValid = false;
    }

    return isValid;
}

function displayHistory(history) {
    const videoHistory = document.getElementById('videoHistory'),
          videoTresholdsReached = document.getElementById('tresholdsReached');

    if(!history) {
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
        <h4>Tresholds Reached: ${history.tresholdsReached}</h4>
    `;

    videoHistory.innerHTML = `
        <h4 class="text-danger">History</h4>
        <h4>${lastMonthName}: ${history[lastMonthName]}</h4>
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
