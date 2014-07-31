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
					<li class="active">Contact</li>
				</ol>
				<div class="row">
					<div class="col-md-4">
						<h3>Submit a new Contact</h3>

						<form class="form-contact well" action="<?php $site->urlTo('/contact/submit', true); ?>" method="post" data-submit="validate">
							<input type="hidden" name="token" value="<?php $site->csrf->getToken(true); ?>">
							<div class="form-group">
								<label for="name" class="control-label">Name</label>
								<input type="text" name="name" id="name" class="form-control" data-validate="required">
							</div>
							<div class="form-group">
								<label for="last_name" class="control-label">Last Name</label>
								<input type="text" name="last_name" id="last_name" class="form-control" data-validate="required">
							</div>
							<div class="form-group">
								<label for="email" class="control-label">Email</label>	
								<input type="text" name="email" id="email" class="form-control" data-validate="required">
							</div>
							<div class="attachments">
								<!--  -->
							</div>
							<div class="text-right">
								<button class="btn btn-success" type="submit">Create</button>
							</div>
						</form>
					</div>
				
			

					<div class="col-md-8">
						<?php
							$contacts = Contacts::all();
							$pending = $contacts ? $contacts->count("t.status = 'Pending'") : 0;
							$done = $contacts ? $contacts->count("t.status = 'Done'") : 0;
						?>

						<h3>Available Contacts</h3>
						<div class="form-group">
							
						</div>
						<?php
							if ($contacts):
						?>
						<div class="list-group todos">
							<?php
									foreach ($contacts as $contact):
							?>
							<div class="list-group-item todo" data-status="<?php echo $todo->status; ?>">
								<span class="fa fa-<?php echo ($todo->status == 'Done' ? 'check' : 'exclamation'); ?>-circle text-<?php echo ($contact->status == 'Done' ? 'success' : 'info'); ?>" title="<?php echo $contact->status; ?> Contact"></span>
								<p class="list-group-item title clearfix">
									<?php if ( Contacts::currentContactCan('manage_optins') ): ?>
									<span class="pull-right actions">
										<a href="<?php $site->urlTo("/contact/edit/{$contact->id}", true) ?>" class="btn btn-xs btn-primary" title="Edit"><i class="fa fa-pencil"></i></a>
										<a href="<?php $site->urlTo("/contact/delete/{$contact->id}", true) ?>" class="btn btn-xs btn-danger" title="Delete"><i class="fa fa-trash-o"></i></a>
									</span>
									<?php endif; ?>
									<strong><?php echo $contact->name; ?></strong>
								</p>
								<div class="list-group-item-text details">
									<?php
										echo $parsedown->text($cotact->details);
										$attachments = $contact->attachments ? @unserialize($contact->attachments) : null;
										if ($attachments):
											foreach ($attachments as $attachment_id): 
												$attachment = Attachments::get($attachment_id);
												if(! $attachment) continue;
									?>
									<div class="attachmentss">
										<a href="<?php $attachment->getUrl(true); ?>" target="_blank"><?php echo $attachment->name; ?></a>
										<span class="text-muted"> (<?php echo $attachment->mine; ?>)</span>
									</div>
									<?php
											endforeach;
										endif;
									?>
								</div>
								<div class="text-rigth text-muted"><small><?php echo relative_time( $contact->created ); ?></small></div>
							</div>
							<?php 
									endforeach;
								else:
							?>
							<div class="alert alert-info">There are no contacts yet. <a href="<?php $site->urlTo('/contact/new', true); ?>" class="alert-link">Click here</a> to create a new task.</div>
							<?php
								endif;
							?>
					    </div>
				</div>
			</div>
		</div>


	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>