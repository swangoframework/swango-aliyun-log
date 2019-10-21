<?php
namespace Swango\Aliyun\Log\SlsProto;

// message LogGroupList
class GroupList {
    private $_unknown;
    function write(\Psr\Http\Message\StreamInterface $mem) {
        if (! $this->validateRequired())
            throw new \Exception('Required fields are missing');
        if (! is_null($this->logGroupList_))
            foreach ($this->logGroupList_ as $v) {
                $mem->write("\x0a");
                Protobuf::write_varint($mem, $v->size()); // message
                $v->write($mem);
            }
    }
    public function size() {
        $size = 0;
        if (! is_null($this->logGroupList_))
            foreach ($this->logGroupList_ as $v) {
                $l = $v->size();
                $size += 1 + Protobuf::size_varint($l) + $l;
            }
        return $size;
    }
    public function validateRequired() {
        return true;
    }
    public function __toString() {
        return '' . Protobuf::toString('unknown', $this->_unknown) .
             Protobuf::toString('logGroupList_', $this->logGroupList_);
    }

    // repeated .LogGroup logGroupList = 1;
    private $logGroupList_ = null;
    public function clearLogGroupList() {
        $this->logGroupList_ = null;
    }
    public function getLogGroupListCount() {
        if ($this->logGroupList_ === null)
            return 0;
        else
            return count($this->logGroupList_);
    }
    public function getLogGroupList($index) {
        return $this->logGroupList_[$index];
    }
    public function getLogGroupListArray() {
        if ($this->logGroupList_ === null)
            return [];
        else
            return $this->logGroupList_;
    }
    public function setLogGroupList($index, $value) {
        $this->logGroupList_[$index] = $value;
    }
    public function addLogGroupList($value) {
        $this->logGroupList_[] = $value;
    }
    public function addAllLogGroupList(array $values) {
        foreach ($values as $value) {
            $this->logGroupList_[] = $value;
        }
    }

    // @@protoc_insertion_point(class_scope:LogGroupList)
}