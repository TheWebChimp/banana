<?php
	include $site->baseDir('/lib/Parsedown.php');
	$parsedown = new Parsedown();
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true); ?>">Dashboard</a></li>
					<li><a href="<?php $site->urlTo('/tickets', true); ?>">Tickets</a></li>
					<li><a href="<?php $site->urlTo("/tickets/{$reply->ticket_id}", true); ?>">Ticket #<?php echo $reply->ticket_id; ?></a></li>
					<li class="active">Delete reply</li>
				</ol>
				<div class="ticket-wrap">
					<a href="<?php $site->urlTo("/tickets/{$reply->ticket_id}", true); ?>" class="btn btn-link btn-back"><i class="fa fa-arrow-left"></i></a>
					<div class="ticket-thread">
						<h2>Are you sure you want to delete this reply?</h2>
						<form action="" method="post">
							<a href="<?php $site->urlTo("/tickets/{$reply->ticket_id}", true); ?>" class="btn btn-primary">No, go back to the ticket</a>
							<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
							<button class="btn btn-link" type="submit">Yes, delete reply</button>
						</form>
						<hr>
						<div class="ticket-body">
							<img src="<?php echo get_gravatar($site->user->email, 50); ?>" alt="" class="avatar">
							<div class="panel panel-default">
								<div class="panel-heading"><strong><?php echo $site->user->nickname; ?></strong> <span class="text-muted">commented on <?php echo date('M j', strtotime($reply->created)) ?></span></div>
								<div class="panel-body">
									<div class="content"><?php echo $parsedown->text($reply->details); ?></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>