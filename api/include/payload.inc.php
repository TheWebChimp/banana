<?php

	/**
	 * payload.inc.php
	 * Payload helper class
	 *
	 * Version: 	1.0
	 * Author(s):	biohzrdmx <github.com/biohzrdmx>
	 */

	class Payload {

		/**
		 * Parse a Base64-encoded string, optionally returning a default value if decoding fails
		 * @param  string $data    A Base64-encoded string
		 * @param  mixed $default  A default value to return if decoding fails
		 * @return mixed           The decoded array/object or the specified default value
		 */
		static function fromBase64($data, $default = '') {
			$ret = base64_decode($data);
			return $ret ? $ret : $default;
		}

		/**
		 * Parse a JSON-encoded string, optionally returning a default value if decoding fails
		 * @param  string $data    A JSON-encoded string
		 * @param  mixed $default  A default value to return if decoding fails
		 * @return mixed           The decoded array/object or the specified default value
		 */
		static function fromJSON($data, $default = '') {
			$ret = json_decode($data);
			return $ret ? $ret : $default;
		}

		/**
		 * Parse a serialized string, optionally returning a default value if decoding fails
		 * @param  string $data    A serialized string
		 * @param  mixed $default  A default value to return if decoding fails
		 * @return mixed           The decoded array/object or the specified default value
		 */
		static function fromString($data, $default = '') {
			$ret = unserialize($data);
			return $ret ? $ret : $default;
		}

		/**
		 * Convert the payload into a Base64-encoded string
		 * @param  boolean $echo Whether to print-out the result or not
		 * @return string        A Base64-encoded string
		 */
		function toBase64($echo = false) {
			$ret = base64_encode($this);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Convert the payload into a JSON-encoded string
		 * @param  boolean $echo Whether to print-out the result or not
		 * @return string        A JSON-encoded string
		 */
		function toJSON($echo = false) {
			$ret = json_encode($this);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

		/**
		 * Convert the payload into a serialized string
		 * @param  boolean $echo Whether to print-out the result or not
		 * @return string        A serialized string
		 */
		function toString($echo = false) {
			$ret = serialize($this);
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}

	}

?>