<?php
namespace Swango\Aliyun\Log\Exception;
class IncompleteResultsException extends \Swango\Aliyun\Log\Exception {
    public function __construct(string $requestId) {
        parent::__construct('IncompleteResults', 'Incomplete results. Change query or try again.', $requestId);
    }
    public function getSwangoCnMsg(): string {
        return '事件引擎查询结果不完整，请重试或改变筛选条件';
    }
    public function getSwangoCode(): int {
        return 200;
    }
}