# estring
estring was made for simpler strings operations in PHP just like JS does

# Installation

Right now the only option is using Composer:
```sh
composer require alexeyyashin/estring
```

# Example
```php
$first = '  Some string to operate';

// PHP functions
$second = $first;
if (strpos(strtolower(trim($second)), 'some') === 0) {
  echo str_replace('Some', 'Awesome', trim($second)) . "\n";
  echo "found\n";
} elseif (substr(strtolower(trim($second)), - strlen('operate')) === 'operate') {
  echo str_replace('operate', 'control', trim($second)) . "\n";
  echo "found but ending\n";
}

// Same with estring
$third = estring($first);
if ($third->trim()->startsWith('some', true)) {
  echo $third->trim()->replace(['Some' => 'Awesome']) . "\n";
  echo "found\n";
} elseif ($third->trim()->endsWith('operate')) {
  echo $third->trim()->replace(['operate' => 'control']) . "\n";
  echo "found but ending\n";
}
```

# See more info in documentation
(not finished yet)
[Usage.md](https://github.com/alexeyyashin/estring/blob/documentation/docs/usage.md)
