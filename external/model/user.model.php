<?php

	include $site->baseDir('/lib/StatelessCookie.php');

	/**
	 * User Class
	 *
	 * Provides the abstraction layer for the User object.
	 *
	 * @version  1.0
	 * @author   Raul Vera <raul.vera@thewebchi.mp>
	 * @uses     Model
	 * @category Core modules
	 */
	class User extends MetadataModel {

		public $id;
		public $login;
		public $slug;
		public $password;
		public $nickname;
		public $email;
		public $status;
		public $created;
		public $modified;
		public $role;
		public $capabilities;
		public $first_name;
		public $last_name;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init() {
			$this->meta_id = 'user_id';
			$this->meta_table = 'banana_user_meta';
			//
			if (! $this->id ) {
				$this->id = 0;
				$this->login = '';
				$this->slug = '';
				$this->password = '';
				$this->nickname = '';
				$this->email = '';
				$this->status = 'active';
				$this->created = date('Y-m-d H:i:s');
				$this->modified = '0000-00-00 00:00:00';
				$this->role = 'user';
				$this->capabilities = array('');
				$this->first_name = '';
				$this->last_name = '';
			} else {
				$this->role = $this->getMeta('role', 'user');
				$this->capabilities = $this->getMeta('capabilities', '');
				$this->first_name = $this->getMeta('first_name', '');
				$this->last_name = $this->getMeta('last_name', '');
				//
				if ( class_exists('Clients') ) {
					$this->clients = array();
					$clients = $this->getMeta('clients');
					if ($clients) {
						foreach ($clients as $client_id) {
							$this->clients[] = Clients::get($client_id);
						}
					}
				}
				if ( class_exists('Projects') ) {
					$this->projects = array();
					$projects = $this->getMeta('projects');
					if ($projects) {
						foreach ($projects as $project_id) {
							$this->projects[] = Projects::get($project_id);
						}
					}
				}
			}
		}

		/**
		 * Save the model
		 * @return boolean True if the model was updated, False otherwise
		 */
		function save() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			# Hash the password if required
			if( substr($this->password, 0, 3) != '$X$' ) {
				$cookies = new StatelessCookie( $site->hashPassword('mega-ggi') );
				$this->password = $cookies->hashPassword($this->password);
			}
			$this->slug = $this->slug ? $this->slug : $site->toAscii($this->email);
			$this->login = $this->login ? $this->login : $this->email;
			$this->modified = date('Y-m-d H:i:s');
			try {
				# Create or update user
				$sql = "INSERT INTO banana_user (id, login, slug, password, nickname, email, status, created, modified)
						VALUES (:id, :login, :slug, :password, :nickname, :email, :status, :created, :modified)
						ON DUPLICATE KEY UPDATE password = :password, nickname = :nickname, email = :email, status = :status, modified = :modified";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':login', $this->login);
				$stmt->bindValue(':slug', $this->slug);
				$stmt->bindValue(':password', $this->password);
				$stmt->bindValue(':nickname', $this->nickname);
				$stmt->bindValue(':email', $this->email);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':modified', $this->modified);
				$stmt->bindValue(':status', $this->status);
				$ret = $stmt->execute();
				if (! $this->id && $dbh->lastInsertId() ) {
					$this->id = $dbh->lastInsertId();
				}
				$ret = ($stmt->rowCount() > 0);
				# And set metas
				if ( $this->id ) {
					$this->updateMeta('role', $this->role);
					$this->updateMeta('capabilities', $this->capabilities);
					$this->updateMeta('first_name', $this->first_name);
					$this->updateMeta('last_name', $this->last_name);
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

	}

	# =============================================================================================

	/**
	 * Users Class
	 *
	 * Handles the user account mechanism.
	 *
	 * @version 1.0
	 * @author  Raul Vera <raul.vera@thewebchi.mp>
	 */
	class Users {

		static protected $user_id;
		static protected $roles;

		/**
		 * Initialization function
		 */
		static function init() {
			global $site;
			# Initialize some defaults
			self::$user_id = 0;
			self::$roles = array(
				'superadmin',
				'admin',
				'user'
			);
			# And hook the '/logout' route
			$site->addRoute('/logout', 'Users::_doLogout', true);
		}

		/**
		 * Shorthand get() method
		 * @param  mixed $id  Numeric ID or string slug
		 * @return mixed      User object if the user was found, Null otherwise
		 */
		static function get($id) {
			global $site;
			$dbh = $site->getDatabase();
			$ret = null;
			try {
				$sql = "SELECT id, login, slug, password, nickname, email, status, created, modified FROM banana_user WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Users::getID()");
			}
			return $ret;
		}

		/**
		 * Retrieve all the users from the database
		 * @return array      Array with User objects, False on error
		 */
		static function all() {
			global $site;
			$dbh = $site->getDatabase();
			$ret = array();
			try {
				$sql = "SELECT id, login, slug, password, nickname, email, status, created, modified FROM banana_user";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Users::all()");
			}
			return $ret;
		}

		/**
		 * Get filtered results
		 * @param  string $column   Column name
		 * @param  string $operator Comparison operator, defaults to '='
		 * @param  string $value    Column value
		 * @return array            Array with filtered results
		 */
		static function where($column, $operator = '=', $value = '') {
			global $site;
			$dbh = $site->getDatabase();
			$ret = array();
			try {
				$sql = "SELECT id, login, slug, password, nickname, email, status, created, modified FROM banana_user WHERE {$column} {$operator} '{$value}'";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Users::where()");
			}
			return $ret;
		}

		/**
		 * Retrieve all the users from the database that match the given criteria
		 * This is similar to where() but allows the programmer to specify a manually built string with conditions - this is particularly exposed to sql-injections, so always sanitize your inputs!
		 * @param  string  $conditions  String with the "WHERE foo = 'bar'" conditions (not including the WHERE itself)
		 * @return array                Array with User objects, False on error
		 */
		static function rawWhere($conditions = '') {
			global $site;
			$dbh = $site->getDatabase();
			$ret = array();
			try {
				$sql = "SELECT id, login, slug, password, nickname, email, status, created, modified FROM banana_user {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Users::rawWhere()");
			}
			return $ret;
		}

		/**
		 * Retrieve the current user
		 * @return mixed User object on success, Null otherwise
		 */
		static function getCurrentUser() {
			$ret = self::get( self::$user_id );
			return $ret;
		}

		/**
		 * Retrieve the current user Id
		 * @return integer Current user Id
		 */
		static function getCurrentUserId() {
			return self::$user_id;
		}

		static function currentUserCan($capability) {
			$ret = false;
			$user = self::getCurrentUser();
			if ($user) {
				$ret = in_array($capability, $user->capabilities);
			}
			return $ret;
		}

		static function userCan($user_id, $capability) {
			$ret = false;
			$user = self::get($user_id);
			if ($user) {
				$ret = in_array($capability, $user->capabilities);
			}
			return $ret;
		}

		/**
		 * Get an user by the specified field
		 * @param  string $field Field name: 'login', 'slug' or 'email'
		 * @param  string $value Value of the field
		 * @return mixed         User object on success, Null otherwise
		 */
		static function getBy($field, $value) {
			global $site;
			$dbh = $site->getDatabase();
			$ret = null;
			$fields = array('login', 'slug', 'email');
			if (! in_array($field, $fields) ) {
				return $ret;
			}
			try {
				$sql = "SELECT id, login, slug, password, nickname, email, status, created, modified FROM banana_user WHERE {$field} = :value";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':value', $value);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'User');
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Users::getBy()");
			}
			return $ret;
		}

		/**
		 * Recover a previous session
		 * @return boolean True if the user was re-logged in, False otherwise
		 */
		static function checkLogin() {
			global $site;
			$ret = false;
			$name = sprintf('banana_login%s', $site->hashPassword('cookie'));
			$cookie = isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
			if ($cookie) {
				$cookies = new StatelessCookie( $site->hashPassword('mega-ggi') );
				$login = $cookies->getCookieData($cookie);
				$user = self::getBy('login', $login);
				# Check user and password
				if ( $user && $cookies->checkCookie($cookie, $user->password) ) {
					# Save user id
					self::$user_id = $user->id;
					$ret = true;
				}
			}
			return $ret;
		}

		/**
		 * Check if there's a valid user logged in, otherwise send it to the sign-in page
		 * @return boolean True if the current user is set/valid, otherwise it will be redirected
		 */
		static function requireLogin($redirect = '/sign-in') {
			global $site;
			header("Expires: on, 01 Jan 1970 00:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			# Check user
			if ( self::$user_id ) {
				return true;
			}
			if ($redirect) {
				$site->redirectTo( $site->urlTo($redirect) );
				exit;
			}
			return false;
		}

		/**
		 * Sign a new user in, replaces previous user (if any)
		 * @param  string  $user     User name
		 * @param  string  $password Plain-text password
		 * @param  boolean $remember Whether to set the cookie for 12 hours (normal) or 2 weeks (remember)
		 * @return boolean           True on success, False otherwise
		 */
		static function login($user, $password, $remember = false) {
			global $site;
			$ret = false;
			$user = self::getBy('login', $user);
			if ($user) {
				$cookies = new StatelessCookie( $site->hashPassword('mega-ggi') );
				$auth = $cookies->login($password, $user->password);
				if ($auth) {
					$expires = strtotime($remember ? '+15 day' : '+12 hour');
					$cookie = $cookies->buildCookie($expires, $user->login, $auth);
					$name = sprintf('banana_login%s', $site->hashPassword('cookie'));
					# Set user id
					self::$user_id = $user->id;
					# And set cookie
					$ret = setcookie($name, $cookie, $expires, '/');
				}
			}
			return $ret;
		}

		/**
		 * Sign the current user out
		 * @return boolean     True on success, False otherwise
		 */
		static function logout() {
			global $site;
			self::$user_id = 0;
			$name = sprintf('banana_login%s', $site->hashPassword('cookie'));
			return setcookie($name, '', strtotime('-1 hour'), '/');
		}

		/**
		 * Respond to '/logout' route by disconnecting the current user
		 * @return nothing
		 */
		static function _doLogout() {
			global $site;
			self::logout();
			$site->redirectTo( $site->urlTo('/') );
			exit;
		}

	}

?>