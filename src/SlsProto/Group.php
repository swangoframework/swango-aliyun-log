<?php
namespace Swango\Aliyun\Log\SlsProto; // message LogGroup
class Group {
    private $_unknown;
    function write(\Psr\Http\Message\StreamInterface $mem): void {
        if (! $this->validateRequired())
            throw new \Exception('Required fields are missing');
        if (! is_null($this->logs_))
            foreach ($this->logs_ as $v) {
                $mem->write("\x0a");
                Protobuf::write_varint($mem, $v->size()); // message
                $v->write($mem);
            }
        if (! is_null($this->category_)) {
            $mem->write("\x12");
            Protobuf::write_varint($mem, strlen($this->category_));
            $mem->write($this->category_);
        }
        if (! is_null($this->topic_)) {
            $mem->write("\x1a");
            Protobuf::write_varint($mem, strlen($this->topic_));
            $mem->write($this->topic_);
        }
        if (! is_null($this->source_)) {
            $mem->write('"');
            Protobuf::write_varint($mem, strlen($this->source_));
            $mem->write($this->source_);
        }
    }
    public function size() {
        $size = 0;
        if (! is_null($this->logs_))
            foreach ($this->logs_ as $v) {
                $l = $v->size();
                $size += 1 + Protobuf::size_varint($l) + $l;
            }
        if (! is_null($this->category_)) {
            $l = strlen($this->category_);
            $size += 1 + Protobuf::size_varint($l) + $l;
        }
        if (! is_null($this->topic_)) {
            $l = strlen($this->topic_);
            $size += 1 + Protobuf::size_varint($l) + $l;
        }
        if (! is_null($this->source_)) {
            $l = strlen($this->source_);
            $size += 1 + Protobuf::size_varint($l) + $l;
        }
        return $size;
    }
    public function validateRequired() {
        return true;
    }
    public function __toString() {
        return '' . Protobuf::toString('unknown', $this->_unknown) . Protobuf::toString('logs_', $this->logs_) .
             Protobuf::toString('category_', $this->category_) . Protobuf::toString('topic_', $this->topic_) .
             Protobuf::toString('source_', $this->source_);
    }

    // repeated .Log Logs = 1;
    private $logs_ = null;
    public function clearLogs() {
        $this->logs_ = null;
    }
    public function getLogsCount() {
        if ($this->logs_ === null)
            return 0;
        else
            return count($this->logs_);
    }
    public function getLogs($index) {
        return $this->logs_[$index];
    }
    public function getLogsArray() {
        if ($this->logs_ === null)
            return [];
        else
            return $this->logs_;
    }
    public function setLogs($index, $value) {
        $this->logs_[$index] = $value;
    }
    public function addLogs($value) {
        $this->logs_[] = $value;
    }
    public function addAllLogs(array $values) {
        foreach ($values as $value) {
            $this->logs_[] = $value;
        }
    }

    // optional string Category = 2;
    private $category_ = null;
    public function clearCategory() {
        $this->category_ = null;
    }
    public function hasCategory() {
        return $this->category_ !== null;
    }
    public function getCategory() {
        if ($this->category_ === null)
            return "";
        else
            return $this->category_;
    }
    public function setCategory($value) {
        $this->category_ = $value;
    }

    // optional string Topic = 3;
    private $topic_ = null;
    public function clearTopic() {
        $this->topic_ = null;
    }
    public function hasTopic() {
        return $this->topic_ !== null;
    }
    public function getTopic() {
        if ($this->topic_ === null)
            return "";
        else
            return $this->topic_;
    }
    public function setTopic($value) {
        $this->topic_ = $value;
    }

    // optional string Source = 4;
    private $source_ = null;
    public function clearSource() {
        $this->source_ = null;
    }
    public function hasSource() {
        return $this->source_ !== null;
    }
    public function getSource() {
        if ($this->source_ === null)
            return "";
        else
            return $this->source_;
    }
    public function setSource($value) {
        $this->source_ = $value;
    }

    // @@protoc_insertion_point(class_scope:LogGroup)
}
