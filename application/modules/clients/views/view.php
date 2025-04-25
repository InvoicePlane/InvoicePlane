<script>
    // e-invoice user switch anima
    const switch_fa_toggle = function (id){
        const f = $('#'+id);
        f.toggleClass('fa-toggle-on').toggleClass('fa-toggle-off');
    }

    $(function () {
        const client_id = <?php echo $client->client_id; ?>;
        function add_delete_client_notes_click_event(){
            $('.delete_client_note').click(delete_client_note);
        }
        function reload_client_notes(data){
            <?php echo IP_DEBUG ? 'console.log(data);' : ''; ?>
            var response = JSON.parse(data);
            if (response.success === 1) {
                // The validation was successful
                $('.has-error').removeClass('has-error');
                $('#client_note').val('');

                // Reload all notes
                $('#notes_list').load("<?php echo site_url('clients/ajax/load_client_notes'); ?>",
                    {
                        client_id: client_id
                    }, function (response) {
                        <?php echo IP_DEBUG ? 'console.log(response);' : ''; ?>

                        setTimeout(add_delete_client_notes_click_event, 161);
                    });
            } else {
                // The validation was not successful
                $('.has-error').removeClass('has-error');
                for (var key in response.validation_errors) {
                    $('#' + key).parent().addClass('has-error');
                }
            }
            close_loader();
        }
        function delete_client_note(event) {
            show_loader();
            $.post('<?php echo site_url('clients/ajax/delete_client_note'); ?>',
                {
                    client_note_id: $(this).attr('data-id')
                }, function (data) {
                    reload_client_notes(data);
                }
            );
        }
        $('#save_client_note').click(function () {
            show_loader();
            $.post('<?php echo site_url('clients/ajax/save_client_note'); ?>',
                {
                    client_id: client_id,
                    client_note: $('#client_note').val()
                }, function (data) {
                    reload_client_notes(data);
                }
            );
        });
        add_delete_client_notes_click_event();
    });
</script>

<?php
$locations = [];
foreach ($custom_fields as $custom_field) {
    if (array_key_exists($custom_field->custom_field_location, $locations)) {
        $locations[$custom_field->custom_field_location] += 1;
    } else {
        $locations[$custom_field->custom_field_location] = 1;
    }
}
?>

<div id="headerbar">
    <h1 class="headerbar-title"><?php _htmlsc(format_client($client)); ?></h1>

    <div class="headerbar-item pull-right">
        <div class="btn-group btn-group-sm">
            <a href="#" class="btn btn-default client-create-quote"
               data-client-id="<?php echo $client->client_id; ?>">
                <i class="fa fa-file"></i> <?php _trans('create_quote'); ?>
            </a>
            <a href="#" class="btn btn-default client-create-invoice"
               data-client-id="<?php echo $client->client_id; ?>">
                <i class="fa fa-file-text"></i> <?php _trans('create_invoice'); ?></a>
            <a href="<?php echo site_url('clients/form/' . $client->client_id); ?>"
               class="btn btn-default">
                <i class="fa fa-edit"></i> <?php _trans('edit'); ?>
            </a>
            <form action="<?php echo site_url('clients/delete/' . $client->client_id); ?>"
                  method="POST" class="btn-group btn-group-sm">
                <?php _csrf_field(); ?>
                <button type="submit" class="btn btn-danger"
                        onclick="return confirm('<?php _trans('delete_client_warning'); ?>');">
                    <i class="fa fa-trash-o"></i> <?php _trans('delete'); ?>
                </button>
            </form>
        </div>
    </div>

</div>

<ul id="submenu" class="nav nav-tabs nav-tabs-noborder">
    <li<?php echo $activeTab == 'detail' ? ' class="active"' : ''; ?>><a data-toggle="tab" href="#client-details"><?php _trans('details'); ?></a></li>
    <li<?php echo $activeTab == 'quotes' ? ' class="active"' : ''; ?>><a data-toggle="tab" href="#client-quotes"><?php _trans('quotes'); ?></a></li>
    <li<?php echo $activeTab == 'invoices' ? ' class="active"' : ''; ?>><a data-toggle="tab" href="#client-invoices"><?php _trans('invoices'); ?></a></li>
    <li<?php echo $activeTab == 'payments' ? ' class="active"' : ''; ?>><a data-toggle="tab" href="#client-payments"><?php _trans('payments'); ?></a></li>
