<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$roles = DB::table('roles')->get();
$userRoles = DB::table('user_roles')->get();

echo "Roles:\n";
foreach ($roles as $r) {
    echo "  {$r->id} - {$r->name} ({$r->display_name})\n";
}

echo "\nUser Roles:\n";
foreach ($userRoles as $ur) {
    echo "  user_id: {$ur->user_id}, role_id: {$ur->role_id}\n";
}
