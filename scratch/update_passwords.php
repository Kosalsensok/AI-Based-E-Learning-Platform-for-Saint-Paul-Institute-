<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// 1. Update Kosal Sensok (Admin) password to Kosalsensokpk@12pk
$kosal = User::where('email', 'kosalsensok065@gmail.com')->first();
if ($kosal) {
    $kosal->update([
        'password' => Hash::make('Kosalsensokpk@12pk'),
        'role' => 'admin',
        'status' => 'active',
        'is_active' => true,
        'locked_until' => null,
        'login_attempts' => 0,
    ]);
    echo "Updated kosalsensok065@gmail.com password to Kosalsensokpk@12pk (Role: admin, Status: active)\n";
}

// 2. Ensure default demo accounts are active and unlocked
$accounts = [
    'admin@elms.com' => ['role' => 'admin', 'pass' => 'password'],
    'teacher@elms.com' => ['role' => 'teacher', 'pass' => 'password'],
    'student@elms.com' => ['role' => 'student', 'pass' => 'password'],
];

foreach ($accounts as $email => $info) {
    $user = User::where('email', $email)->first();
    if ($user) {
        $user->update([
            'password' => Hash::make($info['pass']),
            'role' => $info['role'],
            'status' => 'active',
            'is_active' => true,
            'locked_until' => null,
            'login_attempts' => 0,
        ]);
        echo "Reset {$email} with password '{$info['pass']}' and role '{$info['role']}'\n";
    }
}
