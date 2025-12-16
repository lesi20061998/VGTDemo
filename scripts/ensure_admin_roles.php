<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

$emails = ['admin@example.com', 'admin@gmail.com'];
foreach ($emails as $email) {
    $user = User::firstOrCreate(
        ['email' => $email],
        ['name' => 'Administrator', 'password' => bcrypt('password'), 'email_verified_at' => now()]
    );

    // Ensure the role exists
    $role = DB::table('roles')->where('name', 'admin')->first();
    if (!$role) {
        DB::table('roles')->insert(['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full access', 'permissions' => '[]', 'created_at' => now(), 'updated_at' => now()]);
        $role = DB::table('roles')->where('name', 'admin')->first();
    }

    // Attach role in user_roles if not exists
    $exists = DB::table('user_roles')->where('user_id', $user->id)->where('role_id', $role->id)->exists();
    if (!$exists) {
        DB::table('user_roles')->insert(['user_id' => $user->id, 'role_id' => $role->id, 'created_at' => now(), 'updated_at' => now()]);
        echo "Assigned admin role to {$email}\n";
    } else {
        echo "{$email} already has admin role\n";
    }
}
