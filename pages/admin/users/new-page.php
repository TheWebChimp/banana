<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">New User</h1>
			<?php $this->partial( 'users/editor', array('user' => $user) ); ?>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>