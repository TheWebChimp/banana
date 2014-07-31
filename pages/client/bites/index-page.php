<?php
	//
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
						<a href="<?php $site->urlTo('/bites/new', true); ?>" class="btn btn-success"><i class="fa fa-code"></i> Create new bite</a>
					</div>
					<div class="col-right">
						<?php
							foreach ($bites as $bite):
								$creator = Users::get($bite->user_id);
						?>
						<div class="bite">
							<div class="media">
								<img class="media-object pull-left img-circle" src="<?php echo get_gravatar($creator->email, 42); ?>" alt="">
								<div class="media-body">
									<p class="media-heading">
										<?php echo $creator->nickname; ?><span class="text-muted"> / </span><a href="<?php $site->urlTo("/bites/{$bite->id}", true); ?>"><?php echo $bite->name; ?></a><br>
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