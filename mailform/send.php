<?php

require_once('config.php');
require_once('functions.php');

session_start();

// ----------CSRF対策開始---------- //

checkToken();

// ----------CSRF対策終了---------- //


$submitContent = array();
$submitContent = $_POST;


// メールの言語・文字コードの設定
mb_language("Japanese");
mb_internal_encoding("UTF-8");

// タイトルの設定
$send_title = "{$submitContent["name"]}様よりお問い合わせ";
mb_encode_mimeheader($send_title, "ISO-2022-JP");

// メッセージの設定
$send_message = "{$submitContent["name"]}様より、下記内容でお問い合わせが届いています。\n";
$send_message .= "\n";
$send_message .= "■メールアドレス\n";
$send_message .= "{$submitContent["mailaddress"]}\n\n";
$send_message .= "■お問い合わせ内容\n";
$send_message .= "{$submitContent["message"]}";
$send_message = mb_convert_encoding($send_message, "ISO-2022-JP","UTF-8");

// メールの送信 (宛先, 件名, 本文, 送り主(From:が必須))
@mb_send_mail(ADMIN_MAIL, $send_title, $send_message, "From:{$submitContent["mailaddress"]}");

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
        <input class="single" type="button" value="戻る" onclick="window.location='<?php echo RE_URL; ?>';">
    </div>
    
</div>
    
</body>
</html>