<?php
namespace igk\js\common;
 
use IGKException;

///<summary>represent expression data</summary>
class JSExpressionLitteral extends JSExpression{
    public function __construct(?string $value=null)
    { 
        $this->value = $value;
    }
    public function getValue(?object $options = null)
    {
        return $this->value;
    }
    public function setValue($litteral){
        if (is_array($litteral)){
            $litteral = trim((string)implode(", ", array_filter($litteral)));
        }
        $this->value = $litteral;
    }
}