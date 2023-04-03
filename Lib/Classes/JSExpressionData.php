<?php
namespace igk\js\common;
 
use IGKException;

///<summary>represent expression data</summary>
class JSExpressionData extends JSExpression
{
    protected function setValue($v){
        if (!is_array($v)){
            throw new IGKException("Array expected");
        }
        $this->value = $v;
    }
    protected function _getValue($name, $options=null){
        $polyfillv = 3;
        $short = true;
        if ($options && property_exists($options, "polyfill") ){
            if ($options->polyfill)
                $polyfillv = $options->polyfill->getVersion();
            $short = igk_getv($options, 'shortNotation'); 
        } 
        $m = "";
       
        $s = json_encode($this->value);
        if ($polyfillv==3){
            if ($short){
                $m = $name."(){";
            }else {
                $m = "function(){";
            }
            return $m."return " . $s . ";}"; 
        }
        return $s;
    }
    public function getValue($options=null)
    {
        return $this->_getValue("data", $options); 
    }
}