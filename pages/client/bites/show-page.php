<?php
	$creator = Users::get($bite->user_id);
	$description = $bite->getMeta('description');
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true) ?>">Dashboard</a></li>
					<li><a href="<?php $site->urlTo('/bites', true) ?>">Bites</a></li>
					<li class="active">View bite</li>
				</ol>
				<div class="media">
					<img class="media-object pull-left img-circle" src="<?php echo get_gravatar($creator->email, 42); ?>" alt="">
					<div class="media-body">
						<div class="media-actions pull-right">
							<a href="#" class="btn btn-primary btn-copy"><i class="fa fa-clipboard"></i> Copy</a>
							<a href="<?php $site->urlTo('/bites/new', true); ?>" class="btn btn-success"><i class="fa fa-code"></i> New bite</a>
							<?php if ($creator->id == $site->user->id): ?>
							<a href="<?php $site->urlTo("/bites/edit/{$bite->id}", true); ?>" class="btn btn-success"><i class="fa fa-pencil"></i> Edit Bite</a>
							<a href="<?php $site->urlTo("/bites/edit/{$bite->id}", true); ?>" class="btn btn-danger"><i class="fa fa-trash-o"></i> Delete</a>
							<?php endif; ?>
						</div>
						<p class="media-heading">
							<?php echo $creator->nickname; ?><span class="text-muted"> / </span><a href="<?php $site->urlTo("/bites/{$bite->id}", true); ?>"><?php echo $bite->name; ?></a><br>
							<small class="text-muted">Created <?php echo relative_time( $bite->created ); ?></small>
						</p>
					</div>
				</div>
				<hr>
				<p><?php echo $description; ?></p>
				<div class="cols right-fixed">
					<div class="col-right">
						<div class="form-group">
							<label for="embed" class="control-label">Embed URL</label>
							<input type="text" name="embed" id="embed" class="form-control" value="&lt;iframe width=&quot;600&quot; height=&quot;480&quot; src=&quot;<?php echo preg_replace('/(http|https):/', '', $site->urlTo("/bites/embed/{$bite->id}")); ?>&quot; frameborder=&quot;0&quot;&gt;&lt;/iframe&gt;">
						</div>
					</div>
					<div class="col-left">
						<div class="form-group codemirror full-height" data-readonly="true" data-mode="<?php echo ($bite ? $bite->syntax : ''); ?>">
							<textarea type="text" name="content" id="content" class="form-control"><?php echo ($bite ? $bite->content : ''); ?></textarea>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>