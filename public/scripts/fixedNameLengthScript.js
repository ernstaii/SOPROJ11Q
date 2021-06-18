let gameNameText = document.querySelector('#game_name_text');
let name_parts = gameNameText.textContent.match(/.{1,24}/g);
let separated_game_name = '';
if (name_parts.length > 1) {
    name_parts.forEach(part => {
        separated_game_name += part + '\r\n';
    });
}
else {
    separated_game_name = name_parts[0];
}
gameNameText.textContent = separated_game_name;
