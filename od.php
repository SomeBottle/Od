<?php
$link = @$_GET['l']; /*获得请求参数*/
if (!empty($_POST['bs'])) {
    echo '<p>Your base64 Link:<input type=\'text\' value=\'' . base64_encode($_POST['bs']) . '\'/></p>';
}
/*---Settings---*/
$jump = true; /*是否直接重定向*/
/*---SettingsEnd---*/
if (!empty($link)) {
    $file = '';
    $link = base64_decode($link);
    if (stripos($link, 'sharepoint') !== false) { /*BusinessEdition*/
        $v = get_headers($link, 1) ['Location']; /*Get Locations*/
        $viewlink = ''; /*File view page link*/
        if (is_array($v)) {
            foreach ($v as $i) {
                if (stripos($i, 'onedrive.aspx') !== false) { /*抓取请求url及cid*/
                    $viewlink = $i;
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
            $file = $node . $data . '?cid=' . $cid;
        } else {
            $file = $link . '&download=1'; /*没有cid，用下策*/
        }
    } else if (stripos($link, '1drv') !== false) { /*PersonalEdition*/
	    $link=str_ireplace('ms','ws',$link);
        $file = get_headers($link, 1) ['Location']; /*Get Direct Link*/
    }else{
		echo 'Not an acceptable onedrive share url.';
	}
    if ($jump) {
        header('Location: ' . $file);
    } else {
        echo $file;
        echo '<br>';
    }
} else {
    echo 'Usage: od.php?l=<strong>[base64 sharelink]</strong><br><form method=\'post\' action=\'#\'><input type=\'text\' name=\'bs\'/></form>';
}
?>