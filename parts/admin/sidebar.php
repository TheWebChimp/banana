<?php
	//
?>
	<div class="asideback"></div>
	<aside class="sidebar">
		<button class="btn btn-menu btn-warning"><i class="fa fa-bars"></i></button>

		<h2 class="logo">
			<a href="<?php $site->urlTo('/', true); ?>"><img src="<?php $site->img('banana-96x96.png'); ?>" alt=""></a>
		</h2>

		<ul class="menu">
			<li class="divider"></li>

			<li <?php set_active_menu('admin'); ?>>
				<a href="<?php $site->urlTo('/admin', true); ?>"><i class="fa fa-home"></i> Dashboard</a>
			</li>

			<li class="divider"></li>

			<li <?php set_active_menu('attachments'); ?>>
				<div class="arrow"></div>
				<a href="<?php $site->urlTo('/admin/attachments', true); ?>"><i class="fa fa-picture-o"></i> Attachments</a>
				<ul class="submenu">
					<li <?php set_active_submenu('attachments', 'index'); ?>><a href="<?php $site->urlTo('/admin/attachments', true); ?>">View all</a></li>
					<li <?php set_active_submenu('attachments', 'new'); ?>><a href="<?php $site->urlTo('/admin/attachments/new', true); ?>">Add new</a></li>
				</ul>
			</li>

			<li class="divider"></li>

			<li <?php set_active_menu('users'); ?>>
				<div class="arrow"></div>
				<a href="<?php $site->urlTo('/admin/users', true); ?>"><i class="fa fa-user"></i> Users</a>
				<ul class="submenu">
					<li <?php set_active_submenu('users', 'index'); ?>><a href="<?php $site->urlTo('/admin/users', true); ?>">View all</a></li>
					<li <?php set_active_submenu('users', 'new'); ?>><a href="<?php $site->urlTo('/admin/users/new', true); ?>">Add new</a></li>
				</ul>
			</li>

			<li <?php set_active_menu('clients'); ?>>
				<div class="arrow"></div>
				<a href="<?php $site->urlTo('/admin/clients', true); ?>"><i class="fa fa-book"></i> Clients</a>
				<ul class="submenu">
					<li <?php set_active_submenu('clients', 'index'); ?>><a href="<?php $site->urlTo('/admin/clients', true); ?>">View all</a></li>
					<li <?php set_active_submenu('clients', 'new'); ?>><a href="<?php $site->urlTo('/admin/clients/new', true); ?>">Add new</a></li>
				</ul>
			</li>

			<li <?php set_active_menu('projects'); ?>>
				<div class="arrow"></div>
				<a href="<?php $site->urlTo('/admin/projects', true); ?>"><i class="fa fa-rocket"></i> Projects</a>
				<ul class="submenu">
					<li <?php set_active_submenu('projects', 'index'); ?>><a href="<?php $site->urlTo('/admin/projects', true); ?>">View all</a></li>
					<li <?php set_active_submenu('projects', 'new'); ?>><a href="<?php $site->urlTo('/admin/projects/new', true); ?>">Add new</a></li>
				</ul>
			</li>

			<li class="divider"></li>

			<li <?php set_active_menu('settings'); ?> class="hide">
				<!-- <div class="arrow"></div> -->
				<a href="<?php $site->urlTo('/admin/settings', true); ?>"><i class="fa fa-gear"></i> Settings</a>
				<!-- <ul class="submenu">
					<li <?php set_active_submenu('settings', 'index'); ?>><a href="<?php $site->urlTo('/admin/settings', true); ?>">View all</a></li>
				</ul> -->
			</li>
			<li <?php set_active_menu('tools'); ?> class="hide">
				<!-- <div class="arrow"></div> -->
				<a href="<?php $site->urlTo('/admin/tools', true); ?>"><i class="fa fa-wrench"></i> Tools</a>
				<!-- <ul class="submenu">
					<li <?php set_active_submenu('tools', 'index'); ?>><a href="<?php $site->urlTo('/admin/tools', true); ?>">View all</a></li>
				</ul> -->
			</li>

		</ul>
	</aside>