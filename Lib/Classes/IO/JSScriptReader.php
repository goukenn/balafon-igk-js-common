<?php
// @author: C.A.D. BONDJE DOUE
// @file: JSScriptReader.php
// @date: 20230301 18:26:07
namespace igk\js\common\IO;


///<summary></summary>
/**
 * 
 * @package igk\js\common\IO
 */
class JSScriptReader
{
    var $type;
    var $depth;
    var $src;
    var $value;
    var $offset = 0;

    const TOKEN = 'abcdefghijklmnoqrstuvwxyz0123456789ABCDEFGHIJKLMNOQRSTUVWXYZ_';
    const OPERATORS = [
        "+", "-", "/", "%", "*", "++", "--", "==", "===", "=", "||", "|", "!", "!=", "!==", "&&", "^", "+=", "-=", ",", "\n", "/=", "*=", "+=",
        "*=", ">=", ">", "<=", "<"
    ];
    const RESERVERD_WORDS = [
        "abstract",
        "arguments",        "await",    "boolean", "break",        "byte",        "case",        "catch", "char",
        "class",    "const",        "continue", "debugger",        "default",    "delete",        "do", "double",        "else",        "enum",
        "eval", "export",    "extends",    "false",        "final", "finally",        "float",        "for",        "function", "goto",
        "if",        "implements",    "import", "in",        "instanceof",        "int",        "interface", "let",        "long",
        "native",        "new", "null",        "package",        "private",        "protected", "public",
        "return",        "short",        "static", "super",        "switch",    "synchronized",
        "this", "throw",        "throws",        "transient",        "true", "try",        "typeof",        "var",        "void", "volatile",        "while",        "with",
        "yield"
    ];
    const TOKEN_LITTERAL = 1;
    const TOKEN_VAR = 2;
    const TOKEN_CONST = 3;
    const TOKEN_WORD = 4;
    const TOKEN_RESERVED_WORD = 5;
    const TOKEN_REGEX = 6;
    const TOKEN_OPERATOR = 7;
    const TOKEN_COMMENT = 8;
    const TOKEN_BRACKET = 9;
    const TOKEN_NUMBER = 10;

    private $m_length;

    private function replyCore($n)
    {
        if (!empty($n)) {
            if (is_numeric($n)) {
                $this->type = self::TOKEN_NUMBER;
            } else {
                if (in_array($n, self::RESERVERD_WORDS)) {
                    $this->type = self::TOKEN_RESERVED_WORD;
                } else if (in_array($n, self::OPERATORS)) {
                    $this->type = self::TOKEN_OPERATOR;
                } else {
                    $this->type = self::TOKEN_WORD;
                }
            }
            $this->value = $n;
            return true;
        }
        return false;
    }
    public function read()
    {
        if (empty($this->src) || ($this->offset >= $this->length())) {
            return false;
        }
        $n = ""; // name 
        $c = ""; // char
        $pos = &$this->offset;
        $src = $this->src;
        $depth = &$this->depth;
        $op_detect = false;
        while ($pos < $this->length()) {
            $ch = $src[$pos];
            switch ($ch) {
                case '{':
                case '(':
                case '[':
                    if (!empty($n)) {
                        return $this->replyCore($n);
                    }
                    $depth++;
                    $pos++;
                    $this->value = $ch;
                    $this->type = self::TOKEN_BRACKET;
                    return true;
                case '}':
                case ']':
                case ')':
                    if (!empty($n)) {
                        return $this->replyCore($n);
                    }
                    $depth--;
                    $pos++;
                    $this->value = $ch;
                    $this->type = self::TOKEN_BRACKET;
                    return true;
                case ',':
                    if (empty($n)) {
                        $pos++;
                        return $this->replyCore($ch);
                    } else {
                        return $this->replyCore($n);
                        igk_die("not valid: , " . $n);
                    }
                    break;
                case '\'':
                case '"':
                case '`':
                    $this->value = igk_str_read_brank($src, $pos, $ch, $ch);
                    $this->type = self::TOKEN_LITTERAL;
                    $pos++;
                    return true;
                case '.':
                    if (!empty($n) && is_numeric($n.$ch)){
                        $n.='.';
                    }else{
                        return $this->replyCore($n);
                    }
                    break;
                case '=':
                case '|':
                case '&':
                case '!':
                case '>':
                case '<':
                case '/':
                case '*':
                    if (empty($n)) {
                        $n .= $ch;
                        $op_detect = true;
                    } else {
                        if ($op_detect)
                            $n .= $ch;
                        else
                            return $this->replyCore($n);
                    }
                    break;
                default:
                    if ($ch == "/") {
                        $next = $ch . substr($src, $pos + 1, 1);
                        if ($next == "/*") {
                            // multiline comment 
                            $this->type = self::TOKEN_COMMENT;
                            if (($lpos = strpos($src, "*/", $pos + 1)) !== false) {
                                $this->value = substr($src, $pos, $lpos + 2);
                                $pos = $lpos + 2;
                            } else {
                                $this->value = substr($src, $pos) . "*/";
                                $pos = $this->length();
                            }
                            return true;
                        } else if ($next == "//") {
                            // single line comment 
                            $this->type = self::TOKEN_COMMENT;
                            if (($lpos = strpos($src, "\n", $pos + 1)) !== false) {
                                $this->value = substr($src, $pos, $lpos + 1);
                                $pos = $lpos + 1;
                            } else {
                                $this->value = substr($src, $pos);
                                $pos = $this->length();
                            }
                            return true;
                        } else {
                            if ($next == "/=") {
                                $n .= $next;
                                $pos += 2;
                            } else {
                                $n .= $ch;
                                $pos += 1;
                            }
                            // it will be handle by reply core 
                        }
                    }

                    if (!$op_detect && (strpos(self::TOKEN, $ch) !== false)) {
                        $n .= $ch;
                    } else {

                        if ($this->replyCore($n)) {
                            $pos++;
                            return true;
                        }
                    }
                    break;
            }
            $pos++;
        }
        if (!empty($n)) {
            $pos++;
            return $this->replyCore($n);
        }
        return false;
    }
    public function length()
    {
        return $this->m_length ?? $this->m_length = strlen($this->src ?? '');
    }
}
