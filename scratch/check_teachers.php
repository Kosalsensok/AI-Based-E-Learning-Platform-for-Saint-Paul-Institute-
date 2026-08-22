<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

$u = User::where('email', 'teacher@elms.com')->first();
if ($u) {
    echo "Teacher: " . $u->email . " | Role: " . $u->role . " | Status: " . $u->status . " | Active: " . ($u->is_active ? '1' : '0') . "\n";
    echo "Password 'password' matches: " . (Hash::check('password', $u->password) ? 'YES' : 'NO') . "\n";
    echo "Password '12345678' matches: " . (Hash::check('12345678', $u->password) ? 'YES' : 'NO') . "\n";
    echo "Password 'password123' matches: " . (Hash::check('password123', $u->password) ? 'YES' : 'NO') . "\n";
} else {
    echo "Teacher not found\n";
}
