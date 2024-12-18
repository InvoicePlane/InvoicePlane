const ClientTitleEnum = {
  MISTER: 'mr',
  MISSUS: 'mrs',
  DOCTOR: 'doctor',
  PROFESSOR: 'professor',
  CUSTOM: 'custom'
}

$('#client_title').change((event) => {
  const client_title_custom_element = $('#client_title_custom');

  if(ClientTitleEnum.CUSTOM === event.target.value) {
    client_title_custom_element.removeClass('hidden')
  }

  if(ClientTitleEnum.CUSTOM !== event.target.value) {
    client_title_custom_element.addClass('hidden');
    client_title_custom_element.val('');
  }
});
