<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">New Project</h1>
			<?php $this->partial( 'projects/editor', array('project' => null) ); ?>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>