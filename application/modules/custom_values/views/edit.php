<form method="post" class="form-horizontal">
  <div id="headerbar">
      <h1 class="pull-left"><?php echo trans('custom_values_edit'); ?></h1>
      <?php $this->layout->load_view('layout/header_buttons'); ?>
  </div>
  <?php /* ?>
  <div id="headerbar">
      <h1><?php echo trans('custom_values'); ?></h1>

      <div class="pull-right">
          <a class="btn btn-sm btn-success" href="<?php echo site_url('custom_values/edit/'.$value->custom_values_id); ?>">
              <i class="fa fa-check"></i> <?php echo trans('save'); ?>
          </a>
          <a class="btn btn-sm btn-danger" href="<?php echo site_url('custom_values/form/'.$fid); ?>">
              <i class="fa fa-times"></i> <?php echo trans('cancel'); ?>
          </a>
      </div>
  </div><? */ ?>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('field'); ?>: </label>
            <div class="col-xs-12 col-sm-8 col-md-6">
                <input type="text" class="form-control"
                  value="<?php echo htmlentities($value->custom_field_label);?>" disabled="disabled"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('label'); ?>: </label>
            <div class="col-xs-12 col-sm-8 col-md-6">
                <input type="text" name="custom_values_value" id="custom_values_value" class="form-control"
                       value="<?php echo htmlentities($value->custom_values_value); ?>">
            </div>
        </div>

    </div>

</form>
