<?php
namespace Swango\Aliyun\Log\SlsProto;
// message Log
class Log {
    private $_unknown;
    function write(\Psr\Http\Message\StreamInterface $mem): void {
        if (! $this->validateRequired())
            throw new \Exception('Required fields are missing');
        if (! is_null($this->time_)) {
            $mem->write("\x08");
            Protobuf::write_varint($mem, $this->time_);
        }
        if (! is_null($this->contents_))
            foreach ($this->contents_ as $v) {
                $mem->write("\x12");
                Protobuf::write_varint($mem, $v->size()); // message
                $v->write($mem);
            }
    }
    public function size() {
        $size = 0;
        if (! is_null($this->time_)) {
            $size += 1 + Protobuf::size_varint($this->time_);
        }
        if (! is_null($this->contents_))
            foreach ($this->contents_ as $v) {
                $l = $v->size();
                $size += 1 + Protobuf::size_varint($l) + $l;
            }
        return $size;
    }
    public function validateRequired() {
        if ($this->time_ === null)
            return false;
        return true;
    }
    public function __toString() {
        return '' . Protobuf::toString('unknown', $this->_unknown) . Protobuf::toString('time_', $this->time_) .
             Protobuf::toString('contents_', $this->contents_);
    }

    // required uint32 Time = 1;
    private $time_ = null;
    public function clearTime() {
        $this->time_ = null;
    }
    public function hasTime() {
        return $this->time_ !== null;
    }
    public function getTime() {
        if ($this->time_ === null)
            return 0;
        else
            return $this->time_;
    }
    public function setTime($value) {
        $this->time_ = $value;
    }

    // repeated .Log.Content Contents = 2;
    private $contents_ = null;
    public function clearContents() {
        $this->contents_ = null;
    }
    public function getContentsCount() {
        if ($this->contents_ === null)
            return 0;
        else
            return count($this->contents_);
    }
    public function getContents($index) {
        return $this->contents_[$index];
    }
    public function getContentsArray() {
        if ($this->contents_ === null)
            return [];
        else
            return $this->contents_;
    }
    public function setContents($index, $value) {
        $this->contents_[$index] = $value;
    }
    public function addContents($value) {
        $this->contents_[] = $value;
    }
    public function addAllContents(array $values) {
        foreach ($values as $value) {
            $this->contents_[] = $value;
        }
    }

    // @@protoc_insertion_point(class_scope:Log)
}