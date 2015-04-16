<?php
	include $site->baseDir('/lib/Parsedown.php');
	$parsedown = new Parsedown();

	$ticket_project = Projects::get($ticket->project_id);
	$ticket_client = $ticket_project? $ticket_project->clients[0] : '';
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true); ?>">Dashboard</a></li>
					<li><a href="<?php $site->urlTo('/tickets', true); ?>">Tickets</a></li>
					<li><a href="<?php $site->urlTo("/tickets/{$reply->ticket_id}", true); ?>">Ticket #<?php echo $reply->ticket_id; ?></a></li>
					<li class="active">Edit reply</li>
				</ol>
				<div class="ticket-wrap">
					<a href="<?php $site->urlTo("/tickets/{$reply->ticket_id}", true); ?>" class="btn btn-link btn-back"><i class="fa fa-arrow-left"></i></a>
					<div class="ticket-thread">
						<h2><?php echo $ticket->subject; ?> <span class="text-muted">#<?php echo $ticket->id; ?></span></h2>
						<?php if($ticket_project): ?>
							<h5><?php echo $ticket_client->name; ?> &mdash; <?php echo $ticket_project->name; ?></h5>
						<?php endif; ?>
						<hr>
						<div class="ticket-body">
							<img src="<?php echo get_gravatar($site->user->email, 50); ?>" alt="" class="avatar">
							<div class="panel panel-default">
								<div class="panel-heading"><strong>Edit reply</strong></div>
								<div class="panel-body">
									<form class="form-ticket" id="form-reply" action="<?php $site->urlTo("/tickets/edit-reply/{$reply->id}", true); ?>" method="post">
										<input type="hidden" name="ticket_id" value="<?php echo $reply->ticket_id; ?>">
										<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
										<ul class="nav nav-pills">
											<li class="active"><a href="#write" data-toggle="tab">Write</a></li>
											<li><a href="#preview" data-toggle="tab" class="btn-preview">Preview</a></li>
										</ul>
										<div class="tab-content reply">
											<div class="tab-pane active" id="write">
												<div class="form-group">
													<textarea name="details" id="details" class="code-area form-control"><?php echo $reply->details; ?></textarea>
												</div>
											</div>
											<div class="tab-pane" id="preview">
												<div class="form-group">
													<div class="preview-area form-control"></div>
												</div>
											</div>
										</div>
										<div class="well well-sm text-center text-muted dropfiles">
											Drag files or click here to add an attachment
											<div class="fallback">
												<input type="file" name="file" id="">
											</div>
										</div>
										<div class="attachments">
											<?php
												if ($reply):
													$attachments = $reply->attachments ? @unserialize($reply->attachments) : null;
													if ($attachments):
														foreach ($attachments as $attachment_id):
															$attachment = Attachments::get($attachment_id);
															if (! $attachment ) continue;
											?>
											<div class="attachment">
												<i class="fa fa-file"></i> <?php echo $attachment->name; ?> <span class="text-muted"> (<?php echo $attachment->mime; ?>) -</span> <span class="status"><a data-id="9" class="btn-remove" href="#">Remove</a></span>
												<input type="hidden" value="<?php echo $attachment->id; ?>" name="attachments[]">
											</div>
											<?php
														endforeach;
													endif;
												endif;
											?>
										</div>
										<div class="text-right">
											<span class="text-muted pull-left"><small><i class="fa fa-code"></i> Parsed as Markdown</small></span>
											<button type="submit" class="btn btn-success">Update reply</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>