<?php
	$userobj = $user ? Users::get($user) : null;
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true) ?>">Dashboard</a></li>
					<li class="active">Bites</li>
				</ol>
				<div class="cols left-fixed">
					<div class="col-left">
						<div class="alert alert-info">Categories and other goodies coming soon!</div>
					</div>
					<div class="col-right">
						<div class="form-group">
							<?php Pagination::paginate($total, 5, array('user' => $user)); ?>
							<a href="<?php $site->urlTo('/bites/new', true); ?>" class="btn btn-success"><i class="fa fa-code"></i> Create new bite</a>
						</div>
						<?php if ($userobj): ?>
							<div class="alert alert-info">Showing <?php echo $total; ?> bites from <?php echo $userobj->nickname; ?>, <a class="alert-link" href="<?php $site->urlTo('/bites', true); ?>">click here to reset filter.</a></div>
						<?php endif; ?>
						<?php
							foreach ($bites as $bite):
								$creator = Users::get($bite->user_id);
						?>
						<div class="bite">
							<div class="media">
								<img class="media-object pull-left img-circle" src="<?php echo get_gravatar($creator->email, 42); ?>" alt="">
								<div class="media-body">
									<p class="media-heading">
										<a href="<?php $site->urlTo("/bites/?user={$bite->user_id}", true); ?>"><?php echo $creator->nickname; ?></a><span class="text-muted"> / </span><a href="<?php $site->urlTo("/bites/{$bite->id}", true); ?>"><?php echo $bite->name; ?></a><br>
										<small class="text-muted">Created <?php echo relative_time( $bite->created ); ?></small>
									</p>
								</div>
							</div>
						</div>
						<?php
							endforeach;
						?>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>