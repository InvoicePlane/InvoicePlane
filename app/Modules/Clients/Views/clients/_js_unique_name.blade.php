<script type="text/javascript">
  $(function () {

    clientId = {{ isset($client) ? $client->id : 0 }};
    stopHideUniqueName = false;

    if ($('#unique_name').val() == '' || clientId == 0) {
      hasUniqueName = false;
    }
    else {
      hasUniqueName = true;
    }

    function clientNameIsDuplicate (name, callback) {
      $.post('{{ route('clients.ajax.checkDuplicateName') }}', {
        client_name: name,
        client_id: clientId
      }, callback);
    }

    function checkClientNameIsDuplicate (name) {
      clientNameIsDuplicate(name, function (response) {
        if (response.is_duplicate == 1) {
          showUniqueName();
        }
        else {
          hideUniqueName();
        }
      });
    }

    function showUniqueName () {
      $('#col-client-name').removeClass('col-md-4').addClass('col-md-3');
      $('#col-client-email').removeClass('col-md-4').addClass('col-md-3');
      $('#col-client-active').removeClass('col-md-4').addClass('col-md-3');
      $('#col-client-unique-name').show();
      stopHideUniqueName = true;
    }

    function hideUniqueName () {
      if (stopHideUniqueName == false) {
        $('#col-client-name').removeClass('col-md-3').addClass('col-md-4');
        $('#col-client-email').removeClass('col-md-3').addClass('col-md-4');
        $('#col-client-active').removeClass('col-md-3').addClass('col-md-4');
        $('#col-client-unique-name').hide();
      }
    }

    $('#name').keyup(function () {
      if (hasUniqueName == false) {
        $('#unique_name').val($('#name').val());
      }
    });

    $('#unique_name').blur(function () {
      if ($('#unique_name').val() == '') {
        $('#unique_name').val($('#name').val());
      }
    });

    $('#btn-show-unique-name').click(function () {
      showUniqueName();
      $('#btn-show-unique-name').hide();
      stopHideUniqueName = true;
    });

      @if (config('fi.displayClientUniqueName'))
      showUniqueName();
      @else
      checkClientNameIsDuplicate($('#name').val());
    $('#name').blur(function () {
      checkClientNameIsDuplicate($('#name').val());
    });
      @endif

      if ($('#name').val() !== $('#unique_name').val()) {
        showUniqueName();
      }
  });
</script>