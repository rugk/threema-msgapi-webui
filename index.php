<?php
/**
 * @author rugk
 * @copyright Copyright (c) 2015-2016 rugk
 * @license MIT
 */

// only shows content when it is not parsed by a PHP interpreter
if (false):
?>
<!-- PHP parsing error message -->
<div class="warning">
    You are viewing this file within a browser. However you do need to call this file
    through PHP to view it correctly. To do this please setup a local server with
    PHP support and access the file like this: <code>http://127.0.0.1/threema-msgapi-sdk-php/samples/web/</code>.
</div>
<?php endif ?>

<?php
/* INCLUDES */
require_once 'include/GlobalConstants.php';
require_once 'include/UiHelper.php';
require_once 'include/PublicKey.php';
require_once 'include/GetPost.php';
require_once 'include/ErrorCheckCollector.php';

//the config options
require_once 'config.php';

/* HANDLE SENDING OF MESSAGES */
$actionDone = null;
if (ReturnGetPost('recieverid')) {
    require_once 'SendTextMessage.php';
}

/* CHECK PREREQUISITES */
$errCol                 = new ErrorChkColl;
$sendingCheckSuccessful = true; //later more requirements are added

//file: connection credentials
$errCol->registerGenericMessages('files', 'connCred',
    '<code>' . FILENAME_CONNCRED . '</code> was correctly created.',
    '<code>' . FILENAME_CONNCRED . '</code> was not correctly created.'
);

if (!file_exists(FILENAME_CONNCRED)) {
    $errCol->registerError('files', 'connCred', 'The file does not exist.');
} else {
    include_once FILENAME_CONNCRED;
    if (!defined('MSGAPI_GATEWAY_THREEMA_ID') ||
        !defined('MSGAPI_GATEWAY_THREEMA_ID_SECRET')
    ) {
        $errCol->registerError('files', 'connCred', 'Not all required constants are defined.');
    } else {
        if (MSGAPI_GATEWAY_THREEMA_ID == '' ||
            !preg_match('/' . REGEXP_THREEMAID_GATEWAY . '/', MSGAPI_GATEWAY_THREEMA_ID)
        ) {
            $errCol->registerError('files', 'connCred', '\'MSGAPI_GATEWAY_THREEMA_ID\' is invalid.');
        }

        if (MSGAPI_GATEWAY_THREEMA_ID_SECRET == '' ||
            !ctype_alnum(MSGAPI_GATEWAY_THREEMA_ID_SECRET)
        ) {
            $errCol->registerError('files', 'connCred', '\'MSGAPI_GATEWAY_THREEMA_ID_SECRET\' is invalid.');
        }

        // MSGAPI_DEFAULTRECEIVER is optional
        if (!defined('MSGAPI_DEFAULTRECEIVER')) {
            define('MSGAPI_DEFAULTRECEIVER', '');
        }

        if (MSGAPI_DEFAULTRECEIVER != '' &&
            !preg_match('/' . REGEXP_THREEMAID_ANY . '/', MSGAPI_DEFAULTRECEIVER)
        ) {
            $fileConnCredentErr .= ' \'MSGAPI_DEFAULTRECEIVER\' is invalid.';
        }
    }
}

//file: private key
$errCol->registerGenericMessages('files', 'privKey',
    '<code>' . FILENAME_PRIVKEY . '</code> was correctly created.',
    '<code>' . FILENAME_PRIVKEY . '</code> was not correctly created.'
);

if (!file_exists(FILENAME_PRIVKEY)) {
    $errCol->registerError('files', 'privKey', 'The file does not exist.');
} else {
    include_once FILENAME_PRIVKEY;
    if (!defined('MSGAPI_PRIVATE_KEY')) {
        $errCol->registerError('files', 'privKey', 'Not all constants are defined.');
    } else {
        if (MSGAPI_PRIVATE_KEY == '' ||
            !KeyCheck(MSGAPI_PRIVATE_KEY, 'private:')
        ) {
            $errCol->registerError('files', 'privKey', '\'MSGAPI_PRIVATE_KEY\' is invalid.');
        }
    }
}

