const gameInput = document.querySelector('#get_game_input');
const deleteGameForm = document.querySelector('#delete_game_form');
const buttonsBox = document.querySelector('#buttons_box');

async function changeNumberInputs(games) {
    let game_exists = false;
    let openGameButton = document.querySelector('#open_game_button');
    let deleteGameButton = document.querySelector('#delete_game_button');

    if (openGameButton && deleteGameButton) {
        buttonsBox.removeChild(openGameButton);
        deleteGameForm.removeChild(deleteGameButton);
    }

    if (gameInput.value && gameInput.value > 0) {
        games.forEach(game =>{
            if (game.id == gameInput.value)
                game_exists = true;
        });

        if (!game_exists) {
            return;
        }

        let openButtonElem = document.createElement('a');
        openButtonElem.id = 'open_game_button';
        openButtonElem.href = '/games/' + gameInput.value;
        let openButtonTextElem = document.createElement('h3');
        openButtonTextElem.textContent = 'Ga naar spel ' + gameInput.value;
        openButtonElem.appendChild(openButtonTextElem);

        let deleteButtonElem = document.createElement('button');
        deleteButtonElem.class = 'config-delete-button';
        deleteButtonElem.id = 'delete_game_button';
        deleteButtonElem.type = 'submit';
        let deleteButtonTextElem = document.createElement('b');
        deleteButtonTextElem.textContent = 'Verwijder spel ' + gameInput.value;
        deleteButtonElem.appendChild(deleteButtonTextElem);

        buttonsBox.insertBefore(openButtonElem, deleteGameForm);
        deleteGameForm.appendChild(deleteButtonElem);
        deleteGameForm.action = '/games/' + gameInput.value;
    }
}
