<?php
namespace Swango\Aliyun\Log\Models\Request;

/**
 * The request used to get histograms of a query from log service.
 *
 * @author log service dev
 */
class GetHistograms extends \Swango\Aliyun\Log\Models\Request {

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
     * Aliyun_Log_Models_GetHistogramsRequest constructor
     *
     * @param string $project
     *            project name
     * @param string $logstore
     *            logstore name
     * @param integer $from
     *            the begin time
     * @param integer $to
     *            the end time
     * @param string $topic
     *            topic name of logs
     * @param string $query
     *            user defined query
     */
    public function __construct(string $project = null, string $logstore = null, int $from = null, int $to = null, string $topic = null,
        string $query = null) {
        parent::__construct($project);
        $this->logstore = $logstore;
        $this->from = $from;
        $this->to = $to;
        $this->topic = $topic;
        $this->query = $query;
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
    public function setLogstore(string $logstore): ?string {
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
    public function setTopic($topic): ?string {
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
}
