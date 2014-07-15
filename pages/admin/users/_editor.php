<?php
	$editing = $user && $user->id;
?>
<form action="" method="post" id="form-user">
	<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
	<div class="cols right-fixed">
		<div class="col-right">
			<div class="panel panel-default">
				<div class="panel-heading">Properties</div>
				<div class="panel-body">
					<div class="form-group">
						<label for="role" class="control-label">Role<span class="text-danger">*</span></label>
						<select name="role" id="role" class="form-control" data-validate="required">
							<option value=""></option>
							<option <?php option_selected($user ? $user->role : '', 'Administrator'); ?> value="Administrator">Administrator</option>
							<option <?php option_selected($user ? $user->role : '', 'User'); ?> value="User">User</option>
						</select>
					</div>
					<div class="form-group">
						<label for="status" class="control-label">Status<span class="text-danger">*</span></label>
						<select name="status" id="status" class="form-control" data-validate="required">
							<option value=""></option>
							<option <?php option_selected($user ? $user->status : '', 'Active'); ?> value="Active">Active</option>
							<option <?php option_selected($user ? $user->status : '', 'Inactive'); ?> value="Inactive">Inactive</option>
						</select>
					</div>
					<div class="text-right">
						<button class="btn btn-primary btn-submit">Save user</button>
					</div>
				</div>
			</div>
			<!--  -->
			<?php if ( class_exists('Clients') ): ?>
			<div class="panel panel-default">
				<div class="panel-heading">Clients</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="control-label">Related Clients</label>
						<?php
							$clients = Clients::all();
							$cur_clients = $user ? $user->getMeta('clients') : null;
							$cur_clients = $cur_clients ? $cur_clients : array();
						?>
						<ul class="check-list">
							<?php
								if ($clients):
									foreach ($clients as $client):
							?>
							<li class="checkbox"><label for="client_<?php echo $client->id; ?>"><input id="client_<?php echo $client->id; ?>" name="clients[]" <?php echo (in_array($client->id, $cur_clients) ? 'checked="checked"' : ''); ?> type="checkbox" value="<?php echo $client->id; ?>"><?php echo $client->name; ?></label></li>
							<?php
									endforeach;
								endif;
							?>
						</ul>
					</div>
				</div>
			</div>
			<?php endif; ?>
			<!--  -->
			<?php if ( class_exists('Projects') ): ?>
			<div class="panel panel-default">
				<div class="panel-heading">Projects</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="control-label">Related Projects</label>
						<?php
							$projects = Projects::all();
							$cur_projects = $user ? $user->getMeta('projects') : null;
							$cur_projects = $cur_projects ? $cur_projects : array();
						?>
						<ul class="check-list">
							<?php
								if ($projects):
									foreach ($projects as $project):
							?>
							<li class="checkbox"><label for="project_<?php echo $project->id; ?>"><input id="project_<?php echo $project->id; ?>" name="projects[]" <?php echo (in_array($project->id, $cur_projects) ? 'checked="checked"' : ''); ?> type="checkbox" value="<?php echo $project->id; ?>"><?php echo $project->name; ?></label></li>
							<?php
									endforeach;
								endif;
							?>
						</ul>
					</div>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<div class="col-left">
				<div class="form-group">
					<label for="first_name" class="control-label">First name<span class="text-danger">*</span></label>
					<input type="text" name="first_name" id="first_name" class="form-control" data-validate="required" value="<?php echo ($user ? $user->first_name : ''); ?>">
				</div>
				<div class="form-group">
					<label for="last_name" class="control-label">Last name</label>
					<input type="text" name="last_name" id="last_name" class="form-control" value="<?php echo ($user ? $user->last_name : ''); ?>">
				</div>
				<div class="form-group">
					<label for="email" class="control-label">Email<span class="text-danger">*</span></label>
					<input type="text" name="email" id="email" class="form-control" data-validate="email" value="<?php echo ($user ? $user->email : ''); ?>">
				</div>
				<div class="form-group">
					<label for="nickname" class="control-label">Nickname<span class="text-danger">*</span></label>
					<input type="text" name="nickname" id="nickname" class="form-control" data-validate="required" value="<?php echo ($user ? $user->nickname : ''); ?>">
				</div>
				<?php if ($user): ?>
				<div class="form-group">
					<label for="password" class="control-label">Password</label>
					<input type="password" name="password" id="password" class="form-control">
				</div>
				<div class="form-group">
					<label for="confirm" class="control-label">Repeat password</label>
					<input type="password" name="confirm" id="confirm" class="form-control" data-validate="confirm" data-param="input[name=password]">
				</div>
				<span class="help-block">Tip: You may leave both fields empty if you don't want to change the password.</span>
				<?php else: ?>
				<div class="form-group">
					<label for="password" class="control-label">Password<span class="text-danger">*</span></label>
					<input type="password" name="password" id="password" class="form-control" data-validate="required">
				</div>
				<div class="form-group">
					<label for="confirm" class="control-label">Repeat password<span class="text-danger">*</span></label>
					<input type="password" name="confirm" id="confirm" class="form-control" data-validate="confirm" data-param="input[name=password]">
				</div>
				<?php endif; ?>
		</div>
	</div>
</form>