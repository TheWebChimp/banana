<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">All Clients <a href="<?php $site->urlTo('admin/clients/new', true); ?>" class="btn btn-primary btn-xs">Add Client</a></h1>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>Client</th>
						<th>Type</th>
						<th>Status</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>Client</th>
						<th>Type</th>
						<th>Status</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
						if ($clients):
							foreach ($clients as $client):
					?>
					<tr>
						<td>
							<span class="title"><a href="<?php $site->urlTo("/admin/clients/edit/{$client->id}", true); ?>"><?php echo $client->name; ?></a></span>
							<span class="actions">
								<a href="<?php $site->urlTo("/admin/clients/edit/{$client->id}", true); ?>">Edit</a> |
								<a href="<?php $site->urlTo("/admin/clients/delete/{$client->id}", true); ?>" class="text-danger">Delete</a>
							</span>
						</td>
						<td><?php echo $client->type; ?></td>
						<td><?php echo $client->status; ?></td>
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