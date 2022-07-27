<?php

namespace App\Utilities;

use Exception;

class Cipher
{
    static private $key;
    static private $iv;

    public static function init()
    {
        self::$key = hash('sha256', config('app.encryption.secret_key'));
        self::$iv = substr(hash('sha256', config('app.encryption.secret_iv')), 0, 16);
    }

    /**
     * @throws Exception
     */
    public static function encrypt(string $input): string
    {
        try {
            self::init();
            if (!extension_loaded('openssl')) {
                throw new Exception('Encryption requires the OpenSSL PHP extension');
            }

            $output = openssl_encrypt($input, config('app.encryption.method'), self::$key, 0, self::$iv);
            $output = base64_encode($output);
        } catch (\Throwable $ex) {
            throw new \Exception($ex->getMessage());
        }
        return $output;
    }

    /**
     * @throws Exception
     */
    public static function decrypt(string $input): string
    {
        try {
            self::init();
            if (!extension_loaded('openssl')) {
                throw new Exception('Encryption requires the OpenSSL PHP extension');
            }
            $output = openssl_decrypt(base64_decode($input), config('app.encryption.method'), self::$key, 0, self::$iv);

        } catch (Exception $ex) {
            throw new \Exception($ex->getMessage());
        }
        return $output;
    }

}
