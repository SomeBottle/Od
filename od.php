<?php
$link = @$_GET['l']; /*获得请求参数*/
$sharelk = @$_POST['bs'];
$customlk = @$_POST['cus'];
$secret = @$_POST['sc'];
date_default_timezone_set("Asia/Shanghai");
/*---Settings---*/
$jump = false; /*是否直接重定向*/
$finallink = 'od.php?l={link}'; /*设置生成后给出的链接，{link}是生成的链接*/
$restime = 5; /*每x分钟计算一次限频*/
$respermin = 5; /*每个资源文件每x分钟可以被访问几次*/
$strPol = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789abcdefghijklmnopqrstuvwxyz'; /*短链接字符集*/
$shortstart = 2; /*短链接开始的位数*/
$key = '179ad45c6ce2cb97cf1029e212046e81'; /*md5加密的key*/
/*---SettingsEnd---*/
function multip($un, $n) { /*计算短链接可能性(短链接字符数,总随机字符数)*/
    return pow($n, $un);
}
function grc($length) { /*CharacterGenerator*/
    global $strPol;
    $str = null;
    $max = strlen($strPol) - 1;
    for ($i = 0;$i < $length;$i++) {
        $str.= $strPol[rand(0, $max) ];
    }
    return $str;
}
if (!file_exists('./odres.php')) { /*Init*/
    $res = array('resource' => array(), 'accesslog' => array());
    file_put_contents('./odres.php', '<?php $m=' . var_export($res, true) . ';?>');
}
function request($link) { /*请求链接资源*/
    global $restime, $respermin;
    require './odres.php';
    $ress = $m['resource'];
    $aces = $m['accesslog'];
    $rt = '';
    if (!array_key_exists($link, $ress)) { /*查无此物-A-*/
        return false;
    }
    if (!array_key_exists($link, $aces)) { /*第一次有人请求*/
        $m['accesslog'][$link]['time'] = time();
        $m['accesslog'][$link]['rq'] = 1;
        $rt = $ress[$link];
    } else {
        $ar = $aces[$link];
        if (time() - intval($ar['time']) >= 60 * $restime) { /*重新计算限频*/
            $m['accesslog'][$link]['time'] = time();
            $m['accesslog'][$link]['rq'] = 1;
            $rt = $ress[$link];
        } else if (intval($ar['rq']) >= $respermin) {
            return false;
        } else {
            $m['accesslog'][$link]['rq'] = intval($ar['rq']) + 1;
            $rt = $ress[$link];
        }
    }
    file_put_contents('./odres.php', '<?php $m=' . var_export($m, true) . ';?>'); /*存入文件*/
    return $rt; /*返回值*/
}
function generatelink($u = false, $url) { /*生成并储存链接*/
    global $strPol, $shortstart;
    require './odres.php';
    $ress = $m['resource'];
    $rt = '';
    if (stripos($url, 'sharepoint') == false && stripos($url, '1drv') == false) { /*防止其他链接*/
        return false;
    }
    if (in_array($url, $ress)) { /*已经存在了*/
        return array_search($url, $ress);
    }
    if (!$u) {
        $total = multip($shortstart, strlen($strPol));
        if (count($ress) >= $total) {
            $GLOBALS['shortstart'] = $shortstart + 1; /*尝试*/
            return generatelink($u, $url);
        } else { /*超过了数量*/
            $link = grc($shortstart);
            if (!array_key_exists($link, $ress)) {
                $m['resource'][$link] = $url; /*存入链接*/
                $rt = $link;
            } else { /*链接存在了*/
                return generatelink($u, $url);
            }
        }
    } else { /*有自定义链接*/
        if (!array_key_exists($u, $ress)) {
            $m['resource'][$u] = $url; /*存入链接*/
            $rt = $u;
        } else { /*链接存在了*/
            return false;
        }
    }
    file_put_contents('./odres.php', '<?php $m=' . var_export($m, true) . ';?>'); /*存入文件*/
    return $rt;
}
if (!empty($sharelk)) { /*处理onedrive链接*/
    $lk = '';
    if (!empty($secret)) {
        if (md5($secret) == $key && !empty($customlk)) {
            $lk = generatelink($customlk, $sharelk);
            if (!$lk) { /*不接受该链接*/
                header('HTTP/1.1 403 Forbidden');
                echo 'Link not acceptable.';
                exit();
            }
        }
    } else { /*普通创建模式*/
        $lk = generatelink(false, $sharelk);
        if (!$lk) { /*不接受该链接*/
            header('HTTP/1.1 403 Forbidden');
            echo 'Link not acceptable.';
            exit();
        }
    }
    echo '<p>Your share Link:<input type=\'text\' value=\'' . $lk . '\'/></p>';
    $flk = str_ireplace('{link}', $lk, $finallink); /*获得最终链接*/
    echo '<p>Right Click and Copy: <a href=\'' . $flk . '\' target=\'_blank\'>Here</a></p>';
}
if (!empty($link)) {
    $file = '';
    $rq = request($link); /*请求资源*/
    if (!$rq) {
        header('HTTP/1.1 403 Forbidden');
        echo 'File unreachable.';
        exit();
    }
    $link = $rq;
    if (stripos($link, 'sharepoint') !== false) { /*BusinessEdition*/
        $v = get_headers($link, 1) ['Location']; /*Get Locations*/
        $viewlink = ''; /*File view page link*/
        $guestlink = '';
        if (is_array($v)) {
            foreach ($v as $i) {
                if (stripos($i, 'onedrive.aspx') !== false) { /*抓取请求url及cid*/
                    $viewlink = $i;
                } else {
                    $guestlink = $i;
                }
            }
        } else {
            $viewlink = $v;
        }
        $parsed = parse_url($viewlink);
        $node = $parsed['scheme'] . '://' . $parsed['host']; /*获取主节点*/
        $analyze = explode('&', $parsed['query']); /*逐个参数提取*/
        $data = '';
        $cid = '';
        foreach ($analyze as $k => $v) {
            $vu = explode('=', $v); /*分离键和值*/
            if ($vu[0] == 'parent') {
                unset($analyze[$k]);
            } else if ($vu[0] == 'cid') { /*提取临时访问码*/
                $cid = $vu[1];
            } else {
                $data = $data . urldecode($vu[1]);
            }
        }
        if (!empty($cid)) {
            if (!empty($guestlink)) {
                $file = $guestlink . '&download=1';
            } else {
                $file = $node . $data . '?cid=' . $cid;
            }
        } else {
            $file = $link . '&download=1'; /*没有cid，用下策*/
        }
    } else if (stripos($link, '1drv') !== false) { /*PersonalEdition*/
        $link = str_ireplace('ms', 'ws', $link);
        $file = get_headers($link, 1) ['Location']; /*Get Direct Link*/
    } else {
        echo 'Not an acceptable onedrive share url.';
    }
    if ($jump) {
        header('Location: ' . $file);
    } else {
        echo $file;
        echo '<br>';
    }
} else {
?>
<title>Od</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<p>Usage: od.php?l=<strong>[sharelink]</strong></p>
<br>
<form method='post' action='#'>
<p><input type='text' name='bs' placeholder='Type your onedrive sharelink' style='min-width:200px;'/></p>
<p><input type='text' name='cus' placeholder='Custom Link(need key)' style='min-width:200px;'/></p>
<p><input type='password' name='sc' placeholder='Secret Key' style='min-width:200px;'/></p>
<p><input type='submit' value='submit'></input></p>
</form>
<?php
};
?>