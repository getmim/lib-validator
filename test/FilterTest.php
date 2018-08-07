<?php

use PHPUnit\Framework\TestCase;
use LibValidator\Filter\General;

$base = dirname(__DIR__);

require_once $base . '/modules/lib-validator/filter/General.php';

class FilterTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function testFilter($method, $value, $expect){
        $val = General::$method($value, 0, 0, 0, 0);
        $this->assertEquals($expect, $val);
    }

    public function dataProvider(){
        return [
            ['array', 0, [0]],
            ['array', 1, [1]],
            ['array', [], []],
            ['array', (object)[], []],
            ['array', (object)['a'=>1], ['a'=>1]],

            ['boolean', 0, false],
            ['boolean', [], false],
            ['boolean', 1, true],
            ['boolean', [1], true],
            ['boolean', (object)[], true],

            ['float', 0, (float)0],
            ['float', 1, (float)1],
            ['float', '12.21', 12.21],

            ['integer', 0, 0],
            ['integer', 1, 1],
            ['integer', [], 0],
            ['integer', '0', 0],
            ['integer', '12.2', 12],
            ['integer', 33.21, 33],

            ['lowercase', 'Lorem ipsum', 'lorem ipsum'],
            ['lowercase', 'LoremIpsum', 'loremipsum'],
            ['lowercase', 'lorem', 'lorem'],

            ['object', [], (object)[]],
            ['object', [1], (object)[1]],
            ['object', 1, (object)1],

            ['string', 1, '1'],
            ['string', 0, '0'],
            ['string', 12.22, '12.22'],

            ['ucwords', 'loremIpsum', 'LoremIpsum'],
            ['ucwords', 'lorem ipsum', 'Lorem Ipsum'],

            ['uppercase', 'lorem', 'LOREM'],
            ['uppercase', 'LoREm', 'LOREM']
        ];
    }
}