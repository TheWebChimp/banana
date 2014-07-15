<?php
	$open = Tickets::count("status = 'Open'");
	$closed = Tickets::count("status = 'Closed'");
	$labels = TicketTags::where('type', '=', 'Label');
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true) ?>">Dashboard</a></li>
					<li class="active">Tickets</li>
				</ol>
				<div class="row">
					<div class="col-md-4">
						<h3>Submit a new ticket</h3>

						<form class="form-ticket well" action="<?php $site->urlTo('/tickets/submit', true); ?>" method="post" data-submit="validate">
							<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
							<div class="form-group">
								<label for="subject" class="control-label">Subject</label>
								<input type="text" name="subject" id="subject" class="form-control" data-validate="required">
							</div>
							<div class="form-group">
								<label for="project_id" class="control-label">Project</label>
								<select name="project_id" id="project_id" class="form-control">
									<option value="">None</option>
									<?php
										$projects = Users::currentUserCan('manage_options') ? Projects::all() : $site->user->projects;
										if ($projects):
											foreach ($projects as $project):
									?>
									<option value="<?php echo $project->id; ?>"><?php echo $project->name; ?></option>
									<?php
											endforeach;
										endif;
									?>
								</select>
							</div>
							<ul class="nav nav-pills">
								<li class="active"><a href="#write" data-toggle="tab">Write</a></li>
								<li><a href="#preview" data-toggle="tab" class="btn-preview">Preview</a></li>
							</ul>
							<div class="tab-content reply">
								<div class="tab-pane active" id="write">
									<div class="form-group">
										<textarea name="details" id="details" class="code-area form-control" data-validate="required"></textarea>
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
								<button type="submit" class="btn btn-success">Submit ticket</button>
							</div>
						</form>
						<?php
							if ( Users::currentUserCan('manage_options') && 0 ):
						?>
						<h3>Manage tickets</h3>
						<div class="panel panel-default">
							<div class="panel-heading">Labels</div>
							<div class="list-group labels">
								<?php
									if ($labels):
										foreach ($labels as $label):
								?>
								<a href="#" class="list-group-item"><strong class="pull-right"><?php echo $label->count(); ?></strong><span class="color" style="background: <?php echo $label->description; ?>"></span> <?php echo $label->name; ?></a>
								<?php
										endforeach;
									else:
								?>
								<div class="list-group-item">There are no labels yet</div>
								<?php
									endif;
								?>
							</div>
							<div class="panel-body">
								<form action="<?php $site->urlTo('/tickets/add-label', true); ?>" method="post" id="add-label-form">
									<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
									<div class="form-group">
										<label for="label" class="control-label">Add a new label</label>
										<input type="text" name="label" id="label" class="form-control" data-validate="required">
									</div>
									<div class="extras hide">
										<div class="form-group">
											<input type="text" name="color" id="color" class="form-control" value="#CC0000" data-validate="required">
										</div>
										<div class="text-right">
											<button class="btn btn-success" type="submit">Create</button>
										</div>
									</div>
								</form>
							</div>
						</div>
						<?php
							endif;
						?>
					</div>
					<div class="col-md-8">
						<?php if ( Users::currentUserCan('manage_options') ): ?>
						<h3>Available tickets</h3>
						<?php else: ?>
						<h3>Your tickets</h3>
						<?php endif; ?>
						<!--  -->
						<form action="" method="get">
							<input type="hidden" name="page" value="<?php echo $page; ?>">
							<input type="hidden" name="show" value="<?php echo $show; ?>">
							<input type="hidden" name="sort" value="<?php echo $sort; ?>">
							<div class="form-group">
								<div class="btn-group" data-toggle="buttons">
									<label for="filter-opened" class="btn btn-default <?php echo ($filter == 'open' ? 'active' : ''); ?>"><input <?php option_selected($filter, 'open', 'checked'); ?> type="radio" name="filter" value="open" id="filter-open"> <strong><?php echo $open; ?></strong> Open</label>
									<label for="filter-closed" class="btn btn-default <?php echo ($filter == 'closed' ? 'active' : ''); ?>"><input <?php option_selected($filter, 'closed', 'checked'); ?> type="radio" name="filter" value="closed" id="filter-closed"> <strong><?php echo $closed; ?></strong> Closed</label>
								</div>
								<div class="btn-group">
									<?php
										$vars = array(
											'newest' => 'Newest',
											'oldest' => 'Oldest',
											'recently-updated' => 'Recently updated',
											'least-updated' => 'Least recently updated',
											'most-replies' => 'Most commented',
											'least-replies' => 'Least commented'
										);
									?>
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
										Sort: <strong><span class="sort"><?php echo $vars[$sort]; ?></span></strong> <span class="caret"></span>
									</button>
									<ul class="dropdown-menu" role="menu">
										<?php
											foreach ($vars as $key => $value):
										?>
										<li><a href="#" data-update="input[name=sort]" data-value="<?php echo $key; ?>"><?php echo $value; ?></a></li>
										<?php
											endforeach;
										?>
									</ul>
								</div>
							</div>
						</form>
						<!--  -->
						<div class="list-group">
							<?php
								if ($tickets):
									foreach ($tickets as $ticket):
										$site->user = Users::get($ticket->user_id);
										$reply_count = $ticket->replies;
							?>
							<a href="<?php $site->urlTo("/tickets/{$ticket->id}", true); ?>" class="list-group-item">
								<span class="fa fa-<?php echo ($ticket->status == 'Open' ? 'check' : 'times'); ?>-circle text-<?php echo ($ticket->status == 'Open' ? 'success' : 'danger'); ?>" title="<?php echo $ticket->status; ?> Ticket"></span>
								<p class="list-group-item-text">
									<span class="text-muted pull-right">#<?php echo $ticket->id; ?></span>
									<strong><?php echo htmlspecialchars($ticket->subject); ?></strong>
								</p>
								<p class="list-group-item-text">
									<small><span class="text-muted">Opened by</span> <?php echo $site->user->nickname; ?> <span class="text-muted">on <?php echo date('M j', strtotime($ticket->created)) ?>. <i class="fa fa-comments"></i></span> <?php echo $reply_count; ?> <?php echo ($reply_count == 1 ? 'comment' : 'comments') ?></small>
								</p>
							</a>
							<?php
									endforeach;
								else:
							?>
							<div class="list-group-item list-group-item-success">There are no tickets yet</div>
							<?php
								endif;
							?>
						</div>
						<?php Pagination::paginate($total, 5, array('sort' => $sort)); ?>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>