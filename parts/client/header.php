	<div class="wrapper">

		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand" href="<?php $site->urlTo('/dashboard', true); ?>">Banana</a>
				</div>
				<div class="collapse navbar-collapse navbar-ex1-collapse">
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<?php $user = Users::getCurrentUser(); ?>
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><img class="avatar" src="<?php echo get_gravatar($user->email, 24); ?>" alt=""> <?php echo $user->nickname; ?> <b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="<?php $site->urlTo('/dashboard', true); ?>">My dashboard</a></li>
								<li><a href="<?php $site->urlTo('/profile', true); ?>">My profile</a></li>
								<?php if ( Users::currentUserCan('manage_options') ): ?>
								<li><a href="<?php $site->urlTo('/admin', true); ?>">Manage instance</a></li>
								<?php endif; ?>
								<li class="divider"></li>
								<li><a href="<?php $site->urlTo('/logout', true); ?>">Sign out</a></li>
							</ul>
						</li>
						<!-- <li class="active"><a href="<?php $site->urlTo('/dashboard', true) ?>">Dashboard</a></li>
						<li><a href="<?php $site->urlTo('/tickets', true) ?>">Tickets</a></li> -->
					</ul>
				</div>
			</div>
		</nav>

		<header>
			<div class="container">
				<!--  -->
			</div>
		</header>