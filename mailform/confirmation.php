<?php

require_once(__DIR__ . '/config.php');
require_once(__DIR__ . '/functions.php');
require_once(__DIR__ . '/class/Confirmation.php');

session_start();

$confirmation = new Confirmation($adminMail);

// ----------CSRF対策開始---------- //

setToken();

// ----------CSRF対策終了---------- //


// ----------パラメータチェック・エラーチェック開始---------- //


// $_POSTのチェック
$confirmation->postCheck($_POST);

// $_FILESのチェック
$confirmation->filesCheck($_FILES);

// 重大なエラーチェック
$confirmation->seriousErrorCheck();


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
    
    <?php if(!empty($confirmation->seriousError)): ?>
    <p class="confirmation"><?php echo $confirmation->seriousError; ?></p>
    <?php else: ?>
    <p class="confirmation">下記内容で送信してよろしいですか？</p>
    <?php endif; ?>
    
    <div class="submit_content">
        <?php if(!empty($confirmation->submitContent) && empty($confirmation->seriousError)): ?>
        <form action="send.php" method="post" enctype="multipart/form-data">
            <dl>
                <?php foreach($confirmation->submitContent as $key => $value): ?>
                <dt>■<?php echo h($key); ?></dt>
                <dd>
                    <p>
                        <?php
                            if(empty($confirmation->err[$key])){
                                if(strpos($value, "\n") !== false){
                                    echo nl2br(h($value));
                                }else{
                                    echo empty($value) ? "&nbsp;\n" : h($value);
                                }
                            }else{
                                echo "<span class=\"err\">{$confirmation->err[$key]}</span>";
                            }
                        ?>
                    </p>
                    <input type="hidden" name="submitContent[<?php echo h($key); ?>]" value="<?php echo h($value); ?>">
                </dd>
                <?php endforeach; ?>
                <?php foreach($confirmation->submitFile as $key => $value): ?>
                <dt>■<?php echo h($key); ?></dt>
                <dd>
                    <p>
                        <?php
                            if(empty($confirmation->err[$key])){
                                if(strpos("jpg,jpeg,git", $value["ext"]) !== false){
                                    $img = base64_encode(file_get_contents($value["tmp"]));
                                    echo "<img src=\"data:image/{$value["ext"]};base64,{$img}\" width=\"150\" ><br>\n";
                                }
                                echo "{$value["name"]}\n";
                                echo "<input type=\"hidden\" name=\"submitFile[{$key}][{$value["name"]}]\" value=\"{$value["file"]}\" >";
                            }else{
                                echo "<span class=\"err\">{$confirmation->err[$key]}</span>";
                            }
                        ?>
                    </p>
                </dd>
                <?php endforeach; ?>
            </dl>

            <div class="submit_area">
                <input type="hidden" name="requiredItem[name]" value="<?php echo h($confirmation->requiredItem["name"]); ?>">
                <input type="hidden" name="requiredItem[mailaddress]" value="<?php echo h($confirmation->requiredItem["mailaddress"]); ?>">
                <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
                <?php if(empty($confirmation->err) && empty($confirmation->seriousError)){ echo "<input type=\"submit\" value=\"送信\">";} ?>
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