<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Facade;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
Facade::setFacadeApplication($app);
DB::statement('DROP TABLE IF EXISTS `product_attribute_value_mappings`');
echo "dropped\n";
