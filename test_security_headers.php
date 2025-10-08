<?php
/**
 * Security Headers Test Script
 * 
 * This script tests if the security headers are properly implemented
 * Run this script to verify all security headers are working correctly
 */

echo "<h1>Security Headers Test</h1>";
echo "<p>Testing security headers implementation...</p>";

// Get current headers
$headers = get_headers('https://mcc-nac.com/', 1);

echo "<h2>Security Headers Status:</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Header</th><th>Status</th><th>Value</th></tr>";

// Required security headers
$required_headers = [
    'Strict-Transport-Security' => 'max-age=31536000; includeSubDomains; preload',
    'Referrer-Policy' => 'strict-origin-when-cross-origin',
    'Permissions-Policy' => 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), speaker=(), vibrate=(), fullscreen=(self), sync-xhr=()',
    'X-Content-Type-Options' => 'nosniff',
    'X-Frame-Options' => 'DENY',
    'X-XSS-Protection' => '1; mode=block',
    'Content-Security-Policy' => 'default-src \'self\'',
    'X-Permitted-Cross-Domain-Policies' => 'none',
    'Cross-Origin-Embedder-Policy' => 'require-corp',
    'Cross-Origin-Opener-Policy' => 'same-origin',
    'Cross-Origin-Resource-Policy' => 'same-origin'
];

$all_good = true;

foreach ($required_headers as $header => $expected_value) {
    $status = '❌ Missing';
    $value = 'Not set';
    
    if (isset($headers[$header])) {
        $status = '✅ Present';
        $value = is_array($headers[$header]) ? $headers[$header][0] : $headers[$header];
        
        // Check if the value contains expected content
        if (strpos($value, 'max-age=31536000') !== false || 
            strpos($value, 'nosniff') !== false || 
            strpos($value, 'DENY') !== false ||
            strpos($value, 'strict-origin-when-cross-origin') !== false) {
            $status = '✅ Correct';
        }
    } else {
        $all_good = false;
    }
    
    echo "<tr>";
    echo "<td><strong>$header</strong></td>";
    echo "<td>$status</td>";
    echo "<td>" . htmlspecialchars($value) . "</td>";
    echo "</tr>";
}

echo "</table>";

// Check for removed headers
echo "<h2>Removed Headers:</h2>";
echo "<table border='1' cellpadding='5' cellspacing='0'>";
echo "<tr><th>Header</th><th>Status</th></tr>";

$removed_headers = ['X-Powered-By', 'Server'];
foreach ($removed_headers as $header) {
    $status = isset($headers[$header]) ? '❌ Still Present' : '✅ Removed';
    echo "<tr><td><strong>$header</strong></td><td>$status</td></tr>";
}

echo "</table>";

// Overall status
echo "<h2>Overall Security Status:</h2>";
if ($all_good) {
    echo "<p style='color: green; font-weight: bold;'>✅ All security headers are properly implemented!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Some security headers are missing or incorrect.</p>";
}

// Security recommendations
echo "<h2>Security Recommendations:</h2>";
echo "<ul>";
echo "<li><strong>HSTS:</strong> Enforces HTTPS connections for 1 year</li>";
echo "<li><strong>Referrer Policy:</strong> Controls referrer information leakage</li>";
echo "<li><strong>Permissions Policy:</strong> Restricts browser features and APIs</li>";
echo "<li><strong>CSP:</strong> Prevents XSS attacks by controlling resource loading</li>";
echo "<li><strong>Frame Options:</strong> Prevents clickjacking attacks</li>";
echo "<li><strong>Content Type Options:</strong> Prevents MIME type sniffing</li>";
echo "</ul>";

// Test HTTPS redirection
echo "<h2>HTTPS Redirection Test:</h2>";
$http_url = 'http://mcc-nac.com/';
$context = stream_context_create([
    'http' => [
        'method' => 'HEAD',
        'follow_location' => false,
        'ignore_errors' => true
    ]
]);

$response = file_get_contents($http_url, false, $context);
$http_code = null;
if (isset($http_response_header)) {
    foreach ($http_response_header as $header) {
        if (strpos($header, 'HTTP/') === 0) {
            $http_code = $header;
            break;
        }
    }
}

if (strpos($http_code, '301') !== false || strpos($http_code, '302') !== false) {
    echo "<p style='color: green;'>✅ HTTPS redirection is working: $http_code</p>";
} else {
    echo "<p style='color: red;'>❌ HTTPS redirection may not be working: $http_code</p>";
}

echo "<hr>";
echo "<p><em>Security headers test completed.</p>";
?>
