<?php
namespace Swango\Aliyun\Log\Models;

/**
 * The base response class of all log response.
 *
 * @author log service dev
 */
class Response {

    /**
     *
     * @var array HTTP response header
     */
    private $headers;

    /**
     * Aliyun_Log_Models_Response constructor
     *
     * @param array $header
     *            HTTP response header
     */
    public function __construct(array $headers) {
        $this->headers = $headers;
    }

    /**
     * Get all http headers
     *
     * @return array HTTP response header
     */
    public function getAllHeaders(): array {
        return $this->headers;
    }

    /**
     * Get specified http header
     *
     * @param string $key
     *            key to get header
     *
     * @return string HTTP response header. '' will be return if not set.
     */
    public function getHeader(string $key): string {
        return isset($this->headers[$key]) ? $this->headers[$key] : '';
    }

    /**
     * Get the request id of the response.
     * '' will be return if not set.
     *
     * @return string request id
     */
    public function getRequestId(): string {
        return isset($this->headers['x-log-requestid']) ? $this->headers['x-log-requestid'] : '';
    }
}
