<?php

 	class Keyring extends Model {

		public $id;
		public $slug;
		public $name;
		public $description;
		public $type;
		public $created;
		public $modified;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init() {
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = '';
				$this->slug = '';
				$this->name = '';
				$this->description = '';
				$this->type = '';
				$this->created = $now;
				$this->modified = $now;
			}
		}
		/**
		 * Save model
		 * @return boolean True on success, False otherwise
		 */
		function save() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$this->modified = date('Y-m-d H:i:s');
			$this->slug = $this->slug ? $this->slug : $site->toAscii($this->name);
			try {
				$sql = "INSERT INTO banana_keyring (id, slug, name, description, type, created, modified)
						VALUES (:id, :slug, :name, :description, :type, :created, :modified)
						ON DUPLICATE KEY UPDATE name = :name, description = :description, type = :type, modified = :modified";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':slug', $this->slug);
				$stmt->bindValue(':name', $this->name);
				$stmt->bindValue(':description', $this->description);
				$stmt->bindValue(':type', $this->type);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':modified', $this->modified);
				$stmt->execute();
				if (! $this->id && $dbh->lastInsertId() ) {
					$this->id = $dbh->lastInsertId();
				}
				$ret = true;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Delete model
		 * @return boolean True on success, False otherwise
		 */
		function delete() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$this->modified = date('Y-m-d H:i:s');
			try {
				$sql = "DELETE FROM banana_keyring WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = true;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function __toString() {
			return json_encode($this);
		}

	}

	class Keyrings {

		/**
		 * Get an item
		 * @param Integer $id ID of the item to retrieve
		 */
		static function get($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_keyring WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Keyring');
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Get all items
		 * @param integer $offset How much ites to skip
		 * @param integer $limit  How much ites to retrieve
		 * @param string  $order  Colum for ordering
		 * @param string  $sort   Sort order (ASC, DESC)
		 * @param mixed           Array with fetched objects or False on error
		 */
		static function all($offset = 0, $limit = 1000, $order = 'id', $sort = 'DESC') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = is_numeric($offset) ? $offset : 0;
			$limit = is_numeric($limit) ? $limit : 1000;
			$sort = in_array(strtoupper($sort), array('ASC', 'DESC')) ? $sort : 'DESC';
			$order = in_array(strtolower($order), array('id', 'slug', 'name', 'description', 'type', 'created', 'modified')) ? $order : 'id';
			try {
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_keyring ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Keyring');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Get all items that match a condition
		 * @param  string  $field    Which column to check
		 * @param  mixed   $value    Which value to compare
		 * @param  string  $operator SQL comparison operator (=, >, <, LIKE, IN, etc)
		 * @param  integer $offset   How much items to skip
		 * @param  integer $limit    How much items to retrieve
		 * @param  string  $order    Column for ordering
		 * @param  string  $sort     Sort order (ASC, DESC)
		 * @return mixed             Array with fetched objects or False on error
		 */
		static function where($field, $value, $operator = '=', $offset = 0, $limit = 1000, $order = 'id', $sort = 'DESC') {
			global $site;
			$dbh = $site->getDatabase();
			$field = in_array(strtolower($field), array('id', 'slug', 'name', 'description', 'type', 'created', 'modified')) ? $field : 'id';
			$value = is_numeric($value) ? $value : $dbh->quote($value);
			return self::rawWhere("{$field} {$operator} {$value}", $offset, $limit, $order, $sort);
		}

		/**
		 * Get all items that match some conditions
		 * @param  string  $conditions A valid, well-formed SQL set of WHERE conditions (without the WHERE keyword itself)
		 * @param  integer $offset     How much items to skip
		 * @param  integer $limit      How much items to retrieve
		 * @param  string  $order      Column for ordering
		 * @param  string  $sort       Sort order (ASC, DESC)
		 * @return mixed               Array with fetched objects or False on error)
		 */
		static function rawWhere($conditions, $offset = 0, $limit = 1000, $order = 'id', $sort = 'DESC') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = is_numeric($offset) ? $offset : 0;
			$limit = is_numeric($limit) ? $limit : 1000;
			$sort = in_array(strtoupper($sort), array('ASC', 'DESC')) ? $sort : 'DESC';
			$order = in_array(strtolower($order), array('id', 'slug', 'name', 'description', 'type', 'created', 'modified')) ? $order : 'id';
			try {
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_keyring WHERE {$conditions} ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Keyring');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Get the amount of items that match certain conditions
		 * @param  string  $conditions A valid, well-formed SQL set of WHERE conditions (without the WHERE keyword itself)
		 * @return integer              Number of elements that match the conditions
		 */
		static function count($conditions = 1) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT COUNT(id) AS total FROM banana_keyring WHERE {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch();
				if ($row) {
					$ret = $row->total;
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}
	}

 	class KeyringKey extends Model {

		public $id;
		public $keyring_id;
		public $name;
		public $description;
		public $created;
		public $modified;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init() {
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = 0;
				$this->keyring_id = 0;
				$this->name = '';
				$this->description = '';
				$this->created = $now;
				$this->modified = $now;
			}
		}
		/**
		 * Save model
		 * @return boolean True on success, False otherwise
		 */
		function save() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$this->modified = date('Y-m-d H:i:s');
			try {
				$sql = "INSERT INTO banana_keyring_key (id, keyring_id, name, description, created, modified)
						VALUES (:id, :keyring_id, :name, :description, :created, :modified)
						ON DUPLICATE KEY UPDATE name = :name, description = :description, modified = :modified";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':keyring_id', $this->keyring_id);
				$stmt->bindValue(':name', $this->name);
				$stmt->bindValue(':description', $this->description);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':modified', $this->modified);
				$stmt->execute();
				if (! $this->id && $dbh->lastInsertId() ) {
					$this->id = $dbh->lastInsertId();
				}
				$ret = true;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Delete model
		 * @return boolean True on success, False otherwise
		 */
		function delete() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "DELETE FROM banana_keyring_key WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = true;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function __toString() {
			return json_encode($this);
		}

	}

	class KeyringKeys {

		/**
		 * Get an item
		 * @param Integer $id ID of the item to retrieve
		 */
		static function get($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, keyring_id, name, description, created, modified FROM banana_keyring_key WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'KeyringKey');
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Get all items
		 * @param integer $offset How much ites to skip
		 * @param integer $limit  How much ites to retrieve
		 * @param string  $order  Colum for ordering
		 * @param string  $sort   Sort order (ASC, DESC)
		 * @param mixed           Array with fetched objects or False on error
		 */
		static function all($offset = 0, $limit = 1000, $order = 'id', $sort = 'DESC') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = is_numeric($offset) ? $offset : 0;
			$limit = is_numeric($limit) ? $limit : 1000;
			$sort = in_array(strtoupper($sort), array('ASC', 'DESC')) ? $sort : 'DESC';
			$order = in_array(strtolower($order), array('id', 'keyring_id', 'name', 'description', 'created', 'modified')) ? $order : 'id';
			try {
				$sql = "SELECT id, keyring_id, name, description, created, modified FROM banana_keyring_key ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'KeyringKey');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Get all items that match a condition
		 * @param  string  $field    Which column to check
		 * @param  mixed   $value    Which value to compare
		 * @param  string  $operator SQL comparison operator (=, >, <, LIKE, IN, etc)
		 * @param  integer $offset   How much items to skip
		 * @param  integer $limit    How much items to retrieve
		 * @param  string  $order    Column for ordering
		 * @param  string  $sort     Sort order (ASC, DESC)
		 * @return mixed             Array with fetched objects or False on error
		 */
		static function where($field, $value, $operator = '=', $offset = 0, $limit = 1000, $order = 'id', $sort = 'DESC') {
			global $site;
			$dbh = $site->getDatabase();
			$field = in_array(strtolower($field), array('id', 'keyring_id', 'name', 'description', 'created', 'modified')) ? $field : 'id';
			$value = is_numeric($value) ? $value : $dbh->quote($value);
			return self::rawWhere("{$field} {$operator} {$value}", $offset, $limit, $order, $sort);
		}

		/**
		 * Get all items that match some conditions
		 * @param  string  $conditions A valid, well-formed SQL set of WHERE conditions (without the WHERE keyword itself)
		 * @param  integer $offset     How much items to skip
		 * @param  integer $limit      How much items to retrieve
		 * @param  string  $order      Column for ordering
		 * @param  string  $sort       Sort order (ASC, DESC)
		 * @return mixed               Array with fetched objects or False on error)
		 */
		static function rawWhere($conditions, $offset = 0, $limit = 1000, $order = 'id', $sort = 'DESC') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = is_numeric($offset) ? $offset : 0;
			$limit = is_numeric($limit) ? $limit : 1000;
			$sort = in_array(strtoupper($sort), array('ASC', 'DESC')) ? $sort : 'DESC';
			$order = in_array(strtolower($order), array('id', 'keyring_id', 'name', 'description', 'created', 'modified')) ? $order : 'id';
			try {
				$sql = "SELECT id, keyring_id, name, description, created, modified FROM banana_keyring_key WHERE {$conditions} ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'KeyringKey');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Get the amount of items that match certain conditions
		 * @param  string  $conditions A valid, well-formed SQL set of WHERE conditions (without the WHERE keyword itself)
		 * @return integer              Number of elements that match the conditions
		 */
		static function count($conditions = 1) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT COUNT(id) AS total FROM banana_keyring_key WHERE {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch();
				if ($row) {
					$ret = $row->total;
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}
	}

?>