<?php
	$err = get_item($_GET, 'err');
	$messages = array(
		'e100' => 'The user/password combination is not valid',
		'e200' => 'You don\'t have enough permissions'
	);
?>
<?php $site->getParts(array('client/header_html')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<div class="row">
					<div class="col-md-4 col-md-offset-4">
						<div class="login-form">
							<p class="text-center">
								<img src="<?php $site->img('banana-128x128.png'); ?>" alt="">
							</p>
							<?php if ($err): ?>
							<br>
								<div class="text-danger text-center"><?php echo $messages["e{$err}"]; ?></div>
							<?php endif; ?>
							<form id="form-login" action="" method="post">
								<label for="user" class="control-label sr-only">User</label>
								<input type="text" name="user" id="user" class="form-control" placeholder="User" data-validate="required">
								<label for="password" class="control-label sr-only">Password</label>
								<input type="password" name="password" id="password" class="form-control" placeholder="Password" data-validate="required">
								<div class="checkbox">
									<label for="remember"><input type="checkbox" name="remember" id="remember">Remember me</label>
								</div>
								<div>
									<button class="btn btn-primary btn-block btn-submit">Continue</button>
								</div>
							</form>
							<p class="text-center"><a href="<?php $site->urlTo('/login', true); ?>">Looking for the sign-in page?</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer_html')) ?>