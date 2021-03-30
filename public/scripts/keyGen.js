const ALPHANUMERIC_CAPITALS = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
let keysBox = document.querySelector('#keys_box');
let formBox = document.querySelector('#form_box');

if (keysBox.childElementCount > 0) {
    formBox.innerHTML = '';
}

function generateKey(id) {
    let input = parseInt(document.querySelector('#participants_number').value);
    let errorMsg = document.querySelector('#validation_msg');
    if (errorMsg !== null) {
        formBox.removeChild(errorMsg)
    }

    if (input === undefined || input < 1 || input > 50 || !Number.isInteger(input)) {
        let errorMsgElem = document.createElement('p');
        errorMsgElem.id = 'validation_msg';
        errorMsgElem.style.color = 'red';
        errorMsgElem.textContent = 'Vul a.u.b. een nummer tussen 1 en 50 in.';
        formBox.appendChild(errorMsgElem);
    }
    else {
        keysBox.innerHTML = '';
        let keys = [];
        for (i = 0; i < input; i++) {
            let key = '';
            for(j = 0; j < 4; j++) {
                key += ALPHANUMERIC_CAPITALS[Math.floor(Math.random() * 36)];
            }
            let item = document.createElement('p');
            item.id='key-'+(i+1).toString();
            item.textContent = key;
            keysBox.appendChild(item);
            keys[i] = key;
        }
        if (hasDuplicates(keys)) {
            generateKey(id);
            return;
        }
        submitKeys(keys, id);
    }
}

async function submitKeys(keys, id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    await $.ajax({
        url: '/storeKeys',
        type: 'POST',
        data: { keys: keys, id: id },
        success:function() {
            formBox.innerHTML = '';
        },
        error: function () {
            console.log('An unknown error occurred.');
        },
    });
}

function hasDuplicates(array) {
    var valuesSoFar = Object.create(null);
    for (var i = 0; i < array.length; i++) {
        var value = array[i];
        if (value in valuesSoFar) {
            return true;
        }
        valuesSoFar[value] = true;
    }
    return false;
}
