<?php

namespace Blondie101010\EmbeddedDriver\IO;
use Blondie101010\EmbeddedDriver\Driver;


class FileException extends \Exception {};


/**
 *	Class to handle safe concurrent IO controls.
 *
 *	Though it is integrated to follow the driver structure, it is designed to allow more complete file IO on its own.
 *
 *	This class has a single setting:  filename
 **/
class File extends Driver{
	private $fp = null;


	/**
	 *	Apply a setting change, basically open a file or compatible stream.
	 *
	 *	This is called automatically by the Driver's constructor.
	 **/
	protected function applySettings(){
		$this->close();

		if (empty($this->settings['filename'])) {
			throw new Exception("File Driver called without a 'filename' setting.");
		}

		$this->open();
	}


	/**
	 *	Lock a file.
	 *
	 *	@param int $operation Operation corresponds to flock()'s $operation parameter.
	 **/
	private function lock(int $operation) {
		if (!flock($this->fp, $operation)) {
			throw new FileException("Error locking {$this->settings['filename']} for operation $operation: " . error_get_last());
		}
	}


	/**
	 *	Open the file provided in the settings.
	 *
	 *	@param int $operation Operation corresponds to flock()'s $operation parameter.
	 **/
	private function open() {
		if (!$this->fp = fopen($this->settings['filename'], 'c+')) {
			throw new FileException("Error opening {$this->settings['filename']}: " . error_get_last());
		}
	}


	/**
	 *	Close the file if it's open.
	 **/
	private function close() {
		if (isset($this->fp)) {
			fclose($this->fp);
		}

	}


	/**
	 *	File Driver's send() appends data at the end of the file.
	 *
	 *	@param string $data Data to append to the file.
	 **/
	public function send($data) {
		$this->write($data, 0, SEEK_END);
	}


	/**
	 *	Write $data to the file at the current offset (by default), or at the specified $offset.
	 *
	 *	@param string $data Data to write.
	 *	@param int $offset Offset where to write.  null by default
	 *	@param int $whence Whence corresponding to fseek()'s $whence parameter.
	 **/
	public function write(string $data, int $offset = null, int $whence = null) {
		$this->lock(LOCK_EX);

		if (isset($whence)) {
			if (is_null($offset)) {
				throw new FileException("Can not seek to a null offset.");
			}

			fseek($this->fp, $offset, $whence);
		}
		elseif (isset($offset)) { 
			fseek($this->fp, $offset, SEEK_SET);
		}

		if (fwrite($this->fp, $data, PHP_INT_MAX) < strlen($data)) {
			throw new FileException("Error writing to {$this->settings['filename']}: " . error_get_last());
		}

		fflush($this->fp);
		$this->lock(LOCK_UN);
	}


	/**
	 *	File Driver's receive() method reads a line of input.
	 **/
	public function receive(int $size = PHP_INT_MAX): ?string {
		return $this->getLine();
	}


	/**
	 *	Read $size bytes from the file at the current offset (by default), or at the specified $offset.
	 *
	 *	@param int $size Size to read.
	 *	@param int $offset Offset where to read from.  null by default to read at the current cursor.
	 *	@return string The string read or null if EOF reached.
	 **/
	public function read(int $size, int $offset = null): ?string {
		$this->lock(LOCK_SH);

		if (isset($offset)) { 
			fseek($this->fp, $offset, SEEK_SET);
		}

		$data = fread($this->fp, $size);

		if ($data == false || !strlen($data)) {
			if (feof($this->fp)) {
				$data = null;
			}
			else {
				throw new FileException("Error reading from {$this->settings['filename']}: " . error_get_last());
			}
		}

		$this->lock(LOCK_UN);

		return $data;
	}


	/**
	 *	Get a line from the file at current offset, performing a regular trim() on it (including \n) by default.
	 *
	 *	@param bool $trim Whether to trim() the input or not.  true by default, which also skips empty lines.
	 *	@return string The line read or null if there are none left.
	 **/
	public function getLine(bool $trim = true): ?string {
		$this->lock(LOCK_SH);

		do {
			$data = trim(fgets($this->fp, PHP_INT_MAX));
		} while ($data == "" && !feof($this->fp));
		
		$this->lock(LOCK_UN);

		return $data == "" ? null : $data;
	}


	/**
	 *	Rewind the file cursor.
	 **/
	public function rewind() {
		rewind($this->fp);
	}
}
