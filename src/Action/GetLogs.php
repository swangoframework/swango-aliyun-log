<?php
namespace Swango\Aliyun\Log\Action;
class GetLogs extends \Swango\Aliyun\Log\Client {
    protected const TIMEOUT = 120, USE_POOL = false;
    private function _execute(\Swango\Aliyun\Log\Models\Request\GetLogs $request): void {
        $headers = [
            'Content-Type' => 'application/x-protobuf'
        ];
        $params = [
            'type' => 'log'
        ];
        if ($request->getTopic() !== null)
            $params['topic'] = $request->getTopic();
        if ($request->getFrom() !== null)
            $params['from'] = $request->getFrom();
        if ($request->getTo() !== null)
            $params['to'] = $request->getTo();
        if ($request->getQuery() !== null)
            $params['query'] = $request->getQuery();
        if ($request->getLine() !== null)
            $params['line'] = $request->getLine();
        if ($request->getOffset() !== null)
            $params['offset'] = $request->getOffset();
        if ($request->getReverse() !== null)
            $params['reverse'] = $request->getReverse() ? 'true' : 'false';
        $logstore = $request->getLogstore() ?? '';
        $project = $request->getProject() ?? '';
        $resource = "/logstores/$logstore";
        $this->send('GET', $project, NULL, $resource, $params, $headers, false);
    }
    /**
     * Get logs from Log service.
     * Unsuccessful opertaion will cause an Exception.
     *
     * @param \Swango\Aliyun\Log\Models\Request\GetLogs $request
     *            the GetLogs request parameters class.
     * @throws \Swango\Aliyun\Log\Exception
     * @return \Swango\Aliyun\Log\Models\Response\GetLogs
     */
    public function execute(\Swango\Aliyun\Log\Models\Request\GetLogs $request): \Swango\Aliyun\Log\Models\Response\GetLogs {
        $this->_execute($request);
        [
            $resp,
            $header
        ] = $this->getResponse();
        $requestId = $header['x-log-requestid'];
        if ($header['x-log-progress'] !== 'Complete')
            throw new \Swango\Aliyun\Log\Exception\IncompleteResultsException($requestId);
        $resp = $this->parseToJson($resp, $requestId);
        return new \Swango\Aliyun\Log\Models\Response\GetLogs($resp, $header);
    }
    public function setTimeout(float $timeout): self {
        $this->client->withTimeout($timeout);
        return $this;
    }
    public function deferExecute(\Swango\Aliyun\Log\Models\Request\GetLogs $request): self {
        $this->_execute($request);
        return $this;
    }
    public function recvResult(): array {
        [
            $resp,
            $header
        ] = $this->getResponse();
        $requestId = $header['x-log-requestid'];
        if ($header['x-log-progress'] !== 'Complete')
            throw new \Swango\Aliyun\Log\Exception\IncompleteResultsException($requestId);
        return $this->parseToJson($resp, $requestId);
    }
}