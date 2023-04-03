<?php

// @author: C.A.D. BONDJE DOUE
// @filename: JSExpressionOptions.php
// @date: 20221203 08:37:56
// @desc: js expression options


namespace igk\js\common;
/**
 * 
 * @package 
 */
class JSExpressionOptions {
    /**
     * no string key
     */
    var $noStringKey;

    /**
     * use object notation
     * @var ?bool
     */
    var $objectNotation;

    /**
     * ignore null
     * @var ?bool
     */
    var $ignoreNull;

    var $lineFeed = "\n";

    var $indent;
}