</ul>

<div id="content" class="tabbable tabs-below no-padding">
    <div class="tab-content no-padding">

        <div id="client-details" class="tab-pane tab-rich-content<?php echo $activeTab == 'detail' ? ' active' : ''; ?>">

            <?php $this->layout->load_view('layout/alerts'); ?>

            <div class="row">
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-8">

                    <h3><?php _htmlsc(format_client($client)); ?></h3>
                    <p><?php $this->layout->load_view('clients/partial_client_address'); ?></p>

                </div>
                <div class="col-xs-12 col-sm-6 col-md-6 col-lg-4">

                    <table class="table table-bordered no-margin">
                        <tr>
                            <th><?php _trans('language'); ?></th>
                            <td class="td-amount"><?php echo ucfirst($client->client_language); ?></td>
                        </tr>
                        <tr>
                            <th><?php _trans('total_billed'); ?></th>
                            <td class="td-amount"><?php echo format_currency($client->client_invoice_total); ?></td>
                        </tr>
                        <tr>
                            <th><?php _trans('total_paid'); ?></th>
                            <th class="td-amount"><?php echo format_currency($client->client_invoice_paid); ?></th>
                        </tr>
                        <tr>
                            <th><?php _trans('total_balance'); ?></th>
                            <td class="td-amount"><?php echo format_currency($client->client_invoice_balance); ?></td>
                        </tr>
                    </table>

                </div>
            </div>

            <hr>
<?php
$colClass = 'col-xs-12 col-sm-6' . ($einvoicing ? ' col-lg-4' : '');
?>
            <div class="row">
                <div class="<?php echo $colClass; ?>">

                    <div class="panel panel-default no-margin">
                        <div class="panel-heading"><?php _trans('contact_information'); ?></div>
                        <div class="panel-body table-content">
                            <table class="table no-margin">
<?php if ($client->client_invoicing_contact) { ?>
                                <tr>
                                    <th><?php _trans('contact'); ?> (<?php _trans('invoicing'); ?>)</th>
                                    <td><?php _htmlsc($client->client_invoicing_contact); ?></td>
                                </tr>
<?php } ?>
<?php if ($client->client_email) { ?>
                                <tr>
                                    <th><?php _trans('email'); ?></th>
                                    <td><?php _auto_link($client->client_email, 'email'); ?></td>
                                </tr>
<?php } ?>
<?php if ($client->client_phone) { ?>
                                <tr>
                                    <th><?php _trans('phone'); ?></th>
                                    <td><?php _htmlsc($client->client_phone); ?></td>
                                </tr>
<?php } ?>
<?php if ($client->client_mobile) { ?>
                                <tr>
                                    <th><?php _trans('mobile'); ?></th>
                                    <td><?php _htmlsc($client->client_mobile); ?></td>
                                </tr>
<?php } ?>
<?php if ($client->client_fax) { ?>
                                <tr>
                                    <th><?php _trans('fax'); ?></th>
                                    <td><?php _htmlsc($client->client_fax); ?></td>
                                </tr>
<?php } ?>
<?php if ($client->client_web) { ?>
                                <tr>
                                    <th><?php _trans('web'); ?></th>
                                    <td><?php _auto_link($client->client_web, 'url', true); ?></td>
                                </tr>
<?php } ?>

<?php
foreach ($custom_fields as $custom_field) {
    if ($custom_field->custom_field_location == 2) {
        $column = $custom_field->custom_field_label;
        $value  = $this->mdl_client_custom->form_value('cf_' . $custom_field->custom_field_id);
?>
                                <tr>
                                    <th><?php _htmlsc($column); ?></th>
                                    <td><?php _htmlsc($value); ?></td>
                                </tr>
<?php
    }
}
?>
                            </table>
                        </div>
                    </div>

                </div>
                <div class="<?php echo $colClass; ?>">
                    <div class="panel panel-default no-margin">

                        <div class="panel-heading"><?php _trans('tax_information'); ?></div>
                        <div class="panel-body table-content">
                            <table class="table no-margin">
                                <tr>
                                    <th><?php _trans('company'); ?></th>
                                    <td><?php _htmlsc($client->client_company ? $client->client_company : ''); ?></td>
                                </tr>
