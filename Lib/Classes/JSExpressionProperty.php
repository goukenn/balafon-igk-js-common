<?php
namespace igk\js\common;
  

///<summary>represent expression data</summary>
class JSExpressionProperty extends JSExpressionData
{
    private $property;
    protected function setValue($args){
        if (is_array($args)){
            list($name, $data) = $args;
            $this->property = $name;
            $this->value = $data;
        }else { 
            $this->value = $args;
        }
    }
    public function getValue($options = null)
    {    
        if (is_string($this->value)){
            $s = $this->value;
            $ck = ""; 
            if ($options){
                $n = igk_getv($options->express, 'a');
                if (!$n && is_numeric($options->express->name)){
                    $ck = $this->property.":";                    
                }
            } 
            return $ck.$s;
        } 
        return $this->_getValue($this->property, $options);       
    }
}