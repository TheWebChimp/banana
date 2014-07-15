<?php
	$msg = get_item($_GET, 'msg');
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">

				<?php if ($msg == 200): ?>
				<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>You profile has been updated</div>
				<?php endif; ?>

				<form action="" method="post" data-submit="validate">
					<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group has-feedback">
								<label for="first_name" class="control-label">First Name</label>
								<input type="text" name="first_name" id="first_name" class="form-control" value="<?php echo $site->user->first_name; ?>" data-validate="required">
								<span class="fa fa-asterisk form-control-feedback"></span>
							</div>
							<div class="form-group">
								<label for="last_name" class="control-label">Last Name</label>
								<input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo $site->user->last_name; ?>">
							</div>
							<div class="form-group has-feedback">
								<label for="email" class="control-label">Email</label>
								<input type="text" name="email" id="email" class="form-control" value="<?php echo $site->user->email; ?>" data-validate="email">
								<span class="fa fa-asterisk form-control-feedback"></span>
							</div>
							<div class="form-group has-feedback">
								<label for="nickname" class="control-label">Nickname</label>
								<input type="text" name="nickname" id="nickname" class="form-control" value="<?php echo $site->user->nickname; ?>" data-validate="required">
								<span class="fa fa-asterisk form-control-feedback"></span>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="password" class="control-label">New password</label>
								<input type="password" name="password" autocomplete="off" id="password" class="form-control">
							</div>
							<div class="form-group">
								<label for="confirm" class="control-label">Confirm new password</label>
								<input type="password" name="confirm" autocomplete="off" id="confirm" class="form-control" data-validate="confirm" data-param="input[name=password]">
								<span class="help-block">Tip: If you don't want to change your password leave both fields empty</span>
							</div>
						</div>
					</div>
					<p class="text-muted"><i class="fa fa-asterisk"></i> Denotes a required field</p>
					<div class="form-group text-right">
						<button class="btn btn-primary" type="submit">Update profile</button>
					</div>
				</form>

			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>