# neoan3-ops

Helper library for common tasks. Developed for [neoan3](https://github.com/sroehrl/neoan3), put perfectly usable as stand-alone.

This library facilitates 

- templating
- string-manipulation

## Templating
**Ops** is not a template engine! It is meant to be used for final rendering and string operations. 
However, **Ops** can render a template:

_profile.html_
```HTML
<h1>{{user}}</h1>
<p>{{profile.name}}</p>

```
_profile.php_
```PHP
$dynamicContent = [
    'user' => 'Test',
    'profile' => [
        'name' => 'John Doe',
        ...
    ]
];
echo \Neoan3\Apps\Ops::embraceFromFile('profile.html',$dynamicContent);
```
_output_
```HTML
<h1>Test</h1>
<p>John Doe</p>
```

### Main templating methods
#### embrace($string, $substitutionArray)
Replaces array-keys indicated by double curly braces with the appropriate value
#### embraceFromFile($fileLocation, $substitutionArray)
Reads content of a file and executes the embrace function.
>When using Neoan3, the location starts at the root of your application. As a stand-alone, either define "path" accordingly or use the fallback to the root of the server.

#### hardEmbrace($string, $substitutionArray)
When working with front-end technology, the similarity of markup can either be wanted (e.g. Vue fills content PHP could not), or ambiguous.
You can choose "hardEmbrace" to use double hard brackets instead.

`<h1>[[key]]</h1>`
#### tEmbrace($string, $substitutionArray)
Used for i18n (internationalization) in Neoan3, this method replaces based on t-tags in your markup.
```PHP
$german = ['hello'=>'hallo'];
$html = '
    <h1><t>hello</t></h1>
';
\Neoan3\Apps\Ops::tEmbrace($html, $german);
```
Output:
`<h1>hallo</h1>`

## String manipulation

#### serialize($any)
Serializes strings, arrays and objects.

#### pin($length)
Returns a random integer in the requested length.

#### hash($length = 10, $special = false)
Returns a random string (with or without special characters) in the requested length.

#### encrypt($string, $key)
Encrypts a string with a symmetric AES-256 algorithm. 

#### decrypt($encrypted, $key)
Encrypts a string with a symmetric AES-256 algorithm. 

#### extrude($targets,$array)
Returns selected part of $array.
```PHP
$userInput = [
    'id'=>1,
    'random'=>'value'
];
$clean = Ops::extrude(['id'],$userInput);
// Output $clean: ['id'=>1]
```
#### toPascalCase($string)
Converts spaces, snake-, kebab- or camelCase to PascalCase
#### toCamelCase($string)
Converts spaces, snake-, kebab- or PascalCase to camelCase
#### toSnakeCase
Converts spaces, camel-, kebab- or PascalCase to snake_case
#### toKebabCase
Converts spaces, camel-, snake- or PascalCase to kebab-case