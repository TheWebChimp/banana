<?php

 	class ToDo extends Model {

		public $id;
		public $user_id;
		public $project_id;
		public $client_id;
		public $name;
		public $details;
		public $status;
		public $priority;
		public $attachments;
		public $deadline;
		public $created;
		public $modified;

		/**
		 * Initialization callback
		 * @return nothing
		 */
		function init() {
			$now = date('Y-m-d H:i:s');
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = '';
				$this->user_id = '';
				$this->project_id = '';
				$this->client_id = '';
				$this->name = '';
				$this->details = '';
				$this->status = '';
				$this->priority = '';
				$this->attachments = '';
				$this->deadline = '';
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
				$sql = "INSERT INTO banana_todo (id, user_id, project_id, client_id, name, details, status, priority, attachments, deadline, created, modified)
						VALUES (:id, :user_id, :project_id, :client_id, :name, :details, :status, :priority, :attachments, :deadline, :created, :modified)
						ON DUPLICATE KEY UPDATE id = :id, user_id = :user_id, project_id = :project_id, client_id = :client_id, name = :name, details = :details, status = :status, priority = :priority, attachments = :attachments, deadline = :deadline, created = :created, modified = :modified";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':user_id', $this->user_id);
				$stmt->bindValue(':project_id', $this->project_id);
				$stmt->bindValue(':client_id', $this->client_id);
				$stmt->bindValue(':name', $this->name);
				$stmt->bindValue(':details', $this->details);
				$stmt->bindValue(':status', $this->status);
				$stmt->bindValue(':priority', $this->priority);
				$stmt->bindValue(':attachments', $this->attachments);
				$stmt->bindValue(':deadline', $this->deadline);
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
				$sql = "DELETE FROM banana_todo WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = true;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function getCategory() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT t.id, t.slug, t.name, t.description, t.type, t.created, t.modified FROM banana_todo_tag t, banana_todo_relationship r WHERE r.todo_id = :todo_id AND r.tag_id = t.id AND t.type = 'Category' ORDER BY name ASC";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':todo_id', $this->id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDoTag');
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function getTags() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT t.id, t.slug, t.name, t.description, t.type, t.created, t.modified FROM banana_todo_tag t, banana_todo_relationship r WHERE r.todo_id = :todo_id AND r.tag_id = t.id AND t.type = 'Tag' ORDER BY name ASC";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':todo_id', $this->id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDoTag');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function __toString() {
			return json_encode($this);
		}

	}

	class ToDos {

		/**
		 * Get an item
		 * @param Integer $id ID of the item to retrieve
		 */
		static function get($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, user_id, project_id, client_id, name, details, status, priority, attachments, deadline, created, modified FROM banana_todo WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDo');
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
			$order = in_array(strtolower($order), array('id', 'user_id', 'project_id', 'client_id', 'name', 'details', 'status', 'priority', 'attachments', 'deadline', 'created', 'modified')) ? $order : 'id';
			try {
				$sql = "SELECT id, user_id, project_id, client_id, name, details, status, priority, attachments, deadline, created, modified FROM banana_todo ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDo');
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
			$field = in_array(strtolower($field), array('id', 'user_id', 'project_id', 'client_id', 'name', 'details', 'status', 'priority', 'attachments', 'deadline', 'created', 'modified')) ? $field : 'id';
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
			$order = in_array(strtolower($order), array('id', 'user_id', 'project_id', 'client_id', 'name', 'details', 'status', 'priority', 'attachments', 'deadline', 'created', 'modified')) ? $order : 'id';
			try {
				$sql = "SELECT id, user_id, project_id, client_id, name, details, status, priority, attachments, deadline, created, modified FROM banana_todo WHERE {$conditions} ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDo');
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
				$sql = "SELECT COUNT(id) AS total FROM banana_todo WHERE {$conditions}";
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

	class ToDoTag extends Model {

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
			$now = date('Y-m-d H:i:s');
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
			try {
				$sql = "INSERT INTO banana_todo_tag (id, slug, name, description, type, created, modified)
						VALUES (:id, :slug, :name, :description, :type, :created, :modified)
						ON DUPLICATE KEY UPDATE id = :id, slug = :slug, name = :name, description = :description, type = :type, created = :created, modified = :modified";
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
				$sql = "DELETE FROM banana_todo_tag WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = true;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function all($offset = 0, $limit = 1000, $order = 'id', $sort = 'DESC') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = is_numeric($offset) ? $offset : 0;
			$limit = is_numeric($limit) ? $limit : 1000;
			$sort = in_array(strtoupper($sort), array('ASC', 'DESC')) ? $sort : 'DESC';
			$order = in_array(strtolower($order), array('id', 'user_id', 'project_id', 'client_id', 'name', 'details', 'status', 'priority', 'attachments', 'deadline', 'created', 'modified')) ? $order : 'id';
			try {
				$sql = "SELECT t.id, t.user_id, t.project_id, t.client_id, t.name, t.details, t.status, t.priority, t.attachments, t.deadline, t.created, t.modified FROM banana_todo t, banana_todo_relationship r WHERE r.tag_id = :tag_id AND r.todo_id = t.id ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':tag_id', $this->id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDo');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function count($conditions = 1) {
			global $site;
			$ret = 0;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT COUNT(r.todo_id) AS total FROM banana_todo_relationship r, banana_todo t WHERE r.tag_id = :tag_id AND r.todo_id = t.id AND {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':tag_id', $this->id);
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

		function __toString() {
			return json_encode($this);
		}

	}

	class ToDoTags {

		function setRelation($tag_id, $todo_id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "INSERT INTO banana_todo_relationship (tag_id, todo_id) VALUES (:tag_id, :todo_id) ON DUPLICATE KEY UPDATE todo_id = :todo_id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':tag_id', $tag_id);
				$stmt->bindValue(':todo_id', $todo_id);
				$stmt->execute();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function breakRelation($tag_id, $todo_id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "DELETE FROM banana_todo_relationship WHERE tag_id = :tag_id AND todo_id = :todo_id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':tag_id', $tag_id);
				$stmt->bindValue(':todo_id', $todo_id);
				$stmt->execute();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function moveRelations($old_tag_id, $new_tag_id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "UPDATE banana_todo_relationship SET tag_id = :new_tag_id WHERE tag_id = :old_tag_id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':old_tag_id', $old_tag_id);
				$stmt->bindValue(':new_tag_id', $new_tag_id);
				$stmt->execute();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function clearRelations($todo_id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "DELETE FROM banana_todo_relationship WHERE todo_id = :todo_id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':todo_id', $todo_id);
				$stmt->execute();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Get an item by ID
		 * @param Integer $id ID of the item to retrieve
		 */
		protected static function _getById($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_todo_tag WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDoTag');
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Get an item by slug
		 * @param string $id Slug of the item to retrieve
		 */
		protected static function _getBySlug($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_todo_tag WHERE slug = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDoTag');
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		/**
		 * Get an item by either ID or Slug
		 * @param mixed $id Slug or ID of the item to retrieve
		 */
		static function get($id) {
			return is_numeric($id) ? self::_getById($id) : self::_getBySlug($id);
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
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_todo_tag ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDoTag');
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
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_todo_tag WHERE {$conditions} ORDER BY {$order} {$sort} LIMIT {$offset},{$limit}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'ToDoTag');
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
				$sql = "SELECT COUNT(id) AS total FROM banana_todo_tag WHERE {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->fetch();
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