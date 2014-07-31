<?php 

 	class Contact extends MetadataModel {

		public $id;
		public $slug;
		public $name;
		public $status;
		public $type;
		public $created;
		public $updated;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init() {
			$this->meta_id = 'contact_id';
			$this->meta_table = 'banana_contact_meta';
			//
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = 0;
				$this->slug = 0;
				$this->name = '';
				$this->status = '';
				$this->type = '';
				$this->created = $now;
				$this->updated = $now;
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
			$this->slug = $this->slug ? $this->slug : $site->toAscii($this->nombre);
			$this->modified = date('Y-m-d H:i:s');
			try {
				$sql = "INSERT INTO banana_contact (id, slug, name, status, type, created, updated)
						VALUES (:id, :slug, :name, :status, :type, :created, :updated)
						ON DUPLICATE KEY UPDATE name = :name, status = :status, type = :type, updated = :updated";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':slug', $this->slug);
				$stmt->bindValue(':name', $this->name);
				$stmt->bindValue(':status', $this->status);
				$stmt->bindValue(':type', $this->type);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':updated', $this->updated);
				$stmt->execute();
				if (! $this->id && $dbh->lastInsertId() ) {
					$this->id = $dbh->lastInsertId();
				}
				$ret = true;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Client::save');

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
				$sql = "DELETE FROM banana_contact WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = true;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Client::delete');

			}
			return $ret;
		}

		function __toString() {
			return json_encode($this);
		}

	}

	class Contacts {

		/**
		 * Get an item
		 * @param Integer $id ID of the item to retrieve
		 */
		static function get($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, slug, name, status, type, created, updated FROM banana_contact WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Contact');
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
			$order = in_array(strtolower($order), array('id', 'slug', 'name', 'status', 'type', 'created', 'updated')) ? $order : 'id';
			try {
				$sql = "SELECT id, slug, name, status, type, created, updated FROM banana_contact ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Contact');
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
			$field = in_array(strtolower($field), array('id', 'slug', 'name', 'status', 'type', 'created', 'updated')) ? $field : 'id';
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
			$order = in_array(strtolower($order), array('id', 'slug', 'name', 'status', 'type', 'created', 'updated')) ? $order : 'id';
			try {
				$sql = "SELECT id, slug, name, status, type, created, updated FROM banana_contact WHERE {$conditions} ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Contact');
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
				$sql = "SELECT COUNT(id) AS total FROM banana_contact WHERE {$conditions}";
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