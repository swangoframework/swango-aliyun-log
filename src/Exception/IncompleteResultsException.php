<?php
namespace Swango\Aliyun\Log\Exception;
class IncompleteResultsException extends \Swango\Aliyun\Log\Exception {
    public function __construct(string $requestId) {
        parent::__construct('IncompleteResults', 'Incomplete results. Change query or try again.', $requestId);
    }
}