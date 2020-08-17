# Contents
* [Basic usage](#basic-usage)
  * [Create object](#create-object)
  * [Casting](#casting)
* [String tests](#string-tests)
  * [Substrings](#substrings)
    * [startsWith](#startswith)
    * [endsWith](#endsWith)

# Basic usage

### Create object

Use
```php
$foo = new AlexeyYashin\EString\EString('bar'); 
```
or just short
```php
$foo = estring('bar');
```

### Casting

EString implements __toString() method, so you can use any PHP solution to cast EString to a simple string:
```php
$string = (string) $EString;

// or

$string = "" . $EString;

// or using in string functions

$modifiedString = preg_replace('~ {2,}~', ' ', $EString); // makes string
```

# String tests

## Substrings

### startsWith

**Check if you string starts with a substring.**

```startsWith(mixed $substr, bool $i = false): bool```

Parameters:

|Param|Descr|
|---|---|
|$substr|Substring to search. Any type of string-castable data|
|$i|Case-independent flag. Set true to check in case-independent mode|

Return value:

* ```true``` if tested string starts with ```$substr```
* ```false``` otherwise

> Example
> ```php
> var_dump(estring('foo bar')->startsWith('foo')); // bool(true)
> var_dump(estring('foo bar')->startsWith('bar')); // bool(false)
>
> /* Case dependency: */
> var_dump(estring('FOO BAR')->startsWith('foo')); // bool(false)
> var_dump(estring('FOO BAR')->startsWith('foo'), true); // bool(true)
> ```

### endsWith

**Check if you string ends with a substring.**

```endsWith(mixed $substr, bool $i = false): bool```

Parameters:

|Param|Descr|
|---|---|
|$substr|Substring to search. Any type of string-castable data|
|$i|Case-independent flag. Set true to check in case-independent mode|

Return value:

* ```true``` if tested string ends with ```$substr```
* ```false``` otherwise

> Example
> ```php
> var_dump(estring('foo bar')->endsWith('bar')); // bool(true)
> var_dump(estring('foo bar')->endsWith('foo')); // bool(false)
>
> /* Case dependency: */
> var_dump(estring('FOO BAR')->endsWith('bar')); // bool(false)
> var_dump(estring('FOO BAR')->endsWith('bar'), true); // bool(true)
> ```

