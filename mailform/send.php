<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/class/Send.php');

session_start();

$send = new Send($adminMail,$adminName,$returnMailHeader,$returnMailFooter,$_POST["submitFile"]);

// ----------CSRF対策開始---------- //

checkToken();

// ----------CSRF対策終了---------- //



// ----------特殊文字の置換開始---------- //

$send->substitutionSubmitContent($_POST["submitContent"]);

$send->substitutionRequiredItem($_POST["requiredItem"]);

// ----------特殊文字の置換終了---------- //




// ----------送信処理開始---------- //

// 文字コード設定
$send->characterSetting();

// $adminMailへの送信
$send->adminSend();

// リターンメール送信
$send->returnSend();

// ----------送信処理完了---------- //

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メールフォーム</title>
    <link rel="stylesheet" href="../css/html5reset-1.6.1.css">
    <link rel="stylesheet" href="../css/ini.css">
    <link rel="stylesheet" href="../css/mailform.css">
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
    <![endif]-->
</head>
<body>
    
<div class="container">
    
    <h1><span>送信完了</span></h1>
    
    <p class="completion">
        <?php echo nl2br(h($completionMessage)); ?>
    </p>
    
    <div class="submit_area">
        <input class="single" type="button" value="戻る" onclick="window.location='<?php echo $returnUrl; ?>';">
    </div>
    
</div>
    
</body>
</html>