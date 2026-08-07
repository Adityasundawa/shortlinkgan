<?php
error_reporting(E_ALL & ~E_DEPRECATED);
session_start();
require_once realpath(__DIR__ . '/vendor/autoload.php');
require_once realpath(__DIR__ . '/inc/CustomHelper.php');

// Looing for .env at the root directory
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

# GET IP Client

$custom_helper = new CustomHelper;
$client_ip = $custom_helper->getClientIP();
$configuredDomain = $_ENV['DOMAIN'] ?? ($_SERVER['SERVER_NAME'] ?? '');
$apiDomain = parse_url($configuredDomain, PHP_URL_HOST) ?: $configuredDomain;
$apiDomain = preg_replace('/^www\./', '', strtolower($apiDomain));

$currentUrl = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/'; // get path from current link

// Parse the URL to get the custom parameter value
$urlParts = array_values(array_filter(explode('/', trim($currentUrl, '/'))));
$customParamValue = end($urlParts) ?: '';

if ($customParamValue === '') {
    include('content/content-welcome.php');
    exit;
}

$sessionName = $apiDomain . $customParamValue;

if (!isset($_SESSION[$sessionName]) || empty($_SESSION[$sessionName])) {
    // set session client_session
    $_SESSION[$sessionName] = date('Ymd') . $customParamValue . rand(100, 999);
}

$curl = curl_init();
$headers = array_filter(array(
    'Authorization: Bearer ' . ($_ENV['API_KEY'] ?? ''),
    !empty($_ENV['API_X_APIKEY']) ? 'x-apikey: ' . $_ENV['API_X_APIKEY'] : null,
));
curl_setopt_array($curl, array(
    CURLOPT_URL => $_ENV['API_URL'] . $customParamValue,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('ip_address' => $_SESSION[$sessionName], 'domain' => $apiDomain),
    CURLOPT_HTTPHEADER => $headers,
    CURLOPT_SSL_VERIFYPEER => false,  // Add this line to ignore SSL verification
));

$rawResponse = curl_exec($curl);
$curlError = curl_error($curl);
$response = $rawResponse ? json_decode($rawResponse) : null;


curl_close($curl);

$isValidResponse = $response
    && json_last_error() === JSON_ERROR_NONE
    && isset($response->status)
    && $response->status === 'success'
    && isset($response->type)
    && isset($response->data);

if (! $isValidResponse) {
    error_log('Invalid API response for code='.$customParamValue.' domain='.$apiDomain.': '.($curlError ?: ($rawResponse ?: 'empty response')));
    include('content/content-404.php');
    exit;
}

$sessionNameWithType = $apiDomain . $customParamValue . $response->type;

// function getVisitorIp() {
//     $ip = '127.0.0.1';
//     if (isset($_SERVER['HTTP_CLIENT_IP'])) {
//         $ip = $_SERVER['HTTP_CLIENT_IP'];
//     } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//         $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
//     } elseif (isset($_SERVER['HTTP_X_REAL_IP'])) {
//         $ip = $_SERVER['HTTP_X_REAL_IP'];
//     } elseif (isset($_SERVER['REMOTE_ADDR'])) {
//         $ip = $_SERVER['REMOTE_ADDR'];
//     }
//     return filter_var(trim($ip), FILTER_VALIDATE_IP);
// }



// if (!isset($_SESSION[$sessionNameWithType]) || empty($_SESSION[$sessionNameWithType])) {

//     // ===================================================
//     // 1. Gather Visitor Information Here
//     // ===================================================

//     // Panggil fungsi untuk mendapatkan IP pengunjung yang sebenarnya
//     $visitorIp = getVisitorIp(); // <-- MODIFIKASI: Menggunakan fungsi

//     // Get Geolocation from IP (pastikan IP yang didapat adalah IP publik)
//     $country = 'Unknown';
//     $city = 'Unknown';
//     if ($visitorIp && filter_var($visitorIp, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
//         // Hanya panggil API jika IP valid dan bukan IP lokal/private
//         $locationData = json_decode(file_get_contents("http://ip-api.com/json/{$visitorIp}"));
//         if ($locationData && $locationData->status == 'success') {
//             $country = $locationData->country;
//             $city = $locationData->city;
//         }
//     }
    
//     // ... (sisa kode Anda untuk device detection, dll. tetap sama) ...
//     $userAgent = $_SERVER['HTTP_USER_AGENT'];
//     $isMobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|rim)|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent);
//     $deviceType = $isMobile ? 'mobile' : 'desktop';

//     // ===================================================
//     // 2. Call Your Laravel API with All the Data
//     // ===================================================
    
//     $curl = curl_init();

//     // Data yang akan dikirim tetap sama
//     $postData = [
//         'domain' => $_SERVER['SERVER_NAME'],
//         'url_code' => $customParamValue,
//         'type' => $response->type,
//         'country' => $country,
//         'city' => $city,
//         'device_type' => $deviceType,
//     ];

//     // ... sisa kode cURL Anda ...
//     curl_setopt_array($curl, array(
//         CURLOPT_URL => $_ENV['API_URL'] . 'traffict',
//         CURLOPT_RETURNTRANSFER => true,
//         CURLOPT_ENCODING => '',
//         CURLOPT_MAXREDIRS => 10,
//         CURLOPT_TIMEOUT => 0,
//         CURLOPT_FOLLOWLOCATION => true,
//         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//         CURLOPT_CUSTOMREQUEST => 'POST',
//         CURLOPT_POSTFIELDS => $postData,
//         CURLOPT_HTTPHEADER => array(
//             'Authorization: Bearer ' . $_ENV['API_KEY'],
//         ),
//     ));

//     $resTraffict = curl_exec($curl);
//     curl_close($curl);
    
//     $_SESSION[$sessionNameWithType] = true;
// }

if ($response->type == 'default') {
    include('content/content-redirect.php');
} else {
    include('content/content-microsite.php');
}
