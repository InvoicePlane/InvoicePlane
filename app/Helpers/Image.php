<?php

function image($imagePath, $width, $height)
{
    $image = base64_encode(file_get_contents($imagePath));

    return '<img src="data:image/png;base64,' . $image . '" style="width: ' . $width . 'px; height: ' . $height . 'px;">';
}
