	<div class="wrapper">
		<header>

			<?php
				$user = Users::getCurrentUser();
			?>
			<div class="pull-right">
				<ul class="menu">
					<li class="menu-item">
						<img class="avatar" src="<?php echo get_gravatar($user->email, 24); ?>" alt="">
						<?php echo $user->nickname; ?>
						<ul class="sub-menu">
							<li class="menu-item"><a href="<?php $site->urlTo("/admin/users/edit/{$user->id}", true); ?>">My Profile</a></li>
							<li class="menu-item"><a href="<?php $site->urlTo('/admin/logout', true); ?>">Sign out</a></li>
						</ul>
					</li>
				</ul>
			</div>

			<div class="pull-left">
				<ul class="menu">
					<li class="menu-item"><a href="<?php $site->urlTo('/admin/settings', true); ?>"><span class="fa fa-gear"></span> Settings</a></li>
					<li class="menu-item">
						<span class="fa fa-plus"></span> Add new
						<ul class="sub-menu">
							<li class="menu-item"><a href="<?php $site->urlTo("/admin/users/new", true); ?>">User</a></li>
							<li class="menu-item"><a href="<?php $site->urlTo("/admin/clients/new", true); ?>">Client</a></li>
							<li class="menu-item"><a href="<?php $site->urlTo("/admin/projects/new", true); ?>">Project</a></li>
						</ul>
					</li>
				</ul>
			</div>

		</header>