<?php

namespace Imj;

/**
 * Class Filter
 * @package Imj
 */
class Filter
{
    const STRING_TYPE        = 1;
    const INT_TYPE           = 2;
    const UINT_TYPE          = 3;
    const FLOAT_TYPE         = 4;
    const UFLOAT_TYPE        = 5;
    const ENUM_TYPE          = 6;
    const ENUM_KEYS_TYPE     = 7;
    const JSON_TYPE          = 8;
    const NONE_TYPE          = 9;

    public static function validate(&$value, $var_type, $options = [])
    {
        if (!isset($value)) {
            if (isset($options['default'])) {
                return $options['default'];
            }
            return null;
        }
        switch ($var_type) {
            case self::STRING_TYPE:
                self::string($value, $options);
                break;
            case self::INT_TYPE:
                self::int($value, $options);
                break;
            case self::UINT_TYPE:
                self::uint($value, $options);
                break;
            case self::FLOAT_TYPE:
                self::float($value, $options);
                break;
            case self::UFLOAT_TYPE:
                self::ufloat($value, $options);
                break;
            case self::ENUM_TYPE:
                self::enum($value, $options);
                break;
            case self::ENUM_KEYS_TYPE:
                self::enumByKey($value, $options);
                break;
            case self::JSON_TYPE:
                self::json($value, $options);
                break;
            case self::NONE_TYPE:
            default:
                break;
        }

        return $value;

    }

    /**
     * filter string
     * @param $value
     * @param $options
     * @return string
     */
    public static function string(&$value, $options = ['length'=>null, 'regex'=>null, 'default'=>null])
    {
        $value = htmlspecialchars(trim($value));
        self::processString($value, $options);
        return $value;
    }

    /**
     * filter int
     * @param $value
     * @param array $options
     * @return int
     */
    public static function int(&$value, $options = ['max'=>null, 'min'=>null, 'default'=>null])
    {
        $value = (int)$value;
        self::processNum($value, $options);
        return $value;
    }

    /**
     * filter positive int
     * @param $value
     * @param array $options
     * @return int
     */
    public static function uInt(&$value, $options = ['max'=>null, 'min'=>null, 'default'=>null])
    {
        $value = (int)$value;
        self::processUNum($value, $options);
        return $value;
    }

    /**
     * filter float
     * @param $value
     * @param array $options
     * @return float
     */
    public static function float(&$value, $options = ['max'=>null, 'min'=>null, 'default'=>null])
    {
        $value = (float)$value;
        self::processNum($value, $options);
        return $value;
    }

    /**
     * filter positive float
     * @param $value
     * @param array $options
     * @return float
     */
    public static function ufloat(&$value, $options = ['max'=>null, 'min'=>null, 'default'=>null])
    {
        $value = (float)$value;
        self::processUNum($value, $options);
        return $value;
    }

    /**
     * enum
     * If $value is in an array of enum, then return value.
     * otherwise, according to the enum array and default parameters, select the appropriate value to return.
     * @param $value
     * @param array $options
     * @return mixed|null
     */
    public static function enum(&$value, $options = ['enum'=>[], 'strict'=>false, 'default'=>null])
    {
        if (isset($options['enum']) && is_array($options['enum']) && $options['enum'] != []) {
            $value = in_array($value, $options['enum'], isset($options['strict']) && $options['strict']) ?
                $value : (isset($options['default']) ?
                    $options['default'] : current($options['enum']));
        } else {
            $value = isset($options['default']) ? $options['default'] : null;
        }
        return $value;
    }

    /**
     * enum by key
     * If array_key_exists($value), then returns the corresponding value
     * otherwise, according to the array and default_key, default parameters submitted, select the appropriate value to return.
     * @param $value
     * @param array $options
     * @return mixed|null
     */
    public static function enumByKey(&$value, $options = ['enum'=>[], 'default_key'=>null, 'enum_key'=>false, 'default'=>null])
    {
        if (isset($options['enum']) && is_array($options['enum']) && $options['enum'] != []) {
            $key = null;

            if (isset($options['enum'][$value])) {
                // 如果有值，则记录为值
                $key   = $value;
                $value = $options['enum'][$value];
            } elseif (isset($options['default_key']) && !is_null($options['default_key'])) {
                // 如果没有值，则看是否有default_key
                $key   = $options['default_key'];
                $value = $options['enum'][$options['default_key']];
            } elseif (isset($options['default']) && in_array($options['default'], $options['enum'])) {
                // 如果没有没有 default_key 则看有无 default 值，且 default 值应该在 options[enum] 中
                $value = $options['default'];
            } else {
                // 如果情况都不符合，则使用 enum 的第一个元素
                $value = current($options['enum']);
            }
            // enum_key 为 true 则返回key，否则返回上述获取的value
            if (isset($options['enum_key']) && $options['enum_key']) {
                if ($key !== null) {
                    $value = $key;
                } else {
                    $value = array_search($value, $options['enum']);
                }
            }
        } else {
            $value = isset($options['default']) ? $options['default'] : null;
        }
        return $value;
    }

