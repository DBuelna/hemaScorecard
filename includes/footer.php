<?php
// Complete the legacy content capture; no HTML containers cross file boundaries.
displayPageAlerts();
$contentHtml = ob_get_clean();
include __DIR__ . '/layout.php';
