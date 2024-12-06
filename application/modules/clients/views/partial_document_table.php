<!-- modules/clients/views/partial_document_table.php -->
<?php //$this->load->helper('custom_values_helper'); ?>
<div class="table-responsive">
    <table class="table table-hover table-striped">
        <thead>
        <tr>
<th>ID</th>
            <th><?php _trans('filename'); ?></th>
            <th><?php _trans('description'); ?></th>
            <th><?php _trans('creation_date'); ?></th>
<th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($documents as $d) : ?>
            <tr>
<td><?php echo $d->document_id; ?></td>
<td>
<a href="/uploads/documents/<?php echo ($d->document_filename); ?>">
<?php echo $d->document_filename; ?>
</a>
</td>
<td><?php echo $d->document_description; ?></td>
<td>
<?php
	$date = new DateTime($d->document_created);
	echo $date->format('d.m.Y');
?>
</td>

<td>
 <a href="<?php echo site_url('clients/document_del/' . $client->client_id.'/'.$d->document_id); ?>" class="btn btn-default client-delete-document">
<?php _trans('delete'); ?>
</a>

</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
