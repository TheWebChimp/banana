<?php $site->getParts(array('client/header_html', 'client/header')) ?>

		<section>
			<div class="container">
				<br>
				<div class="text-center">
					<p><img src="<?php $site->img('banana-404.png') ?>" alt=""></p>
					<h1>Oh noes! <small>The page you were looking for could not be found</small></h1>
					<p>It may be due to a broken link (or an evil troll, who knows!)</p>
					<p>Please try the <a href="<?php $site->urlTo('/', true); ?>">home page</a></p>
					<p class="text-muted"><small>Just for the record, this was a 404 error</small></p>
				</div>
			</div>
		</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>