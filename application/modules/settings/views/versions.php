<div id="headerbar">
    <h1><?php echo trans('version_history'); ?></h1>

    <div class="pull-right">
        <?php echo pager(site_url('settings/versions/index'), 'mdl_versions'); ?>
    </div>
</div>

<div id="content" class="table-content">

    <div class="table-responsive-">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php echo trans('date_applied'); ?></th>
                <th><?php echo trans('sql_file'); ?></th>
                <th><?php echo trans('errors'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($versions as $version) { ?>
                <tr>
                    <td><?php echo date_from_timestamp($version->version_date_applied); ?></td>
                    <td><?php echo $version->version_file; ?></td>
                    <td><?php echo $version->version_sql_errors; ?></td>
                </tr>
            <?php } ?>
            </tbody>

        </table>
    </div>

</div>

