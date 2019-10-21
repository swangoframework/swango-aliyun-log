<?php
namespace Swango\Aliyun\Log\Action;
class GetLogHistograms extends \Swango\Aliyun\Log\Client {
    /**
     * Get histograms of requested query from log service.
     * Unsuccessful opertaion will cause an Exception.
     *
     * @param \AliYun\Log\Models\Request\GetHistograms $request
     *            the GetHistograms request parameters class.
     * @throws \AliYun\Log\Exception
     * @return \AliYun\Log\Models\Response\GetHistograms
     */
    public function execute(\Swango\Aliyun\Log\Models\Request\GetHistograms $request): \Swango\Aliyun\Log\Models\Response\GetHistograms {
        $headers = [
            'Content-Type' => 'application/x-protobuf'
        ];
        $params = [
            'type' => 'histogram'
        ];
        if ($request->getTopic() !== null)
            $params['topic'] = $request->getTopic();
        if ($request->getFrom() !== null)
            $params['from'] = $request->getFrom();
        if ($request->getTo() !== null)
            $params['to'] = $request->getTo();
        if ($request->getQuery() !== null)
            $params['query'] = $request->getQuery();

        $logstore = $request->getLogstore() ?? '';
        $project = $request->getProject() ?? '';
        $resource = "/logstores/$logstore";
        [
            $resp,
            $header
        ] = $this->send('GET', $project, NULL, $resource, $params, $headers);
        $requestId = $header['x-log-requestid'] ?? '';
        $resp = $this->parseToJson($resp, $requestId);
        return new \Swango\Aliyun\Log\Models\Response\GetHistograms($resp, $header);
    }
}