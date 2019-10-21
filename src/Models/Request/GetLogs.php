<?php
namespace Swango\Aliyun\Log\Models\Request;

/**
 * The request used to get logs by a query from log service.
 *
 * @author log service dev
 */
class GetLogs extends \Swango\Aliyun\Log\Models\Request {

    /**
     *
     * @var string logstore name
     */
    private $logstore;

    /**
     *
     * @var string topic name of logs
     */
    private $topic;

    /**
     *
     * @var integer the begin time
     */
    private $from;

    /**
     *
     * @var integer the end time
     */
    private $to;

    /**
     *
     * @var string user defined query
     */
    private $query;

    /**
     *
     * @var integer max line number of return logs
     */
    private $line;

    /**
     *
     * @var integer line offset of return logs
     */
    private $offset;

    /**
     *
     * @var bool if reverse is set to true, the query will return the latest logs first
     */
    private $reverse;

    /**
     * Aliyun_Log_Models_GetLogsRequest Constructor
     *
     * @param string $project
     *            project name
     * @param string $logStore
     *            logstore name
     * @param integer $from
     *            the begin time
     * @param integer $to
     *            the end time
     * @param string $topic
     *            topic name of logs
     * @param string $query
     *            user defined query
     * @param integer $line
     *            query return line number
     * @param integer $offset
     *            the log offset to return
     * @param bool $reverse
     *            if reverse is set to true, the query will return the latest logs first
     */
    public function __construct(string $project = null, string $logstore = null, int $from = null, int $to = null, string $topic = null,
        string $query = null, int $line = 100, int $offset = 0, bool $reverse = false) {
        parent::__construct($project);

        $this->logstore = $logstore;
        $this->from = $from;
        $this->to = $to;
        $this->topic = $topic;
        $this->query = $query;
        $this->line = $line;
        $this->offset = $offset;
        $this->reverse = $reverse;
    }

    /**
     * Get logstore name
     *
     * @return string logstore name
     */
    public function getLogstore(): ?string {
        return $this->logstore;
    }

    /**
     * Set logstore name
     *
     * @param string $logstore
     *            logstore name
     */
    public function setLogstore(string $logstore): void {
        $this->logstore = $logstore;
    }

    /**
     * Get topic name
     *
     * @return string topic name
     */
    public function getTopic(): ?string {
        return $this->topic;
    }

    /**
     * Set topic name
     *
     * @param string $topic
     *            topic name
     */
    public function setTopic(string $topic): void {
        $this->topic = $topic;
    }

    /**
     * Get begin time
     *
     * @return integer begin time
     */
    public function getFrom(): ?int {
        return $this->from;
    }

    /**
     * Set begin time
     *
     * @param integer $from
     *            begin time
     */
    public function setFrom(int $from): void {
        $this->from = $from;
    }

    /**
     * Get end time
     *
     * @return integer end time
     */
    public function getTo(): ?int {
        return $this->to;
    }

    /**
     * Set end time
     *
     * @param integer $to
     *            end time
     */
    public function setTo(int $to): void {
        $this->to = $to;
    }

    /**
     * Get user defined query
     *
     * @return string user defined query
     */
    public function getQuery(): ?string {
        return $this->query;
    }

    /**
     * Set user defined query
     *
     * @param string $query
     *            user defined query
     */
    public function setQuery(string $query): void {
        $this->query = $query;
    }

    /**
     * Get max line number of return logs
     *
     * @return integer max line number of return logs
     */
    public function getLine(): ?int {
        return $this->line;
    }

    /**
     * Set max line number of return logs
     *
     * @param integer $line
     *            max line number of return logs
     */
    public function setLine(int $line): void {
        $this->line = $line;
    }

    /**
     * Get line offset of return logs
     *
     * @return integer line offset of return logs
     */
    public function getOffset(): ?int {
        return $this->offset;
    }

    /**
     * Set request line offset of return logs
     *
     * @param integer $offset
     *            line offset of return logs
     */
    public function setOffset($offset): void {
        $this->offset = $offset;
    }

    /**
     * Get request reverse flag
     *
     * @return bool reverse flag
     */
    public function getReverse(): bool {
        return $this->reverse;
    }

    /**
     * Set request reverse flag
     *
     * @param bool $reverse
     *            reverse flag
     */
    public function setReverse(bool $reverse): void {
        $this->reverse = $reverse;
    }
}
