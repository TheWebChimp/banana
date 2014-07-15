<?php
	//
?>
<form action="" method="post" id="form-client">
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
							<option <?php option_selected($client ? $client->status : '', 'Active'); ?> value="Active">Active</option>
							<option <?php option_selected($client ? $client->status : '', 'Inactive'); ?> value="Inactive">Inactive</option>
						</select>
					</div>
					<div class="text-right">
						<button class="btn btn-primary btn-submit">Save client</button>
					</div>
				</div>
			</div>
		</div>
		<div class="col-left">
			<div class="form-group">
				<label for="name" class="control-label">Name<span class="text-danger">*</span></label>
				<input type="text" name="name" id="name" class="form-control" data-validate="required" value="<?php echo ($client ? $client->name : ''); ?>">
			</div>
			<div class="form-group">
				<label for="notes" class="control-label">Notes</label>
				<textarea name="notes" id="notes" class="form-control"><?php echo ($client ? $client->notes : ''); ?></textarea>
			</div>
			<!-- <div class="form-group">
				<label for="name" class="control-label">Name<span class="text-danger">*</span></label>
				<input type="text" name="name" id="name" class="form-control" data-validate="required" value="<?php echo ($client ? $client->name : ''); ?>">
			</div> -->
		</div>
	</div>
</form>