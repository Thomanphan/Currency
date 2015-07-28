#!/usr/bin/env php
<?php

require __DIR__ . "/../vendor/autoload.php";

$fromCurrencyCode = empty($argv[1]) ? "GBP" : $argv[1];
$toCurrencyCode   = empty($argv[2]) ? "USD" : $argv[2];

$calculator = new djekl\Currency\Calculator;

print "convert one to one currency ({$fromCurrencyCode} -> {$toCurrencyCode})" . PHP_EOL;
print json_encode($calculator->convert($fromCurrencyCode, $toCurrencyCode), JSON_PRETTY_PRINT);

print PHP_EOL;
print PHP_EOL;

print "convert a group of currencies against each other" . PHP_EOL;
print json_encode($calculator->massConvert(__DIR__, [
    "CHF" => "Swiss Franc",
    "EUR" => "Euro",
    "GBP" => "British Pound",
    "USD" => "US Dollar",
]), JSON_PRETTY_PRINT);

print PHP_EOL;
print PHP_EOL;
