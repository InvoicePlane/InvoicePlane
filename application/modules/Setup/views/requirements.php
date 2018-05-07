<!-- @TODO Move texts into lang file [Kovah 2018-05-04] -->
<h2>Requirements check</h2>

<ul class="list-group mt-5 mb-5">
    <?php foreach ($basics as $check) {
        if (isset($check['warning'])) { ?>
            <li class="list-group-item">
                <i class="fa fa-exclamation text-warning fa-mr">&excl;</i> <?php echo $check['message']; ?>
            </li>
        <?php } elseif ($check['success']) { ?>
            <li class="list-group-item">
                <i class="fa fa-check text-success fa-mr">&check;</i> <?php echo $check['message']; ?>
            </li>
        <?php } else { ?>
            <li class="list-group-item">
                <i class="fa fa-close text-danger fa-mr">&cross;</i> <?php echo $check['message']; ?>
            </li>
        <?php }
    } ?>
</ul>

<ul class="list-group mt-5 mb-5">
    <?php foreach ($writables as $check) {
        if ($check['success']) { ?>
            <li class="list-group-item">
                <i class="fa fa-check text-success fa-mr">&check;</i> <?php echo $check['message']; ?>
            </li>
        <?php } else { ?>
            <li class="list-group-item">
                <i class="fa fa-close text-danger fa-mr">&cross;</i> <?php echo $check['message']; ?>
            </li>
        <?php }
    } ?>
</ul>

<form action="<?php echo site_url('setup/requirements'); ?>" method="post">

    <?php _csrf_field(); ?>

    <?php if ($errors) : ?>
        <a href="javascript:history.go(0);" class="btn btn-danger"><?php _trans('try_again'); ?></a>
    <?php else : ?>
        <button class="btn btn-primary" name="btn_continue" value="1">Continue</button>
    <?php endif; ?>

</form>
