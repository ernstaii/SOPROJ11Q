async function setSpecialRole(user_id) {
    let checked = document.querySelector('#thief_fake_agent_checkbox_' + user_id).checked;

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    await $.ajax({
        url: '/users/' + user_id + '/special-role',
        type: 'PATCH',
        data: { is_special_role: checked },
        success: function () {
        },
        error: function (err) {
            console.log(err);
        },
    });
}
