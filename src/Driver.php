<?php

namespace Blondie101010\EmbeddedDriver;


/**
 *	Driver is the main (abstract) class which is meant to allow nested drivers of all kinds.
 *
 *	A Driver operates exclusively with read and write operations that it passes to its $deeperDriver.
 *
 *	Every Driver's receive() method *must* begin by calling its $this->deeperDriver->receive(), except if it's a low-level driver, apply its logic to the data returned, and return it.
 *
 *	Every Driver's send() method *must* apply its logic to the data it is passed, and end by calling its $this->deeperDriver->send() with its transformed data, except if it's a low-level driver.
 *
 *	Low-level drivers are defined as drivers that operate outside the application, like IO, sockets, and possibly many other things.  They do not have to be explicit as they are always the deepest driver injected and their functionality doesn't allow going one step further.
 *
 *	Although read and write could seem limited to IO, they can actually be used for sending commands and data that the lower level driver will understand, and reading the response or data.
 *
 *	In order to allow a maximum of flexibility, what is returned by receive() can be anything and so is what can be passed to send().
 *
 *	usage: $myIo = new Io\Driver\Connector(new Io\Driver\WhateverDriverNeeded($parms));
 **/
abstract class Driver {
	/** @var Driver $deeperDriver all our operations begin by calling the deeperDriver's **/
	protected $deeperDriver;

	/** @var array $settings Settings are specific to each driver, so consult the specific driver's documentation for details. **/
	protected $settings;


	/**
	 *	Class constructor.
	 *
	 *	@param Driver $deeperDriver The lower-level driver that this driver will use.
	 *	@param array $settings Array of settings specific to the driver.  Refer to the specific Driver's documentation for details.
	 **/
	final public function __construct(Driver $deeperDriver = null, array $settings) {
		$this->deeperDriver = $deeperDriver;
		$this->changeSettings($settings);
	}


	/**
	 *	Change the driver settings.  This causes $this->applySettings() to be applied, making it possible to even perform explicit operations.  These are used to specify filenames, IP addresses, charsets, and almost anything else.
	 *
	 *	@param array $settings Array of settings specific to the driver.  Refer to the specific Driver's documentation for details.
	 **/
	final public function changeSettings(array $settings) {
		$this->settings = $settings;

		$this->applySettings();
	}


	/**
	 *	Apply a driver setting change, which can actually perform operations specified in the new $settings provided that replaced $this->settings.
	 *
	 *	This is also where settings should be validated.
	 **/
	abstract protected function applySettings();


	/**
 	 * 	receive() is to get data from the Driver.  For IO drivers, the behaviour is obvious, but other types of drivers could return status codes, instructions, or almost anything else.
	 *
	 * 	@param int $size Size to read.  This _must_ not be used for anything other than size, and is meant to be passed on to the lower-level driver, but it can otherwise be ignored.  It is typically ignored by all drivers exect the lowest level.
	 *	@return mixed Anything that the driver is meant to return.
 	**/
	abstract public function receive(int $size = PHP_INT_MAX);

	/**
 	 * 	send() is to send data to the Driver.  For IO drivers, the behaviour is obvious, but it could be used to send instructions/commands and almost anything else.
	 * 
	 * 	@param mixed $data Anything that this driver is mean to accept as input.
	 *	@return null
 	**/
	abstract public function send($data);
}
