<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">Edit Client <a href="<?php $site->urlTo('admin/clients/new', true); ?>" class="btn btn-primary btn-xs">Add Client</a></h1>
			<?php $this->partial( 'clients/editor', array('client' => $client) ); ?>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>