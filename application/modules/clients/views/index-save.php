<div id="headerbar">

    <h1 class="headerbar-title"><?php _trans('clients'); ?></h1>

    <div class="headerbar-item pull-right">
        <button type="button" class="btn btn-default btn-sm submenu-toggle hidden-lg"
                data-toggle="collapse" data-target="#ip-submenu-collapse">
            <i class="fa fa-bars"></i> <?php _trans('submenu'); ?>
        </button>
        <a class="btn btn-primary btn-sm" href="<?php echo site_url('clients/form'); ?>">
            <i class="fa fa-plus"></i> <?php _trans('new'); ?>
        </a>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        <?php echo pager(site_url('clients/status/' . $this->uri->segment(3)), 'mdl_clients'); ?>
    </div>

    <div class="headerbar-item pull-right visible-lg">
        <div class="btn-group btn-group-sm index-options">
            <a href="<?php echo site_url('clients/status/active'); ?>"
               class="btn <?php echo $this->uri->segment(3) == 'active' || !$this->uri->segment(3) ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('active'); ?>
            </a>
            <a href="<?php echo site_url('clients/status/inactive'); ?>"
               class="btn  <?php echo $this->uri->segment(3) == 'inactive' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('inactive'); ?>
            </a>
            <a href="<?php echo site_url('clients/status/all'); ?>"
               class="btn  <?php echo $this->uri->segment(3) == 'all' ? 'btn-primary' : 'btn-default' ?>">
                <?php _trans('all'); ?>
            </a>
        </div>
    </div>
</div>

    <div class="collapse clearfix" id="ip-submenu-collapse">

        <div class="submenu-row">
            <?php echo pager(site_url('clients/status/' . $this->uri->segment(3)), 'mdl_clients'); ?>
        </div>


<div id="submenu">
        <div class="submenu-row">
            <div class="btn-group btn-group-sm index-options">
                <a href="<?php echo site_url('clients/status/active'); ?>"
                   class="btn <?php echo $this->uri->segment(3) == 'active' || !$this->uri->segment(3) ? 'btn-primary' : 'btn-default' ?>">
                    <?php _trans('active'); ?>
                </a>
                <a href="<?php echo site_url('clients/status/inactive'); ?>"
                   class="btn  <?php echo $this->uri->segment(3) == 'inactive' ? 'btn-primary' : 'btn-default' ?>">
                    <?php _trans('inactive'); ?>
                </a>
                <a href="<?php echo site_url('clients/status/all'); ?>"
                   class="btn  <?php echo $this->uri->segment(3) == 'all' ? 'btn-primary' : 'btn-default' ?>">
                    <?php _trans('all'); ?>
                </a>
            </div>
        </div>

    </div>
</div>

<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>
    <div id="filter_results">
        <?php $this->layout->load_view('clients/partial_client_table'); ?>
    </div>

</div>
<!-- infinite scroll -->

<div id="loader" style="text-align: center; display: none;">
    <p>Scroll ...</p>
</div>


<script type="text/javascript">
$(document).ready(function () {
    let offset = 15; // Startpunkt - 15 already there
    const limit = 5; // Anzahl der Eintrage pro Anfrage
    let loading = false; // Ladezustand

    // Funktion zum Laden von Clients
    function loadClients() {
        if (loading) return;
        loading = true;
        $("#loader").show();

       $.getJSON("<?php echo site_url('clients/ajax/get_ajax'); ?>/"+offset, { }, function (data) {

            if (data.length > 0) {
                data.forEach(client => {
//console.log(client);
$("#content").append(`
<?php $this->layout->load_view('clients/partial_client_table_ajax'); ?>
`);
/*
                    $("#content").append(`
                        <div class="cl1">
                            ${client[0].client_active}
                            ${client[0].client_id}
                            ${client.htmlsc_name}
                            ${client[0].client_extended_customer_no}
                            ${client[0].client_extended_customer_no}
                            ${client[0].client_extended_contract}
                            ${client[0].client_extended_direct_debit}
                            ${client[0].client_extended_flags}
                            ${client[0].client_email}
                            ${client[0].client_phone}
                            ${client[0].client_invoice_balance}
                        </div>
                    `);
*/

                });
                offset += limit; // Offset erhöhen
            } else {
                // Keine weiteren Inhalte
                $(window).off("scroll");
            }
            loading = false;
            $("#loader").hide();
        });
    }

    // Lade neue Inhalte, wenn der Benutzer das Ende der Seite erreicht
    $(window).on("scroll", function () {
        if ($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
            loadClients();
        }
    });

    // initial load clients - not in this case
    //loadCustomers();
});
</script>

