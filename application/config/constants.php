<?php

defined('BASEPATH') || exit('No direct script access allowed');

// Constants are now defined in bootstrap/constants.php, which kernel.php loads
// before CI3 boots. This file is kept so CI3's auto-loader finds it and does
// not log a warning, but all the work happens in the bootstrap layer.
require_once dirname(__DIR__, 2) . '/bootstrap/constants.php';
