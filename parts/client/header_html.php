<!DOCTYPE html>
<html lang="en">
<head>
	<?php $site->metaTags(); ?>
	<title><?php echo $site->getPageTitle() ?></title>
	<link rel="shortcut icon" href="<?php $site->urlTo('/favicon.ico?v3', true) ?>">
	<link rel="icon" href="<?php $site->urlTo('/favicon.png', true) ?>" type="image/png">
	<script type="text/javascript">
		var constants = {
			siteUrl: '<?php $site->urlTo("", true) ?>',
			ajaxUrl: '<?php $site->urlTo("/ajax", true) ?>',
			mvc: {
				controller: '<?php echo $site->mvc->getRequest()->controller; ?>',
				action: '<?php echo $site->mvc->getRequest()->action; ?>',
				id: '<?php echo $site->mvc->getRequest()->id; ?>'
			}
		};
	</script>
	<?php $site->includeStyles() ?>
	<?php $site->includeScript('modernizr'); ?>
</head>
<body class="<?php $site->bodyClass() ?>">