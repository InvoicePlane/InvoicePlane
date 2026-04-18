const ClientTitleEnum = new Array(
    'mr',
    'mrs',
    'doctor',
    'professor'
);

$('#client_title').change((event) => {
    const client_title_custom_element = $('#client_title_custom');

    if (ClientTitleEnum.indexOf(event.target.value) == -1) {
        client_title_custom_element.removeClass('hidden')
    } else {
        client_title_custom_element.addClass('hidden');
        client_title_custom_element.val('');
    }
});
