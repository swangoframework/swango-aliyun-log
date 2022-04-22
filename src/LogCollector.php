<?php
namespace Swango\Aliyun\Log;
use Swango\Aliyun\Log;
class LogCollector {
    private static \Swoole\Coroutine\Channel $channel;
    private static bool $is_stopped = false;
    public static function start(int $concurrency = 8, int $queue_size = 128) {
        self::$channel = new \Swoole\Coroutine\Channel($queue_size);
        \Swoole\Coroutine\parallel($concurrency, '\\Swango\\Aliyun\\Log\\LogCollector::loop');
    }
    public static function stop() {
        if (! self::$is_stopped && isset(self::$channel)) {
            self::$is_stopped = true;
            if (self::$channel->isEmpty()) {
                self::$channel->close();
            }
        }
    }
    public static function loop() {
        while (($request = self::$channel->pop()) instanceof Log\Models\Request\PutLogs) {
            self::sendLog($request);
            unset($request);
            if (self::$is_stopped && self::$channel->isEmpty()) {
                self::$channel->close();
                break;
            }
        }
    }
    public static function sendLog(Log\Models\Request\PutLogs $request): bool {
        try {
            $client = new Log\Action\PutLogs();
            $client->execute($request);
            return true;
        } catch (\ApiErrorException\ApiTimeoutException $e) {
            // 连接超时的请求，放回队列
            \FileLog::logThrowable($e, \Swango\Environment::getDir()->log . 'error/', 'LogCollector');
            if (isset(self::$channel) && ! self::$channel->push($request, 0.001)) {
                trigger_error('Channel push error: ' . self::$channel->errCode);
            }
        } catch (\Throwable $e) {
            $title = 'LogCollector';
            if ('Required fields are missing' === $e->getMessage()) {
                $title .= ' ' . \Json::encode($request->getLogItems()) . ' ';
            }
            \FileLog::logThrowable($e, \Swango\Environment::getDir()->log . 'error/', $title);
        }
        return false;
    }
    public static function buildLogItem(int $time, array &$data): Log\Models\LogItem {
        return new Log\Models\LogItem($time, $data);
    }
    public static function addLog(string             $project,
                                  string             $log_store,
                                  string             $topic,
                                  Log\Models\LogItem ...$log_item): bool {
        $request = new Log\Models\Request\PutLogs($project, $log_store, $topic, logitems: $log_item);
        if (isset(self::$channel) && ! self::$is_stopped) {
            $result = self::$channel->push($request, 0.001);
            if (! $result) {
                trigger_error('Channel push error: ' . self::$channel->errCode);
            }
            return $result;
        } else {
            return self::sendLog($request);
        }
    }
}