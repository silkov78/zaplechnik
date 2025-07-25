<?php

declare(strict_types=1);

$points = file_get_contents('../data/svir_campings.geojson');

$jsonPoints = json_decode($points, true);

echo "<pre>";
print_r($jsonPoints);
echo "</pre>";
