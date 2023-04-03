<?php
namespace igk\js\common;

use IGK\System\IO\StringBuilder;

///<summary>represent expression data</summary>
class JSExpressionMethod extends JSExpression
{
    /**
     * method name
     * @var mixed
     */
    var $name;   

    public function setValue($args){
        list($name, $data) = $args;
        $this->name = $name;
        $this->value = $data;
    }
    public function getValue($options = null){
        $s="";
        $meth_name = "";
        $s = $this->name;
        if (strpos($s, "(")=== false)
            $s .= '()';
        $meth_name = $s;
        $s = "";

        if (is_string($v = $this->value)){        
            $v = trim($v);         
            if (strpos($v, "{")!==0)
                $v = "{".$v;
            if (strpos($v, "}", -1)===false) {
                $v .='}';
            } 
            $s .= $v;
        }else {
            $sb = new StringBuilder($s);
            $sb->append("{");
                $sb->append("return ".JSExpression::Stringify($this->value).";");
            $sb->append("}");
            $s = $meth_name .$s;
        }
        return trim($s);
    }
}