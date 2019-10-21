<?php
namespace Swango\Aliyun\Log\SlsProto;

/**
 * Class to aid in the parsing and creating of Protocol Buffer Messages
 * This class should be included by the developer before they use a
 * generated protobuf class.
 *
 * @author Andrew Brampton
 *
 */
class Protobuf {
    const TYPE_DOUBLE = 1; // double, exactly eight bytes on the wire.
    const TYPE_FLOAT = 2; // float, exactly four bytes on the wire.
    const TYPE_INT64 = 3; // int64, varint on the wire. Negative numbers
                          // take 10 bytes. Use TYPE_SINT64 if negative
                          // values are likely.
    const TYPE_UINT64 = 4; // uint64, varint on the wire.
    const TYPE_INT32 = 5; // int32, varint on the wire. Negative numbers
                          // take 10 bytes. Use TYPE_SINT32 if negative
                          // values are likely.
    const TYPE_FIXED64 = 6; // uint64, exactly eight bytes on the wire.
    const TYPE_FIXED32 = 7; // uint32, exactly four bytes on the wire.
    const TYPE_BOOL = 8; // bool, varint on the wire.
    const TYPE_STRING = 9; // UTF-8 text.
    const TYPE_GROUP = 10; // Tag-delimited message. Deprecated.
    const TYPE_MESSAGE = 11; // Length-delimited message.
    const TYPE_BYTES = 12; // Arbitrary byte array.
    const TYPE_UINT32 = 13; // uint32, varint on the wire
    const TYPE_ENUM = 14; // Enum, varint on the wire
    const TYPE_SFIXED32 = 15; // int32, exactly four bytes on the wire
    const TYPE_SFIXED64 = 16; // int64, exactly eight bytes on the wire
    const TYPE_SINT32 = 17; // int32, ZigZag-encoded varint on the wire
    const TYPE_SINT64 = 18; // int64, ZigZag-encoded varint on the wire

    /**
     * Returns a string representing this wiretype
     */
    public static function get_wiretype($wire_type) {
        switch ($wire_type) {
            case 0 :
                return 'varint';
            case 1 :
                return '64-bit';
            case 2 :
                return 'length-delimited';
            case 3 :
                return 'group start';
            case 4 :
                return 'group end';
            case 5 :
                return '32-bit';
            default :
                return 'unknown';
        }
    }

    /**
     * Returns how big (in bytes) this number would be as a varint
     */
    public static function size_varint($i) {
        if ($i < 0x80)
            return 1;
        if ($i < 0x4000)
            return 2;
        if ($i < 0x200000)
            return 3;
        if ($i < 0x10000000)
            return 4;
        if ($i < 0x800000000)
            return 5;
        if ($i < 0x40000000000)
            return 6;
        if ($i < 0x2000000000000)
            return 7;
        if ($i < 0x100000000000000)
            return 8;
        if ($i < 0x8000000000000000)
            return 9;
    }

    /**
     * Writes a varint to $mem
     * returns the number of bytes written
     *
     * @param \Psr\Http\Message\StreamInterface $mem
     *
     * @param $i The
     *            int to encode
     * @return The number of bytes written
     */
    public static function write_varint(\Psr\Http\Message\StreamInterface $mem, int $i): int {
        $len = 0;
        do {
            $v = $i & 0x7F;
            $i = $i >> 7;

            if ($i != 0)
                $v |= 0x80;

            if ($mem->write(chr($v)) !== 1)
                throw new \Exception("write_varint(): Error writing byte");

            $len ++;
        } while ( $i != 0 );

        return $len;
    }

    /**
     * Used to aid in pretty printing of Protobuf objects
     */
    private static $print_depth = 0;
    private static $indent_char = "\t";
    private static $print_limit = 50;
    public static function toString($key, $value) {
        if (is_null($value))
            return;
        $ret = str_repeat(self::$indent_char, self::$print_depth) . "$key=>";
        if (is_array($value)) {
            $ret .= "array(\n";
            self::$print_depth ++;
            foreach ($value as $i=>$v)
                $ret .= self::toString("[$i]", $v);
            self::$print_depth --;
            $ret .= str_repeat(self::$indent_char, self::$print_depth) . ")\n";
        } else {
            if (is_object($value)) {
                self::$print_depth ++;
                $ret .= get_class($value) . "(\n";
                $ret .= $value->__toString() . "\n";
                self::$print_depth --;
                $ret .= str_repeat(self::$indent_char, self::$print_depth) . ")\n";
            } elseif (is_string($value)) {
                $safevalue = addcslashes($value, "\0..\37\177..\377");
                if (strlen($safevalue) > self::$print_limit) {
                    $safevalue = substr($safevalue, 0, self::$print_limit) . '...';
                }

                $ret .= '"' . $safevalue . '" (' . strlen($value) . " bytes)\n";
            } elseif (is_bool($value)) {
                $ret .= ($value ? 'true' : 'false') . "\n";
            } else {
                $ret .= (string)$value . "\n";
            }
        }
        return $ret;
    }
}
