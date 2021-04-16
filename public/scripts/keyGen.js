let police_keys_box = document.querySelector('#police_keys_box');
let thieves_keys_box = document.querySelector('#thieves_keys_box');
let formBox = document.querySelector('#form_box');
let code_input = document.querySelector('#code_input');
let code_button = document.querySelector('#code_button');

if (police_keys_box.childElementCount > 0 || thieves_keys_box.childElementCount > 0) {
    formBox.children[0].children[1].removeChild(code_input);
    formBox.children[0].children[1].removeChild(code_button);
    formBox.children[0].children[1].removeChild(document.querySelector('#ratio_slider'));
}

function generateKey(id) {
    let ratio = parseInt(document.querySelector('#ratio_range').value);
    let input = parseInt(document.querySelector('#participants_number').value);
    let errorMsg = document.querySelector('#validation_msg');
    if (errorMsg !== null) {
        formBox.removeChild(errorMsg)
    }

    if (!input || input < 1 || input > 50) {
        let errorMsgElem = document.createElement('p');
        errorMsgElem.id = 'validation_msg';
        errorMsgElem.style.color = 'red';
        errorMsgElem.textContent = 'Vul a.u.b. een nummer tussen 1 en 50 in.';
        formBox.appendChild(errorMsgElem);
        let errorMessage = document.querySelector('#validation_msg');
        setInterval(function() {
            if (errorMessage !== null) {
                formBox.removeChild(errorMessage);
            }
        }, 7500);
    } else {
        getKeys(input, ratio, id);
    }
}

async function getKeys(input, ratio, id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    await $.ajax({
        url: '/storeKeys',
        type: 'POST',
        data: {input: input, ratio: ratio, id: id},
        success: function (data) {
            formBox.children[0].children[1].removeChild(code_input);
            formBox.children[0].children[1].removeChild(code_button);
            formBox.children[0].children[1].removeChild(document.querySelector('#ratio_slider'));
            let ratio_div = (ratio / 100);
            if (data !== null) {
                police_keys_box.innerHTML = '';
                thieves_keys_box.innerHTML = '';
                for (let i = 0; i < data.length; i++) {
                    let div = document.createElement('div');
                    div.className = "key-item";
                    let item = document.createElement('p');
                    item.id = 'somekey';
                    item.textContent = data[i];
                    div.appendChild(item);
                    if (i < Math.round((data.length * ratio_div))) {
                        police_keys_box.appendChild(div);
                    } else {
                        thieves_keys_box.appendChild(div);
                    }
                }
            }
        },
        error: function () {
            console.log('An unknown error occurred.');
        },
    });
}

function performCopyAction(actor) {
    if (actor === 'agent' && police_keys_box.childElementCount < 1) {
        alert('Er zijn geen politie toegangscodes om te kopiëren!');
        return;
    } else if (actor === 'thief' && thieves_keys_box.childElementCount < 1) {
        alert('Er zijn geen boeven toegangscodes om te kopiëren!');
        return;
    }

    let text = '';
    if (actor === 'agent') {
        for (let i = 0; i < police_keys_box.childElementCount; i++) {
            text += police_keys_box.children[i].children[0].textContent;
            if ((i + 1) < police_keys_box.childElementCount) {
                text += '\r\n';
            }
        }
    } else if (actor === 'thief') {
        for (let i = 0; i < thieves_keys_box.childElementCount; i++) {
            text += thieves_keys_box.children[i].children[0].textContent;
            if ((i + 1) < thieves_keys_box.childElementCount) {
                text += '\r\n';
            }
        }
    }
    copyTextToClipboard(text);
}

function copyTextToClipboard(text) {
    if (!navigator.clipboard) {
        fallbackCopyTextToClipboard(text);
        return;
    }
    navigator.clipboard.writeText(text);
}

function printKeys() {
    if (police_keys_box.childElementCount < 1 && thieves_keys_box.childElementCount < 1) {
        alert('Er zijn geen toegangscodes om te printen!');
        return;
    }
    let myWindow = window.open('', 'PRINT', 'height=400,width=600');

    myWindow.document.write('<html lang="en"><head><title>' + document.title + '</title>');
    myWindow.document.write('</head><body >');
    myWindow.document.write('<h1>Toegangscodes</h1>');
    myWindow.document.write('<div style="display: flex; flex-direction: row;"><h2>Politie</h2><h2 style="margin-left: 38%">Boeven</h2></div>');
    myWindow.document.write('<div style="display: flex; flex-direction: row; flex-wrap: wrap; max-height: 850px">');
    myWindow.document.write('<div style="display: flex; flex-direction: column; border-right-style: dashed; border-width: 1px; flex-wrap: wrap; width: 45%; max-height: 850px">' + police_keys_box.innerHTML + '</div>');
    myWindow.document.write('<div style="display: flex; flex-direction: column; margin-left: 3%; flex-wrap: wrap; width: 45%; max-height: 850px">' + thieves_keys_box.innerHTML + '</div>');
    myWindow.document.write('</div>');
    myWindow.document.write('</body></html>');

    myWindow.document.close();
    myWindow.focus();

    myWindow.print();

    return true;
}

function fallbackCopyTextToClipboard(text) {
    let textArea = document.createElement("textarea");
    textArea.value = text;

    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";

    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        let successful = document.execCommand('copy');
    } catch (err) {
        console.error('Fallback: Oops, unable to copy', err);
    }

    document.body.removeChild(textArea);
}

setInterval(function() {
    let error_box_total = document.querySelector('#error-box');
    if (error_box_total !== null && error_box_total.children[0].childNodes.length > 0) {
        document.body.removeChild(error_box_total);
    }
}, 7500);
