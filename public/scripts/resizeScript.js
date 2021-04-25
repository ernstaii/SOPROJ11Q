const itemBox = document.querySelector("#keys_item_box");
const keysBox = document.querySelector("#keys_box");
const mouse_hover_event = document.querySelector(".mouse-hover-event");
let m_pos;

function resize(e){
    const dy = m_pos - e.y;
    m_pos = e.y;
    itemBox.style.height = (parseInt(getComputedStyle(itemBox, '').height) - dy) + "px";
    keysBox.style.height = (parseInt(getComputedStyle(itemBox, '').height) - dy) + "px";
}

mouse_hover_event.addEventListener("mousedown", function(e){
    if ("buttons" in e) {
        if (e.buttons === 1) {
            if (e.pageY >= mouse_hover_event.getBoundingClientRect().top && e.pageY <= mouse_hover_event.getBoundingClientRect().bottom) {
                m_pos = e.y;
                document.addEventListener("mousemove", resize, false);
            }
        }
    }
}, false);

document.addEventListener("mouseup", function(){
    document.removeEventListener("mousemove", resize, false);
}, false);
