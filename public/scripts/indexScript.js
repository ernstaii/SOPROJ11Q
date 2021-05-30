const gameInput = document.querySelector('#get_game_input');
const deleteGameForm = document.querySelector('#delete_game_form');
const buttonsBox = document.querySelector('#buttons_box');
const passwordFieldGet = document.querySelector('#password_get_input');
const sideBarItem2 = document.querySelector('#side_bar_link2');
const sideBarItem3 = document.querySelector('#side_bar_link3');

let gameData = [];
let password_correct;
let game_exists;
let old_value;
let callBack = 1;
let is_occupied = false;

function setGameData(data) {
    gameData = JSON.parse(data);
}

setInterval(() => {
    changeNumberInputs(gameData);
}, 300);

async function changeNumberInputs(gameData) {
    if (is_occupied === false) {
        let openGameButton = document.querySelectorAll('#open_game_button');
        let deleteGameButton = document.querySelectorAll('#delete_game_button');

        if (!game_exists || !password_correct || old_value !== gameInput.value) {
            if (openGameButton.length > 0 && deleteGameButton.length > 0) {
                buttonsBox.removeChild(openGameButton[0]);
                deleteGameForm.removeChild(deleteGameButton[0]);
                deleteGameForm.action = '';
                sideBarItem2.href = '/games';
                sideBarItem3.href = '/games';
                openGameButton = document.querySelectorAll('#open_game_button');
                deleteGameButton = document.querySelectorAll('#delete_game_button');
                console.log('[INITIAL] removed buttons on callback: ' + callBack);
            }
        }

        game_exists = false;

        if (gameInput.value) {
            game_id = -1;
            game_name = "";
            gameData.forEach(game => {
                if (game.name == gameInput.value) {
                    game_exists = true;
                    game_id = game.id;
                    game_name = game.name;
                }
            });

            if (!game_exists) {
                callBack++;
                return;
            }

            let ms = 0;
            setInterval(() => {
                ms++;
            }, 1);

            is_occupied = true;
            await checkPassword(game_id).then(() => {
                if (password_correct === true) {
                    console.log('AJAX callback finished in ' + ms + ' ms!');
                }
            });

            if (password_correct === false) {
                callBack++;
                return;
            }

            if (openGameButton.length < 1 && deleteGameButton.length < 1) {
                let openButtonElem = document.createElement('a');
                openButtonElem.id = 'open_game_button';
                openButtonElem.href = '/games/' + game_name;
                let openButtonTextElem = document.createElement('h3');
                openButtonTextElem.textContent = 'Ga naar spel: ' + game_name;
                openButtonElem.appendChild(openButtonTextElem);

                let deleteButtonElem = document.createElement('button');
                deleteButtonElem.class = 'config-delete-button';
                deleteButtonElem.id = 'delete_game_button';
                deleteButtonElem.type = 'submit';
                let deleteButtonTextElem = document.createElement('b');
                deleteButtonTextElem.textContent = 'Verwijder spel: ' + game_name;
                deleteButtonElem.appendChild(deleteButtonTextElem);

                buttonsBox.insertBefore(openButtonElem, deleteGameForm);
                deleteGameForm.appendChild(deleteButtonElem);
                deleteGameForm.action = '/games/' + game_id;

                old_value = game_name;
                console.log('[INITIAL] added buttons on callback: ' + callBack);
            }

            sideBarItem2.href = '/games/' + game_name;
            sideBarItem3.href = '/games/' + game_name;
        }

        if (openGameButton.length > 1)
            for (let i = openGameButton.length - 1; i > 0; i--) {
                buttonsBox.removeChild(openGameButton[i]);
                console.log('[CLEANUP] removed open game button on callback: ' + callBack);
            }

        if (deleteGameButton.length > 1)
            for (let i = deleteGameButton.length - 1; i > 0; i--) {
                deleteGameForm.removeChild(deleteGameButton[i]);
                console.log('[CLEANUP] removed delete game button on callback: ' + callBack);
            }

        setTimeout(() => {
            if (openGameButton.length > 1)
                for (let i = openGameButton.length - 1; i > 0; i--) {
                    buttonsBox.removeChild(openGameButton[i]);
                    console.log('[EXT CLEANUP] removed open game button on callback: ' + callBack);
                }

            if (deleteGameButton.length > 1)
                for (let i = deleteGameButton.length - 1; i > 0; i--) {
                    deleteGameForm.removeChild(deleteGameButton[i]);
                    console.log('[EXT CLEANUP] removed delete game button on callback: ' + callBack);
                }
        }, 500);
        callBack++;
    }
}

async function checkPassword(game_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/games/' + game_id + '/password',
        type: 'GET',
        data: { password: passwordFieldGet.value },
        success: function (data) {
            if (data == 1) {
                password_correct = true;
            }
            else {
                password_correct = false;
            }
            is_occupied = false;
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function showPassword(actor) {
    if (actor === 'create') {
        let passwordFieldCreate = document.querySelector('#password_create_input');
        passwordFieldCreate.type = switchType(passwordFieldCreate.type);
    }
    else {
        passwordFieldGet.type = switchType(passwordFieldGet.type);
    }
}

function switchType(fromType) {
    if (fromType === 'password') {
        return 'text';
    } else {
        return 'password';
    }
}
