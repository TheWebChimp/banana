<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">Delete Attachment</h1>
			<h4>Are you sure you want to delete <?php echo $attachment->name; ?>?</h4>
			<span class="help-block">Warning: This action can not be undone!</span>
			<form action="" method="post">
				<input type="hidden" name="token" value="delete">
				<a href="<?php $site->urlTo('/admin/attachments', true); ?>" class="btn btn-primary">No, take me back</a>
				<button class="btn btn-link">Yes, delete this attachment</button>
			</form>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>