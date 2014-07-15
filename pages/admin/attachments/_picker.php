<?php
	$attachments = Attachments::all(1, 1000, 'desc');
?>
	<div id="modal-attachments" class="mfp-hide mfp-modal">
		<div class="filter clearfix">
			<ul class="nav nav-tabs">
				<li><a href="#upload" data-toggle="tab">Upload new</a></li>
				<li class="active"><a href="#library" data-toggle="tab">Attachment Library</a></li>
				<form action="<?php $site->urlTo('/admin/attachments', true); ?>" class="form-inline pull-right" method="post">
					<div class="form-group">
						<input type="text" name="search" id="search" class="form-control" placeholder="Search" value="<?php echo htmlspecialchars( get_item($_POST, 'search') ); ?>">
					</div>
				</form>
			</ul>
		</div>
		<br>
		<div class="tab-content">
			<div class="tab-pane" id="upload">
				<form id="form-attachment" action="<?php $site->urlTo('/admin/attachments/new', true); ?>" method="post" enctype="multipart/form-data" class="dropzone">
					<div class="fallback">
						<input type="file" name="file" id="file">
					</div>
				</form>
			</div>
			<div class="tab-pane active" id="library">
				<div class="media-list">
					<?php
						foreach ($attachments as $attachment):
							$image = $attachment->getImage();
							$ext = substr($attachment->attachment, strrpos($attachment->attachment, '.') + 1);
					?>
					<div class="media <?php echo $ext; ?>" data-type="<?php echo $ext; ?>" data-slug="<?php echo $attachment->slug; ?>" data-name="<?php echo $attachment->name; ?>" data-id="<?php echo $attachment->id; ?>">
						<a href="#">
							<?php if ($image): ?>

							<img data-original="<?php echo $image; ?>" width="150" height="150" alt="">

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