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
        $str = preg_replace_callback('/([A-Z]{2,})/', function ($m)
        {
            return sprintf('_%s_', $m[0]);
        }, $this->string);
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

    public function fill(array $vars, string $group_delimeter = '.')
    {
        $vars = self::getSingleArray($vars, $group_delimeter);

        $newString = preg_replace_callback('/{(?<mod>.*?){\\s*(?<key>.*?)\\s*(=\\s*(?<default>.*?))?\\s*}}/',
            function ($matches) use ($vars)
            {
                if (array_key_exists($matches['key'], $vars)) {
                    $value = $vars[$matches['key']];
                } elseif (array_key_exists('default', $matches)) {
                    $value = $matches['default'];
                } else {
                    return '';
                }

                switch ($matches['mod']) {
                    case '%':
                    {
                        return urlencode($value);
                    }
                    case '&':
                    {
                        return htmlspecialchars($value);
                    }
                    default:
                    {
                        if (
                            mb_strlen($matches['mod'])
                            && function_exists($matches['mod'])
                        ) {
                            return call_user_func($matches['mod'], $value);
                        }

                        return $value;
                    }
                }
            },
            $this
        );

        return new self($newString);
    }

    private static function getSingleArray($array, $delimiter, $startKey = '')
    {
        if ( ! is_array($array)) {
            $array = [$array];
        }

        $newArray = [];

        $startKey = $startKey ? ($startKey . $delimiter) : '';

        foreach ($array as $key => $value) {

            if (is_object($value)) {
                if (method_exists($value, '__toString')) {
                    $value = call_user_func([$value, '__toString']);
                } else {
                    $value = get_object_vars($value);
                }
            }

            if (is_array($value)) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $newArray = array_merge($newArray, self::getSingleArray($value, $delimiter, $startKey . $key));
            } else {
                $newArray[$startKey . $key] = $value;
            }
        }

        return $newArray;
    }

    public function __toString()
    {
        return $this->string;
    }
}
