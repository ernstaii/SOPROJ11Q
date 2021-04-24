const itemBox = document.querySelector("#keys_item_box");
const keysBox = document.querySelector("#keys_box");
const buttonBox = document.querySelector("#keys_button_box");
let m_pos;

function resize(e){
    const dy = m_pos - e.y;
    m_pos = e.y;
    itemBox.style.height = (parseInt(getComputedStyle(itemBox, '').height) - dy) + "px";
    keysBox.style.height = (parseInt(getComputedStyle(itemBox, '').height) - dy) + "px";
}

itemBox.addEventListener("mousedown", function(e){
    if (e.offsetY <= buttonBox.getBoundingClientRect().top) {
        m_pos = e.y;
        document.addEventListener("mousemove", resize, false);
    }
}, false);

document.addEventListener("mouseup", function(){
    document.removeEventListener("mousemove", resize, false);
}, false);
