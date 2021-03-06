<?php
/**
 * @author rugk
 * @copyright Copyright (c) 2015-2016 rugk
 * @license MIT
 */

header('Content-Type: application/json');

//include SDK
require_once 'include/BootstrapSdk.php';

//include credentials
require_once 'include/GlobalConstants.php';
include_once FILENAME_CONNCRED;
include_once FILENAME_PRIVKEY;

//include web files used
require_once 'include/GlobalConstants.php';
require_once 'include/Connection.php';
require_once 'include/PublicKey.php';
require_once 'include/GetPost.php';

/**
 * Fetches the public key of an ID from the Threema server.
 *
 * @param Threema\MsgApi\Connection $connector connector
 * @param string                    $threemaId The id whose public key should be fetched
 *
 * @throws Exception
 * @return string
 */
function FetchPublicKey(Threema\MsgApi\Connection $connector, $threemaId)
{
    $result = $connector->fetchPublicKey($threemaId);
    if ($result->isSuccess()) {
        return $result->getPublicKey();
    } else {
        throw new Exception($result->getErrorMessage());
    }
}

//get params
$threemaId = null;
if (ReturnGetPost('threemaid') &&
    preg_match('/' . REGEXP_THREEMAID_ANY . '/', ReturnGetPost('threemaid'))
) {
    $threemaId = htmlentities(ReturnGetPost('threemaid'));
}

//create connection
$connector = CreateConnection();

//fetch public key and return a 500 error in case of a failure
if ($threemaId !== null) {
    try {
        //success: return all variants of the key as a JSON
        $publicKey['long']             = FetchPublicKey($connector, $threemaId);
        $publicKey['shortuserdisplay'] = KeyGetUserDisplay($publicKey['long']);
        echo json_encode($publicKey);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['errorMessage' => $e->getMessage()]);
    }
} else {
    http_response_code(500);
    echo json_encode(['errorMessage' => 'Invalid Threema ID']);
}
