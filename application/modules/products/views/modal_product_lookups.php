<script>
    $(function () {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

        $(".simple-select").select2();

        // Creates the invoice
        $('.select-items-confirm').click(function () {
            var product_ids = [];

            $("input[name='product_ids[]']:checked").each(function () {
                product_ids.push(parseInt($(this).val()));
            });

            $.post("<?php echo site_url('products/ajax/process_product_selections'); ?>", {
                product_ids: product_ids
            }, function (data) {
                <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                var items = JSON.parse(data);

                for (var key in items) {
                    // Set default tax rate id if empty
                    if (!items[key].tax_rate_id) items[key].tax_rate_id = 0;

                    if ($('#item_table tbody:last input[name=item_name]').val() !== '') {
                        $('#new_row').clone().appendTo('#item_table').removeAttr('id').addClass('item').show();
                    }

                    var last_item_row = $('#item_table tbody:last');

                    last_item_row.find('input[name=item_name]').val(items[key].product_name);
                    last_item_row.find('textarea[name=item_description]').val(items[key].product_description);
                    last_item_row.find('input[name=item_price]').val(items[key].product_price);
                    last_item_row.find('input[name=item_quantity]').val('1');
                    last_item_row.find('select[name=item_tax_rate_id]').val(items[key].tax_rate_id);
                    last_item_row.find('input[name=item_product_id]').val(items[key].product_id);
                    last_item_row.find('select[name=item_product_unit_id]').val(items[key].unit_id);

                    $('#modal-choose-items').modal('hide');
                }
            });
        });

        // Toggle checkbox when click on row
        $(document).on('click', '.product', function (event) {
            if (event.target.type !== 'checkbox') {
                $(':checkbox', this).trigger('click');
            }
        });

        // Reset the form
        $('#product-reset-button').click(function () {
            var product_table = $('#product-lookup-table');

            product_table.html('<h2 class="text-center"><i class="fa fa-spin fa-spinner"></i></h2>');

            var lookup_url = "<?php echo site_url('products/ajax/modal_product_lookups'); ?>/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';
            lookup_url += "&reset_table=true";

            // Reload modal with settings
            window.setTimeout(function () {
                product_table.load(lookup_url);
            }, 250);
        });

        // Filter on search button click
        $('#filter-button').click(function () {
            products_filter();
        });

        // Filter on family dropdown change
        $("#filter_family").change(function () {
            products_filter();
        });

        // Filter products
        function products_filter() {
            var filter_family = $('#filter_family').val();
            var filter_product = $('#filter_product').val();
            var product_table = $('#product-lookup-table');

            product_table.html('<h2 class="text-center"><i class="fa fa-spin fa-spinner"></i></h2>');

            var lookup_url = "<?php echo site_url('products/ajax/modal_product_lookups'); ?>/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';

            if (filter_family) {
                lookup_url += "&filter_family=" + filter_family;
            }

            if (filter_product) {
                lookup_url += "&filter_product=" + filter_product;
            }

            // Reload modal with settings
            window.setTimeout(function () {
                product_table.load(lookup_url);
            }, 250);
        }
    });
</script>

<div id="modal-choose-items" class="modal col-xs-12 col-sm-10 col-sm-offset-1"
     role="dialog" aria-labelledby="modal-choose-items" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('add_product'); ?></h4>
        </div>
        <div class="modal-body">

            <div class="form-inline">
                <div class="form-group filter-form">
                    <select name="filter_family" id="filter_family" class="form-control simple-select">
                        <option value=""><?php _trans('any_family'); ?></option>
                        <?php foreach ($families as $family) { ?>
                            <option value="<?php echo $family->family_id; ?>"
                                <?php if (isset($filter_family) && $family->family_id == $filter_family) {
                                    echo ' selected="selected"';
                                } ?>>
                                <?php _htmlsc($family->family_name); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" name="filter_product" id="filter_product"
                           placeholder="<?php _trans('product_name'); ?>"
                           value="<?php echo $filter_product ?>">
                </div>
                <button type="button" id="filter-button"
                        class="btn btn-default"><?php _trans('search_product'); ?></button>
                <button type="button" id="product-reset-button" class="btn btn-default">
                    <?php _trans('reset'); ?>
                </button>
            </div>

            <br/>

            <div id="product-lookup-table">
                <?php $this->layout->load_view('products/partial_product_table_modal'); ?>
            </div>

        </div>
        <div class="modal-footer">
            <div class="btn-group">
                <button class="select-items-confirm btn btn-success" type="button">
                    <i class="fa fa-check"></i>
                    <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>
    </form>

</div>
