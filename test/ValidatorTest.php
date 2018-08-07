<?php

use PHPUnit\Framework\TestCase;
use LibValidator\Validator\General;

$base = dirname(__DIR__);

require_once $base . '/modules/lib-validator/validator/General.php';
require_once $base . '/test/core/global.php';

class ValidatorTest extends TestCase
{

    /**
     * @dataProvider validatorProvider
     */
    public function testValidator($method, $expect, $value, $options=null){
        $result = General::$method($value, $options, 0, 0, 0);
        if(is_array($result))
            $result = $result[0];

        $this->assertEquals($expect, $result);
    }

    public function validatorProvider(){
        return [
            ['array',   null,   [],         true],
            ['array',   null,   [1,2],      true],
            ['array',   null,   ['a'=>1],   true],
            ['array',   '1.0',  1,          true],
            ['array',   '1.0',  (object)[], true],
            ['array',   '1.0',  null,       true],

            ['array',   null,   [],         'indexed'],
            ['array',   null,   [1,2],      'indexed'],
            ['array',   '1.1',  ['a'=>1],   'indexed'],

            ['array',   null,   ['a'=>1],   'assoc'],
            ['array',   '1.2',  [12,3],     'assoc'],

            ['date',    null,   '2018-08-04',       'Y-m-d'],
            ['date',    null,   '2018/08/04 13',    'Y/m/d H'],
            ['date',    '2.0',  'lorem',            'Y-m-d'],
            ['date',    '2.0',  false,              'Y-m-d'],
            
            ['email',   null,   'name@host.com',    true],
            ['email',   '3.0',  'host.com',         true],

            ['in',      null,   'one',  ['one', 'two', 'three']],
            ['in',      '4.0',  'one',  ['four', 'five', 'six']],

            ['ip',      null,   '192.168.1.2',          true],
            ['ip',      null,   '2001:db8:0:0:1::1',    true],
            ['ip',      '5.0',  '12.3333',              true],

            ['ip',      null,   '192.168.1.2',          '4'],
            ['ip',      '5.1',  '2001:db8:0:0:1::1',    '4'],
            ['ip',      '5.1',  'lorem',                '4'],

            ['ip',      null,   '2001:db8:0:0:1::1',    '6'],
            ['ip',      '5.2',  '192.168.1.2',          '6'],
            ['ip',      '5.2',  'lr',                   '6'],

            ['length',  null,   'lorem',    (object)['min'=>3, 'max'=>6]],
            ['length',  null,   'lorem',    (object)['min'=>5, 'max'=>5]],

            ['length',  '6.0',  'lorem',    (object)['min'=>6, 'max'=>10]],
            ['length',  '6.1',  'lorem',    (object)['min'=>1, 'max'=>4]],

            ['notin',   null,   'one',  ['two', 'three', 'four']],
            ['notin',   '7.0',  'one',  ['one', 'two', 'three']],

            ['numeric', null,   12,         true],
            ['numeric', null,   '12',       true],
            ['numeric', null,   '12.3',     true],
            ['numeric', null,   12.33,      true],
            ['numeric', '8.0',  'lorem',    true],
            ['numeric', '8.0',  true,       true],

            ['numeric', null,   '13',       (object)['min'=>13]],
            ['numeric', null,   14,         (object)['min'=>13]],
            ['numeric', '8.1',  12,         (object)['min'=>13]],
            ['numeric', '8.1',  '12',       (object)['min'=>13]],

            ['numeric', null,   12,         (object)['max'=>12]],
            ['numeric', null,   '11',       (object)['max'=>12]],
            ['numeric', '8.2',  12,         (object)['max'=>11]],
            ['numeric', '8.2',  '12',       (object)['max'=>11]],

            ['numeric', null,   12.12,      (object)['decimal'=>2]],
            ['numeric', null,   '12.123',   (object)['decimal'=>3]],
            ['numeric', '8.3',  12.12,      (object)['decimal'=>1]],
            ['numeric', '8.3',  '12.12',    (object)['decimal'=>1]],
            ['numeric', '8.3',  32.1,       (object)['decimal'=>2]],

            ['object',  null,   (object)[],         true],
            ['object',  null,   new \DateTime(),    true],
            ['object',  '9.0',  [],                 true],
            ['object',  '9.0',  12,                 true],

            ['regex',   null,   'a',    '!^.$!'],
            ['regex',   null,   '12a1', '!^[0-9]{2}[a-z][0-9]$!'],
            ['regex',   '10.0', '12',   '!^[a-z]$!'],

            ['required',    null,   'a',    true],
            ['required',    null,   'a',    true],
            ['required',    '11.0', null,   true],
            ['required',    '11.0', null,   true],

            ['text',    null,   'lorem',    true],
            ['text',    '12.0', 12,         true],
            ['text',    '12.0', true,       true],
            ['text',    '12.0', null,       true],

            ['text',    null,   'lorem-09_str', 'slug'],
            ['text',    '12.1', 'Lorem-09_str', 'slug'],

            ['text',    null,   'AlNum-0-9',    'alnumdash'],
            ['text',    '12.2', 'AlNum_0-9',    'alnumdash'],

            ['text',    null,   'AlphA',        'alpha'],
            ['text',    '12.3', 'Alph0',        'alpha'],

            ['text',    null,   'Alph0',        'alnum'],
            ['text',    '12.4', 'Alph-9',       'alnum'],

            ['url',     null,   'http://site.com',          true],
            ['url',     null,   'http://site.com/?a=b',     true],
            ['url',     null,   'http://site.com/l/?a=b',   true],
            ['url',     '13.0', 'site.com/l/?a=b',          true],
            ['url',     '13.0', '/l/?a=b',                  true],

            ['url',     null,   'http://site.com/',         (object)['path'=>true]],
            ['url',     null,   'http://site.com/lorem',    (object)['path'=>true]],
            ['url',     null,   'http://site.com/lorem/',   (object)['path'=>true]],
            ['url',     '13.1', 'http://site.com',          (object)['path'=>true]],

            ['url',     null,   'http://site.com?a=b',      (object)['query'=>true]],
            ['url',     null,   'http://site.com/?a=b',     (object)['query'=>true]],
            ['url',     null,   'http://site.com/a?a=b',    (object)['query'=>true]],
            ['url',     null,   'http://site.com/a/?a=b',   (object)['query'=>true]],
            ['url',     '13.2', 'http://site.com/a/',       (object)['query'=>true]],
            ['url',     '13.2', 'http://site.com/a',        (object)['query'=>true]],

            ['url',     null,   'http://site.com?a=b',      (object)['query'=>['a']]],
            ['url',     '13.3', 'http://site.com?b=b',      (object)['query'=>'a']],
            ['url',     '13.3', 'http://site.com?b=b',      (object)['query'=>['a']]]
        ];
    }
}