<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('version_history'); ?></h1>

    <div class="headerbar-item pull-right">
        <?php echo pager(site_url('settings/versions/index'), 'mdl_versions'); ?>
    </div>
</div>

<div id="content" class="table-content">

    <div class="table-responsive">
        <table class="table">

            <thead>
            <tr>
                <th><?php _trans('date_applied'); ?></th>
                <th><?php _trans('sql_file'); ?></th>
                <th><?php _trans('errors'); ?></th>
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

