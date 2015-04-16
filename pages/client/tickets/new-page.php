
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true); ?>">Dashboard</a></li>
					<li><a href="<?php $site->urlTo('/tickets', true); ?>">Tickets</a></li>
					<li class="active">Create ticket</li>
				</ol>

				<form class="form-ticket" action="" method="post" data-submit="validate">
					<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">

					<div class="cols right-fixed">
						<div class="col-right">
							<!--  -->
							<div class="form-group">
								<label for="project_id" class="control-label">Client</label>
								<select name="project_id" id="project_id" class="form-control">
									<option value="">None</option>
									<?php
										$clients = Users::currentUserCan('manage_options') ? Clients::all() : $site->user->clients;
										if ($clients):
											foreach ($clients as $client):
									?>
									<option <?php option_selected($ticket ? $ticket->client_id : '', $client->id); ?> value="<?php echo $client->id; ?>"><?php echo $project->name; ?></option>
									<?php
											endforeach;
										endif;
									?>
								</select>
							</div>
							<div class="form-group">
								<label for="project_id" class="control-label">Project</label>
								<select name="project_id" id="project_id" class="form-control">
									<option value="">None</option>
									<?php
										$projects = Users::currentUserCan('manage_options') ? Projects::all() : $site->user->projects;
										if ($projects):
											foreach ($projects as $project):
									?>
									<option <?php option_selected($ticket ? $ticket->project_id : '', $project->id); ?> value="<?php echo $project->id; ?>"><?php echo $project->name; ?></option>
									<?php
											endforeach;
										endif;
									?>
								</select>
							</div>
							<div class="text-right">
								<button type="submit" class="btn btn-primary">Update ticket</button>
							</div>
						</div>
						<div class="col-left">
							<div class="form-group">
								<label for="subject" class="control-label">Subject</label>
								<input type="text" name="subject" id="subject" class="form-control" data-validate="required" value="<?php echo ($ticket ? $ticket->subject : ''); ?>">
							</div>
							<ul class="nav nav-pills">
								<li class="active"><a href="#write" data-toggle="tab">Write</a></li>
								<li><a href="#preview" data-toggle="tab" class="btn-preview">Preview</a></li>
							</ul>
							<div class="tab-content reply">
								<div class="tab-pane active" id="write">
									<div class="form-group">
										<textarea name="details" id="details" class="code-area form-control" data-validate="required"><?php echo ($ticket ? $ticket->details : ''); ?></textarea>
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
									if ($ticket):
										$attachments = $ticket->attachments ? @unserialize($ticket->attachments) : null;
										if ($attachments):
											foreach ($attachments as $attachment_id):
												$attachment = Attachments::get($attachment_id);
												if (! $attachment ) continue;
								?>
								<div class="attachment">
									<i class="fa fa-file"></i> <?php echo $attachment->name; ?> <span class="text-muted"> (<?php echo $attachment->mime; ?>) -</span> <span class="status"><a data-id="9" class="btn-remove" href="#">Remove</a></span>
									<input type="hidden" value="<?php echo $attachment->id; ?>" name="attachments[]">
								</div>
								<?php
											endforeach;
										endif;
									endif;
								?>
							</div>
							<p>
								<span class="text-muted"><small><i class="fa fa-code"></i> Parsed as Markdown</small></span>
							</p>
						</div>
					</div>
				</form>
			</div>

		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>