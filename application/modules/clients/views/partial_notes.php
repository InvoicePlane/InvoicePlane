<?php foreach ($client_notes as $client_note) : ?>
    <div class="panel panel-default small">
        <div class="panel-body">
            <?php echo nl2br(htmlsc($client_note->client_note)); ?>
        </div>
        <div class="panel-footer text-muted">
            <?php echo date_from_mysql($client_note->client_note_date, true); ?>
        </div>
    </div>
<?php endforeach; ?>
