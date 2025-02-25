<?php
namespace Swango\Aliyun\Log\Action;
class PutLogs extends \Swango\Aliyun\Log\Client {

    /**
     * Put logs to Log Service.
     * Unsuccessful opertaion will cause an Exception.
     *
     * @param \Swango\Aliyun\Log\Models\Request\PutLogs $request
     *            the PutLogs request parameters class
     * @throws \Swango\Aliyun\Log\Exception
     * @return \Swango\Aliyun\Log\Models\Response\PutLogs
     */
    public function execute(\Swango\Aliyun\Log\Models\Request\PutLogs $request): \Swango\Aliyun\Log\Models\Response\PutLogs {
        if (count($request->getLogitems()) > 4096)
            throw new \Swango\Aliyun\Log\Exception('InvalidLogSize',
                "logItems' length exceeds maximum limitation: 4096 lines.");

        $logGroup = new \Swango\Aliyun\Log\SlsProto\Group();
        $topic = $request->getTopic() ?? '';
        $logGroup->setTopic($request->getTopic());
        $source = $request->getSource();

        if (! $source)
            $source = $this->source;
        $logGroup->setSource($source);
        $logitems = $request->getLogitems();
        foreach ($logitems as $logItem) {
            $log = new \Swango\Aliyun\Log\SlsProto\Log();
            $log->setTime($logItem->getTime());
            $content = $logItem->getContents();
            foreach ($content as $key=>$value) {
                $content = new \Swango\Aliyun\Log\SlsProto\Content();
                $content->setKey($key);
                $content->setValue($value);
                $log->addContents($content);
            }

            $logGroup->addLogs($log);
        }

        $mem = \Swango\Aliyun\Log\Util::toBytes($logGroup);
        unset($logGroup);

        $bodySize = $mem->getSize();
        if ($bodySize > 3 * 1024 * 1024) // 3 MB
            throw new \Swango\Aliyun\Log\Exception('InvalidLogSize', 'logItems\' size exceeds maximum limitation: 3 MB.');
        $params = [];
        $headers = [
            'Content-Type' => 'application/x-protobuf'
        ];

        $logstore = $request->getLogstore() ?? '';
        $project = $request->getProject() ?? '';
        $shardKey = $request->getShardKey();
        $resource = '/logstores/' . $logstore . (isset($shardKey) ? '/shards/route' : '/shards/lb');
        if ($shardKey)
            $params['key'] = $shardKey;
        [
            $resp,
            $header
        ] = $this->send('POST', $project, $mem, $resource, $params, $headers);
        return new \Swango\Aliyun\Log\Models\Response\PutLogs($header);
    }
    protected function writeLogWithStream(\Psr\Http\Message\StreamInterface $log_stream): void {
        // Not write log because it's large and unreadable
    }
    protected function writeLog(string &$log_string): void {
        // Not write log because it's large and unreadable
    }
}
