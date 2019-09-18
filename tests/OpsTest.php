<?php

namespace Neoan3\Apps;


use PHPUnit\Framework\TestCase;

class OpsTest extends TestCase
{
    function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        if(!defined('path')){
            define('path',dirname(__FILE__));
        }
    }

    public function stringProvider(){
        return [
            ['some-case'], ['SomeCase'], ['some_case'], ['someCase'],['Some-Case'],['Some_Case']
        ];
    }


    /**
     * @dataProvider stringProvider
     *
     * @param $a
     */
    public function testToPascalCase($a)
    {

        $this->assertSame('SomeCase', Ops::toPascalCase($a));

    }
    /**
     * @dataProvider stringProvider
     *
     * @param $a
     */
    public function testToKebabCase($a)
    {
        $this->assertSame('some-case', Ops::toKebabCase($a));
    }

    /**
     * @dataProvider stringProvider
     *
     * @param $a
     */
    public function testToCamelCase($a)
    {
        $this->assertSame('someCase', Ops::toCamelCase($a));
    }

    /**
     * @dataProvider stringProvider
     *
     * @param $a
     */
    public function testToSnakeCase($a)
    {
        $this->assertSame('some_case', Ops::toSnakeCase($a));
    }

    public function testEmbrace()
    {
        $str = '<div>{{easy}}-{{deep.value}}</div>';
        $sub = ['easy'=>'a', 'deep'=>['value'=>'b']];
        $this->assertSame('<div>a-b</div>',Ops::embrace($str,$sub));
    }

    public function testComplexEmbrace(){
        $array = ['items'=> ['one','two'] ];
        $t = Ops::embraceFromFile('embraceComplex.html',$array);
        $this->assertIsString($t);
    }

    public function testEmbraceFromFile()
    {
        $sub = ['easy'=>'a', 'deep'=>['value'=>'b']];
        $this->assertSame('<div>a-b</div>',trim(Ops::embraceFromFile('embrace.html',$sub)));
    }


    public function testPin()
    {
        $t = Ops::pin(4);
        $this->assertIsNumeric($t);
        $this->assertStringMatchesFormat('%d',$t,'Wrong format');
    }

    public function testBase64url_to_base64()
    {
        $urlSaveB64 = 'dGVzdC1tZQ';
        $shouldBe = 'dGVzdC1tZQ==';
        $this->assertSame($shouldBe,Ops::base64url_to_base64($urlSaveB64));

    }



    public function testExtrude()
    {
        $incoming = ['some'=>'value','to_strip'=>'value2'];
        $t = Ops::extrude(['some'],$incoming);
        $this->assertArrayHasKey('some',$t);
        $this->assertArrayNotHasKey('to_strip',$t);
        $this->assertCount(1,$t);

    }

    public function testHardEmbrace()
    {
        $str = 'any [[how]]';
        $sub = ['how'=>'test'];
        $this->assertSame('any test',Ops::hardEmbrace($str, $sub));
    }

    public function testDeserialize()
    {
        $input = 'eyJzb21lIjoiV0A9cmQifQ%3D%3D';
        $should = ['some'=>'W@=rd'];
        $this->assertSame($should,Ops::deserialize($input));

    }

    public function testSerialize()
    {
        $input = ['some'=>'W@=rd'];
        $should = 'eyJzb21lIjoiV0A9cmQifQ%3D%3D';
        $this->assertSame($should,Ops::serialize($input));
    }


    public function testTEmbrace()
    {
        $str = 'any <t>how</t>';
        $sub = ['how'=>'test'];
        $this->assertSame('any test',Ops::tEmbrace($str, $sub));
    }

    public function testEncryption()
    {
        $key = 'Secret23';
        $encrypted = Ops::encrypt('Hello',$key);
        $decrypted = Ops::decrypt($encrypted,$key);
        $this->assertSame($decrypted,'Hello');
    }

    public function testFlattenArray()
    {
        $testArray = ['one'=>['some'=>'value'],'two'=>['item1','item2']];
        $shouldBe = ['one.some' => 'value', 'two.0' => 'item1','two.1' =>'item2'];
        $this->assertSame($shouldBe,Ops::flattenArray($testArray));

    }

    public function testHash()
    {
        $t = Ops::hash(12);
        $this->assertRegExp('/[a-z0-9]+/i',$t);
    }

}
