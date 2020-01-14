<?php

$GLOBALS['csrf']['defer'] = false;

$GLOBALS['csrf']['expires'] = 7200;

$GLOBALS['csrf']['callback'] = 'csrf_callback';

$GLOBALS['csrf']['rewrite-js'] = false;

$GLOBALS['csrf']['secret'] = '';

$GLOBALS['csrf']['rewrite'] = true;

$GLOBALS['csrf']['allow-ip'] = true;

$GLOBALS['csrf']['cookie'] = '__csrf_cookie';

$GLOBALS['csrf']['user'] = false;

$GLOBALS['csrf']['key'] = false;

$GLOBALS['csrf']['input-name'] = '__csrf_magic';

$GLOBALS['csrf']['frame-breaker'] = true;

$GLOBALS['csrf']['auto-session'] = true;

$GLOBALS['csrf']['xhtml'] = true;

$GLOBALS['csrf']['version'] = '1.0.4';

function csrf_ob_handler($buffer, $flags) {

    static $is_html = false;
    if (!$is_html) {

        if (stripos($buffer, '<html') !== false) {
            $is_html = true;
        } else {
            return $buffer;
        }
    }
    $tokens = csrf_get_tokens();
    $name = $GLOBALS['csrf']['input-name'];
    $endslash = $GLOBALS['csrf']['xhtml'] ? ' /' : '';
    $input = "<input type='hidden' name='$name' value=\"$tokens\"$endslash>";
    $buffer = preg_replace('#(<form[^>]*method\s*=\s*["\']post["\'][^>]*>)#i', '$1' . $input, $buffer);
    if ($GLOBALS['csrf']['frame-breaker']) {
        $buffer = str_ireplace('</head>', '<script type="text/javascript">if (top != self) {top.location.href = self.location.href;}</script></head>', $buffer);
    }
    if ($js = $GLOBALS['csrf']['rewrite-js']) {
        $buffer = str_ireplace(
            '</head>',
            '<script type="text/javascript">'.
                'var csrfMagicToken = "'.$tokens.'";'.
                'var csrfMagicName = "'.$name.'";</script>'.
            '<script src="'.$js.'" type="text/javascript"></script></head>',
            $buffer
        );
        $script = '<script type="text/javascript">CsrfMagic.end();</script>';
        $buffer = str_ireplace('</body>', $script . '</body>', $buffer, $count);
        if (!$count) {
            $buffer .= $script;
        }
    }
    return $buffer;
}

function csrf_check($fatal = true) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return true;
    csrf_start();
    $name = $GLOBALS['csrf']['input-name'];
    $ok = false;
    $tokens = '';
    do {
        if (!isset($_POST[$name])) break;

        $tokens = $_POST[$name];
        if (!csrf_check_tokens($tokens)) break;
        $ok = true;
    } while (false);
    if ($fatal && !$ok) {
        $callback = $GLOBALS['csrf']['callback'];
        if (trim($tokens, 'A..Za..z0..9:;,') !== '') $tokens = 'hidden';
        $callback($tokens);
        exit;
    }
    return $ok;
}

function csrf_get_tokens() {
    $has_cookies = !empty($_COOKIE);

    $secret = csrf_get_secret();
    if (!$has_cookies && $secret) {

        $IP_ADDRESS = (isset($_SERVER['IP_ADDRESS']) ? $_SERVER['IP_ADDRESS'] : $_SERVER['REMOTE_ADDR']);
        $ip = ';ip:' . csrf_hash($IP_ADDRESS);
    } else {
        $ip = '';
    }
    csrf_start();

    if (session_id()) return 'sid:' . csrf_hash(session_id()) . $ip;
    if ($GLOBALS['csrf']['cookie']) {
        $val = csrf_generate_secret();
        setcookie($GLOBALS['csrf']['cookie'], $val);
        return 'cookie:' . csrf_hash($val) . $ip;
    }
    if ($GLOBALS['csrf']['key']) return 'key:' . csrf_hash($GLOBALS['csrf']['key']) . $ip;

    if (!$secret) return 'invalid';
    if ($GLOBALS['csrf']['user'] !== false) {
        return 'user:' . csrf_hash($GLOBALS['csrf']['user']);
    }
    if ($GLOBALS['csrf']['allow-ip']) {
        return ltrim($ip, ';');
    }
    return 'invalid';
}
function csrf_flattenpost($data) {
    $ret = array();
    foreach($data as $n => $v) {
        $ret = array_merge($ret, csrf_flattenpost2(1, $n, $v));
    }
    return $ret;
}
function csrf_flattenpost2($level, $key, $data) {
    if(!is_array($data)) return array($key => $data);
    $ret = array();
    foreach($data as $n => $v) {
        $nk = $level >= 1 ? $key."[$n]" : "[$n]";
        $ret = array_merge($ret, csrf_flattenpost2($level+1, $nk, $v));
    }
    return $ret;
}

