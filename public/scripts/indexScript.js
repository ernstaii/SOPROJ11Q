const gameInput = document.querySelector('#get_game_input');
const buttonsBox = document.querySelector('#buttons_box');
const passwordFieldGet = document.querySelector('#password_get_input');
const sideBarItem2 = document.querySelector('#side_bar_link2');
const sideBarItem3 = document.querySelector('#side_bar_link3');

let gameData = [];
let password_correct;
let game_exists;
let old_value;
let is_occupied = false;

function setGameData(data) {
    gameData = JSON.parse(data);
}

setInterval(() => {
    changeNumberInputs(gameData);
}, 300);

async function changeNumberInputs(gameData) {
    if (is_occupied === false) {
        let openGameButton = document.querySelector('#open_game_button');

        if (!game_exists || !password_correct || old_value !== gameInput.value) {
            if (openGameButton) {
                buttonsBox.removeChild(openGameButton);
                sideBarItem2.href = '/games';
                sideBarItem3.href = '/games';
                openGameButton = document.querySelector('#open_game_button');
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
                return;
            }

            is_occupied = true;
            await checkPassword(game_id);

            if (password_correct === false) {
                return;
            }

            if (!openGameButton) {
                let openButtonElem = document.createElement('a');
                openButtonElem.id = 'open_game_button';
                openButtonElem.href = '/games/' + game_name;
                let openButtonTextElem = document.createElement('h3');
                openButtonTextElem.textContent = 'Ga naar spel: ' + game_name;
                openButtonElem.appendChild(openButtonTextElem);

                buttonsBox.appendChild(openButtonElem);

                old_value = game_name;
            }

            sideBarItem2.href = '/games/' + game_name;
            sideBarItem3.href = '/games/' + game_name;
        }
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
