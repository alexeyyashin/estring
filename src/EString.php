<?php

namespace AlexeyYashin\EString;

class EString
{
    public $string = '';
    protected $charset = null;

    /**
     * EString constructor.
     *
     * @param string|\AlexeyYashin\EString\EString $string
     * @param string|null                          $charset
     */
    public function __construct($string = '', string $charset = null)
    {
        $this->string = (string) $string;

        if ($string instanceof EString && $charset === null) {
            $this->charset = $string->charset;
        } else {
            $this->charset = $charset;
        }
    }

    /**
     * @param $new_string
     *
     * @return \AlexeyYashin\EString\EString
     */
    protected function getNew($new_string)
    {
        return new self($new_string, $this->getCharset());
    }

    /**
     * @param string|\AlexeyYashin\EString\EString $substr
     * @param bool                                 $i
     *
     * @return bool
     */
    public function startsWith($substr, $i = false): bool
    {
        $substr = $this->getNew($substr);
        $string = $this;

        if ($i) {
            $string = $string->toLowerCase();
            $substr = $substr->toLowerCase();
        }

        return mb_strpos($string, $substr, 0, $this->getCharset()) === 0;
    }

    /**
     * @param string $charset
     *
     * @return $this
     */
    public function charset(string $charset)
    {
        $this->charset = $charset;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCharset()
    {
        if ($this->charset === null) {
            $this->charset = mb_internal_encoding();
        }

        return $this->charset;
    }

    /**
     * @return \AlexeyYashin\EString\EString
     */
    public function toLowerCase()
    {
        return $this->getNew(mb_strtolower($this->string, $this->getCharset()));
    }

    /**
     * @param string|\AlexeyYashin\EString\EString $substr
     * @param bool                                 $i
     *
     * @return bool
     */
    public function endsWith($substr, $i = false)
    {
        $substr = $this->getNew($substr);
        $string = $this;

        if ($i) {
            $string = $string->toLowerCase();
            $substr = $substr->toLowerCase();
        }

        return $substr == $string->substring(-$substr->length());
    }

    /**
     * @deprecated use == instead
     *
     * @param      $string
     * @param bool $strict
     *
     * @return bool
     */
    public function equals($string, $strict = true)
    {
        $string = $this->getNew($string);

        return (
            (
                $strict
                && (
                    $this->string === (string) $string
                )
            )
            || $this->string == $string->string
        );
    }

    /**
     * @return int
     */
    public function length()
    {
        return (int) mb_strlen($this->string, $this->getCharset());
    }

    /**
     * @param      $search
     * @param null $replace
     *
     * @return \AlexeyYashin\EString\EString
     */
    public function replace($search, $replace = null)
    {
        if (
            $replace === null
            && is_array($search)
        ) {
            $replace = $search;
            $search = array_keys($replace);
        }

        return $this->getNew(str_replace($search, $replace, $this->string));
    }

    /**
     * @param string $charlist
     *
     * @return \AlexeyYashin\EString\EString
     */
    public function trim($charlist = " \t\n\r\0\x0B")
    {
        return $this->getNew(trim($this->string, $charlist));
    }

    /**
     * @return \AlexeyYashin\EString\EString
     */
    public function toCamelCase()
    {
        $str = '';
        foreach ($this->getCaseArray() as $word) {
            $str .= $word->ucfirst();
        }

        return $this->getNew($str);
    }

    /**
     * @param bool $toupper
     *
     * @return array
     */
    protected function caseArray($toupper = false)
    {
        $str = preg_replace_callback('/\p{Lu}\p{Ll}/', function($m)
        {
            return '_' . mb_strtolower($m[0], $this->getCharset());
        }, $this->string);
        $str = preg_replace_callback('/(\p{Lu}{2,})/', function($m)
        {
            return sprintf('_%s_', mb_strtolower($m[0], $this->getCharset()));
        }, $str);
        $str = preg_replace('/\p{Lu}/', '_$0', $str);

        $str = $this->getNew($str);
        $str = $toupper ? $str->toUpperCase() : $str->toLowerCase();

        $str = preg_split('/(?!(\p{L}|\d))./', $str);

        return array_filter($str);
    }

    /**
     * @param bool $to_upper
     *
     * @return EString[]
     */
    public function getCaseArray(bool $to_upper = false)
    {
        return array_map(
            function($item)
            {
                return $this->getNew($item);
            },
            $this->caseArray($to_upper)
        );
    }

    /**
     * @return \AlexeyYashin\EString\EString
     */
    public function toUpperCase()
    {
        return $this->getNew(mb_strtoupper($this->string, $this->getCharset()));
    }

    /**
     * @return \AlexeyYashin\EString\EString
     */
    public function toLowerCamelCase()
    {
        $words = $this->caseArray();
        $str = reset($words);
        unset($words[key($words)]);

        foreach ($words as $word) {
            $str .= ucfirst($word);
        }

        return $this->getNew($str);
    }

    /**
     * @return \AlexeyYashin\EString\EString
     */
    public function toSnakeCase()
    {
        return $this->getNew(implode('_', $this->caseArray()));
    }

    /**
     * @return \AlexeyYashin\EString\EString
     */
    public function toUpperSnakeCase()
    {
        return $this->getNew(implode('_', $this->caseArray(true)));
    }

    /**
     * @return \AlexeyYashin\EString\EString
     */
    public function ucfirst()
    {
        if (function_exists('mb_ucfirst')) {
            $result = mb_ucfirst($this);
        } else {
            $letter = $this->substring(0, 1)->toUpperCase();
            $suffix = $this->substring(1, $this->length() - 1);
            $result = $letter . $suffix;
        }

        return $this->getNew($result);
    }

    /**
     * @param      $start
     * @param null $length
     *
     * @return \AlexeyYashin\EString\EString
     */
    public function substring($start, $length = null)
    {
        return $this->getNew(mb_substr($this, $start, $length, $this->getCharset()));
    }

    /**
     * @param        $array
     * @param        $key
     * @param string $delimeter
     *
     * @return array|null
     */
    protected static function getValueFromArray($array, $key, $delimeter = '.')
    {
        $current_value = $array;

        if ( ! $key || ! $array) {
            return null;
        }

        foreach (explode($delimeter, $key) as $key) {
            if (is_array($current_value)) {
                if ( ! array_key_exists($key, $current_value)) {
                    return [null, false];
                }
                $current_value = $current_value[$key];
            } elseif (is_object($current_value)) {
                $current_value = $current_value->$key;
                if ($current_value === null && ! property_exists($current_value, $key)) {
                    return [null, false];
                }
            } else {
                return [null, false];
            }
        }

        return [$current_value, true];
    }

    /**
     * @param object|array $vars
     * @param string       $group_delimeter Deprecated
     *
     * @return \AlexeyYashin\EString\EString
     */
    public function fill($vars, string $group_delimeter = '.')
    {
        $newString = preg_replace_callback('/{(?<mod>[^{]*?){\\s*(?<key>[^{]*?)\\s*(=\s*(?<default>.*?))?\\s*}}/',
            function($matches) use ($vars, $group_delimeter)
            {
                [$value, $found] = self::getValueFromArray($vars, $matches['key'], $group_delimeter);

                if ( ! $found) {
                    if (array_key_exists('default', $matches)) {
                        $value = $matches['default'];
                    } else {
                        return '';
                    }
                }

                switch ($matches['mod']) {
                    case '%':
                        return urlencode($value);
                    case '&':
                        return htmlspecialchars($value);
                    default:
                        if (
                            mb_strlen($matches['mod'], $this->getCharset())
                            && function_exists($matches['mod'])
                        ) {
                            return call_user_func($matches['mod'], $value);
                        }

                        return $value;
                }
            },
            $this
        );

        return $this->getNew($newString);
    }

    /**
     * @param      $substr
     * @param bool $i
     *
     * @return \AlexeyYashin\EString\EString
     */
    public function cropStart($substr, $i = false)
    {
        if ($this->startsWith($substr, $i)) {
            return $this->getNew($this->substring(mb_strlen($substr)));
        }

        return $this->getNew($this);
    }

    /**
     * @param      $substr
     * @param bool $i
     *
     * @return \AlexeyYashin\EString\EString
     */
    public function cropEnd($substr, $i = false)
    {
        if ($this->endsWith($substr, $i)) {
            return $this->getNew($this->substring(0, -mb_strlen($substr)));
        }

        return $this->getNew($this);
    }

    /**
     * @param      $substr
     * @param bool $i
     *
     * @return \AlexeyYashin\EString\EString
     */
    public function crop($substr, $i = false)
    {
        return $this
            ->cropStart($substr, $i)
            ->cropEnd($substr, $i)
        ;
    }

    public function __toString()
    {
        return $this->string;
    }
}
