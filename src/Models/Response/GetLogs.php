<?php
namespace Swango\Aliyun\Log\Models\Response;

/**
 * The response of the GetLog API from log service.
 *
 * @author log service dev
 */
class GetLogs extends \Swango\Aliyun\Log\Models\Response {

    /**
     *
     * @var integer log number
     */
    private $count;

    /**
     *
     * @var string logs query status(Complete or InComplete)
     */
    private $progress;

    /**
     *
     * @var array Aliyun_Log_Models_QueriedLog array, all log data
     */
    private $logs;

    /**
     * Aliyun_Log_Models_GetLogsResponse constructor
     *
     * @param array $resp
     *            GetLogs HTTP response body
     * @param array $header
     *            GetLogs HTTP response header
     */
    public function __construct(array $resp, array $header) {
        parent::__construct($header);
        $this->count = $header['x-log-count'];
        $this->progress = $header['x-log-progress'];
        $this->logs = [];
        foreach ($resp as $data) {
            $contents = $data;
            $time = $data->{'__time__'};
            $source = $data->{'__source__'};
            unset($contents->{'__time__'});
            unset($contents->{'__source__'});
            $this->logs[] = new \Swango\Aliyun\Log\Models\QueriedLog($time, $source, $contents);
        }
    }

    /**
     * Get log number from the response
     *
     * @return integer log number
     */
    public function getCount(): int {
        return $this->count;
    }

    /**
     * Check if the get logs query is completed
     *
     * @return bool true if this logs query is completed
     */
    public function isCompleted(): bool {
        return $this->progress == 'Complete';
    }

    /**
     * Get all logs from the response
     *
     * @return Swango\Aliyun\Log\Models\QueriedLog[]
     */
    public function getLogs(): array {
        return $this->logs;
    }
}
