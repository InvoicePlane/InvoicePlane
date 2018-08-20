<script>
    $(function () {

        var hasUniqueName;
        var clientId = '{{ isset($client) ? $client->id : 0 }}';

        if ($('#unique_name').val() === '' || clientId === '0') {
            hasUniqueName = false;
        } else {
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
                if (response.is_duplicate === 1) {
                    $('#unique_name').attr('required', true);
                } else {
                    $('#unique_name').attr('required', null);
                }
            });
        }

        $('#name').keyup(function () {
            if (hasUniqueName === false) {
                $('#unique_name').val($('#name').val());
            }
        });

        $('#unique_name').blur(function () {
            if ($('#unique_name').val() === '') {
                $('#unique_name').val($('#name').val());
            }
        });

        checkClientNameIsDuplicate($('#name').val());

        $('#name').blur(function () {
            checkClientNameIsDuplicate($('#name').val());
        });
    });
</script>