    /**
     * filter json
     * @param $value
     * @param array $options
     * @return mixed
     */
    public static function json(&$value, $options = ['json_assoc'=>true, 'json_schema'=>[], 'default'=>null])
    {
        $json_assoc = isset($options['json_assoc']) && $options['json_assoc'] ? true : false;
        $value = @json_decode(trim($value), $json_assoc);

        if ($value !== null && $json_assoc && isset($options['json_schema']) && $options['json_schema'] && is_array($options['json_schema'])) {
            foreach ($options['json_schema'] as $field => $opt_arr) {
                self::validate(
                    $value[$field],
                    isset($opt_arr[0]) ? $opt_arr[0] : self::NONE_TYPE,
                    isset($opt_arr[1]) && is_array($opt_arr[1]) ? $opt_arr[1] : []
                );
            }
        } elseif ($value === null) {
            $value = isset($options['default']) ? $options['default'] : null;
        }

        return $value;
    }

    /**
     * process string
     * @param $value
     * @param $options
     */
    protected static function processString(&$value, $options)
    {
        $value = str_replace(chr(0), '', $value);
        if (isset($options['length']) && $options['length']) {
            $value = substr($value, 0, $options['length']);
        }

        if (isset($options['regex']) && $options['regex']) {
            if (!preg_match($options['regex'], $value)) {
                $value = null;
            }
        }
    }

    /**
     * process number
     * @param $value
     * @param $options
     */
    protected static function processNum(&$value, $options)
    {
        if (isset($options['min']) && $options['min'] >= 0) {
            $ic = $value < $options['min'] ? 0 : 1;
        } else {
            $ic = 1;
        }

        if (isset($options['max']) && $options['max']) {
            if (isset($options['min'])) {
                $options['max'] = $options['max'] > $options['min'] ? $options['max'] : $options['min'];
            }
            $ac = $value > $options['max'] ? 0 : 1;
        } else {
            $ac = 1;
        }

        // 满足上限
        if ($ac) {
            // 不满足下限
            if (!$ic) {
                // default -> 下限 -> 上限 -> null
                if (isset($options['default'])) {
                    $value = $options['default'];
                } else if (isset($options['min'])) {
                    $value = $options['min'];
                } else if (isset($options['max'])) {
                    $value = $options['max'];
                } else {
                    $value = 0;
                }
            }
        } else {
            if ($ic) {
                // 满足下限
                // default -> 上限 -> 下限 -> null
                if (isset($options['default'])) {
                    $value = $options['default'];
                } else if (isset($options['max'])) {
                    $value = $options['max'];
                } else if (isset($options['min'])) {
                    $value = $options['min'];
                } else {
                    $value = 0;
                }
            } else {
                // 不满足下限
                // default -> 下限 -> 上限 -> null
                if (isset($options['default'])) {
                    $value = $options['default'];
                } else if (isset($options['min'])) {
                    $value = $options['min'];
                } else if (isset($options['max'])) {
                    $value = $options['max'];
                } else {
                    $value = 0;
                }
            }
        }
        return ;

        $value = $ac & $ic ?
            $value : (isset($options['default']) ?
                $options['default'] : (isset($options['min']) ?
                    $options['min'] : (isset($options['max']) ?
                        $options['max'] : 0
                    )
                )
            );
    }

    /**
     * process positive number
     * @param $value
     * @param $options
     */
    protected static function processUNum(&$value, $options)
    {
        if (!isset($options['min']) || empty($options['min']) || $options['min'] < 0) {
            $options['min'] = 0;
        }
        self::processNum($value, $options);
    }
}
