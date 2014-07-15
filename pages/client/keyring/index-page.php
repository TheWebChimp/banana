<?php
	include $site->baseDir('/lib/Parsedown.php');
	$parsedown = new Parsedown();
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true) ?>">Dashboard</a></li>
					<li class="active">Keyring</li>
				</ol>
				<div class="row">
					<div class="col-md-4">
						<h3>Create new keyring</h3>
						<form action="<?php $site->urlTo('/keyring/new', true); ?>" class="well form-keyring form-markdown" method="post" data-submit="validate">
							<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
							<div class="form-group">
								<label for="name" class="control-label">Name<span class="text-danger">*</span></label>
								<input type="text" name="name" id="name" class="form-control" data-validate="required">
							</div>
							<div class="form-group">
								<label for="type" class="control-label">Type<span class="text-danger">*</span></label>
								<select name="type" id="type" class="form-control" data-validate="required">
									<option value=""></option>
									<option value="Public">Public</option>
									<option value="Private">Private</option>
								</select>
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
								<button type="submit" class="btn btn-success">Create keyring</button>
							</div>
						</form>
					</div>
					<div class="col-md-8">
						<h3>Available keyrings</h3>
						<div class="list-group keyrings">
							<?php
								if ($keyrings):
									foreach ($keyrings as $keyring):
							?>
							<div class="list-group-item keyring">
								<div class="actions pull-right">
									<a href="<?php $site->urlTo("/keyring/delete/{$keyring->id}", true); ?>" class="btn btn-xs btn-danger"><i class="fa fa-trash-o"></i></a>
									<a href="<?php $site->urlTo("/keyring/{$keyring->id}", true); ?>" class="btn btn-xs btn-primary"><i class="fa fa-folder-open"></i></a>
								</div>
								<span class="fa fa-key icon"></span>
								<span class="list-group-item-heading"><strong><?php echo $keyring->name; ?></strong></span>
								<span class="list-group-item-text"><?php echo $parsedown->text($keyring->description); ?></span>
							</div>
							<?php
									endforeach;
								endif;
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>