<?php if ($client->client_vat_id) { ?>
                                <tr>
                                    <th><?php _trans('vat_id'); ?></th>
                                    <td><?php _htmlsc($client->client_vat_id); ?></td>
                                </tr>
<?php } ?>
<?php if ($client->client_tax_code) { ?>
                                <tr>
                                    <th><?php _trans('tax_code'); ?></th>
                                    <td><?php _htmlsc($client->client_tax_code); ?></td>
                                </tr>
<?php } ?>

<?php

$default_custom = false;
foreach ($custom_fields as $custom_field) {
    if (! $default_custom && ! $custom_field->custom_field_location) {
        $default_custom = true;
    }

    if ($custom_field->custom_field_location == 4) {
        $column = $custom_field->custom_field_label;
        $value  = $this->mdl_client_custom->form_value('cf_' . $custom_field->custom_field_id);
?>
                                <tr>
                                    <th><?php _htmlsc($column); ?></th>
                                    <td><?php _htmlsc($value); ?></td>
                                </tr>
<?php
    }
}
?>
                            </table>
                        </div>

                    </div>
                </div>

                <!-- eInvoicing panel -->
                <div class="<?php echo $einvoicing ? $colClass : ' hidden'; ?>">
                    <div class="panel panel-default no-margin">
                        <div class="panel-heading">
                            e-<?php _trans('invoicing'); ?>
<?php
// Panel eInvoicing checks
$title_tip = ' data-toggle="tooltip" data-placement="bottom" title="' . trans('edit'); // Tooltip helper ! Need add: . '"'

// For eInvoicing panel client (users)
$nb_users     = count($req_einvoicing->users);
$me           = $req_einvoicing->users[$_SESSION['user_id']]->show_table;
$nb           = $req_einvoicing->show_table;
$ln           = 'user' . (($nb ?: $nb_users) > 1 ? 's' : ''); // tweak 1 on more nb_users no ok
$user_toggle  = ($req_einvoicing->show_table ? ($me ? 'danger' : 'warning') : 'default') . ' ' . ($me ? '" aria-expanded="true' : '" collapsed" aria-expanded="false');
// For eInvoicing panel User(s) table
$class_checks     = ['fa fa-lg fa-check-square-o text-success', 'fa fa-lg fa-edit text-warning', 'fa fa-lg fa-square-o text-danger']; // Checkboxe icons
$base             = 'address_1 zip city country company vat_id';
$keys             = explode(' ', $base); // to array
$lang             = explode(' ', strtr($base, ['_1' => '']));
$user_fields_nook = ($req_einvoicing->clients[$client->client_id]->einvoicing_empty_fields > 0 && $client->client_einvoicing_version != '');
// eInvoicing button toggle users table checking
if ($client->client_einvoicing_active && ! $user_fields_nook) {
?>
                            <span class="pull-right cursor-pointer btn btn-xs btn-default alert-<?php echo $user_toggle; ?>"
                                  data-toggle="collapse" data-target=".einvoice-users-check"
                                  onclick="switch_fa_toggle('einvoice_users_check_fa_toggle')"
                            >
                                <i class="fa fa-<?php echo $nb ? ($me ? 'ban' : 'warning') : 'check-square-o text-success'; ?>"></i>
                                <span data-toggle="tooltip" data-placement="bottom" title="<?php echo '🗸 ' . ($nb_users - $nb) . '/' . $nb_users . ' ' . trans('user' . ($nb_users > 1 ? 's' : '')); ?>">
                                    <?php echo ($nb ?: $nb_users) . ' ' . trans($ln); ?>
                                </span>
                                <i id="einvoice_users_check_fa_toggle" class="fa fa-toggle-<?php echo $me ? 'on' : 'off'; ?> fa-margin"></i>
                            </span>
<?php
} // End if eInvoicing button toggle users table checking
?>
                        </div>
                        <div class="panel-body table-content">

                            <table class="table no-margin">
                                <tr>
                                    <th>e-<?php _htmlsc(trans('invoice') . ' ' . trans('version') . ' (' . trans('send')); ?>)</th>
                                    <td><?php echo ($client->client_einvoicing_active && $client->client_einvoicing_version) ? get_xml_full_name($client->client_einvoicing_version) : trans('none'); ?></td>
                                </tr>
                            </table>

