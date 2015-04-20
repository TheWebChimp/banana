<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title><?php echo $site->getPageTitle() ?></title>
	<link rel="shortcut icon" href="<?php $site->urlTo('/favicon.ico', true) ?>">
	<script type="text/javascript">
		var constants = {
			siteUrl: '<?php $site->urlTo("", true) ?>',
			ajaxUrl: '<?php $site->urlTo("/ajax", true) ?>'
		};
	</script>
	<?php $site->includeScriptVars() ?>
	<?php $site->includeStyles() ?>
	<?php $site->includeScript('modernizr'); ?>
	<meta name="csrf-token" content="<?php $site->hashToken( $_SESSION['csrf_secret'], true ); ?>">
</head>
<body class="<?php $site->bodyClass() ?>">
	<div class="wrapper">