<?php

	/**
	 * Pretty-print an array or object
	 * @param  mixed $a Array or object
	 */
	function print_a( $a ) {
		print( '<pre>' );
		print_r( $a );
		print( '</pre>' );
	}

	/**
	 * Convert a shorthand byte value from a PHP configuration directive to an integer value
	 * @param    string   $value
	 * @return   int
	 */
	function convert_bytes( $value ) {
		if ( is_numeric( $value ) ) {
			return $value;
		} else {
			$value_length = strlen( $value );
			$qty = substr( $value, 0, $value_length - 1 );
			$unit = strtolower( substr( $value, $value_length - 1 ) );
			switch ( $unit ) {
				case 'k':
					$qty *= 1024;
					break;
				case 'm':
					$qty *= 1048576;
					break;
				case 'g':
					$qty *= 1073741824;
					break;
			}
			return $qty;
		}
	}

	/**
	 * Generate a password of the given length
	 * @param  integer $length Password length
	 * @return string          Generated password on success, False otherwise
	 */
	function generate_password($length) {
		global $site;
		$ret = false;
		try {
			require_once $site->baseDir('/lib/Random.php');
			$random = new Random(false);
			$ret = $random->token($length);
		} catch (Exception $e) {
			error_log( $e->getMessage() );
		}
		return $ret;
	}

	/**
	 * Get an item from an array, or a default value if it's not set
	 * @param  array $array    Array
	 * @param  mixed $key      Key or index, depending on the array
	 * @param  mixed $default  A default value to return if the item it's not in the array
	 * @return mixed           The requested item (if present) or the default value
	 */
	function get_item($array, $key, $default = '') {
		return isset( $array[$key] ) ? $array[$key] : $default;
	}

	/**
	 * Mark an option as selected by evaluating the variable
	 * @param  mixed  $var   Variable expected value
	 * @param  mixed  $val   Variable actual value
	 * @param  string $attr  Attribute to use (selected, checked, etc)
	 * @param  boolean $echo Whether to echo the result or not
	 * @return string        Selected attribute text or an empty text
	 */
	function option_selected($var, $val, $attr = "selected", $echo = true) {
		$ret = ($var == $val) ? "{$attr}=\"{$attr}\"" : '';
		if ($echo) {
			echo $ret;
		}
		return $ret;
	}

	/**
	 * Mark a menu the active one by evaluating the variable
	 * @param  mixed  $var   Variable expected value
	 * @param  mixed  $val   Variable actual value
	 * @param  string $class CSS class that will be added to the item
	 * @param  boolean $echo Whether to echo the result or not
	 * @return string        Active class text or an empty text
	 */
	function current_menu($var, $val, $class = "active", $echo = true) {
		$ret = ($var == $val) ? "class=\"{$class}\"" : '';
		if ($echo) {
			echo $ret;
		}
		return $ret;
	}

	/**
	 * Get either a Gravatar URL or complete image tag for a specified email address.
	 *
	 * @param string $email The email address
	 * @param string $s Size in pixels, defaults to 80px [ 1 - 2048 ]
	 * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
	 * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
	 * @param boole $img True to return a complete IMG tag False for just the URL
	 * @param array $atts Optional, additional key/value attributes to include in the IMG tag
	 * @return String containing either just a URL or a complete image tag
	 * @source http://gravatar.com/site/implement/images/php/
	 */
	function get_gravatar( $email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array() ) {
		$url = 'http://www.gravatar.com/avatar/';
		$url .= md5( strtolower( trim( $email ) ) );
		$url .= "?s=$s&d=$d&r=$r";
		if ( $img ) {
			$url = '<img src="' . $url . '"';
			foreach ( $atts as $key => $val )
				$url .= ' ' . $key . '="' . $val . '"';
			$url .= ' />';
		}
		return $url;
	}

	function set_active_menu($menu) {
		global $site;
		$request = $site->mvc->getRequest();
		$controller = $request->controller;
		echo ($controller == $menu ? 'class="active"' : '');
	}

	function set_active_submenu($menu, $submenu) {
		global $site;
		$request = $site->mvc->getRequest();
		$controller = $request->controller;
		$action = $request->action;
		echo ($controller == $menu && $action == $submenu ? 'class="active"' : '');
	}

	/**
	 * Convert a given date to relative time ('n minutes/hours/etc ago')
	 * @param  string $date     Date to convert ('Y-m-d H:i:s' format recommended)
	 * @param  string $suffix   Date suffix, defaults to 'ago'
	 * @param  string $fallback Date format for fallback (when date is more than a year ago)
	 * @return string           Relative time
	 */
	function relative_time($date, $ago = ' ago', $fallback = 'F Y') {
		global $i18n;
		$ago = ' ago';
		$seconds = 'seconds|second';
		$minutes = 'minutes|minute';
		$hours = 'hours|hour';
		$days = 'days|day';
		$weeks = 'weeks|week';
		$months = 'months|month';
		$format = '{value} {unit} {ago}';
		$seconds = explode('|', $seconds);
		$minutes = explode('|', $minutes);
		$hours = explode('|', $hours);
		$days = explode('|', $days);
		$weeks = explode('|', $weeks);
		$months = explode('|', $months);
		$value = null;
		#
		$diff = time() - strtotime($date);
		if(!$value && $diff < 60) {
			$value = $diff;
			$unit = $diff != 1 ? $seconds[0] : $seconds[1];
		}
		$diff = round($diff/60);
		if(!$value && $diff < 60) {
			$value = $diff;
			$unit = $diff != 1 ? $minutes[0] : $minutes[1];
		}
		$diff = round($diff/60);
		if(!$value && $diff < 24) {
			$value = $diff;
			$unit = $diff != 1 ? $hours[0] : $hours[1];
		}
		$diff = round($diff/24);
		if(!$value && $diff < 7) {
			$value = $diff;
			$unit = $diff != 1 ? $days[0] : $days[1];
		}
		$diff = round($diff/7);
		if(!$value && $diff < 4) {
			$value = $diff;
			$unit = $diff != 1 ? $weeks[0] : $weeks[1];
		}
		$diff = round($diff/4);
		if(!$value && $diff < 12) {
			$value = $diff;
			$unit = $diff != 1 ? $months[0] : $months[1];
		}
		# Fallback
		if (! $value ) {
			$ret = date($fallback, strtotime($date));
		} else {
			$ret = str_replace('{value}', $value, $format);
			$ret = str_replace('{unit}', $unit, $ret);
			$ret = str_replace('{ago}', $ago, $ret);
		}
		return $ret;
	}

?>