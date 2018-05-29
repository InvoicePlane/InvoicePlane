<script type="text/javascript">

  $(function () {

    $('#modal-lookup-company-profile').modal();

    $('#btn-submit-change-company-profile').click(function () {

      $.post('{{ $updateCompanyProfileRoute }}', {
        company_profile_id: $('#change_company_profile_id').val(),
        id: {{ $id }}


      }).done(function () {
        $('#modal-lookup-company-profile').modal('hide');
        $('#col-from').load('{{ $refreshFromRoute }}', {
          id: {{ $id }}
        });
      });

    });
  });

</script>