<?php

namespace Blondie101010\EmbeddedDriver\Converter;
use Blondie101010\EmbeddedDriver\Driver;


/**
 **/
class Base64 extends Driver{
	/**
	 *	Apply a driver setting change.
	 *
	 *	This driver supports one setting: appendLF which is on by default
	 **/
	protected function applySettings() {
		$this->settings['appendLF'] = $this->settings['appendLF'] ?? true;
	}


	/**
 	 * 	receive() is to get data from the Driver.  For IO drivers, the behaviour is obvious, but other types of drivers could return status codes, instructions, or almost anything else.
	 *
	 * 	@param int $size Size to read.  This _must_ not be used for anything other than size, and is meant to be passed on to the lower-level driver, but it can otherwise be ignored.  It is typically ignored by all drivers exect the lowest level.
	 *	@return mixed Anything that the driver is meant to return.
 	**/
	public function receive(int $size = PHP_INT_MAX) {
		return base64_decode($this->deeperDriver->receive($size));
	}


	/**
 	 * 	send() is to send data to the Driver.  For IO drivers, the behaviour is obvious, but it could be used to send instructions/commands and almost anything else.
	 * 
	 * 	@param mixed $data Anything that this driver is mean to accept as input.
	 *	@return null
 	**/
	public function send($data) {
		$this->deeperDriver->send(base64_encode($data) . ($this->settings['appendLF'] ? "\n" : ""));
	}
}
