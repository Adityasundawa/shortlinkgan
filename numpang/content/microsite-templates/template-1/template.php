<?php

$data = $response->data ?? (object) [];
$buttons = isset($data->buttons) && is_array($data->buttons) ? $data->buttons : [];
$micrositePopUnder = isset($data->microsite_pop_under) && is_array($data->microsite_pop_under) ? $data->microsite_pop_under : [];
$title = $data->title ?? $data->custom_title ?? 'Microsite';
$description = $data->description ?? $data->custom_description ?? '';
$profileImage = $data->images ?? '';

?>
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


if (isset($response->data->pop_unders) && is_array($response->data->pop_unders) && count($response->data->pop_unders) > 0) {
    $popUnders = $response->data->pop_unders;
    $totalWeight = 0;
    $weightedUrls = [];

    foreach ($popUnders as $item) {
        $percentage = $item->precentage ?? 0;
        $url = $item->url ?? ($response->data->original_url ?? '#');
        $totalWeight += $percentage;
        for ($i = 0; $i < $percentage; $i++) {
            $weightedUrls[] = $url;
        }
    }

    if ($totalWeight < 100) {
        $remainingWeight = 100 - $totalWeight;
        for ($i = 0; $i < $remainingWeight; $i++) {
            $weightedUrls[] = $response->data->original_url ?? '#';
        }
    }
    
    if (!empty($weightedUrls)) {
        $randomIndex = array_rand($weightedUrls);
        $targetUrl = $weightedUrls[$randomIndex];
    }
} 
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <style>
        <?php include('./template-assets/template_1/css/style.css'); ?><?php

                                                                        if (!empty($data->images_background)) {
                                                                            $bg = $data->images_background;
                                                                        } else {
                                                                            $bg = 'https://sea-static.cdn.phobos.id/assets/svg/black_wallpaper.svg';
                                                                        } ?>.background_image {
            background-image: url(<?= $bg ?>);
            background-position: center;
            background-repeat: repeat;
            background-size: cover;
            position: fixed;
            top: -2.5rem;
            left: -2.5rem;
            right: -2.5rem;
            bottom: -2.5rem;
        }
    </style>
</head>

