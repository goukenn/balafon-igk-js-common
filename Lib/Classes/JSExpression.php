<?php

namespace igk\js\common;

use IGK\Helper\StringUtility;
use IGK\System\Exceptions\ArgumentTypeNotValidException;
use IGKException;
use ReflectionException;

///<summary>JSExpression class </Summary>
class JSExpression
{
    protected $value;

    const JS_METH_EXPRESSION =  "/(\s)?[_\w]([_0-9\w]+)?(\s+)?\((.+)?\)/im";
    const JS_REGEX_EXPRESSION = "/^\/.+\//";

    protected function __construct()
    {
    }
    /**
     * CREATE A JS EXPRESSION TO USE IN VUEJS PHP
     * @param mixed $s 
     * @return static 
     */
    public static function Create($s)
    {
        $exp = new static;
        if (!is_string($s)) {
            if (is_array($s)) {
                $s = (object)$s;
            }
            $s = JSExpression::Stringify($s, (object)[
                "objectNotation" => true
            ]);
        }
        $exp->value = $s;
        return $exp;
    }

    /**
     * create a regex expression
     * @param string $s 
     * @return JSRegexExpression 
     */
    public static function CreateRegex(string $s){
        return new JSRegexExpression($s);
    }
    protected function setValue($value)
    {
        $this->value = $value;
    }
    public static function Import($src)
    {
        return static::Create("()=>import('" . $src . "')");
    }
    /**
     * create a method expression
     * @param string $name 
     * @param array|object|string $expression 
     * @return null|object 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function CreateMethod(string $name, $expression)
    {
        return static::Factory("Method", $name, $expression);
    }
    /**
     * factory method helper
     * @param mixed $type 
     * @param mixed $args 
     * @return null|object 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Factory($type, ...$args)
    {
        $ns = str_replace("/", "\\", dirname(StringUtility::Dir(static::class)));

        $cl = $ns . "\\JSExpression" . ucfirst($type);
        if (!class_exists($cl)) {
            igk_die(sprintf("Factory failed! class not exists : %s ", $cl . " " . static::class));
            return null;
        }
        $c = new $cl();
        $c->setValue($args);
        return $c;
    }
    ///<summary>create a property expression</summary>
    /**
     * create a property expression
     * @param mixed $name property name
     * @param mixed $args argument to pass
     * @return null|JSExpression return a property expression
     * @throws IGKException 
     */
    public static function Property($name, ...$args): JSExpression
    {
        return static::Factory("Property", $name, ...$args);
    }
    /**
     * create a litterla expression that help with : => data :{ js_property,... }
     * @param string $value 
     * @return null|object 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Litteral(string $value){
        return static::Factory(__FUNCTION__, $value);
    }
    /**
     * create a variable expression
     * @param mixed $name 
     * @return JSExpression 
     * @throws IGKException 
     * @throws ArgumentTypeNotValidException 
     * @throws ReflectionException 
     */
    public static function Var($name): JSExpression
    {
        return static::Factory("Var", $name);
    }
    /**
     * get litteral value 
     * @return string|false 
     */
    public function getValue(?object $options = null)
    {
        $_v = $this->value;
        if (is_array($_v)) {
            return json_encode($_v);
        }
        if (!is_numeric($_v) && is_string($_v) &&  (strlen($_v) > 0)) {
            // litteral operation
            switch ($_v[0]) {
                case '{':
                case '[':
                    // + | --------------------------------------------------------------------
                    // + | start with { or [ must be consider as a litteral expression
                    // + |
                    return $_v;
                case ":":
                    if ("::" != substr($_v, 0, 2)) {
                        // + | expression like : ...
                        $_v = substr($_v, 1);
                        return $_v;
                    }
                    break;
                case "\\":
                    if ("\\:" == substr($_v, 0, 2)) {
                        $_v = substr($_v, 1);
                    }
                    break;
                default:
                    if (preg_match(self::JS_METH_EXPRESSION, $_v) || preg_match(self::JS_REGEX_EXPRESSION, $_v)) {
                        return trim($_v);
                    }
                    break;
            }
            return "\"" . $_v . "\"";
        }
        return $_v;
    }

