<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">Edit User <a href="<?php $site->urlTo('admin/users/new', true); ?>" class="btn btn-primary btn-xs">Add User</a></h1>
			<?php $this->partial( 'users/editor', array('user' => $user) ); ?>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>