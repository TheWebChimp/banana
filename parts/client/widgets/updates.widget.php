<?php
	$tickets = Tickets::all(1, 5, 'desc', 'created');
	$bites = Bites::all(0, 5, 'created', 'desc');
	$todos = ToDos::all(0, 5, 'created', 'desc');
?>
	<div class="panel panel-default">
		<div class="panel-heading">Recent Activity</div>
		<div class="panel-body">
			<?php if ($tickets): ?>
			<h4>Latest tickets<small> &mdash; <a href="<?php $site->urlTo('/tickets', true); ?>">View all</a></small></h4>
			<ul>
				<?php
					foreach ($tickets as $ticket):
						$creator = Users::get($ticket->user_id);
				?>
					<li><a href="<?php $site->urlTo("/tickets/{$ticket->id}", true) ?>"><?php echo $ticket->subject; ?></a><span class="text-muted"> &mdash; <?php echo relative_time( $ticket->created );  ?> by <?php echo $creator->nickname ?></span></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
			<?php if ($bites): ?>
			<h4>Latest bites<small> &mdash; <a href="<?php $site->urlTo('/bites', true); ?>">View all</a></small></h4>
			<ul>
				<?php
					foreach ($bites as $bite):
						$creator = Users::get($bite->user_id);
				?>
					<li><a href="<?php $site->urlTo("/bites/{$bite->id}", true) ?>"><?php echo $bite->name; ?></a><span class="text-muted"> &mdash; <?php echo relative_time( $bite->created );  ?> by <?php echo $creator->nickname ?></span></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
			<?php if ($todos): ?>
			<h4>Latest todos<small> &mdash; <a href="<?php $site->urlTo('/todo', true); ?>">View all</a></small></h4>
			<ul>
				<?php
					foreach ($todos as $todo):
						$creator = Users::get($todo->user_id);
				?>
					<li><a href="<?php $site->urlTo("/todo/{$todo->id}", true) ?>"><?php echo $todo->name; ?></a><span class="text-muted"> &mdash; <?php echo relative_time( $todo->created );  ?> by <?php echo $creator->nickname ?></span></li>
				<?php endforeach; ?>
			</ul>
			<?php endif; ?>
		</div>
	</div>