    /**
     * covert to string
     * @param mixed $tab 
     * @param mixed|IStringifyOptions $options stClass with shortNotation|objectNotation|ignoreNull|ignoreMethod|detectMethod property
     * @return string 
     * @throws IGKException 
     */
    public static function Stringify($tab, ?object $options = null)
    {
        $tq = [['n' => $tab, 'data' => null, 'is_array'=>0]];
        $s = "";
        $p = 0;
        $debug = 0;
        if ($options === null) {
            $options = (object)[];
        }
        $v_shortNotation = igk_getv($options, 'shortNotation');
        $objectNotation = igk_getv($options, 'objectNotation');
        $ignoreNull = igk_getv($options, 'ignoreNull');
        $lineFeed = igk_getv($options, 'lineFeed');
        $indent = igk_getv($options, 'indent');
        $detectMethod = igk_getv($options, 'detectMethod', true);
        $ln = $indent? $lineFeed: '';
        $meth_detect = "/\((.+)?\)$/";
        while ($qt = array_shift($tq)) {   
            $p++;
            $q = $qt['n'];
            $data = $qt['data'];
            $v_is_array = $qt['is_array'];
            $end = "";
            $ch = "";
            if ($data === null) {
                if ($objectNotation && !$v_is_array && is_array($q)) {
                    $q = (object)$q;
                }
                if ($v_is_array = is_array($q)) {
                    $s .= "[".$ln;
                    $end = "]";
                    if (igk_array_is_assoc($q)) {
                        $q = (object)$q;
                        $v_is_array = 0;
                        $s .= "{".$ln;
                        $end = "}]";
                    }
                } else if (is_object($q)) {
                    if ($q instanceof self){
                        $s .= $q->getValue($options);
                        continue;
                    }

                    $s .= "{".$ln;
                    $end = '}';
                }
            } else {
                $end = $data->end;
                $ch = $data->ch;
                $v_is_array = $data->is_array;
            }

            $ctab = $data && $data->ctab ? $data->ctab : (array)$q;
            $keys = $data && property_exists($data, 'keys') ? $data->keys : array_keys($ctab);


            while (($k = array_shift($keys)) !== null) {
                $tv = $ctab[$k];
                if ($ignoreNull && ($tv === null)) {
                    continue;
                }
                $s .= $ch;
                $v_meth_key = $detectMethod && (!is_numeric($k) && preg_match($meth_detect, $k));
                if ($v_meth_key && is_array($tv)){
                    $tv = static::CreateMethod($k, (object)$tv);
                    $k = -1;
                }
                // if (is_string($tv) && !is_numeric($k) && preg_match($meth_detect, $k)){
                //     $tv = static::CreateMethod($k, $tv); 
                //     $k = -1;                    
                // }  
                if (is_string($tv)) {
                    if ($v_meth_key ) {
                        $tv = static::CreateMethod($k, $tv);
                        $k = -1;
                    } else if (is_numeric($k)) {
                        $tv = self::Create($tv);
                        $k = -1;
                    }
                }

                // $is_array = is_array($tv);

                // writing keys
                if ((!$v_is_array) && !(is_numeric($k) && ($tv instanceof self))) {
                    // defining method 
                    if (is_numeric($k) && is_string($tv) && ($r = preg_match(self::JS_METH_EXPRESSION, $tv))) {
                        $s .= trim($tv);
                        $ch = ", ".$ln;
                        continue;
                    }
                    if (!($tv instanceof JSExpressionItem) || !$tv->no_key) {
                        $s .= sprintf("%s:", self::_GetKeyName($k, $options));
                    }
                }
                if ($tv === null) {
                    $s .= 'null';
                } else {

                    if (is_numeric($tv)) {
                        $s .= $tv;
                    } else if (is_string($tv)) {
                        // $s .= "---\"" . stripslashes($tv) . "\"";
                        $s .= "\"" . str_replace('"', '\"', $tv) . "\"";
                    } else if (is_bool($tv)) {
                        $s .= $tv ? 'true' : 'false';
                    } else if (($is_a = is_array($tv)) || is_object($tv)) {
                        $ch = ", ".$ln;
                        // igk_wln("data:sss ", $is_a, $tv, $is_a ? "assoc?=" . igk_array_is_assoc($tv) : "no");
                        if ($is_a && igk_array_is_assoc($tv)) {
                            $tv = (object)$tv;
                            // $is_a = 0;
                        }else {
                            if (!$is_a) {
                                if ($tv instanceof self) {
                                    $options->express = (object)array_merge(["name" => $k, 'is_array'=>$v_is_array], compact("end", "ch"));
                                    $s .= $tv->getValue($options);
                                    unset($options->express);
                                    continue;
                                }
                                if ($tv instanceof IJSStringify) {
                                    $options->express = (object)array_merge(["name" => $k, 'is_array'=>$v_is_array],
                                         compact("end", "ch"));
                                    $s .= $tv->stringify($options);
                                    unset($options->express);
                                    continue;
                                }
                            }
                        }
                        // igk_wln_e(__FILE__.":".__LINE__,  "-------------------------", $is_a, $tv);
                        array_unshift($tq, ['n' => $q, 
                        'data' => (object)array_merge(
                                compact("ctab", "keys", "end", "ch"),
                                ['is_array'=>$v_is_array]
                            )
                        , "is_array"=>$v_is_array]);
                        array_unshift($tq, ['n' => $tv, 'data' => null, 'is_array'=> $is_a]);
                        continue 2;
                    }
                }
                $ch = ", ".$ln;
            }
            $s .= $end;
        }
        return $s;
    }
    /**
     * get string key
     * @param mixed $k 
     * @return string 
     */
    private static function _GetKeyName(string $k, $options): string
    {
        $noStringKey = igk_getv($options, "noStringKey");
        // CHECK IF UPDATE.
        if ($noStringKey || !preg_match("/[^0-9a-zA-Z_\$]/", $k)) {
            return $k;
        }
        return "\"" . addslashes($k) . "\"";
    }

    ///<summary>return get value of this expression</summary>
    /**
     * return value of this expression
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
