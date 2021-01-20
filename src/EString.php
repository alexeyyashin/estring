<?php

namespace AlexeyYashin\EString;

class EString
{
    public $string = '';

    public function __construct($string = '')
    {
        $this->string = (string) $string;
    }

    public function startsWith($substr, $i = false): bool
    {
        $substr = new self($substr);
        $string = $this;

        if ($i) {
            $string = $string->toLowerCase();
            $substr = $substr->toLowerCase();
        }

        return mb_strpos($string, $substr) === 0;
    }

    public function toLowerCase()
    {
        return new self(mb_strtolower($this->string));
    }

    public function endsWith($substr, $i = false)
    {
        $substr = new self($substr);
        $string = $this;

        if ($i) {
            $string = $string->toLowerCase();
            $substr = $substr->toLowerCase();
        }

        return $substr->equals(mb_substr($string, -$substr->length()));
    }

    public function equals($string, $strict = true)
    {
        $string = new self($string);

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

    public function length()
    {
        return (int) mb_strlen($this->string);
    }

    public function replace($search, $replace = null)
    {
        if (
            $replace === null
            && is_array($search)
        ) {
            $replace = $search;
            $search = array_keys($replace);
        }

        return new self(str_replace($search, $replace, $this->string));
    }

    public function trim($charlist = " \t\n\r\0\x0B")
    {
        return new self(trim($this->string, $charlist));
    }

    public function toCamelCase()
    {
        $str = '';
        foreach ($this->caseArray() as $word) {
            $str .= ucfirst($word);
        }

        return new self($str);
    }

    protected function caseArray($toupper = false)
    {
        $str = preg_replace_callback('/[A-Z][a-z]/', function($m)
        {
            return '_' . mb_strtolower($m[0]);
        }, $this->string);
        $str = preg_replace_callback('/([A-Z]{2,})/', function($m)
        {
            return sprintf('_%s_', mb_strtolower($m[0]));
        }, $str);
        $str = preg_replace('/[A-Z]/', '_$0', $str);

        $str = (new EString($str));
        $str = $toupper ? $str->toUpperCase() : $str->toLowerCase();

        $str = preg_split('/[^a-zA-Z0-9]+/', $str);

        return array_filter($str);
    }

    public function toUpperCase()
    {
        return new self(mb_strtoupper($this->string));
    }

    public function toLowerCamelCase()
    {
        $words = $this->caseArray();
        $str = reset($words);
        unset($words[key($words)]);

        foreach ($words as $word) {
            $str .= ucfirst($word);
        }

        return new self($str);
    }

    public function toSnakeCase()
    {
        return new self(implode('_', $this->caseArray()));
    }

    public function toUpperSnakeCase()
    {
        return new self(implode('_', $this->caseArray(true)));
    }

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
     * @param string $group_delimeter Deprecated
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
                            mb_strlen($matches['mod'])
                            && function_exists($matches['mod'])
                        ) {
                            return call_user_func($matches['mod'], $value);
                        }

                        return $value;
                }
            },
            $this
        );

        return new self($newString);
    }

    public function __toString()
    {
        return $this->string;
    }
}
