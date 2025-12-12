<div id="headerbar">
    <h1 class="headerbar-title"><?php _trans('customer_statement'); ?></h1>
</div>

<div id="content">

    <div class="row">
        <div class="col-xs-12 col-md-6 col-md-offset-3">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php _trans('report_options'); ?></h3>
                </div>
                <div class="panel-body">

                    <form method="post" action="<?php echo site_url('reports/customer_statement'); ?>">

                        <input type="hidden" name="<?php echo $this->config->item('csrf_token_name'); ?>"
                               value="<?php echo $this->security->get_csrf_hash(); ?>">

                        <div class="form-group">
                            <label for="client_id"><?php _trans('client'); ?></label>
                            <select name="client_id" id="client_id" class="form-control simple-select">
                                <?php foreach ($clients as $client) { ?>
                                    <option value="<?php echo $client->client_id; ?>" 
                                        <?php if(isset($selected_client) && $selected_client == $client->client_id) echo 'selected'; ?>>
                                        <?php echo $client->client_name . ($client->client_surname ? ' ' . $client->client_surname : ''); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="from_date"><?php _trans('from_date'); ?></label>
                            <div class="input-group">
                                <input type="text" name="from_date" id="from_date" class="form-control datepicker"
                                       value="<?php echo date_from_mysql(date('Y-m-01')); ?>">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="to_date"><?php _trans('to_date'); ?></label>
                            <div class="input-group">
                                <input type="text" name="to_date" id="to_date" class="form-control datepicker"
                                       value="<?php echo date_from_mysql(date('Y-m-t')); ?>">
                                <span class="input-group-addon">
                                    <i class="fa fa-calendar fa-fw"></i>
                                </span>
                            </div>
                        </div>

                        <input type="submit" class="btn btn-success" name="btn_submit"
                               value="<?php _trans('run_report'); ?>">

                    </form>

                </div>
            </div>

        </div>
    </div>
</div>