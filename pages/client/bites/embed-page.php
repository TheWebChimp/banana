<?php
	$creator = Users::get($bite->user_id);
	$description = $bite->getMeta('description');
?>
<?php $site->getParts(array('client/header_html')) ?>

	<section>
		<div class="media">
			<img class="media-object pull-left img-circle" src="<?php echo get_gravatar($creator->email, 42); ?>" alt="">
			<div class="media-body">
				<p class="media-heading">
					<?php echo $creator->nickname; ?><span class="text-muted"> / </span><a href="<?php $site->urlTo("/bites/{$bite->id}", true); ?>" target="_blank"><?php echo $bite->name; ?></a><br>
					<small class="text-muted">Created <?php echo relative_time( $bite->created ); ?></small>
				</p>
			</div>
		</div>
		<a href="#" class="btn-menu"><i class="fa fa-2x fa-bars"></i></a>
		<div class="form-group codemirror full-height" data-readonly="nocursor" data-mode="<?php echo ($bite ? $bite->syntax : ''); ?>">
			<textarea type="text" name="content" id="content" class="form-control"><?php echo ($bite ? $bite->content : ''); ?></textarea>
		</div>
	</section>

<?php $site->getParts(array('client/footer_html')) ?>