<?php
namespace igk\js\common;
  

///<summary>represent expression data</summary>
class JSExpressionVar extends JSExpressionData
{ 
    protected function setValue($args){   
        $this->value = igk_getv($args, 0);       
    }
    public function getValue($options = null)
    {      
        return $this->value;    
    }
    
}