<?php
// eInvoicing panel Client checks table
if ($client->client_einvoicing_active && $user_fields_nook) {
?>
                            <div class="alert alert-warning small" style="margin: 0px 10px 10px;">
                                <table>
                                    <tr>
                                        <td><i class="fa fa-exclamation-triangle fa-2x"></i>&emsp;</td>
                                        <td><?php _trans('einvoicing_no_creation_hint'); ?></td>
                                    </tr>
                                </table>
                            </div>

                            <table class="table no-margin" id="client_einvoice_checks">
                                <thead class="einvoice-client-checks-lists">
                                    <tr><th><?php _trans('required_fields'); ?> (<?php _trans('client'); ?>)</th></tr>
                                </thead>
                                <tbody class="einvoice-client-checks-lists">
                                    <tr><td>
<?php
    $reqs = []; // init ! important
    if ($req_einvoicing->clients[$client->client_id]->einvoicing_empty_fields) {
        foreach ($keys as $l => $key) {
            if ($req_einvoicing->clients[$client->client_id]->$key) {
                $reqs[] = '<i class="' . $class_checks[$req_einvoicing->clients[$client->client_id]->$key] . '"></i>'
                        . anchor(
                            '/clients/form/' . $client->client_id . '#client_' . $key,
                            trans($lang[$l]),
                            $title_tip  . ' #' . trans($lang[$l]) . ' (' . trim(trans('field')) . ')"'
                        ); // ! Need add: "
            }
        }
    }
    // Show fields in Errors
?>
                                        <span><?php echo implode(', ', $reqs); ?></span>

                                    </td></tr>
                                </tbody>
                            </table>
<?php
} else {
    // Client ok! Show check fields user(s)
?>
                            <table class="einvoice-users-check table no-margin collapse<?php
                                   echo $req_einvoicing->users[$_SESSION['user_id']]->einvoicing_empty_fields ? ' in" aria-expanded="true' : '" aria-expanded="false'; ?>"
                            >
                                <thead class="einvoice-users-check-lists">
                                    <tr><th colspan="3"><?php _trans('required_fields'); ?> (<?php _trans('user' . ($nb_users > 1 ? 's' : '')); ?>)</th></tr>
                                    <tr><th><?php _trans('user'); ?></th><th>e-<?php _trans('invoice'); ?></th><th><?php _trans('errors'); ?></th></tr>
                                </thead>
<?php
    // eInvoicing panel User(s) checks table
    foreach ($req_einvoicing->users as $uid => $user) {
        $ok = ! $user->einvoicing_empty_fields; // or ->show_table (inverse)
        $tx = $ok ? 'success' : ($_SESSION['user_id'] == $uid ? 'danger' : 'warning');
?>
                                <tbody class="einvoice-user-check-lists">
                                    <tr class="text-<?php echo $tx; ?>">
                                        <td class="te te-1">
                                            <i class="fa fa-fw fa-user"></i>
                                            <span><?php echo anchor('/users/form/' . $uid, $user->user_name); ?></span>
                                        </td>
                                        <td><i class="<?php echo $class_checks[ $ok ? 0 : 2 ]; ?>"></i><?php _trans($ok ? 'yes' : 'no'); ?></td>
                                        <td>
<?php
        $reqs = []; // Re init ! important
        if ($user->einvoicing_empty_fields) {
            $reqs = []; // reuse
            foreach ($keys as $l => $key) {
                if ($user->$key) {
                    $reqs[] = '<i class="' . $class_checks[$user->$key] . '"></i>'
                            . anchor(
                                '/users/form/' . $uid . '#user_' . $key,
                                trans($lang[$l]),
                                $title_tip  . ' #' . trans($lang[$l]) . ' (' . trim(trans('field')) . ' ' . htmlsc($user->user_name) . ')"'
                            ); // ! Need add: "
                }
            }
        }
        // Show Ok or Errors
        $reqs = $reqs === [] ? trans('no') : implode(', ', $reqs);
?>
                                            <span><?php echo $reqs; ?></span>
                                        </td>
                                    </tr>
                                </tbody>
<?php
    } // End foreach users
?>
                            </table>
<?php
} // End if client ok
?>

                        </div>
                    </div>
                </div>
                <!-- /eInvoicing panel -->

            </div>

