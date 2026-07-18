<?php
/**
 * Laravel CORS Cache Clear & Verification Script
 * Upload this to your public folder on shared hosting
 * Access via: https://pulse.anvayabali.com/clear_cors_cache.php
 *
 * SECURITY: Delete this file after use!
 */

// Security check - only allow access from specific IP or with secret key
$secret_key = 'pulse_cors_2024'; // Change this to something secure
$access_granted = false;

// Check for secret key in URL
if (isset($_GET['key']) && $_GET['key'] === $secret_key) {
    $access_granted = true;
}

// Check for allowed IP (you can add your IP here)
$allowed_ips = ['127.0.0.1']; // Add your IP address here
if (in_array($_SERVER['REMOTE_ADDR'], $allowed_ips)) {
    $access_granted = true;
}

if (!$access_granted) {
    die('<h1>Access Denied</h1><p>Add ?key=pulse_cors_2024 to the URL or configure your IP in the script.</p>');
}

echo '<!DOCTYPE html>
<html>
<head>
    <title>Clear Laravel CORS Cache</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 20px auto; padding: 20px; }
        .success { color: green; }
        .error { color: red; }
        .warning { color: orange; }
        .info { color: blue; }
        pre { background: #f4f4f4; padding: 10px; overflow-x: auto; }
        .section { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>🔧 Laravel CORS Cache Clear & Verification</h1>';

$success = true;
$results = [];

// Function to clear directory
function clearDirectory($path) {
    if (!file_exists($path)) return false;

    $files = glob($path . '/*');
    $cleared = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            if (unlink($file)) {
                $cleared++;
            }
        }
    }
    return $cleared;
}

// 1. Clear Config Cache
echo '<div class="section">';
echo '<h2>1. Clearing Configuration Cache</h2>';
try {
    $configPaths = [
        base_path('bootstrap/cache/config.php'),
        base_path('bootstrap/cache/services.php'),
        storage_path('framework/cache/data/config.php'),
    ];

    $cleared = 0;
    foreach ($configPaths as $path) {
        if (file_exists($path)) {
            if (unlink($path)) {
                $cleared++;
                echo '<p class="success">✅ Deleted: ' . basename($path) . '</p>';
            }
        }
    }

    if ($cleared > 0) {
        echo '<p class="success">✅ Cleared ' . $cleared . ' config cache files</p>';
        $results['config_cache'] = true;
    } else {
        echo '<p class="warning">⚠️ No config cache files found (already cleared?)</p>';
        $results['config_cache'] = false;
    }
} catch (Exception $e) {
    echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
    $results['config_cache'] = false;
    $success = false;
}
echo '</div>';

// 2. Clear Application Cache
echo '<div class="section">';
echo '<h2>2. Clearing Application Cache</h2>';
try {
    $cleared = clearDirectory(storage_path('framework/cache/data'));
    if ($cleared > 0) {
        echo '<p class="success">✅ Cleared ' . $cleared . ' application cache files</p>';
        $results['app_cache'] = true;
    } else {
        echo '<p class="info">ℹ️ No application cache files found</p>';
        $results['app_cache'] = false;
    }
} catch (Exception $e) {
    echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
    $results['app_cache'] = false;
}
echo '</div>';

// 3. Clear View Cache
echo '<div class="section">';
echo '<h2>3. Clearing View Cache</h2>';
try {
    $cleared = clearDirectory(storage_path('framework/views'));
    if ($cleared > 0) {
        echo '<p class="success">✅ Cleared ' . $cleared . ' view cache files</p>';
        $results['view_cache'] = true;
    } else {
        echo '<p class="info">ℹ️ No view cache files found</p>';
        $results['view_cache'] = false;
    }
} catch (Exception $e) {
    echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
    $results['view_cache'] = false;
}
echo '</div>';

