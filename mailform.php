<?php
  require_once(__DIR__ . '/Monaka/config/config.php');
  $form = new Monaka\Form();
  $fieldsConfigPath = file_exists(__DIR__ . '/Monaka/config/fields.json')
    ? __DIR__ . '/Monaka/config/fields.json'
    : __DIR__ . '/Monaka/config/fields-sample.json';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>メールフォーム</title>
  <link rel="stylesheet" href="Monaka/css/reset.css">
  <link rel="stylesheet" href="Monaka/css/common.css">
  <link rel="stylesheet" href="Monaka/css/mailform.css">
</head>

<body>

<div class="container">

  <h1><span>メールフォーム</span></h1>

  <div class="mailform">
    <?php $form->create(); ?>
      <?php $form->renderFromConfig($fieldsConfigPath); ?>
    <?php $form->end('確認する'); ?>
  </div>
  <!--
    フォーム項目は Monaka/config/fields-sample.json (未設置時) または
    Monaka/config/fields.json (設置後) を編集することでPHPコードを触らずに
    追加・削除・並び替えができます。
    $form->inputName() などの個別メソッドを直接呼び出す従来の書き方も
    引き続き利用できます(このコメント上部を参照)。
  -->

</div>

</body>
</html>
