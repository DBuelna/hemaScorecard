<?php
// Compatibility adapter for pages that still include header.php/footer.php.
// Request handling runs before output; layout.php owns the complete document.
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/pageFunctions.php';

$vJ = '?=1.9.11';
$vC = '?=1.4.0';
$adminStatsDisplay = ALLOW['EVENT_MANAGEMENT'] || ALLOW['VIEW_SETTINGS'] || ALLOW['STATS_EVENT'];

// Capture the opening components now to preserve their session/alert timing.
$layoutOpening = [];
foreach (['head', 'navbar', 'page-title', 'tournament-nav'] as $partial) {
    ob_start();
    include __DIR__ . '/partials/' . $partial . '.php';
    $layoutOpening[$partial] = ob_get_clean();
}
ob_start();
include __DIR__ . '/pageAlerts.php';
$layoutOpening['alerts'] = ob_get_clean();

// The matching footer hands this page's content to the complete layout.
ob_start();
