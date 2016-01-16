<?php
/**
 * @author rugk
 * @copyright Copyright (c) 2015-2016 rugk
 * @license MIT
 */

// check load state
if (extension_loaded('libsodium')) {
    echo 'libsodium is loaded<br/>';
} else {
    echo 'libsodium is not loaded<br/>';
    exit;
}

// check version
if (method_exists('Sodium', 'sodium_version_string')) {
    echo 'you use an old version of libsodium (<0.2.0)<br/>';
    echo 'Sodium version: ' . Sodium::sodium_version_string();
} else {
    echo 'you use a recent version of libsodium<br/>';
    echo 'Sodium version: ' . \Sodium\version_string();
}
?>
