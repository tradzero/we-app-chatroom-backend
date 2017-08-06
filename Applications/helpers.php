<?php

use Noodlehaus\Config;

function config()
{
    $conf = new Config(__DIR__ . '/configs');
    return $conf;
}
