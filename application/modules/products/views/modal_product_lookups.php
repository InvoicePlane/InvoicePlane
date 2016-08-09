<script type="text/javascript">
    $(function () {
        // Display the create invoice modal
        $('#modal-choose-items').modal('show');

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
                items = JSON.parse(data);

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

                    $('#modal-choose-items').modal('hide');
                }
            });
        });

        // Toggle checkbox when click on row
        $('.product').click(function (event) {
            if (event.target.type !== 'checkbox') {
                $(':checkbox', this).trigger('click');
            }
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
            var lookup_url = "<?php echo site_url('products/ajax/modal_product_lookups'); ?>/";
            lookup_url += Math.floor(Math.random() * 1000) + '/?';

            if (filter_family) {
                lookup_url += "&filter_family=" + filter_family;
            }

            if (filter_product) {
                lookup_url += "&filter_product=" + filter_product;
            }

            // refresh modal
            $('#modal-choose-items').modal('hide');
            $('#modal-placeholder').load(lookup_url);
        }
    });
</script>

<div id="modal-choose-items" class="modal col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2"
     role="dialog" aria-labelledby="modal-choose-items" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <a data-dismiss="modal" class="close"><i class="fa fa-close"></i></a>

            <h3><?php echo trans('add_product'); ?></h3>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-xs-8">
                    <div class="form-inline">
                        <div class="form-group filter-form">
                            <!-- ToDo
					<select name="filter_family" id="filter_family" class="form-control">
						<option value=""><?php echo trans('any_family'); ?></option>
						<?php foreach ($families as $family) { ?>
						<option value="<?php echo $family->family_id; ?>"
							<?php if (isset($filter_family) && $family->family_id == $filter_family) {
                                echo ' selected="selected"';
                            } ?>><?php echo $family->family_name; ?></option>
						<?php } ?>
					</select>
					-->
                        </div>
                        <div class="form-group">
                            <input type="text" class="form-control" name="filter_product" id="filter_product"
                                   placeholder="<?php echo trans('product_name'); ?>"
                                   value="<?php echo $filter_product ?>">
                        </div>
                        <button type="button" id="filter-button"
                                class="btn btn-default"><?php echo trans('search_product'); ?></button>
                        <!-- ToDo
                        <button type="button" id="reset-button" class="btn btn-default">
                            <?php //echo trans('reset'); ?>
                        </button>
                        -->
                    </div>
                </div>
                <div class="col-xs-4 text-right">
                    <div class="btn-group">
                        <button class="btn btn-danger" type="button" data-dismiss="modal">
                            <i class="fa fa-times"></i>
                            <?php echo trans('cancel'); ?>
                        </button>
                        <button class="select-items-confirm btn btn-success" type="button">
                            <i class="fa fa-check"></i>
                            <?php echo trans('submit'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <br/>

            <div class="table-responsive">
                <table id="products_table" class="table table-bordered table-striped">
                    <tr>
                        <th>&nbsp;</th>
                        <th><?php echo trans('product_sku'); ?></th>
                        <th><?php echo trans('family_name'); ?></th>
                        <th><?php echo trans('product_name'); ?></th>
                        <th><?php echo trans('product_description'); ?></th>
                        <th class="text-right"><?php echo trans('product_price'); ?></th>
                    </tr>
                    <?php foreach ($products as $product) { ?>
                        <tr class="product">
                            <td class="text-left">
                                <input type="checkbox" name="product_ids[]"
                                       value="<?php echo $product->product_id; ?>">
                            </td>
                            <td nowrap class="text-left">
                                <b><?php echo $product->product_sku; ?></b>
                            </td>
                            <td>
                                <b><?php echo $product->family_name; ?></b>
                            </td>
                            <td>
                                <b><?php echo $product->product_name; ?></b>
                            </td>
                            <td>
                                <?php echo nl2br($product->product_description); ?>
                            </td>
                            <td class="text-right">
                                <?php echo format_currency($product->product_price); ?>
                            </td>
                        </tr>
                        <!-- Todo
						<tr class="bold-border">
                            <td colspan="3">
                                <?php echo $product->product_description; ?>
                            </td>
                        </tr>
						-->
                    <?php } ?>
                </table>
            </div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i>
                    <?php echo trans('cancel'); ?>
                </button>
                <button class="select-items-confirm btn btn-success" type="button">
                    <i class="fa fa-check"></i>
                    <?php echo trans('submit'); ?>
                </button>
            </div>
        </div>

    </form>

</div>