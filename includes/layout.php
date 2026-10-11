<?php
// The caller bootstraps the application and supplies a fixed content template.
$vJ = '?=1.9.11';
$vC = '?=1.4.0';
?>
<!doctype html>
<html class="no-js" lang="en" dir="ltr">
<head>
    <?php include __DIR__ . '/partials/head.php'; ?>
</head>
<body>
    <?php include __DIR__ . '/partials/navbar.php'; ?>
    <?php include __DIR__ . '/partials/page-title.php'; ?>

    <div id="page-wrapper" class="grid-container">
        <?php include __DIR__ . '/partials/tournament-nav.php'; ?>
        <?php include __DIR__ . '/pageAlerts.php'; ?>
        <?php include $contentTemplate; ?>
        <?php displayPageAlerts(); ?>
    </div>

    <?php include __DIR__ . '/partials/footer.php'; ?>
    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
