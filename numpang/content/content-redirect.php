<?php
// =================================================================
// BAGIAN 1: PENGUMPULAN DATA & LOGIKA REDIRECT (SERVER-SIDE)
// =================================================================

// Asumsi $response sudah ada dari skrip utama yang memanggil file ini
// require_once("inc/RedirectUrl.php");
// $redirectURL = new RedirectUrl;

// --- A. Logika Pengumpulan Data Pelacakan ---

// Fungsi untuk mendapatkan Alamat IP Pengguna
function getIpAddress() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip === '::1' || $ip === '127.0.0.1' ? '114.122.10.19' : $ip; // Contoh IP publik untuk tes di localhost
}

// Fungsi untuk mendeteksi jenis perangkat
function getDeviceType() {
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $isMobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|rim)|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent)
              || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($userAgent, 0, 4));
    return $isMobile ? 'Mobile' : 'Desktop';
}

function getRequestUtmParams() {
    $utmParams = [];

    foreach (['utm_campaign', 'utm_medium', 'utm_source', 'utm_content', 'utm_term'] as $key) {
        if (isset($_GET[$key]) && $_GET[$key] !== '') {
            $utmParams[$key] = substr((string) $_GET[$key], 0, 255);
        }
    }

    return $utmParams;
}

function appendQueryParams($url, array $params) {
    if (empty($params) || empty($url)) {
        return $url;
    }

    $separator = strpos($url, '?') === false ? '?' : '&';

    return $url . $separator . http_build_query($params);
}

$userIp = getIpAddress();
$deviceType = getDeviceType();

$locationApiUrl = "http://ip-api.com/json/{$userIp}";
$locationData = json_decode(@file_get_contents($locationApiUrl));

$country = "Unknown";
$city = "Unknown";
if ($locationData && $locationData->status == 'success') {
    $country = $locationData->country;
    $city = $locationData->city;
}
$requestUtmParams = getRequestUtmParams();

// --- B. Logika Pemilihan URL (Weighted Random Redirect) ---
$data = $response->data ?? (object) [];
$targetUrl = $data->original_url ?? ($_ENV['URL'] ?? '/'); // Default ke original_url

if (isset($data->pop_unders) && is_array($data->pop_unders) && count($data->pop_unders) > 0) {
    $popUnders = $data->pop_unders;
    $totalWeight = 0;
    $weightedUrls = [];

    foreach ($popUnders as $item) {
        $percentage = $item->precentage ?? 0;
        $url = $item->url ?? $targetUrl;
        $totalWeight += $percentage;
        for ($i = 0; $i < $percentage; $i++) {
            $weightedUrls[] = $url;
        }
    }

    if ($totalWeight < 100) {
        $remainingWeight = 100 - $totalWeight;
        for ($i = 0; $i < $remainingWeight; $i++) {
            $weightedUrls[] = $targetUrl;
        }
    }
    
    if (!empty($weightedUrls)) {
        $randomIndex = array_rand($weightedUrls);
        $targetUrl = $weightedUrls[$randomIndex];
    }
} 

$targetUrl = appendQueryParams($targetUrl, $requestUtmParams);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($data->custom_title ?? 'Redirecting...'); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="shortcut icon" href="assets/images/icon.png" type="image/x-icon">

    <meta name="csrf-token" content="">
    
    <meta name="keywords" content="microsite, redirector">
    <meta name="description" content="<?php echo htmlspecialchars($data->custom_description ?? ''); ?>">

    <meta property="og:title" content="<?php echo htmlspecialchars($data->custom_title ?? ''); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($data->custom_description ?? ''); ?>" />

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo htmlspecialchars($data->custom_title ?? ''); ?>">
    <meta name="twitter:description" content="<?php echo htmlspecialchars($data->custom_description ?? ''); ?>">
    <meta name="twitter:image" content="<?php echo htmlspecialchars(($_ENV['URL'] ?? '') . '/public/' . ($data->images_background ?? '')); ?>">
    <meta property="og:image" content="<?php echo htmlspecialchars(($_ENV['URL'] ?? '') . '/public/' . ($data->images_background ?? '')); ?>" />
</head>
<body>
    <div class="container">
        <div class="content">
            <div class="custom-loader"></div>
            <div class="caption">Redirecting ...</div>
        </div>
    </div>

    <script>
        // --- Variabel yang Diteruskan dari PHP ---
        const redirectUrl = <?= json_encode($targetUrl); ?>;
        const redirectDelay = <?= (int) ($_ENV['TIME_REDIRECT'] ?? 3) * 1000; ?>;
        
        const serverData = {
            device_type: <?= json_encode($deviceType); ?>,
            country: <?= json_encode($country); ?>,
            city: <?= json_encode($city); ?>
        };

        /**
         * Fungsi utama yang akan dipanggil oleh 'onload' setelah FingerprintJS siap.
         */
        function initializePage() {
            startRedirectTimer();
            collectAndSendTrackingData(); // Memulai pengumpulan data tracking
        }

        /**
         * Memulai timer untuk mengalihkan pengguna.
         */
        function startRedirectTimer() {
            setTimeout(() => {
                window.location.href = redirectUrl;
            }, redirectDelay);
        }

        /**
         * Mengumpulkan data dan mengirimkannya ke API.
         */
        async function collectAndSendTrackingData() {
            try {
                const path = window.location.pathname;
                const url_code = path.substring(path.lastIndexOf('/') + 1);

                const fp = await FingerprintJS.load();
                const result = await fp.get();
                const fingerprint_id = result.visitorId;

                const payload = {
                    url_code: url_code || 'default',
                    domain: <?= json_encode($_ENV['DOMAIN'] ?? 'unknown_domain'); ?>, 
                    country: serverData.country,
                    city: serverData.city,
                    device_type: serverData.device_type,
                    fingerprint_id: fingerprint_id,
                    ...Object.fromEntries(new URLSearchParams(window.location.search).entries())
                };
                
                console.log("Data tracking yang akan dikirim:", payload);
                sendDataToApi(payload);

            } catch (error) {
                console.error('Gagal mengumpulkan data tracking:', error);
            }
        }

        /**
         * Mengirim data payload ke API menggunakan Fetch.
         */
        function sendDataToApi(data) {
            const trackingApiUrl = <?= json_encode(($_ENV['API_URL'] ?? 'http://127.0.0.1:8000') . 'traffict'); ?>;
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch(trackingApiUrl, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...(csrfToken && {'X-CSRF-TOKEN': csrfToken})
                },
                body: JSON.stringify(data)
            })
            .then(response => {
                if (!response.ok) console.error('API response was not ok:', response.statusText);
                return response.json();
            })
            .then(result => console.log('Tracking data sent successfully:', result))
            .catch(error => console.error('Failed to send tracking data:', error));
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@4/dist/fp.min.js" onload="initializePage()"></script>
</body>
</html>
