
A library provides tools to filter variable
===========================

Installation
------------
```shell
composer require Imj\Filter
```

Basic Usage
------------

filter string
```php
uae Imj\Filter

$str = ' str';
echo Filter::string($str); // str

$str = 'abcdefg';
echo Filter::string($str, ['length'=>2]); // ab

$str = 'abc';
echo Filter::string($str, ['regex'=>"/\d+/"]); // null
```

filter int/uint/float/ufloat
```php
uae Imj\Filter

$int = '10';
echo Filter::int($int, ['max'=>11, 'min'=>8]); // 10

$int = '10';
echo Filter::int($int, ['max'=>8]); // 8

$int = '10';
echo Filter::int($int, ['min'=>11]); // 11

$int = '10';
echo Filter::int($int, ['min'=>11, 'default'=>100]); // 100

$int = -1;
echo Filter::uint($int, ['default'=>100]);

// uint/float/ufloat method is the same
```

filter by enum
```php
uae Imj\Filter

$v = 'foo';
echo Filter::enum($v, ['enum'=>['foo', 'bar']]); // foo

$v = 'baz';
echo Filter::enum($v, ['enum'=>['foo', 'bar']]); // foo

$v = 'baz';
echo Filter::enum($v, ['enum'=>['foo', 'bar'], 'default'=>'fbb']); //fbb
```

filter by enums key
```php
uae Imj\Filter

$enum = [
  'a' => 'foo',
  'b' => 'bar',
  'c' => 'baz'
];

$v = 'a';
echo Filter::enumByKey($v, ['enum'=>$enum]); // foo

$v = 'd';
echo Filter::enumByKey($v, ['enum'=>$enum]); // foo

$v = 'd';
echo Filter::enumByKey($v, ['enum'=>$enum, 'default_key'=>'b']); // bar

$v = 'd';
echo Filter::enumByKey($v, ['enum'=>$enum, 'default'=>'baz']); // baz

$v = 'd';
echo Filter::enumByKey($v, ['enum'=>$enum, 'default_key'=>'b', 'enum_key' => true]); // b
```

filter json
```php
uae Imj\Filter

$arr = ['foo'=>1];
$v = json_encode($arr);
var_dump(Filter::json($v, ['json_assoc'=>true])); // ['foo'=>1]

$arr = ['foo'=>'1', 'bar'=>'2'];
$v = json_encode($arr);
var_dump(Filter::json($v, ['json_assoc'=>true, 'json_schema'=>['foo' => [Filter::UINT_TYPE]]])); // ['foo'=>1, 'bar'=>'2']
```

In addition, you can also use `validate` method and the effect is the same.
```php
uae Imj\Filter

$str = 'abcdefg';
echo Filter::validate($str, Filter::STRING_TYPE, ['length'=>2]); // ab
// ...
```
The second parameter indicates the variable type, it could be:
```php
Filter::STRING_TYPE
Filter::INT_TYPE
Filter::UINT_TYPE
Filter::FLOAT_TYPE
Filter::UFLOAT_TYPE
Filter::ENUM_TYPE
Filter::ENUM_KEYS_TYPE
Filter::JSON_TYPE
Filter::NONE_TYPE
```

License
------------

licensed under the MIT License - see the `LICENSE` file for details
