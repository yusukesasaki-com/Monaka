<?php
  require_once(__DIR__ . '/config/config.php');
  $mailer = new Monaka\Mailer(
    $smtpEnabled ?? false,
    $smtpHost ?? '',
    $smtpPort ?? 587,
    $smtpUser ?? '',
    $smtpPass ?? '',
    $smtpSecure ?? 'tls'
  );
  $send = new Monaka\Send($mailer);
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

  <?php if (!empty($send->adminMailError)) : ?>
    <h1><span>送信エラー</span></h1>
    <p class="completion">
      メールの送信に失敗しました。<br>
      お手数ですが、時間をおいて再度お試しいただくか、<br>
      別の方法で管理者までご連絡ください。
    </p>
  <?php else : ?>
    <h1><span>送信完了</span></h1>
    <p class="completion">
      <?php echo nl2br(h($completionMessage)); ?>
    </p>
  <?php endif; ?>

  <div class="submit_area">
    <input class="single" type="button" value="戻る" onclick="window.location='<?php echo $returnUrl; ?>';">
  </div>

</div>

</body>
</html>
