<?php
/**
 * @author rugk
 * @copyright Copyright (c) 2015-2016 rugk
 * @license MIT
 */

$EOL="<br>\n";

// method1
$timeStart = microtime();

$ext = new ReflectionExtension('libsodium');
var_dump($ext->getVersion());

$timeEnd = microtime();
echo $EOL.$timeEnd-$timeStart.'s'.$EOL;

// method2
echo $EOL;
$timeStart = microtime();

var_dump(phpversion('libsodium'));

$timeEnd = microtime();
echo $EOL.$timeEnd-$timeStart.'s'.$EOL;
