<form method="post" class="form-horizontal">

    <input type="hidden" name="_ip_csrf" value="<?= $this->security->get_csrf_hash() ?>">

    <div id="headerbar">
        <h1 class="headerbar-title"><?php echo trans('custom_field_form'); ?></h1>
        <?php $this->layout->load_view('layout/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/alerts'); ?>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label" for="custom_field_table">
                <?php echo trans('table'); ?>:
            </label>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <select name="custom_field_table" id="custom_field_table" class="form-control simple-select">
                    <?php foreach ($custom_field_tables as $table => $label) { ?>
                        <option value="<?php echo $table; ?>"
                                <?php if ($this->mdl_custom_fields->form_value('custom_field_table') == $table) { ?>selected="selected"<?php } ?>><?php echo lang($label); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('label'); ?>: </label>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <input type="text" name="custom_field_label" id="custom_field_label" class="form-control"
                       value="<?php echo htmlentities($this->mdl_custom_fields->form_value('custom_field_label')); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('type'); ?>: </label>
            <div class="col-xs-12 col-sm-8 col-md-6">
                <select name="custom_field_type" id="custom_field_type" class="form-control simple-select">
                    <?php foreach ($custom_field_types as $type) { ?>
                        <?php $alpha = str_replace("-", "_", strtolower($type)); ?>
                        <option value="<?php echo $type; ?>"
                                <?php if ($this->mdl_custom_fields->form_value('custom_field_type') == $type) { ?>selected="selected"<?php } ?>><?php echo trans($alpha); ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label"><?php echo trans('order'); ?>: </label>

            <div class="col-xs-12 col-sm-8 col-md-6">
                <input type="number" name="custom_field_order" id="custom_field_order" class="form-control"
                       value="<?php echo htmlentities($this->mdl_custom_fields->form_value('custom_field_order')); ?>">
            </div>
        </div>

        <div class="form-group">
            <label class="col-xs-12 col-sm-1 control-label" for="custom_field_table">
                <?php echo trans('position'); ?>:
            </label>
            <?php
              $positions = array(
                'client' => Mdl_client_custom::$positions,
                'invoice' => Mdl_invoice_custom::$positions,
                'payment' => Mdl_payment_custom::$positions,
                'quote' => Mdl_quote_custom::$positions,
                'user' => Mdl_user_custom::$positions,
              );

              foreach($positions as $key=>$val){
                foreach($val as $key2=>$val2){
                  $val[$key2] = trans($val2);
                }
                $positions[$key] = $val;
              }
            ?>
            <script>
              var jsonPositions = '<?php echo json_encode($positions); ?>';
              jsonPositions = JSON.parse(jsonPositions);
            </script>
            <?php $valueSelected = $this->mdl_custom_fields->form_value('custom_field_location'); ?>
            <div class="col-xs-12 col-sm-8 col-md-6">
                <select name="custom_field_location" id="custom_field_location" class="form-control simple-select"></select>
            </div>
        </div>

    </div>

</form>

<script>


  function updatePositions(index, selKey){
    $("#custom_field_location option").remove();
    var key = Object.keys(jsonPositions)[index];
    for(pos in jsonPositions[key]){
      var opt = $("<option>");
      opt.attr("value",pos);
      opt.text(jsonPositions[key][pos]);
      if(selKey == pos){
        opt.attr("selected","selected");
      }
      $("#custom_field_location").append(opt);
    }
  }

  var optionIndex = $("#custom_field_table option:selected").index();

  $("#custom_field_table").on("change", function(){
    optionIndex = $("#custom_field_table option:selected").index();
    updatePositions(optionIndex);
  });

  updatePositions(optionIndex, <?php echo $valueSelected; ?>);

</script>
