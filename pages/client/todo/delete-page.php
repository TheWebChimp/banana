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
					<li><a href="<?php $site->urlTo('/todo', true); ?>">ToDo</a></li>
					<li class="active">Delete task</li>
				</ol>
				<h2>Are you sure you want to delete this task?</h2>
				<form action="" method="post">
					<a href="<?php $site->urlTo("/todo", true); ?>" class="btn btn-primary">No, go back to the list</a>
					<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
					<button class="btn btn-link" type="submit">Yes, delete task</button>
				</form>
				<hr>
				<div class="list-group todos">
					<div class="list-group-item todo in">
						<span class="fa fa-<?php echo ($todo->status == 'Done' ? 'check' : 'exclamation'); ?>-circle text-<?php echo ($todo->status == 'Done' ? 'success' : 'info'); ?>" title="<?php echo $todo->status; ?> Ticket"></span>
						<p class="list-group-item-text title clearfix">
							<strong><?php echo $todo->name; ?></strong>
						</p>
						<div class="list-group-item-text details">
							<?php
								echo $parsedown->text($todo->details);
								$attachments = $todo->attachments ? @unserialize($todo->attachments) : null;
								if ($attachments):
									foreach ($attachments as $attachment_id):
										$attachment = Attachments::get($attachment_id);
										if (! $attachment ) continue;
							?>
							<div class="attachmentss">
								<i class="fa fa-file"></i>
								<a href="<?php $attachment->getUrl(true); ?>" target="_blank"><?php echo $attachment->name; ?></a>
								<span class="text-muted"> (<?php echo $attachment->mime; ?>)</span>
							</div>
							<?php
									endforeach;
								endif;
							?>
						</div>
						<div class="text-right text-muted"><small><?php echo relative_time( $todo->created );  ?></small></div>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>