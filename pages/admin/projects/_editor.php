<?php
	//
?>
<form action="" method="post" id="form-project">
	<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
	<div class="cols right-fixed">
		<div class="col-right">
			<div class="panel panel-default">
				<div class="panel-heading">Properties</div>
				<div class="panel-body">
					<!-- <div class="form-group">
						<label for="type" class="control-label">Type<span class="text-danger">*</span></label>
						<select name="type" id="type" class="form-control" data-validate="required">
							<option value=""></option>
						</select>
					</div> -->
					<div class="form-group">
						<label for="status" class="control-label">Status<span class="text-danger">*</span></label>
						<select name="status" id="status" class="form-control" data-validate="required">
							<option value=""></option>
							<option <?php option_selected($project ? $project->status : '', 'Catchment'); ?> value="Catchment">Catchment</option>
							<option <?php option_selected($project ? $project->status : '', 'Proposal'); ?> value="Proposal">Proposal</option>
							<option <?php option_selected($project ? $project->status : '', 'Development'); ?> value="Development">Development</option>
							<option <?php option_selected($project ? $project->status : '', 'Changes'); ?> value="Changes">Changes</option>
							<option <?php option_selected($project ? $project->status : '', 'Installation'); ?> value="Installation">Installation</option>
							<option <?php option_selected($project ? $project->status : '', 'Closed'); ?> value="Closed">Closed</option>
							<option <?php option_selected($project ? $project->status : '', 'Post-sales'); ?> value="Post-sales">Post-sales</option>
						</select>
					</div>
					<div class="text-right">
						<button class="btn btn-primary btn-submit">Save project</button>
					</div>
				</div>
			</div>

			<?php if ( class_exists('Clients') ): ?>
			<div class="panel panel-default">
				<div class="panel-heading">Clients</div>
				<div class="panel-body">
					<div class="form-group">
						<label class="control-label">Related Clients</label>
						<?php
							$clients = Clients::all();
							$cur_clients = $project ? $project->getMeta('clients') : null;
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

		</div>
		<div class="col-left">
			<div class="form-group">
				<label for="name" class="control-label">Name<span class="text-danger">*</span></label>
				<input type="text" name="name" id="name" class="form-control" data-validate="required" value="<?php echo ($project ? $project->name : ''); ?>">
			</div>
			<div class="form-group">
				<label for="notes" class="control-label">Notes</label>
				<textarea name="notes" id="notes" class="form-control"><?php echo ($project ? $project->notes : ''); ?></textarea>
			</div>
			<!-- <div class="form-group">
				<label for="name" class="control-label">Name<span class="text-danger">*</span></label>
				<input type="text" name="name" id="name" class="form-control" data-validate="required" value="<?php echo ($project ? $project->name : ''); ?>">
			</div> -->
		</div>
	</div>
</form>