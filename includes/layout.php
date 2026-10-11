<?php
// The layout owns every document/container tag. Partials return balanced markup.
$vJ = '?=1.9.11';
$vC = '?=1.4.0';

#output buffer, loads the content as a string to display as rendered html/whatever
// Legacy adapters supply components captured before the page ran.
if (!isset($layoutOpening)) {
    $layoutOpening = [];
    foreach (['head', 'navbar', 'page-title', 'tournament-nav'] as $partial) {
        ob_start();
        include __DIR__ . '/partials/' . $partial . '.php';
        $layoutOpening[$partial] = ob_get_clean();
    }
    ob_start();
    include __DIR__ . '/pageAlerts.php';
    $layoutOpening['alerts'] = ob_get_clean();
}
?>
<!doctype html>
<html class="no-js" lang="en" dir="ltr">
<head>
    <?= $layoutOpening['head'] ?>
</head>
<body>
    <header>
        <?= $layoutOpening['navbar'] ?>
    </header>
    <?= $layoutOpening['page-title'] ?>

    <main id="page-wrapper" class="grid-container">
        <?= $layoutOpening['tournament-nav'] ?>
        <?= $layoutOpening['alerts'] ?>
        <?php
        if (isset($contentHtml)) {
            echo $contentHtml;
        } else {
            include $contentTemplate;
            displayPageAlerts();
        }
        ?>
    </main>

    <?php if (empty($hideFooter)): ?>
        <footer>
            <?php include __DIR__ . '/partials/footer.php'; ?>
        </footer>
    <?php endif; ?>
    <?php include __DIR__ . '/partials/scripts.php'; ?>
</body>
</html>