<?php
if ($client->client_surname != '') { // Client is not a company
?>
            <hr>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default">
                        <div class="panel-heading"><?php _trans('personal_information'); ?></div>

                        <div class="panel-body table-content">
                            <table class="table no-margin">
                                <tr>
                                    <th><?php _trans('birthdate'); ?></th>
                                    <td><?php echo format_date($client->client_birthdate); ?></td>
                                </tr>
                                <tr>
                                    <th><?php _trans('gender'); ?></th>
                                    <td><?php echo format_gender($client->client_gender) ?></td>
                                </tr>
<?php
    if ($this->mdl_settings->setting('sumex') == '1') {
?>
                                <tr>
                                    <th><?php _trans('sumex_ssn'); ?></th>
                                    <td><?php echo format_avs($client->client_avs) ?></td>
                                </tr>

                                <tr>
                                    <th><?php _trans('sumex_insurednumber'); ?></th>
                                    <td><?php _htmlsc($client->client_insurednumber) ?></td>
                                </tr>

                                <tr>
                                    <th><?php _trans('sumex_veka'); ?></th>
                                    <td><?php _htmlsc($client->client_veka) ?></td>
                                </tr>
<?php
    } // fi sumex

    foreach ($custom_fields as $custom_field) {
        if ($custom_field->custom_field_location == 3) {
            $column = $custom_field->custom_field_label;
            $value  = $this->mdl_client_custom->form_value('cf_' . $custom_field->custom_field_id);
?>
                                <tr>
                                    <th><?php _htmlsc($column); ?></th>
                                    <td><?php _htmlsc($value); ?></td>
                                </tr>
<?php
        }
    }
?>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
<?php
} // fi client->client_surname

if ($default_custom) {
?>
            <hr>

            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <div class="panel panel-default no-margin">

                        <div class="panel-heading"><?php _trans('custom_fields'); ?></div>
                        <div class="panel-body table-content">
                            <table class="table no-margin">
<?php
    foreach ($custom_fields as $custom_field) {
        if (! $custom_field->custom_field_location) { // == 0
            $column = $custom_field->custom_field_label;
            $value  = $this->mdl_client_custom->form_value('cf_' . $custom_field->custom_field_id);
?>
                                <tr>
                                    <th><?php _htmlsc($column); ?></th>
                                    <td><?php _htmlsc($value); ?></td>
                                </tr>
<?php
        }
    }
?>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
<?php
} // fi custom_fields
?>

            <hr>

            <div class="row">
                <div class="col-xs-12 col-md-6">

                    <div class="panel panel-default no-margin">
                        <div class="panel-heading">
                            <?php _trans('notes'); ?>
                        </div>
                        <div class="panel-body">
                            <div id="notes_list">
                                <?php echo $partial_notes; ?>
                            </div>
                            <div class="input-group">
                                <textarea id="client_note" class="form-control" rows="2" style="resize:none"></textarea>
                                <span id="save_client_note" class="input-group-addon btn btn-default">
                                    <?php _trans('add_note'); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
<?php
foreach (explode(' ', 'quote invoice payment') as $what) {
    $table = $what . '_table'; // dynamic var name
?>
        <div id="client-<?php echo $what; ?>s" class="tab-pane table-content<?php echo $activeTab == $what . 's' ? ' active' : ''; ?>">
            <div class="container-fluid">
                <div class="pull-right" style="margin:.5rem 0 -1.5rem 0">
                    <?php echo pager(site_url('clients/view/' . $client->client_id . '/' . $what . 's'), 'mdl_' . $what . 's'); ?>
                </div>
            </div>
            <?php echo $$table; ?>
        </div>
<?php
}
?>
    </div>
</div>
