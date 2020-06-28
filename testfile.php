#!/usr/bin/php
<?php

use Blondie101010\EmbeddedDriver\Io\File;
use Blondie101010\EmbeddedDriver\Converter\Base64;

require 'vendor/autoload.php';


$base64FileDriver = new Base64(new File(null, ['filename' => 'testoutput.dat']), []);


for ($i = 0; $i < 10; $i ++) {
	$base64FileDriver->send("This is test $i.");
}


