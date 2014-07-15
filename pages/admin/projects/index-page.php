<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">All Projects <a href="<?php $site->urlTo('admin/projects/new', true); ?>" class="btn btn-primary btn-xs">Add Project</a></h1>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Project</th>
						<th>Clients</th>
						<th>Type</th>
						<th>Status</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Project</th>
						<th>Clients</th>
						<th>Type</th>
						<th>Status</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
						if ($projects):
							foreach ($projects as $project):
								$clients = $project->clients ? '' : '--';
								$href = '';
								if ($project->clients) {
									foreach ($project->clients as $client) {
										$href = $site->urlTo("/clients/{$client->id}");
										$clients .= "<a href=\"{$href}\">{$client->name}</a>, ";
									}
									$clients = rtrim($clients, ', ');
								}
					?>
					<tr>
						<td>
							<span class="title"><a href="<?php $site->urlTo("/admin/projects/edit/{$project->id}", true); ?>"><?php echo $project->name; ?></a></span>
							<span class="actions">
								<a href="<?php $site->urlTo("/admin/projects/edit/{$project->id}", true); ?>">Edit</a> |
								<a href="<?php $site->urlTo("/admin/projects/delete/{$project->id}", true); ?>" class="text-danger">Delete</a>
							</span>
						</td>
						<td><?php echo $clients; ?></td>
						<td><?php echo $project->type; ?></td>
						<td><?php echo $project->status; ?></td>
					</tr>
					<?php
							endforeach;
						else:
					?>
					<tr>
						<td colspan="4">There are no elements to show</td>
					</tr>
					<?php
						endif;
					?>
				</tbody>
			</table>
		</div>
	</section>

<?php $site->getParts(array('admin/footer', 'admin/footer_html')) ?>