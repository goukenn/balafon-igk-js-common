<?php

// @author: C.A.D. BONDJE DOUE
// @filename: JSExpressionTest.php
// @date: 20220730 13:17:35
// @desc: js expression test
// @phpunit: phpunit -c phpunit.xml.dist src/application/Packages/Modules/igk/js/common/Lib/Tests/JSExpressionTest.php  

namespace igk\js\common\Tests;

use igk\js\common\JSExpression;
use IGK\Tests\BaseTestCase; 

class JSExpressionTest extends BaseTestCase
{
    public function setUp():void{
        igk_require_module(\igk\js\common::class);
    }
    public function test_array_expression()
    {
        $m = ["key" => [":innovate", "\:sample", "create", "develop", "1", ":invoke()", ":8 + 80"]];
        $d = JSExpression::Stringify((object)$m, (object) ["noStringKey"=>1]);
        $this->assertEquals(
            '{key:[innovate, ":sample", "create", "develop", 1, invoke(), 8 + 80]}',
            $d,
            "expression not match"
        );
    }
    public function test_array_expression_2()
    {        
        $d =  JSExpression::Stringify(["one", ":help", ":8 + 80"]);
        $this->assertEquals(
            '["one", help, 8 + 80]',
            $d,
            "expression not match"
        );
    }

    public function test_array_expression_3()
    {        
        $d =  JSExpression::Stringify("data(){ return {};}");
        $this->assertEquals(
            'data(){ return {};}',
            $d,
            "expression not match"
        );
    }
    public function test_stringify_array_method(){
        $d =  JSExpression::Stringify((object)['data()'=>[
            'BrandName'=>'test_brand', 
        ]]);
        $this->assertEquals(
            '{data(){return {BrandName:"test_brand"};}}',
            $d,
            "expression not match."
        );
    }

    public function _test_stringify_array_method_2(){
        $d =  JSExpression::Stringify((object)['data()'=>[ 
            JSExpression::CreateMethod('Info()', '{ return 0; }')
        ]]);
        $this->assertEquals(
            'data(){return {Info(){ return 0; }}}',
            $d,
            "expression not match."
        );
    }
}
