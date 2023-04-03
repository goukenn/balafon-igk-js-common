<?php
// @author: C.A.D. BONDJE DOUE
// @file: JExpressionStringifyTest.php
// @date: 20230303 12:44:34
// phpunit -c phpunit.xml.dist ./src/application/Packages/Modules/igk/js/common/Lib/Tests/JExpressionStringifyTest.php
namespace igk\js\common\Tests;

use IGK\Tests\BaseTestCase;
use igk\js\common\JSExpression; 
use IGK\Tests\Controllers\ModuleBaseTestCase;

///<summary></summary>
/**
* 
* @package igk\js\common
*/
class JExpressionStringifyTest extends ModuleBaseTestCase{

   
     
    public function test_render(){

        $d = '{messages:{en:{"app.title":"ok"}, fr:{"app.title":"not ok"}}}';
        $s = JSExpression::Stringify((object)[ 
            "messages" => [
                "en"=>[
                    "app.title"=>"ok"
                ],
                "fr"=>[
                    "app.title"=>"not ok"
                ]
            ]
        ], (object)['objectNotation' => true]);
        $this->assertEquals($d, $s);
    }
    public function test_render_block_quote(){ 
        $d = '{"messages \"info\"":"OK"}';
        $s = JSExpression::Stringify((object)[ 
            "messages \"info\"" => "OK"
        ], (object)['objectNotation' => true]);
        $this->assertEquals($d, $s);
    }
    public function test_render_block_quote_slash(){ 
        $d = '{"err.login.failed":"/!\\ Login ou mot de passe invalide, Essayez à nouveau."}';
        $s = JSExpression::Stringify((object)[ 
            "err.login.failed"=>"/!\ Login ou mot de passe invalide, Essayez à nouveau."
        ], (object)['objectNotation' => true]);
        $this->assertEquals($d, $s);
    }
}