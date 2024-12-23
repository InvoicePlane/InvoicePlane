<?php foreach ($client_notes as $client_note) : ?>
    <div class="panel panel-default small">
        <div class="panel-body">
            <?php echo nl2br(htmlsc($client_note->client_note)); ?>
        </div>
        <div class="panel-footer text-muted">
            <?php echo date_from_mysql($client_note->client_note_date, true); ?>
            <span data-id="<?php echo $client_note->client_note_id; ?>" class="delete_client_note pull-right btn btn-xs btn-danger">
                <i class="fa fa-trash-o"></i> <?php _trans('delete'); ?>
            </span>
        </div>
    </div>
<?php endforeach; ?>
