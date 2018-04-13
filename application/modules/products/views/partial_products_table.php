<div id="content" class="table-content">

    <?php $this->layout->load_view('layout/alerts'); ?>
    
    <div class="table-responsive">
        <table class="table table-striped">

            <thead>
            <tr>
                <th><?php _trans('photo'); ?></th>
                <th><?php _trans('family'); ?></th>
                <th><?php _trans('product_sku'); ?></th>
                <th><?php _trans('product_name'); ?></th>
                <th><?php _trans('product_description'); ?></th>
                <th><?php _trans('product_qty'); ?></th>
                <th><?php _trans('product_price'); ?></th>
                <th><?php _trans('product_unit'); ?></th>
                <th><?php _trans('tax_rate'); ?></th>
                <?php if (get_setting('sumex')) : ?>
                    <th><?php _trans('product_tariff'); ?></th>
                <?php endif; ?>
                <th><?php _trans('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($products as $key=>$product) { ?>
                <tr>
                    <td>
                        <?php
                        for ($i=0; $i<count($product->photos); $i++) {
                            $photo = $product->photos[$i];
                            $types = array('jpg', 'jpeg', 'png', 'gif');
                            $located = false;
                            
                            foreach ($types as $type) {
                                $pos = strpos(strtolower($photo['filename']), $type);
                                
                                if ($pos !== false) {
                                    $located = true;
                                }
                            }
                            
                            if ($located) {
                                if ($i == 0) {
                                    echo '<a data-fancybox="gallery'.$key.'" href="'.site_url('upload/get_file/').$photo['filename'].'">'
                                            . '<img width="auto" height="80" src="'.site_url('upload/get_file/').$photo['filename'].'">'
                                        . '</a>';
                                } else {
                                    echo '<div class="hidden">'
                                            . '<a data-fancybox="gallery'.$key.'" href="'.site_url('upload/get_file/').$photo['filename'].'">'
                                                . '<img width="auto" height="80" src="'.site_url('upload/get_file/').$photo['filename'].'">'
                                            . '</a>'
                                        . '</div>';
                                }
                            }
                        }
                        ?>
                    </td>
                    <td><?php _htmlsc($product->family_name); ?></td>
                    <td><?php _htmlsc($product->product_sku); ?></td>
                    <td>
                        <a href="<?php echo site_url('products/form/' . $product->product_id); ?>">
                            <?php _htmlsc($product->product_name); ?>
                        </a>
                    </td>
                    <td><?php echo nl2br(htmlsc($product->product_description)); ?></td>
                    <td><?php _htmlsc($product->product_qty); ?></td>
                    <td class="amount"><?php echo format_currency($product->product_price); ?></td>
                    <td><?php _htmlsc($product->unit_name); ?></td>
                    <td><?php echo ($product->tax_rate_id) ? htmlsc($product->tax_rate_name) : trans('none'); ?></td>
                    <?php if (get_setting('sumex')) : ?>
                        <td><?php _htmlsc($product->product_tariff); ?></td>
                    <?php endif; ?>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-default btn-sm dropdown-toggle"
                               data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i> <?php _trans('options'); ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="<?php echo site_url('products/form/' . $product->product_id); ?>">
                                        <i class="fa fa-edit fa-margin"></i> <?php _trans('edit'); ?>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo site_url('products/delete/' . $product->product_id); ?>"
                                       onclick="return confirm('<?php _trans('delete_record_warning'); ?>');">
                                        <i class="fa fa-trash-o fa-margin"></i> <?php _trans('delete'); ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>

        </table>
    </div>

</div>