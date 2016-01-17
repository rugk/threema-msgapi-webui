<?php
/**
 * @author rugk
 * @copyright Copyright (c) 2015-2016 rugk
 * @license MIT
 */

// set end of line
$eol = PHP_EOL;
if (php_sapi_name() != 'cli') {
    $eol .= '<br>';
}

//list all functions
$functions = get_extension_funcs('libsodium');

foreach ($functions as $func) {
    echo $func . $eol;
}
echo $eol;
