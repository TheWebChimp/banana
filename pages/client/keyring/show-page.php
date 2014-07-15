<?php
	include $site->baseDir('/lib/Parsedown.php');
	$parsedown = new Parsedown();
	$manager = Users::currentUserCan('manage_options');
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true) ?>">Dashboard</a></li>
					<li><a href="<?php $site->urlTo('/keyring', true) ?>">Keyring</a></li>
					<li class="active"><?php echo $keyring->name; ?></li>
				</ol>
				<div class="row">
					<div class="col-md-4">
						<h3>Add new key</h3>
						<?php if ($manager): ?>
						<form action="<?php $site->urlTo('/keyring/add-key/', true); ?>" class="well form-keyring form-markdown" method="post" data-submit="validate">
							<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
							<input type="hidden" name="keyring_id" value="<?php echo $keyring->id; ?>">
							<div class="form-group">
								<label for="name" class="control-label">Name<span class="text-danger">*</span></label>
								<input type="text" name="name" id="name" class="form-control" data-validate="required">
							</div>
							<ul class="nav nav-pills">
								<li class="active"><a href="#write" data-toggle="tab">Write</a></li>
								<li><a href="#preview" data-toggle="tab" class="btn-preview">Preview</a></li>
							</ul>
							<div class="tab-content markdown">
								<div class="tab-pane active" id="write">
									<div class="form-group">
										<textarea name="description" id="description" class="code-area form-control" data-validate="required"></textarea>
									</div>
								</div>
								<div class="tab-pane" id="preview">
									<div class="form-group">
										<div class="preview-area form-control"></div>
									</div>
								</div>
							</div>
							<div class="text-right">
								<span class="text-muted pull-left"><small><i class="fa fa-code"></i> Parsed as Markdown</small></span>
								<button type="submit" class="btn btn-success">Create key</button>
							</div>
						</form>
						<?php else: ?>
						<div class="well text-center">You don't have enough permissions</div>
						<?php endif; ?>
					</div>
					<div class="col-md-8">
						<h3><?php echo $keyring->name; ?></h3>
						<div class="list-group keyrings">
							<?php
								if ($keys):
									$pass = $site->hashPassword('KEYRING_8:|NwvzafQKVq;pl1P3&');
									$cipher = new Cipher($pass);
									foreach ($keys as $key):
										$description = $manager ? $cipher->decrypt($key->description) : '<span class="text-muted">You don\'t have enough permissions to see this data</span>';
							?>
							<div class="list-group-item keyring">
								<span class="fa fa-key icon"></span>
								<span class="list-group-item-heading"><strong><?php echo $key->name; ?></strong></span>
								<span class="list-group-item-text"><?php echo $parsedown->text($description); ?></span>
							</div>
							<?php
									endforeach;
								else:
							?>
							<div class="alert alert-info">There are no keys in this keyring yet - <a href="<?php $site->urlTo('/keyring', true); ?>" class="alert-link">Go back to list</a></div>
							<?php
								endif;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>