//libsodium: private key
$errCol->registerGenericMessages('libsodium', 'state',
    'Libsodium is loaded.',
    'Libsodium is not loaded.'
);
$errCol->registerGenericMessages('libsodium', 'version',
    'You use a recent version of libsodium.',
    'You use an old outdated version of libsodium. It is very much recommend to update it.'
);

if (extension_loaded('libsodium')) {
    $errCol->registerSuccess('libsodium', 'state');

    if (method_exists('Sodium', 'sodium_version_string')) {
        $errCol->registerWarning('libsodium', 'version', 'Sodium version: ' . Sodium::sodium_version_string());
    } else {
        $errCol->registerSuccess('libsodium', 'version', 'Sodium version: ' . \Sodium\version_string());
    }
} else {
    $errCol->registerWarning('libsodium', 'state', 'It is very much recommend to install and load it.');
    if (PHP_INT_SIZE < 8) {
        $errCol->registerError('libsodium', '64bit', 'To use the SDK without libsodium you have to use a 64bit version of PHP.');
    }
}

//evaluate checks and prepare for an output of the results
$fileCheckResults      = $errCol->evaluateChecks('files');
$libsodiumCheckResults = $errCol->evaluateChecks('libsodium', false);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Development UI - MSGAPI-PHP-SDK - Threema Gateway</title>
        <link rel="stylesheet" type="text/css" href="assets/css/common.css" charset="utf-8">
    </head>
    <body>
        <h1 id="devui">Development UI - MSGAPI-PHP-SDK - Threema Gateway</h1>
        <p>
            This is a development UI for the <a href="https://github.com/rugk/threema-msgapi-sdk-php" title="Threema Gateway PHP SDK">Threema MSGAPI PHP-SDK</a>.
            Here you can test the PHP message SDK.
        </p>

        <?php if ($fileCheckResults['error'] || DISPLSUCC_PREREQ): ?>
            <h2 id="prerequisites">Prerequisites</h2>
            <?php if ($fileCheckResults['error']): ?>
                <p>
                    Before you can use this test you have to get credentials at <a href="https://gateway.threema.ch" title="Threema Gateway">gateway.threema.ch</a> and <a href="https://github.com/rugk/threema-msgapi-sdk-php/wiki/How-to-generate-a-new-key-pair-and-send-a-message">create a key pair</a>. After you did so, you have to open <code><?php echo FILENAME_CONNCRED . FILEEXT_EXAMPLE ?></code> and <code><?php echo FILENAME_PRIVKEY . FILEEXT_EXAMPLE ?></code> and add your credentials and private key. Save them without the <code><?php echo FILEEXT_EXAMPLE ?></code> file extension afterwards.
                    By default these files are excluded from git pulls so you will not accidentally publish these sensitive files.
                </p>
            <?php endif ?>

            <!-- Show graphical indicator -->
            <div class="checkcontainer">
                <?php
                $errCol->showErrors($fileCheckResults, DISPLSUCC_PREREQ);
                ?>
            </div>
        <?php endif ?>

		<h2 id="debug">Debug</h2>
        <?php if ($libsodiumCheckResults['error'] || DISPLSUCC_DEBUG): ?>
            <div class="checkcontainer">
                <?php
                $errCol->showErrors($libsodiumCheckResults, DISPLSUCC_PREREQ);
                ?>
            </div>
        <?php else: ?>
            <span>
                Sodium version: <?php ShowLibsodiumVersion(); ?>
            </span>
        <?php endif ?>
        <div class="smalltext">
            <a href="debug" target="_blank">More debugging scripts</a>
        </div>

        <!-- Sending UI -->
        <h2 id="test">Test</h2>
        <?php if ($errCol->isBreakpoint($fileCheckResults) || $errCol->isBreakpoint($libsodiumCheckResults)): ?>
            <!-- error in checks before -->
            <div class="warning">
                You did not prepared your setup correctly to use the test. Please follow the intructions above to setup your environment.
            </div>
        <?php else: ?>
            <!-- checks successful -->
            <?php if ($actionDone === true): //pending messages from action must be shown ?>
                <?php if ($errorMessage === null): ?>
                    <div class="success">
                        Message successfully sent to <?php echo $threemaId ?>. Message ID: <?php echo $messageId ?>.
                    </div>
                <?php else: ?>
                    <div class="error">
                        Sending message to <?php echo $threemaId ?> failed. Error: <?php echo $errorMessage ?>.
                    </div>
                <?php endif ?>
            <?php endif ?>

            <form id="mainform" action="." method="<?php echo $_SERVER['REQUEST_METHOD']; ?>">
                <div class="formcontainer">
                    <fieldset id="field_generalsettings">
                        <legend>General settings</legend>
                        <label for="senderid">Sender: </label>
                        <input id="SenderIdInput" class="idInput" type="text" maxlength="8" name="senderid" value="<?php echo MSGAPI_GATEWAY_THREEMA_ID ?>" placeholder="*THREEMA" disabled="" pattern="<?php echo REGEXP_THREEMAID_GATEWAY ?>"><br />
                        <div class="publickeynote" id="SenderPubKey">
                            <noscript>
                                Please enable Javascript for a live display of the public keys.
                            </noscript>
                        </div>
                        <label for="recieverid">Receiver: </label>
                        <input id="RecieverIdInput" class="idInput" type="text" list="cachedRecieverIds" maxlength="8" name="recieverid" value="<?php ShowDefaultReceiverId(); ?>" placeholder="ECHOECHO" required="" pattern="<?php echo REGEXP_THREEMAID_ANY ?>" title="Threema ID of the receiver"><br />
                        <datalist id="cachedRecieverIds">
                            <?php ShowDefaultReceiverId(true); ?>
                            <option value="ECHOECHO">
                        </datalist>
                        <div class="publickeynote" id="RecieverPubKey">
                            <noscript>
                                Please enable Javascript for a live display of the public keys.
                            </noscript>
                        </div>
                    </fieldset>
                    <fieldset id="field_message">
                        <legend>Message</legend>
                        <textarea class="maintextinput" id="messageinput" type="text" id="messageedit" name="message" maxlength="3500" wrap="soft" required="" autofocus=""><?php echo ShowDefaultMessage(); ?></textarea>
                    </fieldset>
                    <fieldset id="field_method">
                        <legend>Method</legend>
                        <input id="SrvMethodGet" name="servermethod" type="radio" <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
                            echo 'checked="checked"';
                        } ?> value="get">
                            <label for="SrvMethodGet" title="Use the GET method for sending the request to the (local) server">GET</label>
                        <input id="SrvMethodPost" name="servermethod" type="radio" <?php
                        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                            echo 'checked="checked"';
                        } ?> value="post">
                            <label for="SrvMethodPost" title="Use the POST method for sending the request to the (local) server">POST</label>
                        <br />
                        <input id="ButtonExternalScript" name="isExternal" type="checkbox">
                            <label for="ButtonExternalScript" title="Use a separate page (in a new tab) instead of the index.php for submitting this request">Use own script</label>
                    </fieldset>
                </div>
                <input type="submit" value="Send" title="Submit request and send message">
            </form>

            <!-- Put JS at the end so it is executed when the DOM is loaded completly -->
            <script src="assets/js/binhex.js" charset="utf-8"></script>
            <script src="assets/js/pubkeyfetch.js" charset="utf-8"></script>
            <script src="assets/js/methodswitcher.js" charset="utf-8"></script>
        <?php endif ?>
    </body>
</html>
