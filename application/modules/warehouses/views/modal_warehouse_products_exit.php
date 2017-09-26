<script>
    $(function () {
        // Display the create quote modal
        $('#warehouse-products').modal('show');

        $('.simple-select').select2();
        
        $('#products').change(function(){
            var optionText = $.trim($(this).find('option:selected').html());
            var optionValue = $.trim($(this).find('option:selected').val());
            
            var found = false;
            
            $('#products-selected tbody tr').each(function(){
                if ($(this).attr('product_id') == optionValue) {
                    found = true;
                }
            });
            
            if (!found) {
                var html = '<tr product_id="'+optionValue+'">';
                html = html + '<td>';
                html = html + '<i class="fa fa-times cursor-pointer"></i>';
                html = html + '</td>';
                html = html + '<td>';
                html = html + optionText;
                html = html + '</td>';
                html = html + '<td>';
                html = html + '<input type="text" class="numeric form-control" value="1">';
                html = html + '</td>';
                html = html + '</tr>';

                $('#products-selected tbody').append(html);
            }
        });
        
        $(document).on('click', '#products-selected tbody fa-times', function(){
            $(this).parent().parent().remove();
        });
        
        // Creates the quote
        $('#warehouse_products_confirm').click(function () {
            console.log('clicked');
            
            var products = [];
            
            $('#products-selected tbody tr').each(function(){
                var product = {
                    'product_id' : $(this).attr('product_id'),
                    'product_qty' : $.trim($(this).find('input').val())
                };
                
                products.push(product);
            });
            
            // Posts the data to validate and create the entry;
            $.post("<?php echo site_url('warehouses/ajax/set_products_warehouse'); ?>", {
                    war_pro_type: $('#war_pro_type').val(),
                    products: products
                },
                function (data) {
                    <?php echo(IP_DEBUG ? 'console.log(data);' : ''); ?>
                    var response = JSON.parse(data);
                    if (response.success === 1) {
                        window.location = "<?php echo site_url('warehouses/index'); ?>/";
                    } else {
                        
                    }
                });
        });
    });
</script>

<div id="warehouse-products" class="modal modal-lg" role="dialog" aria-labelledby="modal-warehouse-products-entry" aria-hidden="true">
    <form class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"><i class="fa fa-close"></i></button>
            <h4 class="panel-title"><?php _trans('warehouse_products_entry'); ?></h4>
        </div>
        <div class="modal-body">
                
            <div class="form-group has-feedback">
                <label>
                    <?php _trans('products'); ?>
                </label>

                <select id="products" class="form-control simple-select">
                    <option value="0"> </option>
                    <?php foreach ($products as $product) { ?>
                        <?php if ($product->product_qty > 0) { ?>
                            <option value="<?php echo $product->product_id; ?>">
                                <?php echo $product->product_name; ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </select>
            </div>
            
            <div id="products-selected" class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td><?php _trans('delete') ?></td>
                            <td><?php _trans('product_name') ?></td>
                            <td><?php _trans('product_qty') ?></td>
                        </tr>
                    </thead>
                    
                    <tbody></tbody>
                </table>
            </div>
            
            <div class="form-group has-feedback">
                <div class="input-group">
                    <input name="war_pro_type" id="war_pro_type"
                           class="hidden form-control"
                           value="1">
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <div class="btn-group">
                <button class="btn btn-success ajax-loader" id="warehouse_products_confirm" type="button">
                    <i class="fa fa-check"></i> <?php _trans('submit'); ?>
                </button>
                <button class="btn btn-danger" type="button" data-dismiss="modal">
                    <i class="fa fa-times"></i> <?php _trans('cancel'); ?>
                </button>
            </div>
        </div>

    </form>

</div>
