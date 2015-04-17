<?php

	class Ticket extends MetadataModel {

		public $id;
		public $user_id;
		public $project_id;
		public $client_id;
		public $subject;
		public $details;
		public $attachments;
		public $status;
		public $replies;
		public $due;
		public $created;
		public $modified;

		function init() {
			$this->meta_id = 'ticket_id';
			$this->meta_table = 'banana_ticket_meta';
			//
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = 0;
				$this->user_id = 0;
				$this->project_id = 0;
				$this->client_id = '';
				$this->subject = '';
				$this->details = '';
				$this->attachments = '';
				$this->status = '';
				$this->replies = 0;
				$this->due = '0000-00-00 00:00:00';
				$this->created = $now;
				$this->modified = $now;
			} else {
				//
			}
		}

		function save() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$this->modificado = date('Y-m-d H:i:s');
			try {
				$sql = "INSERT INTO banana_ticket (id, user_id, project_id, client_id, subject, details, attachments, status, replies, due, created, modified)
						VALUES (:id, :user_id, :project_id, :client_id, :subject, :details, :attachments, :status, :replies, :due, :created, :modified)
						ON DUPLICATE KEY UPDATE project_id = :project_id, client_id = :client_id, subject = :subject, details = :details, attachments = :attachments, status = :status, due = :due, modified = :modified";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':user_id', $this->user_id);
				$stmt->bindValue(':project_id', $this->project_id);
				$stmt->bindValue(':client_id', $this->client_id);
				$stmt->bindValue(':subject', $this->subject);
				$stmt->bindValue(':details', $this->details);
				$stmt->bindValue(':attachments', $this->attachments);
				$stmt->bindValue(':status', $this->status);
				$stmt->bindValue(':replies', $this->replies);
				$stmt->bindValue(':due', $this->due);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':modified', $this->modified);
				$stmt->execute();
				if (! $this->id ) {
					$this->id = $dbh->lastInsertId();
				}
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Ticket::save');
			}
			return $ret;
		}

		function delete() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "DELETE FROM banana_ticket WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Ticket::delete');
			}
			return $ret;
		}

		function open() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "UPDATE banana_ticket SET status = 'Open' WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Ticket::open');
			}
			return $ret;
		}

		function close() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "UPDATE banana_ticket SET status = 'Closed' WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Ticket::close');
			}
			return $ret;
		}

		function update() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "UPDATE banana_ticket SET modified = NOW(), replies = (SELECT COUNT(id) FROM banana_ticket_reply WHERE ticket_id = :id) WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Ticket::update');
			}
			return $ret;
		}

		function replies() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, ticket_id, user_id, details, attachments, created, modified FROM banana_ticket_reply WHERE ticket_id = :ticket_id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':ticket_id', $this->id);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'TicketReply');
				$stmt->execute();
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Ticket::replies');
			}
			return $ret;
		}

	}

	class TicketReply extends Model {

		public $id;
		public $ticket_id;
		public $user_id;
		public $details;
		public $attachments;
		public $created;
		public $modified;

		function init() {
			//
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = 0;
				$this->ticket_id = 0;
				$this->user_id = 0;
				$this->details = '';
				$this->attachments = '';
				$this->created = $now;
				$this->modified = $now;
			} else {
				//
			}
		}

		function save() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$this->modificado = date('Y-m-d H:i:s');
			try {
				$sql = "INSERT INTO banana_ticket_reply (id, ticket_id, user_id, details, attachments, created, modified)
						VALUES (:id, :ticket_id, :user_id, :details, :attachments, :created, :modified)
						ON DUPLICATE KEY UPDATE details = :details, attachments = :attachments, modified = :modified";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':ticket_id', $this->ticket_id);
				$stmt->bindValue(':user_id', $this->user_id);
				$stmt->bindValue(':details', $this->details);
				$stmt->bindValue(':attachments', $this->attachments);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':modified', $this->modified);
				$stmt->execute();
				if (! $this->id ) {
					$this->id = $dbh->lastInsertId();
				}
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on TicketReply::save');
			}
			return $ret;
		}

		function delete() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "DELETE FROM banana_ticket_reply WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on TicketReply::delete');
			}
			return $ret;
		}

	}

	class Tickets {

		static function get($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, user_id, project_id, client_id, subject, details, attachments, status, replies, due, created, modified FROM banana_ticket WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Ticket');
				$stmt->execute();
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Tickets::get');
			}
			return $ret;
		}

		static function getReply($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, ticket_id, user_id, details, attachments, created, modified FROM banana_ticket_reply WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'TicketReply');
				$stmt->execute();
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Tickets::getReply');
			}
			return $ret;
		}

		static function all($page = 1, $show = 30, $sort = 'asc', $by = 'id') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = $show * ($page - 1);
			# Sanity checks
			$by = in_array($by, array('id', 'user_id', 'project_id', 'client_id', 'subject', 'details', 'attachments', 'status', 'replies', 'due', 'created', 'modified') ) ? $by : false;
			$sort = strtoupper($sort);
			$sort = in_array($sort, array('ASC', 'DESC') ) ? $sort : false;
			$offset = is_numeric($offset) ? $offset : false;
			$show = is_numeric($show) ? $show : false;
			if ($by === false || $sort === false || $offset === false || $show === false) {
				return $ret;
			}
			try {
				$sql = "SELECT id, user_id, project_id, client_id, subject, details, attachments, status, replies, due, created, modified FROM banana_ticket ORDER BY {$by} {$sort} LIMIT {$offset},{$show}";
				$stmt = $dbh->prepare($sql);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Ticket');
				$stmt->execute();
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Tickets::all');
			}
			return $ret;
		}

		static function where($field, $operator = '=', $value = 1, $page = 1, $show = 30, $sort = 'asc', $by = 'id') {
			$conditions = "{$field} {$operator} '{$value}'";
			return self::rawWhere($conditions, $page, $show, $sort, $by);
		}

		static function rawWhere($conditions, $page = 1, $show = 30, $sort = 'asc', $by = 'id') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = $show * ($page - 1);
			# Sanity checks
			$by = in_array($by, array('id', 'user_id', 'project_id', 'client_id', 'subject', 'details', 'attachments', 'status', 'replies', 'due', 'created', 'modified') ) ? $by : false;
			$sort = strtoupper($sort);
			$sort = in_array($sort, array('ASC', 'DESC') ) ? $sort : false;
			$offset = is_numeric($offset) ? $offset : false;
			$show = is_numeric($show) ? $show : false;
			if ($by === false || $sort === false || $offset === false || $show === false) {
				return $ret;
			}
			try {
				$sql = "SELECT id, user_id, project_id, client_id, subject, details, attachments, status, replies, due, created, modified FROM banana_ticket WHERE {$conditions} ORDER BY {$by} {$sort} LIMIT {$offset},{$show}";
				$stmt = $dbh->prepare($sql);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Ticket');
				$stmt->execute();
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Tickets::rawWhere');
			}
			return $ret;
		}

		static function count($conditions = 1) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT COUNT(id) AS total FROM banana_ticket WHERE {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch();
				if ($row) {
					$ret = $row->total;
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Tickets::count');
			}
			return $ret;
		}

	}

	class TicketTag extends Model {

		public $id;
		public $slug;
		public $name;
		public $description;
		public $type;
		public $created;
		public $modified;

		function init() {
			//
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = 0;
				$this->slug = '';
				$this->name = '';
				$this->description = '';
				$this->type = 'Label';
				$this->created = $now;
				$this->modified = $now;
			} else {
				//
			}
		}

		function save() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$this->modificado = date('Y-m-d H:i:s');
			try {
				$sql = "INSERT INTO banana_ticket_tag (id, slug, name, description, type, created, modified)
						VALUES (:id, :slug, :name, :description, :type, :created, :modified)
						ON DUPLICATE KEY UPDATE name = :name, description = :description, modified = :modified";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':slug', $this->slug);
				$stmt->bindValue(':name', $this->name);
				$stmt->bindValue(':description', $this->description);
				$stmt->bindValue(':type', $this->type);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':modified', $this->modified);
				$stmt->execute();
				if (! $this->id ) {
					$this->id = $dbh->lastInsertId();
				}
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on TicketTag::save');
			}
			return $ret;
		}

		function delete() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "DELETE FROM banana_ticket_tag WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on TicketTag::delete');
			}
			return $ret;
		}

		function count() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT COUNT(id_tag) AS total FROM banana_ticket_relationship WHERE id_tag = :id_tag";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id_tag', $this->id);
				$stmt->execute();
				$row = $stmt->fetch();
				if ($row) {
					$ret = $row->total;
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on TicketTags::count');
			}
			return $ret;
		}

	}

	class TicketTags {

		static function get($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_ticket_tag WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'TicketTag');
				$stmt->execute();
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on TicketTags::get');
			}
			return $ret;
		}

		static function all($page = 1, $show = 30, $sort = 'asc', $by = 'id') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = $show * ($page - 1);
			# Sanity checks
			$by = in_array($by, array('id', 'slug', 'name', 'description', 'type', 'created', 'modified') ) ? $by : false;
			$sort = strtoupper($sort);
			$sort = in_array($sort, array('ASC', 'DESC') ) ? $sort : false;
			$offset = is_numeric($offset) ? $offset : false;
			$show = is_numeric($show) ? $show : false;
			if ($by === false || $sort === false || $offset === false || $show === false) {
				return $ret;
			}
			try {
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_ticket_tag ORDER BY {$by} {$sort} LIMIT {$offset},{$show}";
				$stmt = $dbh->prepare($sql);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'TicketTag');
				$stmt->execute();
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on TicketTags::all');
			}
			return $ret;
		}

		static function where($field, $operator = '=', $value = 1, $page = 1, $show = 30, $sort = 'asc', $by = 'id') {
			$conditions = "{$field} {$operator} '{$value}'";
			return self::rawWhere($conditions, $page, $show, $sort, $by);
		}

		static function rawWhere($conditions, $page = 1, $show = 30, $sort = 'asc', $by = 'id') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = $show * ($page - 1);
			# Sanity checks
			$by = in_array($by, array('id', 'slug', 'name', 'description', 'type', 'created', 'modified') ) ? $by : false;
			$sort = strtoupper($sort);
			$sort = in_array($sort, array('ASC', 'DESC') ) ? $sort : false;
			$offset = is_numeric($offset) ? $offset : false;
			$show = is_numeric($show) ? $show : false;
			if ($by === false || $sort === false || $offset === false || $show === false) {
				return $ret;
			}
			try {
				$sql = "SELECT id, slug, name, description, type, created, modified FROM banana_ticket_tag WHERE {$conditions} ORDER BY {$by} {$sort} LIMIT {$offset},{$show}";
				$stmt = $dbh->prepare($sql);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'TicketTag');
				$stmt->execute();
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on TicketTags::rawWhere');
			}
			return $ret;
		}

		static function count($conditions = 1) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT COUNT(id) AS total FROM banana_ticket_tag WHERE {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch();
				if ($row) {
					$ret = $row->total;
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on TicketTags::count');
			}
			return $ret;
		}

	}

?>