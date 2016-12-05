[![Build Status](https://travis-ci.org/zackkitzmiller/tiny-php.png?branch=master)](https://travis-ci.org/zackkitzmiller/tiny-php)
[![Coverage Status](https://coveralls.io/repos/zackkitzmiller/tiny-php/badge.png?branch=master)](https://coveralls.io/r/zackkitzmiller/tiny-php?branch=master)

# Tiny

A reversible base62 ID obfuscater

## Authors

Originally by Jacob DeHart, with Ruby and Python ports by Kyle Bragger

Now maintained by [Zack Kitzmiller](https://github.com/zackkitzmiller).

## Installation

Install via Composer

```json
{
    "require": {
        "zackkitzmiller/tiny": "1.2.0"
    },
}
```

## Usage

```php
$tiny = new \ZackKitzmiller\Tiny('5SX0TEjkR1mLOw8Gvq2VyJxIFhgCAYidrclDWaM3so9bfzZpuUenKtP74QNH6B');

echo $tiny->to(5);
// E

echo $tiny->from('E');
// 5

echo $tiny->to(126);
// XX

echo $tiny->from('XX');
// 126

echo $tiny->to(999);
// vk

echo $tiny->from('vk');
// 999

```

## Configuration

You must instanciate a new instance of Tiny with a random alpha-numeric set where each character must only be used exactly once. Do **NOT** change this once you start using Tiny, as you won't be able to reverse.

You can generate a random set from the commandline with `$ ./bin/genset`

## Using laravel?

If you're using laravel and want to use a more laravel-like and cleaner syntax you only have to follow these steps.

First open your ``app/config/app.php`` file and scroll down to your providers and add
```php
'providers' => array(
    ...
    'ZackKitzmiller\TinyServiceProvider',
)
```
and then this to aliases
```php
'aliases' => array(
    ...
    'Tiny' => 'ZackKitzmiller\Facades\Tiny',
)
```

Lastly you run ``php artisan config:publish zackkitzmiller/tiny`` to publish the configuration file and then run ``php artisan tiny:generate`` to create a valid key.

### Usage in Laravel
```php
echo Tiny::to(999);
// echos vk

echo Tiny::from('E');
// echos 5
```
