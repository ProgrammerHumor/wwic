<?php

namespace wwic;

class TokenType {
    const PERIOD = 0;
    const SEMICOLON = 1;
    const UNDERSCORE = 2;
    const OPEN_PAREN = 3;
    const CLOSING_PAREN = 4;
    const EQUALS = 5;

    const BURGAR = 6;
    const BORGAR = 7;
    const ANIME = 8;
    const AND = 9;
    const MY = 10;
    const FETISH = 11;
    const THING = 12;
    const FOOT = 13;
    const FORTNITE = 14;
    const YOINK = 15;
    const BIG = 16;
    const THEN = 17;
    const LARGE = 18;
    const BRINGBACK = 19;
    const SHIT = 20;
    const YANK = 21;
    const NOTNOT = 22;

    const STRING = 23;
    const IDENTIFIER = 24;

    const EOF = 25;
    const UNKNOWN = 26;
}

class Token {
    private $type;
    private $string;
    private $line;

    public function __construct($type, $string, $line) {
        $this->type = $type;
        $this->string = $string;
        $this->line = $line;
    }

    public function getType() {
        return $this->type;
    }

    public function getString() {
        return $this->string;
    }

    public function getLine() {
        return $this->line;
    }

    public function __tostring() {
        return "token { " . $this->type . ", " . $this->string . ", " . $this->line . " }";
    }
}

class Scanner {
    private $position = 0;
    private $code;
    private $start;
    private $line;

    public function __construct($code) {
        $this->code = $code;
    }

    private function peek() {
        return $this->code[$this->position];
    }

    private function peekNext() {
        if ($this->position < strlen($this->code)) {
            return $this->code[$this->position + 1];
        }
    }

    private function advance() {
        return $this->code[$this->position++];
    }

    private function match($char) {
        if ($this->peek() == $char) {
            $this->advance();
            return true;
        }

        return false;
    }

    private static function isWhitespace($char) {
        return $char == ' ' || $char == '\n' || $char == '\t';
    }

    private function isEof() {
        return $this->position >= strlen($this->code);
    }

    private function consumeComment() {
        $null = array();
        while (!$this->isEof()) {
            if (preg_match("/\Gpain/", $this->code, $null, 0, $this->position)) {
                $this->position += strlen("pain");
                return;
            }

            $this->advance();
        }
    }

    /*
      Skips whitespace (comments are threated as whitespace because why not)
     */
    private function skipWhitespace() {
        $finished = false;
        while (!$finished) {
            if ($this->isEof()) {
                $finished = true;
            } elseif (self::isWhitespace($this->peek())) {
                $this->advance();
            } else {
                $finished = true;
            }
        }
    }

    private function makeToken($type) {
        $token = new Token(
            $type,
            substr($this->code, $this->start, $this->position - $this->start), // code[start:position]
            $this->line
        );
        return $token;
    }

    private function makeNumber() {
        while (!$this->isEof() && is_numeric($this->peek())) {
            $this->advance();
        }

        return $this->makeToken(TokenType::STRING);
    }

    private function makeIdentOrKW() {
        while (!$this->isEof() && ctype_alpha($this->peek())) {
            $this->advance();
        }

        $kws = array(
            "burgar" => TokenType::BURGAR,
            "borgar" => TokenType::BORGAR,
            "anime" => TokenType::ANIME,
            "and" => TokenType::AND,
            "my" => TokenType::MY,
            "fetish" => TokenType::FETISH,
            "thing" => TokenType::THING,
            "foot" => TokenType::FOOT,
            "fortnite" => TokenType::FORTNITE,
            "yoink" => TokenType::YOINK,
            "big" => TokenType::BIG,
            "then" => TokenType::THEN,
            "large" => TokenType::LARGE,
            "bringback" => TokenType::BRINGBACK,
            "shit" => TokenType::SHIT,
            "yank" => TokenType::YANK,
            "notnot" => TokenType::NOTNOT,
        );

        $ident = substr($this->code, $this->start, $this->position - $this->start);

        if (isset($kws[$ident])) {
            return new Token(
                $kws[$ident],
                $ident,
                $this->line
            );
        }

        return new Token(
            TokenType::IDENTIFIER,
            $ident,
            $this->line
        );
    }

    private function makeString() {
        while (!$this->isEof() && $this->peek() != '"') {
            $this->advance();
        }

        $token = $this->makeToken(TokenType::STRING);

        $this->advance();

        return $token;
    }

    public function getNextToken() {
        $this->skipWhitespace();

        var_dump($this->peek());

        if ($this->isEof()) {
            return $this->makeToken(TokenType::EOF);
        }

        $this->start = $this->position;

        $char = $this->advance();

        // number (ie a string)
        if (is_numeric($char)) {
            return $this->makeNumber();
        }

        // identifier or keyword
        if (ctype_alpha($char)) {
            return $this->makeIdentOrKW();
        }

        switch ($char) {
        case '.':
            return $this->makeToken(TokenType::PERIOD);
        case ';':
            return $this->makeToken(TokenType::SEMICOLON);
        case '_':
            return $this->makeToken(TokenType::UNDERSCORE);
        case '(':
            return $this->makeToken(TokenType::OPEN_PAREN);
        case ')':
            return $this->makeToken(TokenType::CLOSING_PAREN);
        case '=':
            return $this->makeToken(TokenType::EQUALS);
        case '"':
            return $this->makeString();
        }

        while (!$this->isEof() && ctype_alpha($this->peek())) {
            $this->advance();
        }

        return $this->makeToken(TokenType::UNKNOWN);
    }
}
