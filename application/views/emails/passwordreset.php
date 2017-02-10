<style>
    div {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        font-size: 16px;
        color: #444;
        margin: 0 auto;
        max-width: 500px;
    }

    a {
        color: #003399;
    }

    a:hover {
        color: #002166;
    }
</style>
<div>
    <p><?php echo trans('password_reset_email'); ?></p>
    <p><a href="<?php echo $resetlink ?>"><?php echo $resetlink ?></a></p>
</div>
