<?php

	class MetadataModel extends Model {

		public $id;

		protected $meta_table;
		protected $meta_id;

		function init() {
			$meta_table = null;
			$meta_id = null;
		}

		function getMeta($name, $default = '') {
			global $site;
			$dbh = $site->getDatabase();
			$ret = $default;
			try {
				$sql = "SELECT value FROM {$this->meta_table} WHERE {$this->meta_id} = :id AND name = :name";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':name', $name);
				$stmt->execute();
				if ( $row = $stmt->fetch() ) {
					$ret = @unserialize($row->value);
					if ($ret === false) {
						$ret = $row->value;
					}
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}

		function updateMeta($name, $value) {
			global $site;
			$dbh = $site->getDatabase();
			$ret = false;
			if ( is_array($value) || is_object($value) ) {
				$value = serialize($value);
			}
			try {
				$sql = "INSERT INTO {$this->meta_table} (id, {$this->meta_id}, value, name) VALUES (0, :meta_id, :value, :name) ON DUPLICATE KEY UPDATE value = :value";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':meta_id', $this->id);
				$stmt->bindValue(':value', $value);
				$stmt->bindValue(':name', $name);
				$stmt->execute();
				if ( $dbh->lastInsertId() ) {
					$ret = true;
				}
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
			}
			return $ret;
		}
	}

?>