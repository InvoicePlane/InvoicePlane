<?php
/*
 * @var Client client
 */

// $cv = $this->controller->view_data["custom_values"];

// print_r($quote);
?>
<script>

// 	function submitForm(){
// 		document.form1.target = "myActionWin";
// 		window.open("","myActionWin");
// 		document.form1.submit();
// 	}

    $(function () {

        $('#btn_generate_pdf').click(function () {

            $('#statement_form').submit();

        	// submitForm();

           // window.open('<?php echo site_url('statement/generate_pdf/' . '') ; // $quote_id); ?>', '_blank');
        });

        $(document).ready(function () {


        });

//         var fixHelper = function (e, tr) {
//             var $originals = tr.children();
//             var $helper = tr.clone();
//             $helper.children().each(function (index) {
//                 $(this).width($originals.eq(index).width());
//             });
//             return $helper;
//         };
//
//         $('#item_table').sortable({
//             helper: fixHelper,
//             items: 'tbody',
//         });
    });
</script>

<div id="headerbar">
    <h1 class="headerbar-title">
        <?php echo $statement_number; ?>
    </h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="#">
                <?php _trans('options'); ?> <i class="fa fa-chevron-down"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-right">

                <li>
                    <a href="#" id="btn_generate_pdf"
                       data-quote-id="<?php echo $client->client_id; ?>">
                        <i class="fa fa-print fa-margin"></i>
                        <?php _trans('download_pdf'); ?>
                    </a>
                </li>

            </ul>
        </div>

    </div>

</div>

<div id="content">
    <?php echo $this->layout->load_view('layout/alerts'); ?>
    <form id="statement_form" method="post" action="<?php echo site_url('statements/generate_pdf') ; // $quote_id); ?>" target="_blank">
    <input type="hidden" name="cid" value="<?php echo $client->client_id; ?>">
    <input type="hidden" name="sdate" value="<?php echo $statement_start_date; ?>">
    <input type="hidden" name="edate" value="<?php echo $statement_end_date; ?>">

    <?php _csrf_field(); ?>
    <div id="quote_form">
        <div class="quote">

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

                <div class="col-xs-12 visible-xs"><br></div>

                <div class="col-xs-12 col-sm-6 col-md-7">
                    <div class="details-box">
                        <div class="row">

                            <div class="col-xs-12 col-md-6">

                                <div class="quote-properties">
                                    <label for="quote_number">
                                        <?php _trans('statement_number'); ?>
                                    </label>
                                    <input type="text" id="statement_number" name="statement_number" class="form-control input-sm"
                                        value ="<?php echo $statement_number; ?>"
                                        placeholder="statement number" >
                                </div>

                                <div class="quote-properties">
                                    <label for="quote_password">
                                        <?php _trans('quote_password'); ?>
                                    </label>
                                    <input type="text" id="quote_password" name="quote_password" class="form-control input-sm"
                                           value="">
                                </div>


                                <!-- Custom fields -->
                                <?php foreach ($custom_fields as $custom_field): ?>
                                    <?php if ($custom_field->custom_field_location != 1) {
                                        continue;
                                    } ?>
                                    <?php print_field($this->mdl_quotes, $custom_field, $cv); ?>
                                <?php endforeach; ?>

                            </div>
                            <div class="col-xs-12 col-md-6">

                                <div class="quote-properties has-feedback">
                                    <label for="statement_start_date">
                                        <?php _trans('statement_start_date'); ?>
                                    </label>
                                    <div class="input-group">
                                        <input name="statement_start_date" id="statement_start_date"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($statement_start_date); ?>"/>
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="quote-properties has-feedback">
                                    <label for="statement_date_created">
                                        <?php _trans('statement_date'); ?>
                                    </label>
                                    <div class="input-group">
                                        <input name="statement_date_created" id="statement_date_created"
                                               class="form-control input-sm datepicker"
                                               value="<?php echo date_from_mysql($statement_date); ?>"/>
                                        <span class="input-group-addon">
                                            <i class="fa fa-calendar fa-fw"></i>
                                        </span>
                                    </div>
                                </div>



								<br/>
                                <div class="quote-properties">
                        			<button class="btn btn-success" id="quote_tax_submit" type="submit"
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

        <hr/>

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

                <div class="col-xs-12 visible-xs visible-sm"><br></div>

            </div>
        </div>

    </div>
    </form>
</div>
