<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">Edit Project <a href="<?php $site->urlTo('admin/projects/new', true); ?>" class="btn btn-primary btn-xs">Add Project</a></h1>
			<?php $this->partial( 'projects/editor', array('project' => $project) ); ?>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>