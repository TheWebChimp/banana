<?php

	class Pagination {

		static function paginate($total, $visible = 5, $vars = array()) {
			global $site;
			$request = $site->mvc->getRequest();
			if (! $total ) {
				return;
			}
			$search = $request->param('search');
			$filter = $request->param('filter');
			$page = $request->get('page', 1);
			$show = $request->get('show', 30);
			$show = $show ? $show : 30;
			$pages = ceil($total / $show);
			$extra = '';
			$extra .= $search ? htmlspecialchars("&search={$search}") : '';
			$extra .= $filter ? htmlspecialchars("&filter={$filter}") : '';
			foreach ($vars as $var => $value) {
				$extra .= htmlspecialchars("&{$var}={$value}");
			}
			if ($pages <= 1) {
				return;
			}
			# Calculate boundaries
			if ($page < $visible) {
				$lower = 1;
				$upper = $visible;
			} else {
				$lower = $page - ($visible - ($page == $pages ? 1 : 2));
				$upper = $lower + ($visible - 1);
			}
			# Adjust
			$lower = $lower > 0 ? $lower : 1;
			$upper = $upper < $pages ? $upper : $pages;
			$trimmed = false;
			if ($lower > 1 && $upper < $pages) {
				$trimmed = 'both';
			} else if ($lower > 1) {
				# Left-trimmed, increment visible items on the right side
				$trimmed = 'left';
			} else if ($upper < $pages) {
				# Right-trimmed, increment visible items on the left side
				$trimmed = 'right';
			}
			# Adjust again
			$lower = $lower > 0 ? $lower : 1;
			$upper = $upper < $pages ? $upper : $pages;

			echo '<ul class="pagination">';
			# Go to first page
		 	if ($trimmed == 'left' || $trimmed == 'both') {
				echo '<li><a href="?page=1&amp;show='.$show.$extra.'">&laquo;</a></li>';
			} else {
				echo '<li class="disabled"><a href="#">&laquo;</a></li>';
			}
			# Individual pages
			for ($p = $lower; $p <= $upper; $p++) {
				echo '<li class="'.($page == $p ? 'active' : '').'"><a href="?page='.$p.'&amp;show='.$show.$extra.'">'.$p.'</a></li>';
			}
			# Go to last page
			if ($trimmed == 'right' || $trimmed == 'both') {
				echo '<li><a href="?page='.$pages.'&amp;show='.$show.$extra.'">&raquo;</a></li>';
			} else {
				echo '<li class="disabled"><a href="#">&raquo;</a></li>';
			}
			echo '</ul>';
		}

	}

?>