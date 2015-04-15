<?php
	/**
	 * config.inc.php
	 * Here's where you configure your Dragonfly instance
	 */

	# Set the active profile
	define( 'PROFILE', 'development' );
	define( 'VERSION', '1.0' );

	/**
	 * Site settings
	 * @var array 	Array with configuration options
	 */
	$settings = array(
		'development' => array(
			# Global settings
			'site_url' => 'http://localhost/dragonfly',
			# Database settings
			'db_driver' => 'none',
			'db_host' => 'localhost',
			'db_user' => 'root',
			'db_pass' => '',
			'db_name' => '',
			'db_file' => BASE_PATH . '/include/schema.sqlite',
			# Plugins
			'plugins' => array()
		),
		'testing' => array(
			# Global settings
			'site_url' => 'http://dev.yourapp.com/api',
			# Database settings
			'db_driver' => 'none',
			'db_host' => '',
			'db_user' => '',
			'db_pass' => '',
			'db_name' => '',
			'db_file' => BASE_PATH . '/include/schema.sqlite',
			# Plugins
			'plugins' => array()
		),
		'production' => array(
			# Global settings
			'site_url' => 'http://yourapp.com/api',
			# Database settings
			'db_driver' => 'none',
			'db_host' => '',
			'db_user' => '',
			'db_pass' => '',
			'db_name' => '',
			'db_file' => BASE_PATH . '/include/schema.sqlite',
			# Plugins
			'plugins' => array()
		),
		'shared' => array(
			# General
			'app_name' => 'Your App',
			# Security settings
			'pass_salt' => '',
			'token_salt' => ''
		)
	);
?>