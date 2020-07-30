<?php
namespace Swango\Aliyun\Log\SearchBuild;
class In extends \Swango\Aliyun\Log\SearchBuild {
    /**
     * 闭区间
     *
     * @var integer
     */
    const BOUNDED = 1;
    /**
     * 开区间
     *
     * @var integer
     */
    const UNBOUNDED = 2;
    private $min, $max, $left_boundary, $right_boundary;
    public function __construct(int $min, int $max, int $left_boundary = self::BOUNDED, int $right_boundary = self::BOUNDED) {
        $this->min = $min;
        $this->max = $max;
        $this->left_boundary = $left_boundary;
        $this->right_boundary = $right_boundary;
    }
    public function build(): string {
        $left = $this->left_boundary === self::BOUNDED ? '[' : '(';
        $right = $this->right_boundary === self::BOUNDED ? ']' : ')';
        return " in {$left}{$this->min} {$this->max}{$right}";
    }
}