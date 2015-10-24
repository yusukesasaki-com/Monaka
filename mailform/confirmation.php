<?php

require_once('config.php');
require_once('functions.php');

session_start();

// ----------CSRF対策開始---------- //

setToken();

// ----------CSRF対策終了---------- //


$submitContent = array();


// ----------パラメータチェック・エラーチェック開始---------- //

$err = array();
$nameCheck = false;
$mailCheck = false;

foreach($_POST as $key => $values){
    
    // $params = explode(",", $values["params"]); /* 今後in_arrayと組み合わせて使うかも?*/
    
    // 名前チェック
    if(strpos($values["params"], "名前") !== false){
        $nameCheck = true;
        if(empty($values["value"])){
            $err[$key] = "必須項目です。";
        }else{
            $submitContent[$key] = $values["value"];
            $submitContent["name"] = $values["value"];
        }
        continue;
    }
    
    // メールチェック
    if(strpos($values["params"], "メール") !== false){
        $mailCheck = true;
        if(empty($values["value"])){
            $err[$key] = "必須項目です。";
        }else{
            
            // メールアドレスの形式チェック
            if(!mailCheck($values["value"])){
                $err[$key] = "メールアドレスの形式が正しくありません。";
            }
            /* filter_varを使用する場合は下記
            if(!filter_var($submitContent["mailaddress"], FILTER_VALIDATE_EMAIL)){
                $err['mailaddress'] = "メールアドレスの形式が正しくありません";
            }
            */else{
                $submitContent[$key] = $values["value"];
                $submitContent["mailaddress"] = $values["value"];
            }
        }
        continue;
    }
    
    // 必須チェック
    if(strpos($values["params"], "必須") !== false){
        if(empty($values["value"])){
            $err[$key] = "必須項目です。";
        }else{
            $submitContent[$key] = $values["value"];
        }
        continue;
    }
    
    $submitContent[$key] = $values["value"];
    
}

if(!$nameCheck || !$mailCheck){
    echo "メールフォームの構造が正しくありません。<br>\n";
    echo "管理者【{$adminMail}】にお問い合わせください。";
    exit;
}

// ----------パラメータチェック・エラーチェック終了---------- //


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
                    <p><?php echo empty($err["name"]) ? h($submitContent["name"]) : "<span class=\"err\">{$err["name"]}</span>"; ?></p>
                    <input type="hidden" name="name" value="<?php echo h($submitContent["name"]); ?>">
                </dd>

                <dt>メールアドレス:</dt>
                <dd>
                    <p><?php echo empty($err["mailaddress"]) ? h($submitContent["mailaddress"]) : "<span class=\"err\">{$err["mailaddress"]}</span>"; ?></p>
                    <input type="hidden" name="mailaddress" value="<?php echo h($submitContent["mailaddress"]); ?>">
                </dd>

                <dt>お問い合わせ内容:</dt>
                <dd>
                    <p><?php echo empty($err["message"]) ? nl2br(h($submitContent["message"])) : "<span class=\"err\">{$err["message"]}</span>"; ?></p>
                    <input type="hidden" name="message" value="<?php echo h($submitContent["message"]); ?>">
                </dd>
            </dl>

            <div class="submit_area">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
                <?php if(empty($err)){ echo "<input type=\"submit\" value=\"送信\">";} ?>
                <input type="button" value="戻る" onclick="history.back();">
            </div>
        </form>
    </div>
    
</div>
    
</body>
</html>