<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo trans('custom_values'); ?></h1>

        <div class="pull-right">
            <a class="btn btn-sm btn-default" href="<?php echo site_url('custom_values'); ?>">
                <i class="fa fa-arrow-left"></i> <?php echo trans('back'); ?>
            </a>

            <a class="btn btn-sm btn-primary" href="<?php echo site_url('custom_values/create/' . $id); ?>">
                <i class="fa fa-plus"></i> <?php echo trans('new'); ?>
            </a>

        </div>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('field'); ?>: </label>
            <div class="col-xs-12 col-sm-8 col-md-6">
                <input type="text" name="custom_values_label" id="custom_values_label" class="form-control"
                       value="<?php echo $field->custom_field_label; ?>" disabled="disabled">
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('type'); ?>: </label>
            <div class="col-xs-12 col-sm-8 col-md-6">
                <select name="custom_field_types" id="custom_field_types" class="form-control" disabled="disabled">
                    <option value=""></option>
                    <?php foreach ($custom_values_types as $type): ?>
                        <?php $alpha = str_replace("-", "_", strtolower($type)); ?>
                        <option value="<?php echo $type; ?>"
                            <?php echo($field->custom_field_type == $type ? 'selected="selected"' : ''); ?>><?php echo trans($alpha); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <table class="table table-striped">

                <thead>
                <tr>
                    <th><?php echo trans('id'); ?></th>
                    <th><?php echo trans('label'); ?></th>
                    <th><?php echo trans('options'); ?></th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ($elements as $element) { ?>
                    <tr>
                        <td><?php echo $element->custom_values_id; ?></td>
                        <td><?php echo $element->custom_values_value; ?></td>
                        <td>
                            <div class="options btn-group">
                                <a class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-cog"></i> <?php echo trans('options'); ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="<?php echo site_url('custom_values/edit/' . $element->custom_values_id); ?>">
                                            <i class="fa fa-edit fa-margin"></i> <?php echo trans('edit'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="<?php echo site_url('custom_values/delete/' . $element->custom_values_id); ?>"
                                           onclick="return confirm('<?php echo trans('delete_record_warning'); ?>');">
                                            <i class="fa fa-trash-o fa-margin"></i> <?php echo trans('delete'); ?>
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

</form>
