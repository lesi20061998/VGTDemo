<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\ProjectPasswordService;
use App\Models\Project;

echo "Verifying Task 2: ProjectPasswordService Implementation...\n";

$service = app(ProjectPasswordService::class);

// Test 1: Check if service is properly registered
echo "\n1. Service Registration: ";
echo $service instanceof ProjectPasswordService ? "✅ PASS" : "❌ FAIL";

// Test 2: Check password validation
echo "\n2. Password Validation: ";
$weakErrors = $service->validatePassword('123');
$strongErrors = $service->validatePassword('StrongPass123!');
echo (count($weakErrors) > 0 && count($strongErrors) === 0) ? "✅ PASS" : "❌ FAIL";

// Test 3: Check password generation
echo "\n3. Password Generation: ";
$generated = $service->generateSecurePassword();
$generatedErrors = $service->validatePassword($generated);
echo (strlen($generated) >= 8 && count($generatedErrors) === 0) ? "✅ PASS" : "❌ FAIL";

// Test 4: Check if projects have migrated passwords
echo "\n4. Password Migration: ";
$projectsWithPlainPasswords = Project::whereNotNull('project_admin_password_plain')->count();
echo ($projectsWithPlainPasswords > 0) ? "✅ PASS ($projectsWithPlainPasswords projects)" : "❌ FAIL";

// Test 5: Check if we can decrypt a password
echo "\n5. Password Decryption: ";
$project = Project::whereNotNull('project_admin_password_plain')->first();
if ($project) {
    $decrypted = $service->getPlainPassword($project);
    echo ($decrypted !== null && strlen($decrypted) > 0) ? "✅ PASS" : "❌ FAIL";
} else {
    echo "❌ FAIL (No projects with plain passwords)";
}

echo "\n\nTask 2 Verification Complete!\n";