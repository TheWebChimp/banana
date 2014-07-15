<?php $site->getParts(array('admin/header_html', 'admin/header', 'admin/sidebar')) ?>

	<section>
		<div class="margins">
			<h1 class="title">All Users <a href="<?php $site->urlTo('admin/users/new', true); ?>" class="btn btn-primary btn-xs">Add User</a></h1>
			<table class="table table-hover">
				<thead>
					<tr>
						<th>User</th>
						<th>Role</th>
						<th>Email</th>
						<th>Status</th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th>User</th>
						<th>Role</th>
						<th>Email</th>
						<th>Status</th>
					</tr>
				</tfoot>
				<tbody>
					<?php
						if ($users):
							foreach ($users as $user):
					?>
					<tr>
						<td>
							<span class="title"><a href="<?php $site->urlTo("/admin/users/edit/{$user->id}", true); ?>"><?php echo $user->login; ?></a></span>
							<span class="actions">
								<a href="<?php $site->urlTo("/admin/users/edit/{$user->id}", true); ?>">Edit</a> |
								<a href="<?php $site->urlTo("/admin/users/delete/{$user->id}", true); ?>" class="text-danger">Delete</a>
							</span>
						</td>
						<td><?php echo $user->role; ?></td>
						<td><?php echo $user->email; ?></td>
						<td><?php echo $user->status; ?></td>
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