<?php
	$dbh = $site->getDatabase();
	$conditions = '1';
	$search_str = $search ? $dbh->quote("%{$search}%") : '';
	$conditions .= $search_str ? " AND subject LIKE {$search_str}" : '';
	$conditions .= is_numeric($client_id) ? " AND client_id = {$client_id}" : '';
	$conditions .= is_numeric($project_id) ? " AND project_id = {$project_id}" : '';
	$open = Tickets::count("{$conditions} AND status = 'Open'");
	$closed = Tickets::count("{$conditions} AND status = 'Closed'");
	$labels = TicketTags::where('type', '=', 'Label');
?>
<?php $site->getParts(array('client/header_html', 'client/header')) ?>

	<section>
		<div class="container">
			<div class="margins">
				<ol class="breadcrumb">
					<li><a href="<?php $site->urlTo('/dashboard', true) ?>">Dashboard</a></li>
					<li><a href="<?php $site->urlTo('/tickets', true) ?>">Tickets</a></li>
					<li class="active">Calendario</li>
				</ol>

				<div class="page-header">

					<div class="pull-right form-inline">
						<div class="btn-group">
							<button class="btn btn-primary" data-calendar-nav="prev">&laquo; Anterior</button>
							<button class="btn" data-calendar-nav="today">Hoy</button>
							<button class="btn btn-primary" data-calendar-nav="next">Siguiente &raquo;</button>
						</div>
						<div class="btn-group">
							<button class="btn btn-warning" data-calendar-view="year">Año</button>
							<button class="btn btn-warning active" data-calendar-view="month">Mes</button>
							<button class="btn btn-warning" data-calendar-view="week">Semana</button>
							<button class="btn btn-warning" data-calendar-view="day">Día</button>
						</div>
					</div>

					<h3></h3>
				</div>

				<div class="row">
					<div class="col-md-9">
						<div id="tickets-calendar"></div>
					</div>
					<div class="col-md-3">

					</div>
				</div>
			</div>
		</div>
	</section>

<?php $site->getParts(array('client/footer', 'client/footer_html')) ?>