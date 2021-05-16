setInterval(function() {
    let error_box_total = document.querySelector('#error-box');
    if (error_box_total !== null && error_box_total.children[0].childNodes.length > 0) {
        setTimeout(function () {
            if (error_box_total !== null) {
                document.body.removeChild(error_box_total);
            }
        }, 5000);
    }
}, 10000);
