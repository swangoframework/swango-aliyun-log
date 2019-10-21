<?php
namespace Swango\Aliyun\Log\Models;
/**
 * The QueriedLog is a log of the Aliyun_Log_Models_GetLogsResponse which obtained from the log.
 *
 * @author log service dev
 */
class QueriedLog {

    /**
     *
     * @var integer log timestamp
     */
    private $time;

    /**
     *
     * @var string log source
     */
    private $source;

    /**
     *
     * @var object log contents, content many key/value pair
     */
    private $contents;

    public function __get(string $key) {
        return property_exists($this->contents, $key) ? $this->contents->{$key} : null;
    }
    public function __isset(string $key): bool {
        return property_exists($this->contents, $key) && isset($this->contents->{$key});
    }

    /**
     * Aliyun_Log_Models_QueriedLog constructor
     *
     * @param integer $time
     *            log time stamp
     * @param string $source
     *            log source
     * @param object $contents
     *            log contents, content many key/value pair
     */
    public function __construct(int $time, string $source, \stdClass $contents) {
        $this->time = $time;
        $this->source = $source;
        $this->contents = $contents;
    }

    /**
     * Get log source
     *
     * @return string log source
     */
    public function getSource(): string {
        return $this->source;
    }

    /**
     * Get log time
     *
     * @return integer log time
     */
    public function getTime(): int {
        return $this->time;
    }

    /**
     * Get log contents, content many key/value pair.
     *
     * @return object log contents
     */
    public function getContents(): \stdClass {
        return $this->contents;
    }
}
