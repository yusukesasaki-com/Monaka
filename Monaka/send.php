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
