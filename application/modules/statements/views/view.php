<?php
/*
 * @var Client client
 */
?>
<div id="headerbar">
    <h1 class="headerbar-title">
        <?php echo $statement_number; ?>
    </h1>

</div>

<div id="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <form id="statement_form" method="post" action="<?php echo site_url('statements/generate_pdf') ; // $statement_id); ?>" target="_blank">
        <input type="hidden" name="cid" value="<?php echo $client->client_id; ?>">
        <input type="hidden" name="sdate" value="<?php echo $statement_start_date; ?>">
        <input type="hidden" name="edate" value="<?php echo $statement_end_date; ?>">

        <?php _csrf_field(); ?>
    <div id="statement_form">
            <div class="statement">

                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-5">

                        <h3>
                            <a href="<?php echo site_url('clients/view/' . $client->client_id); ?>">
                                <?php _htmlsc(format_client($client)) ?>
                            </a>
                        </h3>
                        <br>
                        <div class="client-address">
                            <?php $this->layout->load_view('clients/partial_client_address', ['client' => $client]); ?>
                        </div>
                        <?php if ($client->client_phone || $client->client_email) : ?>
                            <hr>
                        <?php endif; ?>
                        <?php if ($client->client_phone): ?>
                            <div>
                                <?php _trans('phone'); ?>:&nbsp;
                                <?php _htmlsc($client->client_phone); ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($client->client_email): ?>
                            <div>
                                <?php _trans('email'); ?>:&nbsp;
                                <?php _auto_link($client->client_email); ?>
                            </div>
                        <?php endif; ?>

                </div>

                    <div class="col-xs-12 visible-xs">
                        <br>
                    </div>

                    <div class="col-xs-12 col-sm-6 col-md-7">
                        <div class="details-box">
                            <div class="row">

                                <div class="col-xs-12 col-md-6">

                                    <div class="statement-properties">
                                        <label for="statement_number">
                                        <?php _trans('statement_number'); ?>
                                    </label> <input type="text"
                                            id="statement_number" name="statement_number"
                                            class="form-control input-sm"
                                            value="<?php echo $statement_number; ?>"
                                            placeholder="statement number">
                                    </div>

                                    <div class="statement-properties">
                                        <label for="statement_password">
                                        <?php _trans('statement_password'); ?>
                                    </label> <input type="text"
                                            id="statement_password" name="statement_password"
                                            class="form-control input-sm" value="">
                                    </div>


                                    <br />
                                    <div class="statement-properties">
                                        <button class="btn btn-success" id="statement_download_pdf"
                                            type="submit"
                                            formtarget="_blank">
                                            <i class="fa fa-file-pdf"></i> <?php _trans('download_pdf'); ?>
                                    </button>
                                    </div>

                                </div>
                                <div class="col-xs-12 col-md-6">

                                    <div class="statement-properties has-feedback">
                                        <label for="statement_start_date">
                                        <?php _trans('statement_start_date'); ?>
                                    </label>
                                        <div class="input-group">
                                            <input name="statement_start_date" id="statement_start_date"
                                                class="form-control input-sm datepicker"
                                                value="<?php echo date(date_format_setting(),$statement_start_date); ?>" />
                                            <span class="input-group-addon"> <i
                                                class="fa fa-calendar fa-fw"></i>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="statement-properties has-feedback">
                                        <label for="statement_date_created">
                                        <?php _trans('statement_date'); ?>
                                    </label>
                                        <div class="input-group">
                                            <input name="statement_date_created"
                                                id="statement_date_created"
                                                class="form-control input-sm datepicker"
                                                value="<?php echo date(date_format_setting(),$statement_date); ?>" /> <span
                                                class="input-group-addon"> <i class="fa fa-calendar fa-fw"></i>
                                            </span>
                                        </div>
                                    </div>



                                    <br />
                                    <div class="statement-properties">
                                        <button class="btn btn-success" id="statement_tax_submit"
                                            type="submit"
                                            formaction="<?php echo site_url('statements/view/' . $client->client_id); ?>"
                                            formtarget="_self">
                                            <i class="fa fa-refresh"></i> <?php _trans('refresh'); ?>
                                    </button>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>

        <?php $this->layout->load_view('statements/partial_item_table'); ?>

        <hr />

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default no-margin">
                        <div class="panel-heading">
                        <?php _trans('notes'); ?>
                    </div>
                        <div class="panel-body">
                            <textarea name="notes" id="notes" rows="3"
                                class="input-sm form-control"></textarea>
                        </div>
                    </div>

                    <div class="col-xs-12 visible-xs visible-sm">
                        <br>
                    </div>

                </div>
            </div>

        </div>
    </form>
</div>
