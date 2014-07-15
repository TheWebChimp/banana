<?php
	if ( Users::getCurrentUserId() ) {
		$site->redirectTo( $site->urlTo('/dashboard') );
		exit;
	}
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

		<section>
			<div class="container">
				<!--  -->
			</div>
		</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>