<?php foreach ($client_notes as $client_note) : ?>
    <div class="alert alert-default">
        <p><strong><?php echo date_from_mysql($client_note->client_note_date, true); ?></strong>&nbsp;
            <?php echo nl2br($client_note->client_note); ?>
        </p>
    </div>
<?php endforeach; ?>