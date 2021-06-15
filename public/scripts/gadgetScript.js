let user_ids = [];

async function manageGadget(actor, operator, user_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/users/'+ user_id +'/gadgets',
        type: 'POST',
        data: {
            gadget_name: document.querySelector('#' + actor).textContent,
            operator: operator
        },
        success: function (data) {
            if (data == 1) {
                let amount_element = document.querySelector('#amount_of_' + actor + 's_' + user_id);
                if (operator === 'add') {
                    amount_element.textContent = (parseInt(amount_element.textContent) + 1).toString();
                }
                else {
                    amount_element.textContent = (parseInt(amount_element.textContent) - 1).toString();
                }
            }

            gadgetsEdited = true;
        },
        error: function (err) {
            console.log(err);
        },
    });
}

async function addAllGadgets(game_id) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/games/' + game_id +'/gadgets',
        type: 'POST',
        data: {},
        success: function (data) {
            user_ids.forEach(user_id => {
                let amount_of_alarms = document.querySelector('#amount_of_alarms_' + user_id);
                let amount_of_drones = document.querySelector('#amount_of_drones_' + user_id);
                let amount_of_smokescreens = document.querySelector('#amount_of_smokescreens_' + user_id);
                if (amount_of_alarms)
                    amount_of_alarms.textContent = (parseInt(amount_of_alarms.textContent) + 1).toString();
                if (amount_of_drones)
                    amount_of_drones.textContent = (parseInt(amount_of_drones.textContent) + 1).toString();
                if (amount_of_smokescreens)
                    amount_of_smokescreens.textContent = (parseInt(amount_of_smokescreens.textContent) + 1).toString();

            });
        },
        error: function (err) {
            console.log(err);
        },
    });
}

function getAllUserIds(userIds) {
    user_ids = userIds;
}
