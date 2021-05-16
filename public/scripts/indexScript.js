const gameInput = document.querySelector('#get_game_input');
const deleteGameForm = document.querySelector('#delete_game_form');
const buttonsBox = document.querySelector('#buttons_box');
const passwordFieldGet = document.querySelector('#password_get_input');
const sideBarItem2 = document.querySelector('#side_bar_link2');
const sideBarItem3 = document.querySelector('#side_bar_link3');

let password_correct;

async function changeNumberInputs(gameIds) {
    let game_exists = false;
    password_correct = false;
    let openGameButton = document.querySelector('#open_game_button');
    let deleteGameButton = document.querySelector('#delete_game_button');

    if (openGameButton && deleteGameButton) {
        buttonsBox.removeChild(openGameButton);
        deleteGameForm.removeChild(deleteGameButton);
        deleteGameForm.action = '';
        sideBarItem2.href = '/games';
        sideBarItem3.href = '/games';
    }

    if (gameInput.value && gameInput.value > 0) {
        gameIds.forEach(gameId =>{
            if (gameId == gameInput.value)
                game_exists = true;
        });

        if (!game_exists) {
            return;
        }

        await checkPassword(gameInput.value);

        if (!password_correct) {
            return;
        }

        let openButtonElem = document.createElement('a');
        openButtonElem.id = 'open_game_button';
        openButtonElem.href = '/games/' + gameInput.value + '?password=' + passwordFieldGet.value;
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
        deleteGameForm.action = '/games/' + gameInput.value + '?password=' + passwordFieldGet.value;

        sideBarItem2.href = '/games/' + gameInput.value + '?password=' + passwordFieldGet.value;
        sideBarItem3.href = '/games/' + gameInput.value + '?password=' + passwordFieldGet.value;
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
