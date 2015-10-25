<?php

require_once('config.php');
require_once('functions.php');

session_start();

// ----------CSRF対策開始---------- //

setToken();

// ----------CSRF対策終了---------- //

$requiredItem = array();
$submitContent = array();

// ----------パラメータチェック・エラーチェック開始---------- //

$err = array();
$nameCheck = false;
$mailCheck = false;

foreach($_POST as $key => $values){
    
    // $params = explode(",", $values["params"]); /* 今後in_arrayと組み合わせて使うかも?*/
    
    // 配列(checkbox)を変数に変換
    if(is_array($values["value"])){
        $values["value"] = implode("、", $values["value"]);
    }
    
    // 名前チェック
    if(strpos($values["params"], "名前") !== false){
        $nameCheck = true;
        if(empty($values["value"])){
            $err[$key] = "必須項目です。";
        }
        $submitContent[$key] = $values["value"];
        $requiredItem["name"] = $values["value"];
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
            */
        }
        $submitContent[$key] = $values["value"];
        $requiredItem["mailaddress"] = $values["value"];
        continue;
    }
    
    // 必須チェック
    if(strpos($values["params"], "必須") !== false){
        if(empty($values["value"])){
            $err[$key] = "必須項目です。";
        }
        $submitContent[$key] = $values["value"];
        continue;
    }
    
    $submitContent[$key] = $values["value"];
    
}

if(!$nameCheck || !$mailCheck){
    $seriousError = "エラーが発生しました。<br>\n";
    $seriousError .= "再度お試しいただき、解消しない場合は、<br>\n";
    $seriousError .= "管理者【{$adminMail}】にお知らせください。";
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
    
    <?php if(!empty($seriousError)): ?>
    <p class="confirmation"><?php echo $seriousError; ?></p>
    <?php else: ?>
    <p class="confirmation">下記内容で送信してよろしいですか？</p>
    <?php endif; ?>
    
    <div class="submit_content">
        <?php if(!empty($submitContent) && empty($seriousError)): ?>
        <form action="send.php" method="post">
            <dl>
                <?php foreach($submitContent as $key => $value): ?>
                <dt>■<?php echo h($key); ?></dt>
                <dd>
                    <p>
                        <?php
                            if(empty($err[$key])){
                                if(strpos($value, "\n") !== false){
                                    echo nl2br(h($value));
                                }else{
                                    echo empty($value) ? "&nbsp;\n" : h($value);
                                }
                            }else{
                                echo "<span class=\"err\">{$err[$key]}</span>";
                            }
                        ?>
                    </p>
                    <input type="hidden" name="submitContent[<?php echo h($key); ?>]" value="<?php echo h($value); ?>">
                </dd>
                <?php endforeach; ?>
            </dl>

            <div class="submit_area">
                <input type="hidden" name="requiredItem[name]" value="<?php echo h($requiredItem["name"]); ?>">
                <input type="hidden" name="requiredItem[mailaddress]" value="<?php echo h($requiredItem["mailaddress"]); ?>">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
                <?php if(empty($err) && empty($seriousError)){ echo "<input type=\"submit\" value=\"送信\">";} ?>
                <input type="button" value="戻る" onclick="history.back();">
            </div>
        </form>
        <?php else: ?>
        <div class="submit_area">
            <input type="button" value="戻る" onclick="history.back();">
        </div>
        <?php endif; ?>
    </div>
    
</div>
    
</body>
</html>