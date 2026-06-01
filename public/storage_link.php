<?php
/**
 * Laravel Storage Link Helper
 * URL: https://cloudischool.com/storage_link.php
 */

// Try to determine the base path
$basePath = realpath(__DIR__ . '/..');
$target = $basePath . '/storage/app/public';
$link = __DIR__ . '/storage';

echo "<h3>Laravel Storage Link System Diagnostics</h3>";
echo "<b>Current Directory:</b> " . __DIR__ . "<br>";
echo "<b>Base Path Detected:</b> " . $basePath . "<br>";
echo "<b>Target Path (Actual Files):</b> " . $target . " " . (file_exists($target) ? "<span style='color:green'>(Exists)</span>" : "<span style='color:red'>(NOT FOUND)</span>") . "<br>";
echo "<b>Link Path (Shortcut):</b> " . $link . "<br><br>";

// Check if symlink function exists
if (!function_exists('symlink')) {
    echo "<b style='color:red;'>ERROR: The PHP 'symlink()' function is disabled on your hosting.</b><br>";
    echo "You must ask your hosting support to enable it, or use a 'Storage Proxy' workaround.<br>";
    exit;
}

if (file_exists($link)) {
    if (is_link($link)) {
        echo "<span style='color: orange;'>The link [public/storage] already exists as a symbolic link. Removing it first...</span><br>";
        if (@unlink($link)) {
            echo "Old link removed.<br>";
        } else {
            echo "<span style='color:red;'>Failed to remove old link. Check folder permissions.</span><br>";
            exit;
        }
    } else {
        echo "<span style='color: red;'><b>CRITICAL ERROR:</b> [public/storage] exists but it is a REAL FOLDER, not a link.</span><br>";
        echo "Please use your File Manager/FTP to <b>DELETE the folder</b> named 'storage' inside your 'public' directory, then refresh this page.<br>";
        exit;
    }
}

echo "Attempting to create link...<br>";
if (@symlink($target, $link)) {
    echo "<h2 style='color: green;'>SUCCESS!</h2>";
    echo "The storage link has been created. Refresh your gallery now.";
} else {
    $err = error_get_last();
    echo "<h2 style='color: red;'>FAILED!</h2>";
    echo "Reason: " . ($err['message'] ?? 'Unknown error. Possibly permissions or disabled function.');
    echo "<br><br><b>Workaround:</b> Use your Hosting File Manager (not FTP) to create a 'Symbolic Link' manually if they provide a UI for it.";
}
