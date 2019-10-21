<?php
namespace Swango\Aliyun\Log\Models\Response;

/**
 * The response of the PutLogs API from log service.
 *
 * @author log service dev
 */
class PutLogs extends \Swango\Aliyun\Log\Models\Response {
    /**
     * Aliyun_Log_Models_PutLogsResponse constructor
     *
     * @param array $header
     *            PutLogs HTTP response header
     */
    public function __construct(array $headers) {
        parent::__construct($headers);
    }
}
