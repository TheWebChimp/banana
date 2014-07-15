<?php
	$categories = ToDoTags::where('type', 'Category', '=', 0, 1000, 'id', 'ASC');

	include $site->baseDir('/lib/Parsedown.php');
	$parsedown = new Parsedown();
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true) ?>">Dashboard</a></li>
					<li class="active">ToDo</li>
				</ol>
				<div class="row">
					<div class="col-md-4">
						<h3>
							<button class="btn btn-xs btn-info btn-categories pull-right"><i class="fa fa-th-large"></i> Manage</button>
							<span>Categories</span>
						</h3>
						<div class="list-group categories manageable">
							<?php
								if ($categories):
									foreach ($categories as $category):
							?>
							<a class="list-group-item <?php echo ($category->slug == $cat ? 'active' : '') ?>" data-id="<?php echo $category->id; ?>" href="<?php $site->urlTo("/todo/{$category->slug}", true) ?>">
								<strong class="pull-right count"><?php echo $category->count(); ?></strong>
								<?php if ($category->id > 1): ?>
								<button class="btn btn-info btn-xs btn-delete pull-right"><i class="fa fa-times"></i></button>
								<?php endif; ?>
								<span><?php echo $category->name; ?></span>
							</a>
							<?php
									endforeach;
								endif;
							?>
						</div>
						<form action="<?php $site->urlTo('/todo/add-category', true); ?>" class="with-extras" method="post" data-submit="validate">
							<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
							<div class="form-group">
								<label for="name" class="control-label">Add a new category</label>
								<input type="text" name="name" id="name" class="form-control" data-validate="required">
							</div>
							<div class="extras hide">
								<div class="text-right">
									<button class="btn btn-success" type="submit">Create</button>
								</div>
							</div>
						</form>
					</div>
					<div class="col-md-8">
						<?php
							$category = ToDoTags::get($cat);
							$pending = $category->count("t.status = 'Pending'");
							$done = $category->count("t.status = 'Done'");
						?>
						<h3>Available tasks</h3>
						<?php if ( Users::currentUserCan('manage_options') ): ?>
						<form action="<?php $site->urlTo('/todo/new', true); ?>" method="post" data-submit="validate">
							<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
							<input type="hidden" name="category" value="<?php echo $category->id; ?>">
							<div class="form-group">
								<div class="input-group">
									<input type="text" name="name" placeholder="New Task" class="form-control" data-validate="required">
									<span class="input-group-btn">
										<button class="btn btn-default" type="submit" title="Add"><i class="fa fa-plus"></i></button>
										<a href="<?php $site->urlTo("/todo/new?category={$category->id}", true); ?>" class="btn btn-default" type="button" title="Advanced options"><i class="fa fa-cog"></i></a>
									</span>
								</div>
							</div>
						</form>
						<?php endif; ?>
						<div class="form-group">
							<div class="btn-group">
								<button class="btn btn-default btn-toggle active" data-show="All"></strong> Show all tasks</button>
								<button class="btn btn-default btn-toggle" data-show="Pending"><strong><?php echo $pending; ?></strong> Pending</button>
								<button class="btn btn-default btn-toggle" data-show="Done"><strong><?php echo $done; ?></strong> Done</button>
							</div>
						</div>
						<?php
							if ($category):
						?>
						<div class="list-group todos">
							<?php
								if ($todos):
									foreach ($todos as $todo):
							?>
							<div class="list-group-item todo" data-status="<?php echo $todo->status; ?>">
								<span class="fa fa-<?php echo ($todo->status == 'Done' ? 'check' : 'exclamation'); ?>-circle text-<?php echo ($todo->status == 'Done' ? 'success' : 'info'); ?>" title="<?php echo $todo->status; ?> Ticket"></span>
								<p class="list-group-item-text title clearfix">
									<?php if ( Users::currentUserCan('manage_options') ): ?>
									<span class="pull-right actions">
										<a href="<?php $site->urlTo("/todo/edit/{$todo->id}", true) ?>" class="btn btn-xs btn-primary" title="Edit"><i class="fa fa-pencil"></i></a>
										<a href="<?php $site->urlTo("/todo/delete/{$todo->id}", true) ?>" class="btn btn-xs btn-danger" title="Delete"><i class="fa fa-trash-o"></i></a>
									</span>
									<?php endif; ?>
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
							<?php
									endforeach;
								else:
							?>
							<div class="alert alert-info">There are no tasks yet. <a href="<?php $site->urlTo('/todo/new', true); ?>" class="alert-link">Click here</a> to create a new task.</div>
							<?php
								endif;
							?>
						</div>
						<?php
							endif;
						?>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>