<?php
/**
 * @author rugk
 * @copyright Copyright (c) 2015-2016 rugk
 * @license MIT
 */

function ShowDefaultReceiverId($addOptionsHtmlCode = false)
{
    $isShown = ReturnGetPost('threemaid') != null || ReturnGetPost('recieverid')
    != null || MSGAPI_DEFAULTRECEIVER <> '';

    // Show previous input if there is something
    if ($isShown && $addOptionsHtmlCode) {
        echo '<option value="';
    }

    if (ReturnGetPost('threemaid') != null) {
        echo htmlentities(ReturnGetPost('threemaid'));
    } elseif (ReturnGetPost('recieverid') != null) {
        echo htmlentities(ReturnGetPost('recieverid'));
    } elseif (MSGAPI_DEFAULTRECEIVER <> '') {
        // use receiver in config
        echo MSGAPI_DEFAULTRECEIVER;
    }

    if ($isShown && $addOptionsHtmlCode) {
        echo '">';
    }
}

function ShowDefaultMessage()
{
    // Show previous input if there is something
    if (ReturnGetPost('message') != null) {
        echo htmlentities(ReturnGetPost('message'));
    }
}

function ShowLibsodiumVersion()
{
    if (method_exists('Sodium', 'sodium_version_string')) {
        echo Sodium::sodium_version_string();
    } else {
        echo \Sodium\version_string();
    }
}
