<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">All Attachments <a href="<?php $site->urlTo('/admin/attachments/new', true); ?>" class="btn btn-xs btn-primary">New Attachment</a></h1>
			<div class="filter clearfix">
				<form action="<?php $site->urlTo('/admin/attachments', true); ?>" class="form-inline" method="post">
					<div class="form-group">
						<div class="input-group">
							<input type="text" name="search" id="search" class="form-control" placeholder="Search" value="<?php echo htmlspecialchars( get_item($_POST, 'search') ); ?>">
							<span class="input-group-btn">
								<button class="btn btn-default"><span class="fa fa-search"></span></button>
							</span>
						</div>
					</div>
					<div class="form-group">
						<select name="" id="" class="form-control" disabled="disabled">
							<option value="">Filters not available</option>
						</select>
					</div>
				</form>
				<div class="pages"><?php Pagination::paginate($total); ?></div>
			</div>
			<div class="cols right-fixed">
				<div class="col-right">
					<div class="panel panel-default">
						<div class="panel-heading">Attachment Properties</div>
						<div class="panel-body">
							<form id="form-attachment" action="" data-action="<?php $site->urlTo("/admin/attachments/edit/", true); ?>" method="post">
								<input type="hidden" name="id" value="">
								<div class="form-group">
									<label for="name" class="control-label">Name</label>
									<input type="text" name="name" id="name" class="form-control">
								</div>
								<div class="form-group">
									<label for="name" class="control-label">Description</label>
									<textarea name="description" id="description" class="form-control"></textarea>
								</div>
								<div class="text-right">
									<a data-href="<?php $site->urlTo('/admin/attachments/delete/', true); ?>" href="" class="btn btn-link btn-delete">Delete Attachment</a>
									<button class="btn btn-primary btn-submit">Update</button>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col-left">
					<div class="media-list">
						<?php
							foreach ($attachments as $attachment):
								$image = $attachment->getImage();
								$ext = substr($attachment->attachment, strrpos($attachment->attachment, '.') + 1);
						?>
						<div class="media <?php echo $ext; ?>" data-id="<?php echo $attachment->id; ?>">
							<a href="#">
								<?php if ($image): ?>

								<img class="lazyload" data-original="<?php echo $image; ?>" width="150" height="150" alt="">

								<?php else: ?>

								<div class="details">
									<span class="name"><?php echo $attachment->name; ?></span>
									<span class="ext"><?php echo $ext; ?></span>
								</div>

								<?php endif; ?>
							</a>
						</div>
						<?php
							endforeach;
						?>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>