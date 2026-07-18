<?php
/**
 * Laravel Cache Clear Script for Shared Hosting
 * Upload to: public/clear_cache.php
 * Access: https://pulse.anvayabali.com/clear_cache.php
 * DELETE AFTER USE!
 */

// Simple security - access via URL parameter
$secret = 'pulse2024';
if (!isset($_GET['key']) || $_GET['key'] !== $secret) {
    die('Access denied. Add ?key=pulse2024 to URL');
}

echo '<h1>🔧 Clearing Laravel Cache...</h1>';

// Function to safely delete file
function deleteFile($path) {
    if (file_exists($path)) {
        if (unlink($path)) {
            return true;
        }
    }
    return false;
}

// Function to clear directory
function clearDirectory($path) {
    if (!file_exists($path)) return 0;
    $files = glob($path . '/*');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    return $count;
}

echo '<h2>📁 Clearing Configuration Cache</h2>';

$configFiles = [
    base_path('bootstrap/cache/config.php'),
    base_path('bootstrap/cache/services.php'),
    base_path('bootstrap/cache/routes-v7.php'),
    storage_path('framework/cache/data/config.php'),
];

$configCleared = 0;
foreach ($configFiles as $file) {
    if (deleteFile($file)) {
        echo '✅ Deleted: ' . basename($file) . '<br>';
        $configCleared++;
    }
}

if ($configCleared > 0) {
    echo '<p>✅ Cleared ' . $configCleared . ' config cache files</p>';
} else {
    echo '<p>⚠️ No config cache files found</p>';
}

echo '<h2>📁 Clearing Application Cache</h2>';

$appCacheCleared = clearDirectory(storage_path('framework/cache/data'));
if ($appCacheCleared > 0) {
    echo '<p>✅ Cleared ' . $appCacheCleared . ' application cache files</p>';
} else {
    echo '<p>⚠️ No application cache files found</p>';
}

echo '<h2>📁 Clearing View Cache</h2>';

$viewCacheCleared = clearDirectory(storage_path('framework/views'));
if ($viewCacheCleared > 0) {
    echo '<p>✅ Cleared ' . $viewCacheCleared . ' view cache files</p>';
} else {
    echo '<p>⚠️ No view cache files found</p>';
}

echo '<hr>';
echo '<h2>✅ Cache Clear Complete!</h2>';
echo '<p><strong>Total files cleared:</strong> ' . ($configCleared + $appCacheCleared + $viewCacheCleared) . '</p>';

echo '<hr>';
echo '<h3>🧪 Test CORS Headers</h3>';

$testUrl = 'https://pulse.anvayabali.com/api/login';
$testOrigin = 'https://pulsev2-nu.vercel.app';

echo '<p>Testing CORS for: <code>' . $testOrigin . '</code></p>';

if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $testUrl);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'OPTIONS');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Origin: ' . $testOrigin,
        'Access-Control-Request-Method: POST',
        'Access-Control-Request-Headers: Content-Type, Authorization'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Check for CORS headers
    $hasAllowOrigin = strpos($response, 'Access-Control-Allow-Origin') !== false;
    $hasVercelOrigin = strpos($response, 'pulsev2-nu.vercel.app') !== false;

    if ($hasVercelOrigin) {
        echo '<p style="color: green; font-weight: bold;">✅ CORS WORKING! Vercel origin allowed</p>';
        echo '<h4>CORS Headers Found:</h4>';
        echo '<pre>';
        $lines = explode("\n", $response);
        foreach ($lines as $line) {
            if (strpos($line, 'Access-Control') !== false) {
                echo htmlspecialchars($line) . "\n";
            }
        }
        echo '</pre>';
    } elseif ($hasAllowOrigin) {
        echo '<p style="color: orange; font-weight: bold;">⚠️ CORS present but Vercel domain not found</p>';
        echo '<p>Check that config/cors.php includes: <code>https://pulsev2-nu.vercel.app</code></p>';
    } else {
        echo '<p style="color: red; font-weight: bold;">❌ CORS headers not found</p>';
        echo '<p>Configuration might still be cached. Try running this script again.</p>';
    }
} else {
    echo '<p>⚠️ curl not available - automatic test skipped</p>';
}

echo '<hr>';
echo '<h3>📝 Next Steps</h3>';
echo '<ol>';
echo '<li>Test your Vercel app: <a href="https://pulsev2-nu.vercel.app/" target="_blank">https://pulsev2-nu.vercel.app/</a></li>';
echo '<li>Login with: <strong>ak@ak.ak</strong> / <strong>123456789</strong></li>';
echo '<li>Check browser console - should show successful API calls</li>';
echo '<li><strong>DELETE THIS FILE when done!</strong></li>';
echo '</ol>';

echo '<hr>';
echo '<p style="color: red; font-weight: bold;">⚠️ SECURITY WARNING: Delete this file (clear_cache.php) when you are done!</p>';
?>