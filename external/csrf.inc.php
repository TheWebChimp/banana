<?php

	class CSRF {

		public $secret;
		public $token;
		public $seed;
		public $key;

		function __construct() {
			global $site;
			# Start session
			@session_start();

			$this->key = $site->hashPassword('csrf');

			# Check if the session contains token and secret
			if (! get_item($_SESSION, $this->key) ) {
				$secret = $site->hashPassword('nn7oi){[n/u&o^)zJa^jJ`W!1[DB$;6O');
				$seed = md5( uniqid() );
				$token = $site->hashToken( $seed );
				$_SESSION[$this->key] = array(
					'csrf_secret' => $secret,
					'csrf_seed' => $seed,
					'csrf_token' => $token
				);
			}

			# Set the class members
			$this->secret = $_SESSION[$this->key]['csrf_secret'];
			$this->token = $_SESSION[$this->key]['csrf_token'];
			$this->seed = $_SESSION[$this->key]['csrf_seed'];

			# Add meta token
			$site->addMeta('token', $this->token);
		}


		public function checkToken($token) {
			global $site;
			$check = $site->hashToken( $this->seed );
			return $check == $token;
		}

		public function getToken($echo = false) {
			$ret = $this->token;
			if ($echo) {
				echo $ret;
			}
			return $ret;
		}
	}

?>