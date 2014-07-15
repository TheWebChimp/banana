<?php
	$categories = ToDoTags::where('type', 'Category', '=', 0, 1000, 'id', 'ASC');
	$get_cat = get_item($_GET, 'category', 0);
?>
	<form action="" method="post" class="form-todo" data-submit="validate">
		<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
		<input type="hidden" name="category" value="<?php echo $category->id; ?>">
		<div class="cols right-fixed">
			<div class="col-right">
				<div class="form-group">
					<label for="category" class="control-label">Category</label>
					<select name="category" id="category" class="form-control" data-validate="required">
						<?php
							$cat = $todo ? $todo->getCategory() : 0;
							if ($categories):
								foreach ($categories as $category):
						?>
						<option <?php option_selected($cat ? $cat->id : $get_cat, $category->id); ?> value="<?php echo $category->id; ?>"><?php echo $category->name; ?></option>
						<?php
								endforeach;
							endif;
						?>
					</select>
				</div>
				<?php if ($todo): ?>
				<div class="form-group">
					<label for="status" class="control-label">Status</label>
					<select name="status" id="status" class="form-control" data-validate="required">
						<option <?php option_selected($todo->status, 'Pending'); ?> value="Pending">Pending</option>
						<option <?php option_selected($todo->status, 'Done'); ?> value="Done">Done</option>
					</select>
				</div>
				<?php endif; ?>
				<div class="form-group">
					<label for="project_id" class="control-label">Project</label>
					<select name="project_id" id="project_id" class="form-control">
						<option value="">None</option>
						<?php
							$projects = Users::currentUserCan('manage_options') ? Projects::all() : $site->user->projects;
							if ($projects):
								foreach ($projects as $project):
						?>
						<option <?php option_selected($todo ? $todo->project_id : 0, $project->id); ?> value="<?php echo $project->id; ?>"><?php echo $project->name; ?></option>
						<?php
								endforeach;
							endif;
						?>
					</select>
				</div>
				<div class="text-right">
					<button type="submit" class="btn btn-success">Save Task</button>
				</div>
			</div>
			<div class="col-left">
				<div class="form-group">
					<label for="name" class="control-label">Name</label>
					<input type="text" name="name" class="form-control" value="<?php echo ($todo ? $todo->name : ''); ?>" data-validate="required">
				</div>
				<!--  -->
				<ul class="nav nav-pills">
					<li class="active"><a href="#write" data-toggle="tab">Write</a></li>
					<li><a href="#preview" data-toggle="tab" class="btn-preview">Preview</a></li>
				</ul>
				<div class="tab-content reply">
					<div class="tab-pane active" id="write">
						<div class="form-group">
							<textarea name="details" id="details" class="code-area form-control"><?php echo ($todo ? $todo->details : ''); ?></textarea>
						</div>
					</div>
					<div class="tab-pane" id="preview">
						<div class="form-group">
							<div class="preview-area form-control"></div>
						</div>
					</div>
				</div>
				<div class="well well-sm text-center text-muted dropfiles">
					Drag files or click here to add an attachment
					<div class="fallback">
						<input type="file" name="file" id="">
					</div>
				</div>
				<div class="attachments">
					<?php
						$attachments = $todo ? @unserialize($todo->attachments) : null;
						if ($attachments):
							foreach ($attachments as $attachment_id):
								$attachment = Attachments::get($attachment_id);
								if (! $attachment ) continue;
					?>
					<div class="attachment" data-id="<?php echo $attachment->id ?>">
						<i class="fa fa-file"></i> <a href="<?php $attachment->getUrl(true); ?>" target="_blank"><?php echo $attachment->attachment; ?></a> <span class="text-muted"> (<?php echo $attachment->mime; ?>) - </span>
						<span class="status">
							<a data-id="<?php echo $attachment->id; ?>" class="btn-remove" href="#">Remove</a>
						</span>
						<input type="hidden" value="<?php echo $attachment->id; ?>" name="attachments[]">
					</div>
					<?php
							endforeach;
						endif;
					?>
				</div>
				<p>
					<span class="text-muted"><small><i class="fa fa-code"></i> Parsed as Markdown</small></span>
				</p>
			</div>
		</div>
	</form>