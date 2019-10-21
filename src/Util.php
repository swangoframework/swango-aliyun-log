<?php
namespace Swango\Aliyun\Log;

/**
 * Copyright (C) Alibaba Cloud Computing
 * All rights reserved
 */
class Util {

    /**
     * Get the local machine ip address.
     *
     * @return string
     */
    public static function getLocalIp(): string {
        return \Swango\Environment::getServiceConfig()->local_ip;
    }

    /**
     * If $gonten is raw IP address, return true.
     *
     * @return bool
     */
    public static function isIp(string $gonten): bool {
        return ip2long($gonten) !== false;
    }

    /**
     * Calculate string $value MD5.
     *
     * @return string
     */
    public static function calMD5(string $value): string {
        return strtoupper(md5($value));
    }

    /**
     * Calculate string $content hmacSHA1 with secret key $key.
     *
     * @return string
     */
    public static function hmacSHA1(string $content, string $key): string {
        $signature = hash_hmac('sha1', $content, $key, true);
        return base64_encode($signature);
    }

    /**
     * Change $logGroup to bytes.
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public static function toBytes(\Swango\Aliyun\Log\SlsProto\Group $logGroup): \Psr\Http\Message\StreamInterface {
        $stream = fopen('php://temp', 'r+b');
        $mem = new \Swlib\Http\Stream($stream);
        $logGroup->write($mem);
        $mem->rewind();
        return $mem;
    }

    /**
     * Get url encode.
     *
     * @return string
     */
    public static function urlEncodeValue(string $value): string {
        return urlencode($value);
    }

    /**
     * Get url encode.
     *
     * @return string
     */
    public static function urlEncode(array $params): string {
        ksort($params);
        $url = "";
        $first = true;
        foreach ($params as $key=>$value) {
            $val = self::urlEncodeValue($value);
            if ($first) {
                $first = false;
                $url = "$key=$val";
            } else
                $url .= "&$key=$val";
        }
        return $url;
    }

    /**
     * Get canonicalizedLOGHeaders string as defined.
     *
     * @return string
     */
    public static function canonicalizedLOGHeaders(array $header): string {
        ksort($header);
        $content = '';
        $first = true;
        foreach ($header as $key=>$value)
            if (strpos($key, "x-log-") === 0 || strpos($key, "x-acs-") === 0) { // x-log- header
                if ($first) {
                    $content .= $key . ':' . $value;
                    $first = false;
                } else
                    $content .= "\n" . $key . ':' . $value;
            }
        return $content;
    }

    /**
     * Get canonicalizedResource string as defined.
     *
     * @return string
     */
    public static function canonicalizedResource(string $resource, ?array $params = null): string {
        if ($params) {
            $urlString = '';
            $first = true;
            foreach ($params as $key=>$value) {
                if ($first) {
                    $first = false;
                    $urlString = "$key=$value";
                } else
                    $urlString .= "&$key=$value";
            }
            return $resource . '?' . $urlString;
        }
        return $resource;
    }

    /**
     * Get request authorization string as defined.
     *
     * @return string
     */
    public static function getRequestAuthorization(string $method, string $resource, ?string $key = null,
        ?string $stsToken = null, ?array $params = null, ?array $headers = []): string {
        if (! $key)
            return '';
        $content = $method . "\n";
        if (isset($headers['Content-MD5']))
            $content .= $headers['Content-MD5'];
        $content .= "\n";
        if (isset($headers['Content-Type']))
            $content .= $headers['Content-Type'];
        $content .= "\n";
        $content .= $headers['Date'] . "\n";
        $content .= self::canonicalizedLOGHeaders($headers) . "\n";
        $content .= self::canonicalizedResource($resource, $params);
        return self::hmacSHA1($content, $key);
    }
}