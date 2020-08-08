<?php

namespace Blondie101010\EmbeddedDriver\Converter;
use Blondie101010\EmbeddedDriver\Driver;


/**
 *	Class to transform an array to JSON and back.
 **/
class ArrayJson extends Driver{
	/**
	 *	Apply a driver setting change.
	 *
	 *	This is not implemented here as it's unneeded.
	 **/
	protected function applySettings() { }


	/**
 	 * 	receive() is to get data from the Driver.  For IO drivers, the behaviour is obvious, but other types of drivers could return status codes, instructions, or almost anything else.
	 *
	 * 	@param int $size Size to read.  This _must_ not be used for anything other than size, and is meant to be passed on to the lower-level driver, but it can otherwise be ignored.  It is typically ignored by all drivers exect the lowest level.
	 *	@return mixed Anything that the driver is meant to return.
 	**/
	public function receive(int $size = PHP_INT_MAX) {
		return json_decode($this->deeperDriver->receive($size), true);
	}


	/**
 	 * 	send() is to send data to the Driver.  For IO drivers, the behaviour is obvious, but it could be used to send instructions/commands and almost anything else.
	 * 
	 * 	@param mixed $data Anything that this driver is mean to accept as input.
	 *	@return null
 	**/
	public function send($data) {
		$this->deeperDriver->send(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR));
	}
}
