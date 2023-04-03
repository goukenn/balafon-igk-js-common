<?php

namespace igk\js\common;

/**
 * regex expression
 * @package igk\js\common
 */
class JSRegexExpression extends JSExpression{
    public  function __construct(string $v)
    {
        parent::__construct();
        $this->value = $v;
    }
    public function getValue(?object $options = null)
    {
        return $this->value;
    }
}