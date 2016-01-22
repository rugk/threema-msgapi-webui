<?php
/**
 * @author rugk
 * @copyright Copyright (c) 2015-2016 rugk
 * @license MIT
 */

use Threema\MsgApi\Connection;
use Threema\MsgApi\ConnectionSettings;

/**
 * Creates a connection.
 *
 * Automatically calls CreateConnectionSettings and InitiateConnection.
 *
 * @param string $keystorePath  file for public key store
 * @param bool   $useTlsOptions whether to use advanced options or not
 *
 * @return Connection $connector
 */
function CreateConnection($keystorePath = __DIR__ . '/../keystore.php', $useTlsOptions = true)
{
    $publicKeyStore = CreatePublicKeyStore($keystorePath);
    $settings       = CreateConnectionSettings($useTlsOptions);
    $connector      = InitiateConnection($settings, $publicKeyStore);

    return $connector;
}

/**
 * Createa a PHP public key store.
 *
 * @param string $keystorePath Path public key store file (PHP)
 *
 * @return Threema\MsgApi\PublicKeyStores\PhpFile public key store
 */
function CreatePublicKeyStore($keystorePath)
{
    if (!file_exists($keystorePath)) {
        if (!touch($keystorePath)) {
            throw new Exception('PHP keystore could not be created.');
        }
    }
    return new Threema\MsgApi\PublicKeyStores\PhpFile($keystorePath);
}

/**
 * Creates connection settings.
 *
 * @param bool $useTlsOptions whether to use advanced options or not
 *
 * @return ConnectionSettings
 */
function CreateConnectionSettings($useTlsOptions)
{
    if ($useTlsOptions === true) {
        //create a connection with advanced options
        $settings = new ConnectionSettings(
            MSGAPI_GATEWAY_THREEMA_ID,
            MSGAPI_GATEWAY_THREEMA_ID_SECRET,
            null,
            [
                'forceHttps' => true,
                'tlsVersion' => '1.2',
                'tlsCipher' => 'ECDHE-RSA-AES128-GCM-SHA256'
            ]
        );
    } else {
        //create a connection with default options
        $settings = new ConnectionSettings(
            MSGAPI_GATEWAY_THREEMA_ID,
            MSGAPI_GATEWAY_THREEMA_ID_SECRET
        );
    }

    return $settings;
}

/**
 * Initiates (starts) connection settings.
 *
 * @param Threema\MsgApi\ConnectionSettings $settings       settings got by CreateConnectionSettings
 * @param Threema\MsgApi\PublicKeyStores\PhpFile           $publicKeyStore public key store
 *
 * @return Connection $connector
 */
function InitiateConnection(Threema\MsgApi\ConnectionSettings $settings, Threema\MsgApi\PublicKeyStores\PhpFile $publicKeyStore)
{
    return new Connection($settings, $publicKeyStore);
}