<body>
    <section class="main" style="--dark-color:#000;--light-color:#fff;background-color:rgb(209 213 219 / 1);--text-color:#fcfcfc;--link-color:51, 51, 51;--link-background:239, 239, 239;--link-shadow:102, 102, 102;--link-border:51, 51, 51;color:51, 51, 51;">
        <div class="background">
            <div class="background_image"></div>
        </div>
        <div class="bg_container">
            <div class="container"></div>
        </div>
        <div class="container">
            <div class="container_component">
                <div class="profile">
                    <div class="profile_image">
                        <img src="<?= htmlspecialchars($profileImage) ?>" alt="">
                    </div>
                    <div class="text text_center text_large">
                        <span style="font-weight:bold"><?= htmlspecialchars($title) ?></span>
                    </div>
                    <div class="text text_center">
                        <h4><?= htmlspecialchars($description); ?></h4>
                    </div>

                    <?php foreach ($buttons as $button) : ?>
                        <div class="container_link">
                            <div class="link_outer">

                                <?php

                                if (count($micrositePopUnder) == 0) {
                                ?>
                                    <a href="<?= htmlspecialchars($button->url ?? '#'); ?>" target="_blank" class="link link_circle link_circle_shadow">
                                        <div class="link_outer_text">
                                            <div class="text text_center">
                                                <strong><?= htmlspecialchars($button->title ?? 'Link'); ?></strong>
                                            </div>
                                        </div>
                                    </a>
                                <?php
                                } else { ?>
                                    <a href="javascript:void(0);" onclick="redirectLink(<?= htmlspecialchars(json_encode($button->url ?? '#')); ?>)" target="_blank" class="link link_circle link_circle_shadow">
                                        <div class="link_outer_text">
                                            <div class="text text_center">
                                                <strong><?= htmlspecialchars($button->title ?? 'Link'); ?></strong>
                                            </div>
                                        </div>
                                    </a>

                                <?php
                                }   ?>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
  <script src="https://cdn.jsdelivr.net/npm/@fingerprintjs/fingerprintjs@4/dist/fp.min.js"></script>
  <script>
    /**
     * ✅ Fungsi untuk mendapatkan ID dari localStorage atau membuatnya jika belum ada.
     * ID ini akan tetap sama untuk pengunjung yang sama.
     */
    function getOrSetVisitorSession() {
        let session = localStorage.getItem('visitor_session_id');
        if (!session) {
            session = `${Date.now()}-${Math.random().toString(36).substring(2, 11)}`;
            localStorage.setItem('visitor_session_id', session);
        }
        return session;
    }

    /**
     * Fungsi untuk melacak data pengunjung (Versi JSON Post).
     * Ini adalah skrip yang telah dimodifikasi.
     */
    (async function trackVisitor() {
        try {
            // ✅ Dapatkan ID unik dari localStorage
            const clientSession = getOrSetVisitorSession();

            // --- Dapatkan data IP dan Lokasi ---
            const locationResponse = await fetch('https://ipapi.co/json/');
            if (!locationResponse.ok) throw new Error('Gagal mengambil data lokasi.');
            const locationData = await locationResponse.json();
            
          
                  const fp = await FingerprintJS.load();
                 const result = await fp.get();
           const fingerprint_id = result.visitorId;
            const ua = navigator.userAgent;
            // Pastikan fungsi-fungsi ini ada di skrip Anda
            const getOperatingSystem = () => { /* ... fungsi Anda ... */ return "Unknown"; };
            const getBrowser = () => { /* ... fungsi Anda ... */ return "Unknown"; };
            const getDeviceType = () => /Mobi/i.test(ua) ? 'Mobile' : 'Desktop/Laptop';

            const pathSegments = window.location.pathname.split('/').filter(Boolean);
            const urlCode = pathSegments.length > 0 ? pathSegments[pathSegments.length - 1] : 'N/A';
            
             const serverData = {
            device_type: <?= json_encode($deviceType); ?>,
            country: <?= json_encode($country); ?>,
            city: <?= json_encode($city); ?>
        };


                  
                  
            // --- Buat payload sebagai Objek JSON ---
            const payload = {
                client_session: clientSession,
                ip_address: "192.168.1.1",
                domain: "<?= $_ENV['DOMAIN']; ?>",
                url_code: urlCode,
                type: "microsite",
                 country: serverData.country,
                city: serverData.city,
                operating_system: getOperatingSystem(),
                browser: getBrowser(),
                device_type: serverData.device_type,
                fingerprint_id: fingerprint_id,
                ...Object.fromEntries(new URLSearchParams(window.location.search).entries())
            };
            const apiToken = 'Bearer <?= $_ENV['API_KEY']; ?>'; 
            // --- Kirim data ke API sebagai JSON (seperti skrip kedua) ---
            const apiResponse = await fetch('<?= $_ENV['API_URL']; ?>traffict-microsite', {
                method: 'POST',
                headers: { 
                    'Authorization': apiToken, // Menggunakan Auth dari skrip lama
                    'Content-Type': 'application/json', // Metode dari skrip baru
                    'Accept': 'application/json'      // Metode dari skrip baru
                },
                body: JSON.stringify(payload) // Mengirim data sebagai JSON string
            });

            if (!apiResponse.ok) throw new Error(`API returned status: ${apiResponse.status}`);
            
            const responseData = await apiResponse.json();
            console.log('Tracking success (JSON). SessionID:', clientSession, 'Response:', responseData);

        } catch (error) {
            console.error('Terjadi kesalahan pada skrip pelacakan:', error);
        }
    })();

</script>
</body>
<?php

$microsite_pop_under = $micrositePopUnder;

function selectRandomWithPercentage($items)
{
    $totalPercentage = 0;

    // Hitung total presentase dari semua item
    foreach ($items as $item) {
        $totalPercentage += (int) ($item->precentage ?? 0);
    }

    if ($totalPercentage <= 0) {
        return null;
    }

    // Pilih angka acak antara 0 dan total presentase
    $randomNumber = mt_rand(0, $totalPercentage);

    $currentPercentage = 0;
    // Iterasi melalui setiap item
    foreach ($items as $item) {
        // Jumlahkan presentase pada setiap iterasi
        $currentPercentage += (int) ($item->precentage ?? 0);
        // Jika jumlah presentase melebihi angka acak yang dihasilkan, kembalikan item tersebut
        if ($randomNumber <= $currentPercentage) {
            return $item;
        }
    }
}

$randomData = null; // Inisialisasi
if (!empty($microsite_pop_under)) {
    $randomData = selectRandomWithPercentage($microsite_pop_under);
}

?>

<script>
    function appendCurrentQueryParams(url) {
        const currentParams = new URLSearchParams(window.location.search);

        if (!url || currentParams.size === 0) {
            return url;
        }

        try {
            const target = new URL(url, window.location.origin);
            currentParams.forEach((value, key) => {
                if (key.startsWith('utm_')) {
                    target.searchParams.set(key, value);
                }
            });

            return target.toString();
        } catch (error) {
            return url;
        }
    }

    function redirectLink(originalUrl) {
        // Pastikan $randomData ada dan memiliki properti url sebelum mengaksesnya
        const popUnderUrl = appendCurrentQueryParams(<?= json_encode($randomData->url ?? null); ?>);
        const destinationUrl = appendCurrentQueryParams(originalUrl);

        if (popUnderUrl && localStorage.getItem('visitedLink') !== 'true') {
            window.open(popUnderUrl, '_blank');
            localStorage.setItem('visitedLink', 'true');
        } else {
            window.location.href = destinationUrl;
        }
    }
</script>

</html>
