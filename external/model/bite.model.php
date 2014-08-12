<?php

 	class Bite extends MetadataModel {

		public $id;
		public $user_id;
		public $parent_id;
		public $name;
		public $status;
		public $type;
		public $permissions;
		public $syntax;
		public $content;
		public $created;
		public $updated;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init() {
			$this->meta_id = 'bite_id';
			$this->meta_table = 'banana_bites_bite_meta';
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = 0;
				$this->user_id = 0;
				$this->parent_id = 0;
				$this->name = '';
				$this->status = '';
				$this->type = '';
				$this->permissions = '';
				$this->syntax = '';
				$this->content = '';
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
			$this->modified = date('Y-m-d H:i:s');
			try {
				$sql = "INSERT INTO banana_bites_bite (id, user_id, parent_id, name, status, type, permissions, syntax, content, created, updated)
						VALUES (:id, :user_id, :parent_id, :name, :status, :type, :permissions, :syntax, :content, :created, :updated)
						ON DUPLICATE KEY UPDATE name = :name, status = :status, type = :type, permissions = :permissions, syntax = :syntax, content = :content, updated = :updated";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':user_id', $this->user_id);
				$stmt->bindValue(':parent_id', $this->parent_id);
				$stmt->bindValue(':name', $this->name);
				$stmt->bindValue(':status', $this->status);
				$stmt->bindValue(':type', $this->type);
				$stmt->bindValue(':permissions', $this->permissions);
				$stmt->bindValue(':syntax', $this->syntax);
				$stmt->bindValue(':content', $this->content);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':updated', $this->updated);
				$stmt->execute();
				if (! $this->id && $dbh->lastInsertId() ) {
					$this->id = $dbh->lastInsertId();
				} else {
					$sql = "INSERT INTO banana_bites_history (bite_id, user_id, modified) VALUES (:bite_id, :user_id, NOW())";
					$stmt = $dbh->prepare($sql);
					$stmt->bindValue(':bite_id', $this->id);
					$stmt->bindValue(':user_id', $site->user->id);
					$stmt->execute();
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
				$sql = "DELETE FROM banana_bites_bite WHERE id = :id";
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

	class Bites {

		/**
		 * Get an item
		 * @param Integer $id ID of the item to retrieve
		 */
		static function get($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, user_id, parent_id, name, status, type, permissions, syntax, content, created, updated FROM banana_bites_bite WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Bite');
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
			$order = in_array(strtolower($order), array('id', 'user_id', 'parent_id', 'name', 'status', 'type', 'permissions', 'syntax', 'content', 'created', 'updated')) ? $order : 'id';
			try {
				$sql = "SELECT id, user_id, parent_id, name, status, type, permissions, syntax, content, created, updated FROM banana_bites_bite ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Bite');
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
			$field = in_array(strtolower($field), array('id', 'user_id', 'parent_id', 'name', 'status', 'type', 'permissions', 'syntax', 'content', 'created', 'updated')) ? $field : 'id';
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
			$order = in_array(strtolower($order), array('id', 'user_id', 'parent_id', 'name', 'status', 'type', 'permissions', 'syntax', 'content', 'created', 'updated')) ? $order : 'id';
			try {
				$sql = "SELECT id, user_id, parent_id, name, status, type, permissions, syntax, content, created, updated FROM banana_bites_bite WHERE {$conditions} ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Bite');
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
				$sql = "SELECT COUNT(id) AS total FROM banana_bites_bite WHERE {$conditions}";
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