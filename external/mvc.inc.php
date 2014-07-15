<?php

	/**
	 * Hummingbird MVC (Cinnamon)
	 */

	/////////////////
	// Model Class //
	/////////////////

	abstract class Model {
		# <!-- -->
	}

	////////////////
	// View Class //
	////////////////

	abstract class View {
		protected $templates;
		protected $templates_dir;

		/**
		 * Constructor
		 */
		function __construct() {
			$templates = array();
			$templates_dir = '';
			$this->init();
		}

		/**
		 * Initialization callback
		 * @return nothing
		 */
		abstract function init();

		/**
		 * Set the templates folder for this view
		 * @param string $path Full path to the templates folder
		 */
		function setTemplatesDir($path) {
			$this->templates_dir = $path;
		}

		/**
		 * Register a new template
		 * @param string $name Template name (slug)
		 * @param string $file Template file name (without path nor extension)
		 */
		function addTemplate($name, $file) {
			if (! isset( $this->templates[$name] ) ) {
				$this->templates[$name] = $file;
			}
		}

		/**
		 * Unregister the specified template
		 * @param  string $name Template name
		 * @return nothing
		 */
		function removeTemplate($name) {
			if ( isset( $this->templates[$name] ) ) {
				unset( $this->templates[$name] );
			}
		}

		/**
		 * Render the specified template
		 * @param  string $name Template name
		 * @param  array  $data Array of data that will be passed to the template
		 * @return nothing
		 */
		function render($name, $data = array()) {
			global $site;
			$mvc = $site->mvc;
			if ( isset( $this->templates[$name] ) ) {
				$template = $this->templates[$name];
				$include = sprintf('%s/%s.php', $this->templates_dir, $template);
				# Check whether the template exists or not
				if ( file_exists($include) ) {
					# Set body slug
					$site->addBodyClass($mvc->controller);
					$site->addBodyClass($mvc->controller . '-' . $mvc->action);
					$site->addBodyClass($name . '-page');
					# Import globals
					extract($GLOBALS, EXTR_REFS | EXTR_SKIP);
					# Inject data
					$data = $data;
					# Include file
					include $include;
				} else {
					$site->errorMessage("View error: template '{$name}' does not exist on the specified path.");
					exit;
				}
			}
		}
	}

	//////////////////////
	// Controller Class //
	//////////////////////

	abstract class Controller {

		/**
		 * Constructor
		 */
		function __construct() {
			$this->init();
		}

		/**
		 * Initialization callback
		 * @return nothing
		 */
		abstract function init();

		/**
		 * Base handler function
		 * @param  string  $id     ID to show (if set)
		 * @param  string  $format Format to serve (html, xml, json, etc)
		 * @param  string  $type   Request method (get, post, put, delete, etc)
		 * @return nothing
		 */
		abstract function index($id, $format, $type);
	}

	///////////////
	// MVC Class //
	///////////////

	class MVC {
		protected $controllers;
		public $controller;
		public $action;
		public $id;

		/**
		 * Constructor
		 */
		function __construct() {
			global $site;
			# Register routes and disengage default site router
			$site->removeRoute('/:page');
			$site->addRoute('/:controller', 'MVC::router');
			$site->addRoute('/:controller/:action', 'MVC::router');
			$site->addRoute('/:controller/:action/:id', 'MVC::router');
			# Create internal controller list
			$this->controllers = array();
		}

		/**
		 * Router
		 * @param  array  $params Array of router parameters
		 * @return boolen         True if the routing took place, False otherwise
		 */
		static function router($params) {
			global $site;
			$mvc = $site->mvc;
			# Extract parameters
			$controller = isset( $params[1] ) ? $params[1] : 'index';
			$action =     isset( $params[2] ) ? $params[2] : 'index';
			$id =         isset( $params[3] ) ? $params[3] : '';
			# Check format specifier
			$matches = null;
			if ( preg_match('/(\w+)\.(\w+)$/', $id, $matches) === 1 ) {
				$id = $matches[1];
				$format = $matches[2];
			} else {
				$format = 'html';
			}
			# Save parameters
			$mvc->controller = $controller;
			$mvc->action = $action;
			$mvc->id = $id;
			# Check whether the request may be handled by a controller or not
			$instance = $mvc->getController( $controller );
			if ($instance) {
				# Relay to the controller
				$method = preg_match('/^__\w+$/', $action) === 1 ? '' : $action; // protect magic methods
				$method = method_exists($instance, $method) ? $method : 'show';  // default to 'show'
				$type = isset( $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] ) ? $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] : $_SERVER['REQUEST_METHOD'];
				$type = strtolower($type);
				if ($method == 'show' && $method != $action) {
					$id = $action;
					$action = $method;
					# Adjust variables
					$mvc->action = $action;
					$mvc->id = $id;
				}
				if ( method_exists($instance, $method) ) {
					$instance->$method($id, $format, $type);
					return true;
				} else {
					$site->errorMessage("Router error: method '{$method}' from '{$controller}' controller does not exist.");
					return true;
				}
			} else {
				# Serve a static page
				return $site->getPage($params);
			}
		}

		/**
		 * Add a controller to the internal list
		 * @param string $name  Controller name (slug)
		 * @param string $class Controller class name, for dynamic instantiation
		 */
		function addController($name, $class) {
			if (! isset( $this->controllers[$name] ) ) {
				$this->controllers[$name] = array(
					'name' => $name,
					'class' => $class,
					'instance' => null
				);
			}
		}

		/**
		 * Remove a controller from the internal list
		 * @param  string  $name Controller name
		 * @return nothing
		 */
		function removeController($name) {
			if ( isset( $this->controllers[$name] ) ) {
				unset( $this->controllers[$name] );
			}
		}

		/**
		 * Get a controller instance, dynamically creating it if required
		 * @param  string $name Controller name
		 * @return mixed        Controller instance (object) or null on error
		 */
		function getController($name) {
			if ( isset( $this->controllers[$name] ) ) {
				$controller = $this->controllers[$name];
				if (! $controller['instance'] && class_exists( $controller['class'] ) ) {
					$controller['instance'] = new $controller['class'];
				}
				return $controller['instance'];
			}
			return null;
		}

	}

	$site->mvc = new MVC();

?>