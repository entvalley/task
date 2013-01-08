<?php

namespace Entvalley\AppBundle\Service;

class SecureRandom
{
    public static function rand($numberOfBytes, $secure = true)
    {
        $strong = false;
        if ($secure && function_exists('openssl_random_pseudo_bytes')) {
            $bytes = openssl_random_pseudo_bytes($numberOfBytes, $strong);
        }

        if (!$strong) {
            if (file_exists('/dev/urandom')) {
                $randomHandle = fopen("/dev/urandom", "r");
                $seed = fread($randomHandle, 128);
                fclose($randomHandle);
            } else {
                $seed = __FILE__; // @todo a better seed
            }
            $bytes = '';
            while (strlen($bytes) < $numberOfBytes) {
                $bytes .= hash('sha512', $seed . uniqid(mt_rand(), true) . $numberOfBytes, true);
            }
        }

        return substr($bytes, 0, $numberOfBytes);
    }
}