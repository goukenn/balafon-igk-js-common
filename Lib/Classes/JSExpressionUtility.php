<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSExpressionUtility.php
// @date: 20230331 03:10:32
namespace igk\js\common;


///<summary></summary>
/**
* 
* @package igk\js\common
*/
abstract class JSExpressionUtility{
    /**
     * check if key is valid variable key name
     * @param string $key 
     * @return int|false 
     */
    public static function IsValidKey(string $key):bool{
        return preg_match("/^[\$_a-z]([_a-z]+)?$/i", $key);
    }
}