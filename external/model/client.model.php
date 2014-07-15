<?php

	class Client extends MetadataModel {

		public $id;
		public $slug;
		public $name;
		public $notes;
		public $status;
		public $type;
		public $created;
		public $updated;

		function init() {
			$this->meta_id = 'client_id';
			$this->meta_table = 'banana_client_meta';
			//
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = 0;
				$this->slug = 0;
				$this->name = '';
				$this->notes = '';
				$this->status = '';
				$this->type = '';
				$this->created = $now;
				$this->updated = $now;
			}
		}

		function save() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$this->slug = $this->slug ? $this->slug : $site->toAscii($this->nombre);
			$this->modificado = date('Y-m-d H:i:s');
			try {
				$sql = "INSERT INTO banana_client (id, slug, name, notes, status, type, created, updated)
						VALUES (:id, :slug, :name, :notes, :status, :type, :created, :updated)
						ON DUPLICATE KEY UPDATE name = :name, notes = :notes, status = :status, type = :type, updated = :updated";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':slug', $this->slug);
				$stmt->bindValue(':name', $this->name);
				$stmt->bindValue(':notes', $this->notes);
				$stmt->bindValue(':status', $this->status);
				$stmt->bindValue(':type', $this->type);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':updated', $this->updated);
				$stmt->execute();
				if (! $this->id ) {
					$this->id = $dbh->lastInsertId();
				}
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Client::save');
			}
			return $ret;
		}

		function delete() {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "DELETE FROM banana_client WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = $stmt->rowCount() > 0;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Client::delete');
			}
			return $ret;
		}

	}

	class Clients {

		static function get($id) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT id, slug, name, notes, status, type, created, updated FROM banana_client WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Client');
				$stmt->execute();
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Clients::get');
			}
			return $ret;
		}

		static function all($page = 1, $show = 30, $sort = 'asc', $by = 'id') {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			$offset = $show * ($page - 1);
			# Sanity checks
			$by = in_array($by, array('id', 'slug', 'name', 'notes','status', 'type', 'created', 'updated') ) ? $by : false;
			$sort = strtoupper($sort);
			$sort = in_array($sort, array('ASC', 'DESC') ) ? $sort : false;
			$offset = is_numeric($offset) ? $offset : false;
			$show = is_numeric($show) ? $show : false;
			if ($by === false || $sort === false || $offset === false || $show === false) {
				return $ret;
			}
			try {
				$sql = "SELECT id, slug, name, notes, status, type, created, updated FROM banana_client ORDER BY {$by} {$sort} LIMIT {$offset},{$show}";
				$stmt = $dbh->prepare($sql);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Client');
				$stmt->execute();
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Clients::all');
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
			$by = in_array($by, array('id', 'slug', 'name', 'notes','status', 'type', 'created', 'updated') ) ? $by : false;
			$sort = strtoupper($sort);
			$sort = in_array($sort, array('ASC', 'DESC') ) ? $sort : false;
			$offset = is_numeric($offset) ? $offset : false;
			$show = is_numeric($show) ? $show : false;
			if ($by === false || $sort === false || $offset === false || $show === false) {
				return $ret;
			}
			try {
				$sql = "SELECT id, slug, name, notes, status, type, created, updated FROM banana_client WHERE {$conditions} ORDER BY {$by} {$sort} LIMIT {$offset},{$show}";
				$stmt = $dbh->prepare($sql);
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Client');
				$stmt->execute();
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Clients::rawWhere');
			}
			return $ret;
		}

		static function count($conditions = 1) {
			global $site;
			$ret = false;
			$dbh = $site->getDatabase();
			try {
				$sql = "SELECT COUNT(id) AS total FROM banana_client WHERE {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch();
				if ($row) {
					$ret = $row->total;
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage('Database error ' . $e->getCode() . ' on Clients::count');
			}
			return $ret;
		}

	}

?>