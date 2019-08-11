<?php

include_once "scanner.php";

if ($argc < 2) {
    echo "Usage: php %s PROGRAM" . PHP_EOL;
    exit(1);
}

$filename = $argv[1];
$code = file_get_contents($filename);

$scanner = new wwic\Scanner($code);

$token = $scanner->getNextToken();
while ($token->getType() != wwic\TokenType::EOF) {
    echo "$token" . PHP_EOL;
    $token = $scanner->getNextToken();
}