function csrf_callback($tokens) {

    header($_SERVER['SERVER_PROTOCOL'] . ' 403 Forbidden');
    $data = '';
    foreach (csrf_flattenpost($_POST) as $key => $value) {
        if ($key == $GLOBALS['csrf']['input-name']) continue;
        $data .= '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'" />';
    }
    echo "<html><head><title>CSRF check failed</title></head>
        <body>
        <p>CSRF check failed. Your form session may have expired, or you may not have
        cookies enabled.</p>
        <form method='post' action=''>$data<input type='submit' value='Try again' /></form>
        <p>Debug: $tokens</p></body></html>
";
}

function csrf_check_tokens($tokens) {
    if (is_string($tokens)) $tokens = explode(';', $tokens);
    foreach ($tokens as $token) {
        if (csrf_check_token($token)) return true;
    }
    return false;
}

function csrf_check_token($token) {
    if (strpos($token, ':') === false) return false;
    list($type, $value) = explode(':', $token, 2);
    if (strpos($value, ',') === false) return false;
    list($x, $time) = explode(',', $token, 2);
    if ($GLOBALS['csrf']['expires']) {
        if (time() > $time + $GLOBALS['csrf']['expires']) return false;
    }
    switch ($type) {
        case 'sid':
            return $value === csrf_hash(session_id(), $time);
        case 'cookie':
            $n = $GLOBALS['csrf']['cookie'];
            if (!$n) return false;
            if (!isset($_COOKIE[$n])) return false;
            return $value === csrf_hash($_COOKIE[$n], $time);
        case 'key':
            if (!$GLOBALS['csrf']['key']) return false;
            return $value === csrf_hash($GLOBALS['csrf']['key'], $time);

        case 'user':
            if (!csrf_get_secret()) return false;
            if ($GLOBALS['csrf']['user'] === false) return false;
            return $value === csrf_hash($GLOBALS['csrf']['user'], $time);
        case 'ip':
            if (!csrf_get_secret()) return false;

            if ($GLOBALS['csrf']['user'] !== false) return false;
            if (!empty($_COOKIE)) return false;
            if (!$GLOBALS['csrf']['allow-ip']) return false;
            $IP_ADDRESS = (isset($_SERVER['IP_ADDRESS']) ? $_SERVER['IP_ADDRESS'] : $_SERVER['REMOTE_ADDR']);
            return $value === csrf_hash($IP_ADDRESS, $time);
    }
    return false;
}

function csrf_conf($key, $val) {
    if (!isset($GLOBALS['csrf'][$key])) {
        trigger_error('No such configuration ' . $key, E_USER_WARNING);
        return;
    }
    $GLOBALS['csrf'][$key] = $val;
}

function csrf_start() {
    if ($GLOBALS['csrf']['auto-session'] && !session_id()) {
        session_start();
    }
}

function csrf_get_secret() {
    if ($GLOBALS['csrf']['secret']) return $GLOBALS['csrf']['secret'];
    $dir = dirname(__FILE__);
    $file = $dir . '/csrf-secret.php';
    $secret = '';
    if (file_exists($file)) {
        include $file;
        return $secret;
    }
    if (is_writable($dir)) {
        $secret = csrf_generate_secret();
        $fh = fopen($file, 'w');
        fwrite($fh, '<?php $secret = "'.$secret.'";' . PHP_EOL);
        fclose($fh);
        return $secret;
    }
    return '';
}

function csrf_generate_secret($len = 32) {
    $r = '';
    for ($i = 0; $i < $len; $i++) {
        $r .= chr(mt_rand(0, 255));
    }
    $r .= time() . microtime();
    return sha1($r);
}

function csrf_hash($value, $time = null) {
    if (!$time) $time = time();
    return sha1(csrf_get_secret() . $value . $time) . ',' . $time;
}

if (function_exists('csrf_startup')) csrf_startup();

if ($GLOBALS['csrf']['rewrite'])     ob_start('csrf_ob_handler');

if (!$GLOBALS['csrf']['defer'])      csrf_check();
