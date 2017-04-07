<nav class="navbar navbar-inverse" role="navigation">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#ip-navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <?php echo trans('menu') ?> &nbsp; <i class="fa fa-bars"></i>
            </button>
        </div>

        <div class="collapse navbar-collapse" id="ip-navbar-collapse">
            <ul class="nav navbar-nav">
                <li><?php echo anchor('dashboard', trans('dashboard'), 'class="hidden-md"') ?>
                    <?php echo anchor('dashboard', '<i class="fa fa-dashboard"></i>', 'class="visible-md-inline-block"') ?>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('clients'); ?></span>
                        <i class="visible-md-inline fa fa-users"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('clients/form', trans('add_client')); ?></li>
                        <li><?php echo anchor('clients/index', trans('view_clients')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('quotes'); ?></span>
                        <i class="visible-md-inline fa fa-file"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="create-quote"><?php _trans('create_quote'); ?></a></li>
                        <li><?php echo anchor('quotes/index', trans('view_quotes')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('invoices'); ?></span>
                        <i class="visible-md-inline fa fa-file-text"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="#" class="create-invoice"><?php _trans('create_invoice'); ?></a></li>
                        <li><?php echo anchor('invoices/index', trans('view_invoices')); ?></li>
                        <li><?php echo anchor('invoices/recurring/index', trans('view_recurring_invoices')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('payments'); ?></span>
                        <i class="visible-md-inline fa fa-credit-card"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('payments/form', trans('enter_payment')); ?></li>
                        <li><?php echo anchor('payments/index', trans('view_payments')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('products'); ?></span>
                        <i class="visible-md-inline fa fa-database"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('products/form', trans('create_product')); ?></li>
                        <li><?php echo anchor('products/index', trans('view_products')); ?></li>
                        <li><?php echo anchor('families/index', trans('product_families')); ?></li>
                        <li><?php echo anchor('units/index', trans('product_units')); ?></li>
                    </ul>
                </li>

                <li class="dropdown <?php echo get_setting('projects_enabled') == 1 ?: 'hidden'; ?>">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('tasks'); ?></span>
                        <i class="visible-md-inline fa fa-check-square-o"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('tasks/form', trans('create_task')); ?></li>
                        <li><?php echo anchor('tasks/index', trans('show_tasks')); ?></li>
                        <li><?php echo anchor('projects/index', trans('projects')); ?></li>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-caret-down"></i> &nbsp;
                        <span class="hidden-md"><?php _trans('reports'); ?></span>
                        <i class="visible-md-inline fa fa-bar-chart"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('reports/invoice_aging', trans('invoice_aging')); ?></li>
                        <li><?php echo anchor('reports/payment_history', trans('payment_history')); ?></li>
                        <li><?php echo anchor('reports/sales_by_client', trans('sales_by_client')); ?></li>
                        <li><?php echo anchor('reports/sales_by_year', trans('sales_by_date')); ?></li>
                    </ul>
                </li>

            </ul>

            <?php if (isset($filter_display) and $filter_display == true) { ?>
                <?php $this->layout->load_view('filter/jquery_filter'); ?>
                <form class="navbar-form navbar-left" role="search" onsubmit="return false;">
                    <div class="form-group">
                        <input id="filter" type="text" class="search-query form-control input-sm"
                               placeholder="<?php echo $filter_placeholder; ?>">
                    </div>
                </form>
            <?php } ?>

            <ul class="nav navbar-nav navbar-right">
                <li>
                    <a href="http://docs.invoiceplane.com/" target="_blank"
                       class="tip icon" title="<?php _trans('documentation'); ?>"
                       data-placement="bottom">
                        <i class="fa fa-question-circle"></i>
                        <span class="visible-xs">&nbsp;<?php _trans('documentation'); ?></span>
                    </a>
                </li>

                <li class="dropdown">
                    <a href="#" class="tip icon dropdown-toggle" data-toggle="dropdown"
                       title="<?php _trans('settings'); ?>"
                       data-placement="bottom">
                        <i class="fa fa-cogs"></i>
                        <span class="visible-xs">&nbsp;<?php _trans('settings'); ?></span>
                    </a>
                    <ul class="dropdown-menu">
                        <li><?php echo anchor('custom_fields/index', trans('custom_fields')); ?></li>
                        <li><?php echo anchor('email_templates/index', trans('email_templates')); ?></li>
                        <li><?php echo anchor('invoice_groups/index', trans('invoice_groups')); ?></li>
                        <li><?php echo anchor('invoices/archive', trans('invoice_archive')); ?></li>
                        <!-- // temporarily disabled
                        <li><?php echo anchor('item_lookups/index', trans('item_lookups')); ?></li>
                        -->
                        <li><?php echo anchor('payment_methods/index', trans('payment_methods')); ?></li>
                        <li><?php echo anchor('tax_rates/index', trans('tax_rates')); ?></li>
                        <li><?php echo anchor('users/index', trans('user_accounts')); ?></li>
                        <li class="divider hidden-xs hidden-sm"></li>
                        <li><?php echo anchor('settings', trans('system_settings')); ?></li>
                        <li><?php echo anchor('import', trans('import_data')); ?></li>
                    </ul>
                </li>
                <li>
                    <a href="<?php echo site_url('users/form/' .
                        $this->session->userdata('user_id')); ?>"
                       class="tip icon" data-placement="bottom"
                       title="<?php
                       print($this->session->userdata('user_name'));
                       if ($this->session->userdata('user_company')) {
                           print(" (" . $this->session->userdata('user_company') . ")");
                       }
                       ?>">
                        <i class="fa fa-user"></i>
                        <span class="visible-xs">&nbsp;<?php
                            print($this->session->userdata('user_name'));
                            if ($this->session->userdata('user_company')) {
                                print(" (" . $this->session->userdata('user_company') . ")");
                            }
                            ?></span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo site_url('sessions/logout'); ?>"
                       class="tip icon logout" data-placement="bottom"
                       title="<?php _trans('logout'); ?>">
                        <i class="fa fa-power-off"></i>
                        <span class="visible-xs">&nbsp;<?php _trans('logout'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
