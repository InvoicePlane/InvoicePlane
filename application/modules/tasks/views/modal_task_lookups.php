<script>
    $(function () {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        var selectedTasks = [];
        $('.item-task-id').each(function () {
            var currentVal = $(this).val();
            if (currentVal.length) {
                selectedTasks.push(parseInt(currentVal));
            }
        });

        var hiddenTasks = 0;
        $('.modal-task-id').each(function () {
            var currentId = parseInt($(this).attr('id').replace('task-id-', ''));
            if (selectedTasks.indexOf(currentId) !== -1) {
//                $('#task-id-' + currentId).prop('disabled', true);
                $('#task-id-' + currentId).parent().parent().hide();
                hiddenTasks++;
            }
        });

        if (hiddenTasks >= $('.task-row').length) {
            $('#task-modal-submit').hide();
        }

        // Creates the invoice
        $('.select-items-confirm').click(function () {
            var task_ids = [];

            $("input[name='task_ids[]']:checked").each(function () {
                task_ids.push(parseInt($(this).val()));
            });

            $.post("<?php echo site_url('tasks/ajax/process_task_selections'); ?>", {
                task_ids: task_ids
            }, function (data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                var items = JSON.parse(data);

                for (var key in items) {
                    // Set default tax rate id if empty
                    if (!items[key].tax_rate_id) items[key].tax_rate_id = 0;

                    if ($('#item_table tbody:last input[name=item_name]').val() !== '') {
                        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
                    }
                    $('#item_table tbody:last input[name=item_task_id]').val(items[key].task_id);
                    $('#item_table tbody:last input[name=item_name]').val(items[key].task_name);
                    $('#item_table tbody:last textarea[name=item_description]').val(items[key].task_description);
                    $('#item_table tbody:last input[name=item_price]').val(items[key].task_price);
                    $('#item_table tbody:last input[name=item_quantity]').val('1');
                    $('#item_table tbody:last select[name=item_tax_rate_id]').val(items[key].tax_rate_id);

                    $('#modal-choose-items').modal('hide');
                    $('#invoice_change_client').hide();
                }
            });
        });

        // Toggle checkbox when click on row
        $('#tasks_table tr').click(function (event) {
            if (event.target.type !== 'checkbox') {
                $(':checkbox', this).trigger('click');
            }
        });

    });
</script>

<div id="modal-choose-items" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal-choose-items" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('add_task'); ?></h4>
        </div>

        <div class="modal-body">
            <?php $this->layout->load_view('tasks/partial_task_table_modal'); ?>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button id="task-modal-submit" class="select-items-confirm btn btn-success" type="button">
                    <i class="fa fa-check"></i>
                    <?php echo lang('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?php echo lang('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>