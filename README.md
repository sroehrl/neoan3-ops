# neoan3-ops

[![Build Status](https://travis-ci.com/sroehrl/neoan3-ops.svg?branch=master)](https://travis-ci.com/sroehrl/neoan3-ops)
[![Test Coverage](https://api.codeclimate.com/v1/badges/73cacbc29aa1438b37fd/test_coverage)](https://codeclimate.com/github/sroehrl/neoan3-ops/test_coverage)

Ops provides valuable string helpers for everyday use ranging from simple templating to encryption & hash generation.


This library facilitates 

- [templating](#templating)
- [string-manipulation](#string-manipulation)

## Templating

Templating has grown into a dedicated repository and is now available at [neoan3-apps/template](https://github.com/sroehrl/neoan3-template).
For the time being, Ops will inherit functions as if it where part of Ops.
However, in new projects we recommend using "Template" instead of "Ops" to trigger templating functionality.


## String manipulation

#### serialize($any)
Serializes strings, arrays and objects (url save).

#### unserialize($serializedString)
Reverts serialize()

#### pin($length)
Returns a random integer in the requested length.

#### flattenArray($array)
Converts deep arrays to keyed arrays of one level to resemble JS-object selection.
```PHP
$original = ['items' => ['name' => 'sam']];
$flat = Ops::flattenArray($original);
/*
* output $flat: ['items.name' => 'sam'];
* 
*/
```

#### randomString($length = 10)
Returns a random string (with or without special characters) in the requested length.

#### encrypt($string, $key)
Encrypts a string with a symmetric AES-256 algorithm. 

#### decrypt($encrypted, $key)
Decrypts a string with a symmetric AES-256 algorithm. 

#### extrude($targets,$array)
Returns selected part of $array.
```PHP
$userInput = [
    'id'=>1,
    'name'=>'sam',
    'random'=>'value'
];
$clean = Ops::extrude(['id','name'],$userInput);
// Output $clean: ['id'=>1,'name'=>'sam']
```
#### toPascalCase($string)
Converts spaces, snake-, kebab- or camelCase to PascalCase
#### toCamelCase($string)
Converts spaces, snake-, kebab- or PascalCase to camelCase
#### toSnakeCase($string)
Converts spaces, camel-, kebab- or PascalCase to snake_case
#### toKebabCase($string)
Converts spaces, camel-, snake- or PascalCase to kebab-case
