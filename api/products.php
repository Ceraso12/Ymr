<?php
declare(strict_types=1);
require __DIR__ . '/common.php';
$products = array_values(array_filter(ymr_read_catalog(), static fn($p) => is_array($p) && ($p['active'] ?? true)));
ymr_json(['products' => $products]);
