<?php
	//
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">

				<?php if ( Users::currentUserCan('manage_options') ): ?>

					<ul class="toolbar">
						<li><a href="<?php $site->urlTo('/clients', true) ?>" class="btn btn-default"><i class="fa fa-briefcase"></i> Clients</a></li>
						<li><a href="<?php $site->urlTo('/projects', true) ?>" class="btn btn-default"><i class="fa fa-rocket"></i> Projects</a></li>
						<li><a href="<?php $site->urlTo('/tickets', true) ?>" class="btn btn-default"><i class="fa fa-ticket"></i> Tickets</a></li>
						<li><a href="<?php $site->urlTo('/todo', true) ?>" class="btn btn-default"><i class="fa fa-calendar"></i> ToDo</a></li>
						<li><a href="<?php $site->urlTo('/funnel', true) ?>" class="btn btn-default"><i class="fa fa-th"></i> Funnel</a></li>
						<li><a href="<?php $site->urlTo('/bites', true) ?>" class="btn btn-default"><i class="fa fa-code"></i> Bites</a></li>
						<li><a href="<?php $site->urlTo('/keyring', true) ?>" class="btn btn-default"><i class="fa fa-key"></i> Keyring</a></li>
						<li><a href="<?php $site->urlTo('/contact', true) ?>" class="btn btn-default"><i class="fa fa-phone"></i> Contacts</a></li>
					</ul>

					<br>

					<div class="widgets">
						<div class="row">
							<div class="widget col-md-7"><?php $site->getParts('client/widgets/updates.widget'); ?></div>
							<div class="widget col-md-5">
								<?php $site->getParts('client/widgets/tickets.widget'); ?>
								<?php $site->getParts('client/widgets/todo.widget'); ?>
							</div>
						</div>
					</div>

				<?php else: ?>

					<br>
					<h2 class="text-center"><small>Welcome, </small> <?php echo $site->user->nickname; ?></h2>
					<p class="text-center">These are your available modules</p>
					<br>
					<div class="row">
						<div class="col-md-6 col-md-offset-3">
							<div class="list-group">
								<a href="<?php $site->urlTo('/tickets', true) ?>" class="list-group-item has-icons">
									<i class="fa fa-ticket list-group-item-icon"></i>
									<h4 class="list-group-item-heading">Tickets</h4>
									<p class="list-group-item-text">Tickets are a great way to keep track of tasks, enhancements, and bugs for your projects.</p>
								</a>
							</div>
						</div>
					</div>

				<?php endif; ?>

			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>