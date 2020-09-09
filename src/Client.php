<?php
namespace Swango\Aliyun\Log;
use Swango\Environment;

/**
 * Aliyun_Log_Client class is the main class in the SDK.
 * It can be used to
 * communicate with LOG server to put/get data.
 *
 * @author log_dev
 */
class Client extends \BaseClient {
    protected const TIMEOUT = 5;
    const API_VERSION = '0.6.0';

    /**
     *
     * @var string aliyun accessKey
     */
    protected static $accessKey;

    /**
     *
     * @var string aliyun accessKeyId
     */
    protected static $accessKeyId;

    /**
     *
     * @var string LOG host
     */
    protected static $host;
    public static function setAliyunConfig(string $access_key_id, string $access_key_secret, string $host): void {
        self::$accessKeyId = $access_key_id;
        self::$accessKey = $access_key_secret;
        self::$host = $host;
    }

    /**
     *
     * @var string aliyun sts token
     */
    protected $stsToken;

    /**
     *
     * @var string the local machine ip address.
     */
    protected $source;

    /**
     * Aliyun_Log_Client constructor
     */
    public function __construct() {
        if (self::$accessKey === null) {
            [
                'access_key_id' => self::$accessKeyId,
                'access_key_secret' => self::$accessKey,
                'host' => self::$host
            ] = Environment::getConfig('aliyun/log');
        }
        $this->stsToken = '';
        $this->source = Util::getLocalIp();
    }

    /**
     * GMT format time string.
     *
     * @return string
     */
    protected function getGMT(): string {
        return gmdate('D, d M Y H:i:s') . ' GMT';
    }

    /**
     * Decodes a JSON string to a JSON Object.
     * Unsuccessful decode will cause an Exception.
     *
     * @return string
     * @throws Exception
     */
    protected function parseToJson($resBody, $requestId) {
        if (! $resBody)
            return NULL;
        return \Json::decodeAsObject($resBody);
    }
    protected function handleHttpErrorCode(int $code): bool {
        return true;
    }
    protected function getResponse(): array {
        /**
         *
         * @var \Swlib\Saber\Response $response
         */
        $response = $this->recv();
        $resp_heads = [];
        foreach ($response->getHeaders() as $key=>$values)
            $resp_heads[$key] = current($values);

        $responseCode = $response->getStatusCode();
        $resBody = $response->body;
        $requestId = $resp_heads['x-log-requestid'] ?? '';

        if ($responseCode === 200) {
            return [
                $resBody,
                $resp_heads
            ];
        } else {
            $exJson = $this->parseToJson($resBody, $requestId);
            if (isset($exJson->error_code) && isset($exJson->error_message)) {
                throw new Exception($exJson->error_code, $exJson->error_message, $requestId);
            } else {
                if (isset($exJson)) {
                    $exJson = ' The return json is ' . $resBody;
                } else {
                    $exJson = '';
                }
                throw new Exception('RequestError', "Request is failed. Http code is $responseCode.$exJson", $requestId);
            }
        }
    }

    /**
     *
     * @return array
     * @throws Exception
     */
    protected function sendRequest(string $method, string $host, string $path, ?\Psr\Http\Message\StreamInterface $body,
        $headers, array $params): void {
        $this->makeClient();
        $this->client->withMethod($method);
        $this->client->getUri()->withHost($host);
        $headers['Host'] = $host;
        $this->client->getUri()->withPath($path);
        if (! empty($params))
            $this->client->getUri()->withQuery($params);
        if ($method == 'POST' || $method == 'PUT')
            $this->client->withBody($body);
        $this->client->withHeaders($headers);
        $this->sendHttpRequest();
    }

    /**
     *
     * @return array
     * @throws Exception
     */
    protected function send(string $method, ?string $project, ?\Psr\Http\Message\StreamInterface $body, string $resource,
        array $params, array $headers = [], bool $auto_recv = true): array {
        if (isset($body)) {
            if (isset($headers['x-log-bodyrawsize']) == false)
                $headers['x-log-bodyrawsize'] = 0;
            $headers['Content-MD5'] = strtoupper(md5($body->__toString()));
        } else {
            $headers['x-log-bodyrawsize'] = 0;
        }

        $headers['x-log-apiversion'] = self::API_VERSION;
        $headers['x-log-signaturemethod'] = 'hmac-sha1';
        if (strlen($this->stsToken) > 0)
            $headers['x-acs-security-token'] = $this->stsToken;
        $headers['Date'] = $this->getGMT();
        ksort($params);
        $signature = Util::getRequestAuthorization($method, $resource, self::$accessKey, $this->stsToken, $params,
            $headers);
        $headers['Authorization'] = 'LOG ' . self::$accessKeyId . ":$signature";

        $path = $resource;
        if (is_null($project))
            $host = self::$host;
        else
            $host = $project . '.' . self::$host;
        $this->sendRequest($method, $host, $path, $body, $headers, $params);
        return $auto_recv ? $this->getResponse() : [];
    }
}