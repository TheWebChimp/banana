<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">New Client</h1>
			<?php $this->partial( 'clients/editor', array('client' => null) ); ?>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>