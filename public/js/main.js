function clearElement(element) {
    while (element.firstChild) {
        element.removeChild(element.firstChild);
    }
}

function showError(target) {
    let errorText;
    const input = document.getElementById('search');
    const error = document.getElementById('search-error-msg');

    switch(target) {
        case 'id':
            errorText = document.createTextNode('Please enter a valid ID!');
            break;
        case 'exist':
            errorText = document.createTextNode('That channel is already tracked!');
            break;
        case 'not-exist':
            errorText = document.createTextNode('Channel can\'t be found!');
            break;
        case 'empty':
            errorText = document.createTextNode('Search bar is empty!');
            break;
    }

    error.appendChild(errorText);
    input.classList.add('is-invalid');
    setTimeout(function() {
        input.classList.remove('is-invalid');
        error.innerHTML = ``;
    }, 3000);
}
