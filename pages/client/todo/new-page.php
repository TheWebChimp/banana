<?php
	//
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true) ?>">Dashboard</a></li>
					<li><a href="<?php $site->urlTo('/todo', true) ?>">ToDo</a></li>
					<li class="active">New Task</li>
				</ol>
				<?php $this->partial('/todo/editor', array('todo' => null)); ?>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>