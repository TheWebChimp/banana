<?php
	//
?>
	<form action="" method="post">
		<input type="hidden" name="token" value="<?php echo $site->csrf->getToken(); ?>">
		<div class="cols right-fixed">
			<div class="col-right">
				<div class="panel panel-default">
					<div class="panel-heading">Bite properties</div>
					<div class="panel-body">
						<div class="form-group">
							<label for="status" class="control-label">Status</label>
							<select type="text" name="status" id="status" class="form-control">
								<option <?php option_selected($bite ? $bite->status : '', 'Published'); ?> value="Published">Published</option>
								<option <?php option_selected($bite ? $bite->status : '', 'Draft'); ?> value="Draft">Draft</option>
							</select>
						</div>
						<div class="form-group">
							<label for="type" class="control-label">Type</label>
							<select type="text" name="type" id="type" class="form-control">
								<option <?php option_selected($bite ? $bite->type : '', 'Public'); ?> value="Public">Public</option>
								<option <?php option_selected($bite ? $bite->type : '', 'Private'); ?> value="Private">Private</option>
							</select>
						</div>
						<div class="form-group">
							<label for="syntax" class="control-label">Syntax</label>
							<select type="text" name="syntax" id="syntax" class="form-control">
								<option <?php option_selected($bite ? $bite->syntax : '', 'clike'); ?> value="clike">C/C++</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'css'); ?> value="css">CSS</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'sass'); ?> value="sass">SASS</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'htmlmixed'); ?> value="htmlmixed">HTML</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'coffeescript'); ?> value="coffeescript">CoffeeScript</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'javascript'); ?> value="javascript">JavaScript</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'markdown'); ?> value="markdown">Markdown</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'ruby'); ?> value="ruby">Ruby</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'php'); ?> value="php">PHP</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'python'); ?> value="python">Python</option>
								<option <?php option_selected($bite ? $bite->syntax : '', 'sql'); ?> value="sql">SQL</option>
							</select>
						</div>
						<div class="text-right">
							<button type="submit" class="btn btn-primary">Save Bite</button>
						</div>
					</div>
				</div>
			</div>
			<div class="col-left">
				<div class="form-group">
					<label for="name" class="control-label sr-only">Name</label>
					<input type="text" name="name" id="name" class="form-control input-lg" placeholder="Bite name" value="<?php echo ($bite ? $bite->name : ''); ?>">
				</div>
				<div class="fullscreen-container">
					<div class="form-group">
						<div class="btn-group">
							<a href="#" class="btn btn-default active btn-theme" data-theme="neo">Light theme</a>
							<a href="#" class="btn btn-default btn-theme" data-theme="monokai">Dark theme</a>
						</div>
						<div class="pull-right">
							<a href="#" class="btn btn-default btn-copy"><i class="fa fa-clipboard"></i> Copy</a>
							<a href="#" class="btn btn-default" data-toggle="fullscreen"><i class="fa fa-desktop"></i> Fullscreen editor</a>
						</div>
					</div>
					<div class="form-group codemirror" data-mode="<?php echo ($bite ? $bite->syntax : ''); ?>">
						<label for="content" class="control-label sr-only">Content</label>
						<textarea type="text" name="content" id="content" class="form-control"><?php echo ($bite ? $bite->content : ''); ?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label for="description" class="control-label">Description</label>
					<textarea type="text" name="description" id="description" class="form-control"><?php echo ($bite ? $bite->getMeta('description') : ''); ?></textarea>
				</div>
			</div>
		</div>
	</form>