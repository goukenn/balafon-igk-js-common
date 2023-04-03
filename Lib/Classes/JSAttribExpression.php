<?php

// @author: C.A.D. BONDJE DOUE
// @filename: JSAttribExpresson.php
// @date: 20220706 21:29:07
// @desc: JS Attribute expression

namespace igk\js\common;

use IGK\System\Html\IHtmlGetValue;

/**
 * js non evalyatable expression . 
 * @package igk\js\common
 */
class JSAttribExpression implements IHtmlGetValue{
    private $value;
    protected function __construct(){        
    }
    public static function Create($expression){
        $s = new static;
        $s->value = $expression;
        return $s;
    }
    /**
     * retrieve the value 
     * @param mixed $options 
     * @return mixed 
     */
    public function getValue($options=null){
        if ($options)
            $options->flag_no_attrib_escape = true;
        return $this->value;
    }
}

