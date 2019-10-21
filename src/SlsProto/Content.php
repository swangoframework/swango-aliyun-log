<?php
namespace Swango\Aliyun\Log\SlsProto;
class Content {
    private $_unknown;
    function write(\Psr\Http\Message\StreamInterface $mem): void {
        if (! $this->validateRequired())
            throw new \Exception('Required fields are missing');
        if (! is_null($this->key_)) {
            $mem->write("\x0a");
            Protobuf::write_varint($mem, strlen($this->key_));
            $mem->write($this->key_);
        }
        if (! is_null($this->value_)) {
            $mem->write("\x12");
            Protobuf::write_varint($mem, strlen($this->value_));
            $mem->write($this->value_);
        }
    }
    public function size(): int {
        $size = 0;
        if (! is_null($this->key_)) {
            $l = strlen($this->key_);
            $size += 1 + Protobuf::size_varint($l) + $l;
        }
        if (! is_null($this->value_)) {
            $l = strlen($this->value_);
            $size += 1 + Protobuf::size_varint($l) + $l;
        }
        return $size;
    }
    public function validateRequired(): bool {
        if ($this->key_ === null)
            return false;
        if ($this->value_ === null)
            return false;
        return true;
    }
    public function __toString(): string {
        return '' . Protobuf::toString('unknown', $this->_unknown) . Protobuf::toString('key_', $this->key_) .
             Protobuf::toString('value_', $this->value_);
    }

    // required string Key = 1;
    private $key_ = null;
    public function clearKey(): void {
        $this->key_ = null;
    }
    public function hasKey(): bool {
        return $this->key_ !== null;
    }
    public function getKey(): string {
        if ($this->key_ === null)
            return "";
        else
            return $this->key_;
    }
    public function setKey($value): void {
        $this->key_ = $value;
    }

    // required string Value = 2;
    private $value_ = null;
    public function clearValue(): void {
        $this->value_ = null;
    }
    public function hasValue(): bool {
        return $this->value_ !== null;
    }
    public function getValue(): string {
        if ($this->value_ === null)
            return "";
        else
            return $this->value_;
    }
    public function setValue($value): void {
        $this->value_ = $value;
    }

    // @@protoc_insertion_point(class_scope:Log.Content)
}