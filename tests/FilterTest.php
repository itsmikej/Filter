<?php

namespace Imj\test;

use Imj\Filter;

/**
 * Class FrequencyTest
 * @package Imj
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testValidate()
    {
        $str = ' str';
        $this->assertEquals('str', Filter::validate($str, Filter::STRING_TYPE));
        // ...
    }

    public function testString()
    {
        $str = ' str';
        $this->assertEquals('str', Filter::string($str));

        $str = 'abcdefg';
        $this->assertEquals('ab', Filter::string($str, ['length'=>2]));

        $str = 'abc';
        $this->assertEquals(null, Filter::string($str, ['regex'=>"/\d+/"]));
    }

    public function testInt()
    {
        $int = '10';
        $this->assertEquals(10, Filter::int($int, ['max'=>11, 'min'=>8]));

        $int = '10';
        $this->assertEquals(8, Filter::int($int, ['max'=>8]));

        $int = '10';
        $this->assertEquals(11, Filter::int($int, ['min'=>11]));

        $int = '10';
        $this->assertEquals(100, Filter::int($int, ['min'=>11, 'default'=>100]));

        $int = -1;
        $this->assertEquals(100, Filter::uint($int, ['default'=>100]));
    }

    public function testEnum()
    {
        $v = 'foo';
        $this->assertEquals('foo', Filter::enum($v, ['enum'=>['foo', 'bar']]));

        $v = 'baz';
        $this->assertEquals('foo', Filter::enum($v, ['enum'=>['foo', 'bar']]));

        $v = 'baz';
        $this->assertEquals('fbb', Filter::enum($v, ['enum'=>['foo', 'bar'], 'default'=>'fbb']));
    }

    public function testEnumByKey()
    {
        $enum = [
            'a' => 'foo',
            'b' => 'bar',
            'c' => 'baz'
        ];

        $v = 'a';
        $this->assertEquals('foo', Filter::enumByKey($v, ['enum'=>$enum]));

        $v = 'd';
        $this->assertEquals('foo', Filter::enumByKey($v, ['enum'=>$enum]));

        $v = 'd';
        $this->assertEquals('bar', Filter::enumByKey($v, ['enum'=>$enum, 'default_key'=>'b']));

        $v = 'd';
        $this->assertEquals('baz', Filter::enumByKey($v, ['enum'=>$enum, 'default'=>'baz']));

        $v = 'd';
        $this->assertEquals('b', Filter::enumByKey($v, ['enum'=>$enum, 'default_key'=>'b', 'enum_key' => true]));
    }

    public function testJson()
    {
        $arr = ['foo'=>1];
        $v = json_encode($arr);
        $this->assertEquals($arr, Filter::json($v, ['json_assoc'=>true]));

        $arr = ['foo'=>'1', 'bar'=>'2'];
        $v = json_encode($arr);
        $this->assertEquals($arr, Filter::json($v, ['json_assoc'=>true, 'json_schema'=>['foo' => [Filter::UINT_TYPE]]]));
    }
}