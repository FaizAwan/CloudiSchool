<?php
$f = 'resources/views/layouts/app.blade.php';
$c = file_get_contents($f);
$c = preg_replace('/<script src="\{\{ asset\(\'dashra\/js\/jquery\.min\.js\'\)\}\}"\><\/script>/', '<!-- Removed duplicate jquery.min.js -->', $c);
file_put_contents($f, $c);
echo "Duplicate jQuery removed";
