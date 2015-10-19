<?php

require_once('config.php');
require_once('functions.php');

$submit_content = array();
$submit_content = $_POST;

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
                <dd><p><?php echo h($submit_content["name"]); ?>&nbsp;</p></dd>

                <dt>メールアドレス:</dt>
                <dd><p><?php echo h($submit_content["mailaddress"]); ?>&nbsp;</p></dd>

                <dt>お問い合わせ内容:</dt>
                <dd>
                    <p>
                    <?php echo nl2br(h($submit_content["message"])); ?>
                    </p>
                </dd>
            </dl>

            <div class="submit_area">
                <input type="submit" value="送信">
                <input type="button" value="戻る">
            </div>
        </form>
    </div>
    
</div>
    
</body>
</html>