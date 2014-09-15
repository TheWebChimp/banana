<?php
	$starter = Users::get($ticket->user_id);
	$reply_count = $ticket->replies;

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
					<li class="active">View ticket</li>
				</ol>
				<div class="ticket-wrap">
					<a href="<?php $site->urlTo('/tickets', true); ?>" class="btn btn-link btn-back"><i class="fa fa-arrow-left"></i></a>
					<div class="ticket-thread">
						<h2><?php echo $ticket->subject; ?> <span class="text-muted">#<?php echo $ticket->id; ?></span></h2>
						<p>
							<?php if ($ticket->status == 'Open'): ?>
								<span class="label label-success"><i class="fa fa-check-circle"></i> Open</span>
							<?php else: ?>
								<span class="label label-danger"><i class="fa fa-times-circle"></i> Closed</span>
							<?php endif; ?>
							<strong><?php echo $starter->nickname; ?></strong>
							<span class="text-muted">opened this ticket on <?php echo date('M j', strtotime($ticket->created)) ?> · <i class="fa fa-comments"></i> <?php echo $reply_count; ?> <?php echo ($reply_count == 1 ? 'comment' : 'comments') ?></span>
						</p>
						<?php if ( Users::currentUserCan('manage_options') ): ?>
							<p>
								<?php if ($ticket->status == 'Open'): ?>
									<form action="<?php $site->urlTo("/tickets/close/{$ticket->id}", true); ?>" method="post">
										<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
										<button type="submit" class="btn btn-success btn-sm">Close Ticket</button>
										<a href="<?php $site->urlTo("/tickets/edit/{$ticket->id}", true); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Edit Ticket</a>
										<a href="<?php $site->urlTo("/tickets/delete/{$ticket->id}", true); ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> Delete Ticket</a>
									</form>
								<?php else: ?>
									<form action="<?php $site->urlTo("/tickets/open/{$ticket->id}", true); ?>" method="post">
										<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
										<button type="submit" class="btn btn-success btn-sm">Reopen Ticket</button>
										<a href="<?php $site->urlTo("/tickets/edit/{$ticket->id}", true); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Edit Ticket</a>
										<a href="<?php $site->urlTo("/tickets/delete/{$ticket->id}", true); ?>" class="btn btn-danger btn-sm"><i class="fa fa-trash-o"></i> Delete Ticket</a>
									</form>
								<?php endif; ?>
							</p>
						<?php elseif ( Users::getCurrentUserId() == $ticket->user_id ): ?>
							<a href="<?php $site->urlTo("/tickets/edit/{$ticket->id}", true); ?>" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i> Edit Ticket</a>
						<?php endif; ?>
						<hr>
						<div class="ticket-body">
							<img src="<?php echo get_gravatar($starter->email, 50); ?>" alt="" class="avatar">
							<div class="panel panel-default">
								<div class="panel-heading"><strong><?php echo $starter->nickname; ?></strong> <span class="text-muted">commented on <?php echo date('M j', strtotime($ticket->created)) ?></span></div>
								<div class="panel-body">
									<div class="content"><?php echo $parsedown->text($ticket->details); ?></div>
									<?php
										$attachments = $ticket->attachments ? @unserialize($ticket->attachments) : null;
										if ($attachments):
											foreach ($attachments as $attachment_id):
												$attachment = Attachments::get($attachment_id);
												if (! $attachment ) continue;
									?>
									<div class="attachment">
										<i class="fa fa-file"></i>
										<a href="<?php $attachment->getUrl(true); ?>" target="_blank"><?php echo $attachment->name; ?></a>
										<span class="text-muted"> (<?php echo $attachment->mime; ?>)</span>
									</div>
									<?php
											endforeach;
										endif;
									?>
								</div>
							</div>
						</div>
						<!--  -->
						<?php
							$replies = $ticket->replies();
							if ($replies):
								foreach ($replies as $reply):
									$replier = Users::get($reply->user_id);
									$unreply = '';
									if ( $reply->user_id == Users::getCurrentUserId() || Users::currentUserCan('manage_options') ) {
										$unreply = '<a href="'.$site->urlTo("/tickets/unreply/{$reply->id}").'" class="btn btn-danger btn-xs pull-right"><i class="fa fa-trash-o"></i></a>';
									}
						?>
							<div class="ticket-body">
								<img class="avatar" src="<?php echo get_gravatar($replier->email, 50); ?>" alt="">
								<div class="panel panel-default">
									<div class="panel-heading"><?php echo $unreply; ?><strong><?php echo $replier->nickname; ?></strong> <span class="text-muted">commented on <?php echo date('M j', strtotime($reply->created)) ?></span></div>
									<div class="panel-body">
										<div class="content"><?php echo $parsedown->text($reply->details); ?></div>
										<?php
											$attachments = $reply->attachments ? @unserialize($reply->attachments) : null;
											if ($attachments):
												foreach ($attachments as $attachment_id):
													$attachment = Attachments::get($attachment_id);
													if (! $attachment ) continue;
										?>
										<div class="attachment">
											<i class="fa fa-file"></i>
											<a href="<?php $attachment->getUrl(true); ?>" target="_blank"><?php echo $attachment->name; ?></a>
											<span class="text-muted"> (<?php echo $attachment->mime; ?>)</span>
										</div>
										<?php
												endforeach;
											endif;
										?>
									</div>
								</div>
							</div>
						<?php
								endforeach;
							endif;
						?>
						<!--  -->
						<div class="ticket-body">
							<img class="avatar" src="<?php echo get_gravatar($site->user->email, 50); ?>" alt="">
							<div class="panel panel-default">
								<div class="panel-heading"><strong>Write a reply</strong></div>
								<div class="panel-body">
									<form class="form-ticket" id="form-reply" action="<?php $site->urlTo("/tickets/reply/{$ticket->id}", true); ?>" method="post">
										<input type="hidden" name="ticket_id" value="<?php echo $ticket->id; ?>">
										<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
										<ul class="nav nav-pills">
											<li class="active"><a href="#write" data-toggle="tab">Write</a></li>
											<li><a href="#preview" data-toggle="tab" class="btn-preview">Preview</a></li>
										</ul>
										<div class="tab-content reply">
											<div class="tab-pane active" id="write">
												<div class="form-group">
													<textarea name="details" id="details" class="code-area form-control"></textarea>
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
											<!--  -->
										</div>
										<div class="text-right">
											<span class="text-muted pull-left"><small><i class="fa fa-code"></i> Parsed as Markdown</small></span>
											<button type="submit" class="btn btn-success">Submit reply</button>
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