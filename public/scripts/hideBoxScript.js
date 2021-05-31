function hideBox(actor, parent_actor) {
    let actorElement = document.querySelector('#' + actor);
    let actorParentElement;

    if (parent_actor === 'config')
        actorParentElement = document.querySelector('.config-main-screen');
    else if (parent_actor === 'game')
        actorParentElement = document.querySelector('.game-main-screen');
    else
        actorParentElement = document.querySelector('.main-screen');

    if (actorElement.style.display === 'none') {
        actorElement.style.display = 'flex';
        actorParentElement.removeChild(document.querySelector('#' + actor + '_show_div'));
    }
    else {
        actorElement.style.display = 'none';
        let showDiv = document.createElement('div');
        showDiv.className = 'show-div shadow';
        showDiv.id = actor + '_show_div';

        let divHeader = document.createElement('h2');
        divHeader.textContent = actorElement.children[0].children[0].textContent;

        let showButton = document.createElement('div');
        showButton.className = 'close-tab-button';
        showButton.onclick = function() {
            hideBox(actor, parent_actor);
        };

        showDiv.appendChild(divHeader);
        showDiv.appendChild(showButton);
        actorParentElement.insertBefore(showDiv, actorElement);
    }
}
