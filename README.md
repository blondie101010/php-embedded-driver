# php-embedded-driver
Embedded driver design pattern that allows universal operation chaining

## Purpose
This project aims to abstract all operation from the application logic.  This includes file, socket, conversions, and an unlimited amount of other options.

## Installation
    composer require blondie101010/php-embedded-driver

## Usage

### General usage example
https://github.com/blondie101010/php-embedded-driver/blob/master/testfile.php shows a two Driver layer example.

### Making a driver
To make a driver, you just extend Blondie101010\EmbeddedDriver\Driver and define its required methods:
- applySettings(): validate and apply the settings passed to the class constructor, performing setup operation;
- receive(): receive data from the Driver;
- send(): send data to the Driver.

The source code is much more extensively documented and _should_ be consulted first.

If interested in a simple Driver example, be sure to look at https://github.com/blondie101010/php-embedded-driver/blob/master/src/Converter/Base64.php.

## Pull requests
A project needs to evolve to stay alive and become more useful.  Pull requests are very welcome whether to offer improvements to the core, or integrate new drivers directly in this project for convenience.

You can of course make your own repository which only use this package as a dependency.  Feel free to let me know of your Drivers and I'll make a list available.

## Bug reporting
Please use the GitHub regular issue reporting tool, *except* for security issues.  In order to protect everyone, please contact me directly if you find any, and they will be addressed promptly.
