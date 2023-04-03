<?php

// @author: C.A.D. BONDJE DOUE
// @filename: JSExpressionObjectResult.php
// @date: 20220812 08:55:46
// @desc: object expression

namespace igk\js\common;

/**
 * 
 * @package igk\js\common
 */
class JSExpressionObjectResult extends JSExpressionItem implements IJSStringify{
    var $data;

    var $no_key = true;
    /**
     * express data to be include in 
     * @param mixed $option 
     * @return string 
     */
    public function stringify($option = null) {       
        return sprintf("...%s", $this->data);
    } 

}
