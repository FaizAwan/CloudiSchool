<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== ADMIN PASSWORD RESET ===" . PHP_EOL;
echo PHP_EOL;

// Reset superadmin password
$superadmin = User::where('email', 'superadmin@school.com')->first();
if ($superadmin) {
    $superadmin->update(['password' => bcrypt('superadmin123')]);
    echo "✅ Superadmin password reset!" . PHP_EOL;
    echo "Email: superadmin@school.com" . PHP_EOL;
    echo "New Password: superadmin123" . PHP_EOL;
    echo "---" . PHP_EOL;
}

// Reset admin password
$admin = User::where('email', 'admin@school.com')->first();
if ($admin) {
    $admin->update(['password' => bcrypt('admin123')]);
    echo "✅ Admin password reset!" . PHP_EOL;
    echo "Email: admin@school.com" . PHP_EOL;
    echo "New Password: admin123" . PHP_EOL;
    echo "---" . PHP_EOL;
}

echo PHP_EOL;
echo "🚀 You can now login with these credentials!" . PHP_EOL;
echo PHP_EOL;
echo "📝 Remember:" . PHP_EOL;
echo "• Superadmin = Full access to all features" . PHP_EOL;
echo "• Admin = Restricted access (only sees access info page)" . PHP_EOL;
echo "• Students = Use format student[rollnumber]@school.edu / pass_[rollnumber]" . PHP_EOL;