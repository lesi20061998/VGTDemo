<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
Illuminate\Support\Facades\Facade::setFacadeApplication($app);
Illuminate\Support\Facades\DB::statement('DROP TABLE IF EXISTS `product_attribute_value_mappings`');
echo "dropped\n";
