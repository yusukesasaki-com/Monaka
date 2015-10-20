<?php

require_once('config.php');
require_once('functions.php');

$submit_content = array();
$submit_content = $_POST;


// ----------エラーチェック開始---------- //
$err = array();

// お名前の必須チェック
if(empty($submit_content['name'])){
    $err["name"] = "必須項目です。";
}

// メールアドレスの必須チェック
if(empty($submit_content['mailaddress'])){
    $err["mailaddress"] = "必須項目です。";
}

// メールアドレスの形式チェック
if(empty($err["mailaddress"]) && !mail_check($submit_content['mailaddress'])){
    $err["mailaddress"] = "メールアドレスの形式が正しくありません。";
}
/* filter_varを使用する場合は下記
if(!filter_var($submit_content["mailaddress"], FILTER_VALIDATE_EMAIL)){
    $err['mailaddress'] = "メールアドレスの形式が正しくありません";
}
*/

// お問い合わせ内容の必須チェック
if(empty($submit_content["message"])){
    $err["message"] = "必須項目です。";
}

// ----------エラーチェック終了---------- //


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
    
    <h1><span>確認画面</span></h1>
    
    <p class="confirmation">下記内容で送信してよろしいですか？</p>
    
    <div class="submit_content">
        <form action="send.php" method="post">
            <dl>
                <dt>お名前:</dt>
                <dd>
                    <p><?php echo empty($err["name"]) ? h($submit_content["name"]) : "<span class=\"err\">{$err["name"]}</span>"; ?></p>
                    <input type="hidden" name="name" value="<?php echo h($submit_content["name"]); ?>">
                </dd>

                <dt>メールアドレス:</dt>
                <dd>
                    <p><?php echo empty($err["mailaddress"]) ? h($submit_content["mailaddress"]) : "<span class=\"err\">{$err["mailaddress"]}</span>"; ?></p>
                    <input type="hidden" name="mailaddress" value="<?php echo h($submit_content["mailaddress"]); ?>">
                </dd>

                <dt>お問い合わせ内容:</dt>
                <dd>
                    <p><?php echo empty($err["message"]) ? nl2br(h($submit_content["message"])) : "<span class=\"err\">{$err["message"]}</span>"; ?></p>
                    <input type="hidden" name="message" value="<?php echo h($submit_content["message"]); ?>">
                </dd>
            </dl>

            <div class="submit_area">
                <?php if(empty($err)){ echo "<input type=\"submit\" value=\"送信\">";} ?>
                <input type="button" value="戻る">
            </div>
        </form>
    </div>
    
</div>
    
</body>
</html>