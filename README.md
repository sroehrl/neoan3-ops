# neoan3-ops

Ops provides valuable string helpers for everyday use ranging from simple templating to encryption & hash generation.


This library facilitates 

- [templating](#templating)
- [string-manipulation](#string-manipulation)

## Templating
**Ops** is not a full blown template engine! 
With modern JavaScript solutions creating a dynamic approach, Ops focuses on the necessities of static rendering. 

_profile.html_
```HTML
<h1>{{user}}</h1>
<p>{{profile.name}}</p>
<n-template for="items as key => item"> 
    <p>{{item}}-{{key}}</p>
</n-template>

```
_profile.php_
```PHP
$dynamicContent = [
    'user' => 'Test',
    'items' => ['one','two'],
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
<p>one-0</p>
<p>two-1</p>
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
Serializes strings, arrays and objects (url save).

#### unserialize($serializedString)
Reverts serialize()

#### pin($length)
Returns a random integer in the requested length.

#### flattenArray($array)
Converts deep arrays to keyed arrays of one level to resemble JS-object selection.
```PHP
$original = ['items' => ['name' => 'sam']];
$flat = Ops::flattenArray($original)
/*
* output $flat: ['items.name' => 'sam'];
* 
*/
```

#### hash($length = 10, $special = false)
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
    'name'=>'sam'
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
