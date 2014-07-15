<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">New Attachment</h1>
			<form id="form-attachment" action="<?php $site->urlTo('/admin/attachments/new', true); ?>" method="post" enctype="multipart/form-data" class="dropzone">
				<div class="fallback">
					<input type="file" name="file" id="file">
				</div>
			</form>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>