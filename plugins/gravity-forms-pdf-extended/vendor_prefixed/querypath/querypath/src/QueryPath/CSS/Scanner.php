<?php

/** @file
 * The scanner.
 */
namespace GFPDF_Vendor\QueryPath\CSS;

/**
 * Scanner for CSS selector parsing.
 *
 * This provides a simple scanner for traversing an input stream.
 *
 * @ingroup querypath_css
 */
final class Scanner
{
    var $is = NULL;
    public $value = NULL;
    public $token = NULL;
    var $recurse = FALSE;
    var $it = 0;
    /**
     * Given a new input stream, tokenize the CSS selector string.
     * @see InputStream
     * @param InputStream $in
     *  An input stream to be scanned.
     */
    public function __construct(\GFPDF_Vendor\QueryPath\CSS\InputStream $in)
    {
        $this->is = $in;
    }
    /**
     * Return the position of the reader in the string.
     */
    public function position()
    {
        return $this->is->position;
    }
    /**
     * See the next char without removing it from the stack.
     *
     * @return char
     *  Returns the next character on the stack.
     */
    public function peek()
    {
        return $this->is->peek();
    }
    /**
     * Get the next token in the input stream.
     *
     * This sets the current token to the value of the next token in
     * the stream.
     *
     * @return int
     *  Returns an int value corresponding to one of the Token constants,
     *  or FALSE if the end of the string is reached. (Remember to use
     *  strong equality checking on FALSE, since 0 is a valid token id.)
     */
    public function nextToken()
    {
        $tok = -1;
        ++$this->it;
        if ($this->is->isEmpty()) {
            if ($this->recurse) {
                throw new \GFPDF_Vendor\QueryPath\Exception("Recursion error detected at iteration " . $this->it . '.');
                exit;
            }
            //print "{$this->it}: All done\n";
            $this->recurse = TRUE;
            $this->token = FALSE;
            return FALSE;
        }
        $ch = $this->is->consume();
        //print __FUNCTION__ . " Testing $ch.\n";
        if (\ctype_space($ch)) {
            $this->value = ' ';
            // Collapse all WS to a space.
            $this->token = $tok = \GFPDF_Vendor\QueryPath\CSS\Token::white;
            //$ch = $this->is->consume();
            return $tok;
        }
        if (\ctype_alnum($ch) || $ch == '-' || $ch == '_') {
            // It's a character
            $this->value = $ch;
            //strtolower($ch);
            $this->token = $tok = \GFPDF_Vendor\QueryPath\CSS\Token::char;
            return $tok;
        }
        $this->value = $ch;
        switch ($ch) {
            case '*':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::star;
                break;
            case \chr(\ord('>')):
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::rangle;
                break;
            case '.':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::dot;
                break;
            case '#':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::octo;
                break;
            case '[':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::lsquare;
                break;
            case ']':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::rsquare;
                break;
            case ':':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::colon;
                break;
            case '(':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::lparen;
                break;
            case ')':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::rparen;
                break;
            case '+':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::plus;
                break;
            case '~':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::tilde;
                break;
            case '=':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::eq;
                break;
            case '|':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::pipe;
                break;
            case ',':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::comma;
                break;
            case \chr(34):
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::quote;
                break;
            case "'":
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::squote;
                break;
            case '\\':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::bslash;
                break;
            case '^':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::carat;
                break;
            case '$':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::dollar;
                break;
            case '@':
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::at;
                break;
        }
        // Catch all characters that are legal within strings.
        if ($tok == -1) {
            // TODO: This should be UTF-8 compatible, but PHP doesn't
            // have a native UTF-8 string. Should we use external
            // mbstring library?
            $ord = \ord($ch);
            // Characters in this pool are legal for use inside of
            // certain strings. Extended ASCII is used here, though I
            // Don't know if these are really legal.
            if ($ord >= 32 && $ord <= 126 || $ord >= 128 && $ord <= 255) {
                $tok = \GFPDF_Vendor\QueryPath\CSS\Token::stringLegal;
            } else {
                throw new \GFPDF_Vendor\QueryPath\CSS\ParseException('Illegal character found in stream: ' . $ord);
            }
        }
        $this->token = $tok;
        return $tok;
    }
    /**
     * Get a name string from the input stream.
     * A name string must be composed of
     * only characters defined in Token:char: -_a-zA-Z0-9
     */
    public function getNameString()
    {
        $buf = '';
        while ($this->token === \GFPDF_Vendor\QueryPath\CSS\Token::char) {
            $buf .= $this->value;
            $this->nextToken();
            //print '_';
        }
        return $buf;
    }
    /**
     * This gets a string with any legal 'string' characters.
     * See CSS Selectors specification, section 11, for the
     * definition of string.
     *
     * This will check for string1, string2, and the case where a
     * string is unquoted (Oddly absent from the "official" grammar,
     * though such strings are present as examples in the spec.)
     *
     * Note:
     * Though the grammar supplied by CSS 3 Selectors section 11 does not
     * address the contents of a pseudo-class value, the spec itself indicates
     * that a pseudo-class value is a "value between parenthesis" [6.6]. The
     * examples given use URLs among other things, making them closer to the
     * definition of 'string' than to 'name'. So we handle them here as strings.
     */
    public function getQuotedString()
    {
        if ($this->token == \GFPDF_Vendor\QueryPath\CSS\Token::quote || $this->token == \GFPDF_Vendor\QueryPath\CSS\Token::squote || $this->token == \GFPDF_Vendor\QueryPath\CSS\Token::lparen) {
            $end = $this->token == \GFPDF_Vendor\QueryPath\CSS\Token::lparen ? \GFPDF_Vendor\QueryPath\CSS\Token::rparen : $this->token;
            $buf = '';
            $escape = FALSE;
            $this->nextToken();
            // Skip the opening quote/paren
            // The second conjunct is probably not necessary.
            while ($this->token !== FALSE && $this->token > -1) {
                //print "Char: $this->value \n";
                if ($this->token == \GFPDF_Vendor\QueryPath\CSS\Token::bslash && !$escape) {
                    // XXX: The backslash (\) is removed here.
                    // Turn on escaping.
                    //$buf .= $this->value;
                    $escape = TRUE;
                } elseif ($escape) {
                    // Turn off escaping
                    $buf .= $this->value;
                    $escape = FALSE;
                } elseif ($this->token === $end) {
                    // At end of string; skip token and break.
                    $this->nextToken();
                    break;
                } else {
                    // Append char.
                    $buf .= $this->value;
                }
                $this->nextToken();
            }
            return $buf;
        }
    }
    // Get the contents inside of a pseudoClass().
    public function getPseudoClassString()
    {
        if ($this->token == \GFPDF_Vendor\QueryPath\CSS\Token::quote || $this->token == \GFPDF_Vendor\QueryPath\CSS\Token::squote || $this->token == \GFPDF_Vendor\QueryPath\CSS\Token::lparen) {
            $end = $this->token == \GFPDF_Vendor\QueryPath\CSS\Token::lparen ? \GFPDF_Vendor\QueryPath\CSS\Token::rparen : $this->token;
            $buf = '';
            $escape = FALSE;
            $this->nextToken();
            // Skip the opening quote/paren
            // The second conjunct is probably not necessary.
            while ($this->token !== FALSE && $this->token > -1) {
                //print "Char: $this->value \n";
                if ($this->token == \GFPDF_Vendor\QueryPath\CSS\Token::bslash && !$escape) {
                    // XXX: The backslash (\) is removed here.
                    // Turn on escaping.
                    //$buf .= $this->value;
                    $escape = TRUE;
                } elseif ($escape) {
                    // Turn off escaping
                    $buf .= $this->value;
                    $escape = FALSE;
                } elseif ($this->token == \GFPDF_Vendor\QueryPath\CSS\Token::lparen) {
                    $buf .= "(";
                    $buf .= $this->getPseudoClassString();
                    $buf .= ")";
                } elseif ($this->token === $end) {
                    // At end of string; skip token and break.
                    $this->nextToken();
                    break;
                } else {
                    // Append char.
                    $buf .= $this->value;
                }
                $this->nextToken();
            }
            return $buf;
        }
    }
    /**
     * Get a string from the input stream.
     * This is a convenience function for getting a string of
     * characters that are either alphanumber or whitespace. See
     * the Token::white and Token::char definitions.
     *
     * @deprecated This is not used anywhere in QueryPath.
     */
    /*
      public function getStringPlusWhitespace() {
        $buf = '';
        if($this->token === FALSE) {return '';}
        while ($this->token === Token::char || $this->token == Token::white) {
          $buf .= $this->value;
          $this->nextToken();
        }
        return $buf;
      }*/
}
