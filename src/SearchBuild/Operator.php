<?php
namespace Swango\Aliyun\Log\SearchBuild;
class Operator extends \Swango\Aliyun\Log\SearchBuild {
    const Operator_Is = ':';
    const Operator_EqualTo = ' = ';
    const Operator_GreaterThan = ' > ';
    const Operator_GreaterThanOrEqualTo = ' >= ';
    const Operator_LessThan = ' < ';
    const Operator_LessThanOrEqualTo = ' <= ';
    private $value, $operator;
    public function __construct(string $value, string $operator = self::Operator_Is) {
        $this->value = $value;
        $this->operator = $operator;
    }
    public function build(): string {
        if (strpos($this->value, ' ') === false)
            return "{$this->operator}{$this->value}";
        return "{$this->operator}\"{$this->value}\"";
    }
}