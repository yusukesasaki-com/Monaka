<?php
  require_once(__DIR__ . '/config/config.php');
  $send = new Monaka\Send();
  $send->run($adminMail, $adminName, $returnMailTitle, $returnMailHeader, $returnMailFooter);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>メールフォーム</title>
  <link rel="stylesheet" href="./css/reset.css">
  <link rel="stylesheet" href="./css/common.css">
  <link rel="stylesheet" href="./css/confirmation.css">
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