// 4. Verify CORS Configuration
echo '<div class="section">';
echo '<h2>4. Verifying CORS Configuration</h2>';
try {
    $corsConfig = require base_path('config/cors.php');

    echo '<h3>Current CORS Configuration:</h3>';
    echo '<pre>';
    echo 'Allowed Origins:' . PHP_EOL;
    foreach ($corsConfig['allowed_origins'] as $origin) {
        $hasVercel = strpos($origin, 'vercel.app') !== false;
        $marker = $hasVercel ? ' 🎯' : '';
        echo '  - ' . $origin . $marker . PHP_EOL;
    }
    echo '</pre>';

    $hasVercel = false;
    foreach ($corsConfig['allowed_origins'] as $origin) {
        if (strpos($origin, 'vercel.app') !== false) {
            $hasVercel = true;
            break;
        }
    }

    if ($hasVercel) {
        echo '<p class="success">✅ Vercel domain found in CORS configuration</p>';
        $results['cors_config'] = true;
    } else {
        echo '<p class="error">❌ Vercel domain NOT found in CORS configuration</p>';
        echo '<p class="warning">⚠️ Please add https://pulsev2-nu.vercel.app to config/cors.php</p>';
        $results['cors_config'] = false;
        $success = false;
    }
} catch (Exception $e) {
    echo '<p class="error">❌ Error reading CORS config: ' . $e->getMessage() . '</p>';
    $results['cors_config'] = false;
    $success = false;
}
echo '</div>';

// 5. Test CORS Headers
echo '<div class="section">';
echo '<h2>5. Testing CORS Headers</h2>';
try {
    $testUrl = 'https://pulse.anvayabali.com/api/login';
    $testOrigin = 'https://pulsev2-nu.vercel.app';

    echo '<p>Testing CORS preflight request to: <code>' . $testUrl . '</code></p>';
    echo '<p>Origin: <code>' . $testOrigin . '</code></p>';

    // Use curl if available
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

        if ($httpCode == 204 || $httpCode == 200) {
            // Check for CORS headers
            $hasCorsHeaders = (strpos($response, 'Access-Control-Allow-Origin') !== false);

            if ($hasCorsHeaders) {
                echo '<p class="success">✅ CORS headers detected in response!</p>';
                echo '<h4>Response Headers:</h4>';
                echo '<pre>';
                $headers = substr($response, 0, strpos($response, "\r\n\r\n"));
                echo $headers;
                echo '</pre>';
                $results['cors_test'] = true;
            } else {
                echo '<p class="error">❌ No CORS headers in response</p>';
                echo '<p class="warning">⚠️ Configuration might still be cached</p>';
                $results['cors_test'] = false;
                $success = false;
            }
        } else {
            echo '<p class="error">❌ Unexpected HTTP code: ' . $httpCode . '</p>';
            $results['cors_test'] = false;
            $success = false;
        }
    } else {
        echo '<p class="warning">⚠️ curl not available - automatic test skipped</p>';
        echo '<p class="info">💡 Manually test: <code>curl -X OPTIONS ' . $testUrl . ' -H "Origin: ' . $testOrigin . '" -v</code></p>';
    }
} catch (Exception $e) {
    echo '<p class="error">❌ Error testing CORS: ' . $e->getMessage() . '</p>';
    $results['cors_test'] = false;
}
echo '</div>';

// 6. Final Summary
echo '<div class="section">';
echo '<h2>6. Summary & Next Steps</h2>';

if ($success) {
    echo '<h3 class="success">✅ SUCCESS! All cache cleared and CORS configured correctly</h3>';
    echo '<p>Your Vercel app should now work properly!</p>';
    echo '<ol>
        <li>Test your Vercel app: <a href="https://pulsev2-nu.vercel.app/" target="_blank">https://pulsev2-nu.vercel.app/</a></li>
        <li>Login with: ak@ak.ak / 123456789</li>
        <li>Delete this file for security</li>
    </ol>';
} else {
    echo '<h3 class="error">❌ Issues Found - Please Review</h3>';
    echo '<p>Some problems were detected. Please review the errors above.</p>';
}

echo '<h4>Results Summary:</h4>';
echo '<ul>';
foreach ($results as $test => $passed) {
    $status = $passed ? '✅' : '❌';
    echo '<li>' . $status . ' ' . str_replace('_', ' ', ucfirst($test)) . '</li>';
}
echo '</ul>';

echo '<h4>Security Reminder:</h4>';
echo '<p class="warning">⚠️ <strong>Delete this file when done:</strong> /public/clear_cors_cache.php</p>';
echo '</div>';

echo '</body>
</html>';
?>