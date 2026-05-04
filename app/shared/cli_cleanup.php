<?php
require_once __DIR__ .'/../bootstrap/app.php'; 
$dryRun = in_array('--dry-run', $argv ?? []);
$result = cleanupOrphanImages($db, $dryRun);

foreach ($result['deleted'] as $line) echo $line . PHP_EOL;
foreach ($result['errors']  as $line) echo "ERROR: " . $line . PHP_EOL;

echo PHP_EOL . count($result['deleted']) . " file(s) " . ($dryRun ? "would be deleted" : "deleted") . PHP_EOL;