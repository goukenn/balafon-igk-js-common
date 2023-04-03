<?php
// @author: C.A.D. BONDJE DOUE
// @file: %modules%/igk/js/common/global.php
// @date: 20211012 11:07:11

// + module entry file 

use igk\js\common\JSExpression;

use function igk_get_module as get_module;

function igk_html_node_script_var($name, $data, $type="const"){
    if (!in_array($type, explode("|", "var|const|let")))
        igk_die("type not allowed"); 
    $n = igk_create_node("script");
    $n->Content = $type." ".$name. " = ".JSExpression::Create($data);
    return $n;
}