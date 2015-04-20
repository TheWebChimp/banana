<?php
	$dbh = $site->getDatabase();
	$conditions = '1';
	$search_str = $search ? $dbh->quote("%{$search}%") : '';
	$conditions .= $search_str ? " AND subject LIKE {$search_str}" : '';
	$conditions .= is_numeric($client_id) ? " AND client_id = {$client_id}" : '';
	$conditions .= is_numeric($project_id) ? " AND project_id = {$project_id}" : '';
	$open = Tickets::count("{$conditions} AND status = 'Open'");
	$closed = Tickets::count("{$conditions} AND status = 'Closed'");
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
						<h3>Search tickets</h3>

						<form class="form-ticket well" action="<?php $site->urlTo('/tickets', true); ?>" method="post" data-submit="validate">
							<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
							<input type="hidden" name="page" value="<?php echo $page; ?>">
							<input type="hidden" name="show" value="<?php echo $show; ?>">
							<input type="hidden" name="sort" value="<?php echo $sort; ?>">
							<input type="hidden" name="filter" value="<?php echo $filter; ?>">
							<div class="form-group">
								<label for="search" class="control-label">Search</label>
								<input type="text" name="search" id="search" class="form-control" value="<?php echo htmlspecialchars($search) ?>">
							</div>
							<div class="form-group">
								<label for="client_id" class="control-label">Client</label>
								<select name="client_id" id="client_id" class="form-control">
									<option value="">None</option>
									<?php
										$clients = Users::currentUserCan('manage_options') ? Clients::all() : $site->user->clients;
										if ($clients):
											foreach ($clients as $client):
									?>
									<option <?php option_selected($client_id ? $client_id : 0, $client->id) ?> value="<?php echo $client->id; ?>"><?php echo $client->name; ?></option>
									<?php
											endforeach;
										endif;
									?>
								</select>
							</div>
							<div class="form-group">
								<label for="project_id" class="control-label">Project</label>
								<select name="project_id" id="project_id" class="form-control">
									<option value="">None</option>
									<?php
										$projects = Users::currentUserCan('manage_options') ? Projects::all() : $site->user->projects;
										if ($projects):
											foreach ($projects as $project):
												$client = $project->clients ? $project->clients[0] : null;
									?>
									<option <?php option_selected($project_id ? $project_id : 0, $project->id) ?> value="<?php echo $project->id; ?>"><?php echo "{$client->name} &mdash; {$project->name}"; ?></option>
									<?php
											endforeach;
										endif;
									?>
								</select>
							</div>
							<div class="text-right">
								<button type="submit" class="btn btn-primary">Search tickets</button>
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
							<input type="hidden" name="search" value="<?php echo $search ?>">
							<input type="hidden" name="client_id" value="<?php echo $client_id ?>">
							<input type="hidden" name="project_id" value="<?php echo $project_id ?>">
							<div class="form-group">
								<a href="<?php $site->urlTo('/tickets/new', true); ?>" class="btn btn-success pull-right">Create new ticket</a>
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
						<?php if ($search): ?>
							<div class="alert alert-info">Showing tickets for <strong>&quot;<?php echo htmlspecialchars($search) ?>&quot;</strong> &mdash; <a href="<?php $site->urlTo('/tickets', true) ?>">Click here to reset filters</a></div>
						<?php elseif ($client_id || $project_id): ?>
							<div class="alert alert-info">Filtering by client and/or project &mdash; <a href="<?php $site->urlTo('/tickets', true) ?>">Click here to reset filters</a></div>
						<?php endif; ?>
						<!--  -->
						<div class="list-group">
							<?php
								if ($tickets):
									foreach ($tickets as $ticket):

										$ticket_user = Users::get($ticket->user_id);
										$reply_count = $ticket->replies;

										$ticket_client = Clients::get($ticket->client_id);
										$ticket_project = Projects::get($ticket->project_id);
							?>
							<a href="<?php $site->urlTo("/tickets/{$ticket->id}", true); ?>" class="list-group-item">
								<span class="fa fa-<?php echo ($ticket->status == 'Open' ? 'check' : 'times'); ?>-circle text-<?php echo ($ticket->status == 'Open' ? 'success' : 'danger'); ?>" title="<?php echo $ticket->status; ?> Ticket"></span>
								<p class="list-group-item-text">
									<span class="text-muted pull-right">#<?php echo $ticket->id; ?></span>
									<strong><?php echo htmlspecialchars($ticket->subject); ?></strong>
									<small class="text-muted">
										<?php
											if($ticket_project){
												echo $ticket_project->name;
											}

											if($ticket_project && $ticket_client){
												echo " &mdash; ";
											}

											if($ticket_client){
												echo $ticket_client->name;
											}
										?>
									</small>
								</p>
								<p class="list-group-item-text">
									<small><span class="text-muted">
										Opened by</span> <?php echo $ticket_user? $ticket_user->nickname : '-'; ?> <span class="text-muted">on <?php echo date('M j', strtotime($ticket->created)) ?>. <i class="fa fa-comments"></i></span> <?php echo $reply_count; ?> <?php echo ($reply_count == 1 ? 'comment' : 'comments') ?></small>
									<?php if ($ticket->due != '0000-00-00 00:00:00'): ?>
										<br><small><span class="text-muted">Due by</span> <strong><?php echo date('d/m/Y', strtotime($ticket->due)); ?></strong></small>
									<?php endif ?>
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