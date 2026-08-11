<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
Facade::setFacadeApplication($app);

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Facade;

$users = User::with('roles')->get();
foreach ($users as $user) {
    $roleNames = $user->roles->pluck('name')->toArray();
    echo "User: {$user->email} (ID: {$user->id})\n";
    echo "  Name: {$user->name}\n";
    echo '  Roles: '.(empty($roleNames) ? '(none)' : implode(', ', $roleNames))."\n\n";
}
