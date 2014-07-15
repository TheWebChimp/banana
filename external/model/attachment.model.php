<?php

	class Attachment extends MetadataModel {

		public $id;
		public $slug;
		public $name;
		public $attachment;
		public $mime;
		public $created;
		public $modified;

		function init() {
			$this->meta_id = 'attachment_id';
			$this->meta_table = 'banana_attachment_meta';
			if (! $this->id ) {
				$now = date('Y-m-d H:i:s');
				$this->id = 0;
				$this->slug = '';
				$this->name = '';
				$this->attachment = '';
				$this->mime = '';
				$this->created = $now;
				$this->modified = $now;
			} else {
				$this->description = $this->getMeta('description');
			}
		}

		function getPath($echo = false) {
			global $site;
			$ret = false;
			$dir = date('Y/m', strtotime($this->created));
			$ret = $site->urlTo("/upload/$dir/{$this->attachment}");
			if ($echo) {
				echo $ret;
			}
			return;
		}

		function getUrl($echo = false) {
			global $site;
			$ret = false;
			$dir = date('Y/m', strtotime($this->created));
			$ret = $site->urlTo("/upload/$dir/{$this->attachment}");
			if ($echo) {
				echo $ret;
			}
			return;
		}

		function getImage($type = 'url', $size = 'thumbnail', $echo = false) {
			global $site;
			$ret = false;
			if ( substr($this->mime, 0, 5) == 'image' ) {
				# Generate path
				$dir = date('Y/m', strtotime($this->created));
				# Generate the image object (just in case)
				switch ($this->mime) {
					case 'image/png':  $ext = 'png'; break;
					case 'image/gif':  $ext = 'gif'; break;
					case 'image/jpeg': $ext = 'jpg'; break;
				}
				$image = array(
					'url' => $site->urlTo("/upload/$dir/{$this->slug}.{$ext}"),
					'sizes' => array(
						'thumbnail' => $site->urlTo("/upload/$dir/{$this->slug}-thumb.{$ext}"),
						'medium' => $site->urlTo("/upload/$dir/{$this->slug}-medium.{$ext}"),
						'large' => $site->urlTo("/upload/$dir/{$this->slug}-large.{$ext}")
					)
				);
				# Return what the user wants
				switch ($type) {
					case 'url':
						$ret = isset( $image['sizes'][$size] ) ? $image['sizes'][$size] : $image['url'];
						break;
					case 'img':
						$ret = isset( $image['sizes'][$size] ) ? "<img src=\"{$image['sizes'][$size]}\" alt=\"\" />" : "<img src=\"{$image['url']}\" alt=\"\" />";
						break;
					case 'object':
						$ret = $image;
						break;
				}
				if ($echo) {
					echo $ret;
				}
			}
			return $ret;
		}

		function save() {
			global $site;
			$dbh = $site->getDatabase();
			$ret = false;
			#
			$this->slug = $this->slug ? $this->slug : $site->toAscii($this->name);
			$this->modified = date('Y-m-d H:i:s');
			try {
				$sql = "INSERT INTO banana_attachment (id, slug, name, attachment, mime, created, modified)
						VALUES (:id, :slug, :name, :attachment, :mime, :created, :modified)
						ON DUPLICATE KEY UPDATE name = :name, attachment = :attachment, mime = :mime, modified = :modified";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->bindValue(':slug', $this->slug);
				$stmt->bindValue(':name', $this->name);
				$stmt->bindValue(':attachment', $this->attachment);
				$stmt->bindValue(':mime', $this->mime);
				$stmt->bindValue(':created', $this->created);
				$stmt->bindValue(':modified', $this->modified);
				$stmt->execute();
				if ( $dbh->lastInsertId() ) {
					$this->id = $dbh->lastInsertId();
				}
				$ret = ($stmt->rowCount() > 0);
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Attachment::save()");
			}
			return $ret;
		}

		function delete() {
			global $site;
			$dbh = $site->getDatabase();
			$ret = false;
			try {
				# Generate path
				$dir = date('Y/m', strtotime($this->created));
				# Delete files
				if ( substr($this->mime, 0, 5) == 'image' ) {
					# Generate the image object (just in case)
					switch ($this->mime) {
						case 'image/png':  $ext = 'png'; break;
						case 'image/gif':  $ext = 'gif'; break;
						case 'image/jpeg': $ext = 'jpg'; break;
					}
					$images = array(
						$site->baseDir("/upload/{$dir}/{$this->slug}-thumb.{$ext}"),
						$site->baseDir("/upload/{$dir}/{$this->slug}-medium.{$ext}"),
						$site->baseDir("/upload/{$dir}/{$this->slug}-large.{$ext}"),
						$site->baseDir("/upload/{$dir}/{$this->slug}.{$ext}")
					);
					foreach ($images as $image) {
						unlink($image);
					}
				} else {
					unlink( $site->baseDir("/upload/{$dir}/{$this->slug}.{$ext}") );
				}
				# Delete from database
				$sql = "DELETE FROM banana_attachment WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $this->id);
				$stmt->execute();
				$ret = ($stmt->rowCount() > 0);
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Attachment::delete()");
			}
			return $ret;
		}

	}

	class Attachments {

		static function get($id) {
			if ( is_numeric($id) ) {
				return self::_getByID($id);
			} else {
				return self::_getBySlug($id);
			}
		}

		static function count($conditions = 1) {
			global $site;
			$dbh = $site->getDatabase();
			$ret = 0;
			try {
				$sql = "SELECT COUNT(id) AS total FROM banana_attachment WHERE {$conditions}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$row = $stmt->fetch();
				$ret = $row->total;
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Attachments::count()");
			}
			return $ret;
		}

		static function all($page = 1, $show = 100, $sort = 'asc', $by = 'id') {
			global $site;
			$dbh = $site->getDatabase();
			$ret = array();
			$offset = $show * ($page - 1);
			# Sanity checks
			$by = in_array($by, array('id', 'slug', 'name', 'attachment', 'mime', 'created', 'modified') ) ? $by : false;
			$sort = strtoupper($sort);
			$sort = in_array($sort, array('ASC', 'DESC') ) ? $sort : false;
			$offset = is_numeric($offset) ? $offset : false;
			$show = is_numeric($show) ? $show : false;
			if ($by === false || $sort === false || $offset === false || $show === false) {
				return $ret;
			}
			try {
				$sql = "SELECT id, slug, name, attachment, mime, created, modified FROM banana_attachment ORDER BY {$by} {$sort} LIMIT {$offset},{$show}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Attachment');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Attachments::all()");
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
		static function where($column, $operator = '=', $value = '', $page = 1, $show = 100, $sort = 'asc', $by = 'id') {
			$conditions = "{$column} {$operator} '{$value}'";
			return self::rawWhere($conditions, $page, $show, $sort, $by);
		}

		/**
		 * Retrieve all the users from the database that match the given criteria
		 * This is similar to where() but allows the programmer to specify a manually built string with conditions - this is particularly exposed to sql-injections, so always sanitize your inputs!
		 * @param  string  $conditions  String with the "WHERE foo = 'bar'" conditions (not including the WHERE itself)
		 * @return array                Array with User objects, False on error
		 */
		static function rawWhere($conditions = '', $page = 1, $show = 100, $sort = 'asc', $by = 'id') {
			global $site;
			$dbh = $site->getDatabase();
			$ret = array();
			$offset = $show * ($page - 1);
			# Sanity checks
			$by = in_array($by, array('id', 'slug', 'name', 'attachment', 'mime', 'created', 'modified') ) ? $by : false;
			$sort = strtoupper($sort);
			$sort = in_array($sort, array('ASC', 'DESC') ) ? $sort : false;
			$offset = is_numeric($offset) ? $offset : false;
			$show = is_numeric($show) ? $show : false;
			if ($by === false || $sort === false || $offset === false || $show === false) {
				return $ret;
			}
			try {
				$sql = "SELECT id, slug, name, attachment, mime, created, modified FROM banana_attachment WHERE {$conditions} ORDER BY {$by} {$sort} LIMIT {$offset},{$show}";
				$stmt = $dbh->prepare($sql);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Attachment');
				$ret = $stmt->fetchAll();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Attachments::rawWhere()");
			}
			return $ret;
		}

		static function _getByID($id) {
			global $site;
			$dbh = $site->getDatabase();
			$ret = null;
			try {
				$sql = "SELECT id, slug, name, attachment, mime, created, modified FROM banana_attachment WHERE id = :id";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':id', $id);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Attachment');
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Attachments::getID()");
			}
			return $ret;
		}

		static function _getBySlug($slug) {
			global $site;
			$dbh = $site->getDatabase();
			$ret = null;
			try {
				$sql = "SELECT id, slug, name, attachment, mime, created, modified FROM banana_attachment WHERE slug = :slug";
				$stmt = $dbh->prepare($sql);
				$stmt->bindValue(':slug', $slug);
				$stmt->execute();
				$stmt->setFetchMode(PDO::FETCH_CLASS, 'Attachment');
				$ret = $stmt->fetch();
			} catch (PDOException $e) {
				error_log( $e->getMessage() );
				$site->errorMessage("Database error: {$e->getCode()} in Attachments::getID()");
			}
			return $ret;
		}

		static function upload($file) {
			global $site;
			$ret = null;
			//
			if( $file && $file['tmp_name'] ) {
				# Get name parts
				$name = substr( $file['name'], 0, strrpos($file['name'], '.') );
				$ext = substr( $file['name'], strrpos($file['name'], '.') + 1 );
				# Normalize JPEG extensions
				$ext = ($ext == 'jpeg') ? 'jpg' : $ext;
				# Check destination folder
				$year = date('Y');
				$month = date('m');
				$dest_dir = "{$year}/{$month}";
				if (! file_exists( $site->baseDir("/upload/{$dest_dir}") ) ) {
					@mkdir( $site->baseDir("/upload/{$year}") );
					@mkdir( $site->baseDir("/upload/{$year}/{$month}") );
				}
				# Generate a destination name
				$dest_name = $site->toAscii($name);
				$dest_path = $site->baseDir("/upload/{$dest_dir}/{$dest_name}.{$ext}");
				# Check whether the name exists nor not
				if ( file_exists($dest_path) ) {
					$dest_name = $site->toAscii( $name . uniqid() );
					$dest_path = $site->baseDir("/upload/{$dest_dir}/{$dest_name}.{$ext}");
				}
				# Get MIME type
				if ( $file['type'] ) {
					$mime = $file['type'];
				} else {
					switch ($ext) {
						case 'gif':
						case 'png':
							$mime = "image/{$ext}";
						case 'jpg':
							$mime = 'image/jpeg';
							break;
						case 'mpeg':
						case 'mp4':
						case 'ogg':
						case 'webm':
							$mime = "video/{$ext}";
							break;
						case 'pdf':
						case 'zip':
							$mime = "application/{$ext}";
							break;
						case 'csv':
						case 'xml':
							$mime = "text/{$ext}";
							break;
						default:
							$mime = 'application/octet-stream';
					}
				}
				# Crunching
				if ( substr($mime, 0, 5) == 'image' ) {
					$images = array(
						'thumbnail' => $site->baseDir("/upload/{$dest_dir}/{$dest_name}-thumb.{$ext}"),
						'medium' => $site->baseDir("/upload/{$dest_dir}/{$dest_name}-medium.{$ext}"),
						'large' => $site->baseDir("/upload/{$dest_dir}/{$dest_name}-large.{$ext}")
					);
					require_once $site->baseDir('/lib/PHPThumb/ThumbLib.inc.php');
					try {
						# Thumbnail
						$thumb = PhpThumbFactory::create( $file['tmp_name'] );
						$thumb->adaptiveResize(150, 150);
						$thumb->save($images['thumbnail']);
						# Medium image
						$thumb = PhpThumbFactory::create( $file['tmp_name'] );
						$thumb->resize(300, 300);
						$thumb->save($images['medium']);
						# Large image
						$thumb = PhpThumbFactory::create( $file['tmp_name'] );
						$thumb->resize(1024, 1024);
						$thumb->save($images['large']);
					} catch (Exception $e) {
						error_log( $e->getMessage() );
					}
				}
				# Move the uploaded file
				move_uploaded_file($file['tmp_name'], $dest_path);
				# Create and save the attachment
				$attachment = new Attachment();
				$attachment->slug = $dest_name;
				$attachment->name = $name;
				$attachment->attachment = "{$dest_name}.{$ext}";
				$attachment->mime = $mime;
				$attachment->save();
				$ret = $attachment;
			}
			return $ret;
		}

	}

?>