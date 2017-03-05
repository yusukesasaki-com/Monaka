<?php
  require_once(__DIR__ . '/Monaka/config/config.php');
  $form = new Monaka\Form();
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
      <dl>
        <dt>お名前</dt>
        <dd>
          <?php $form->inputName("お名前"); ?>
        </dd>
      </dl>
      <dl>
        <dt>メールアドレス</dt>
        <dd>
          <?php $form->inputMail("メールアドレス"); ?>
        </dd>
      </dl>
      <dl>
        <dt>メールアドレス確認</dt>
        <dd>
          <?php $form->inputMailCheck("メールアドレス確認"); ?>
        </dd>
      </dl>
      <dl>
        <dt>電話番号</dt>
        <dd>
          <?php $form->inputTel("電話番号", "必須"); ?>
        </dd>
      </dl>
      <dl>
        <dt>郵便番号</dt>
        <dd>
          <?php $form->inputZip("郵便番号"); ?>
        </dd>
      </dl>
      <dl>
        <dt>住所</dt>
        <dd>
          <?php $form->inputText("住所"); ?>
        </dd>
      </dl>
      <dl>
        <dt>折り返しの連絡方法</dt>
        <dd>
          <?php
            $params = array(
              "選択してください" => "noValue",
              "メール",
              "電話",
            );
            $form->select("折り返しの連絡方法", $params, "必須");
          ?>
        </dd>
      </dl>
      <dl>
        <dt>年齢</dt>
        <dd>
          <?php $form->inputText("年齢"); ?>
        </dd>
      </dl>
      <dl>
        <dt>性別</dt>
        <dd>
          <?php
            $params = array(
              "男",
              "女",
              "その他" => "text",
            );
            $form->inputRadioBR("性別", $params);
          ?>
        </dd>
      </dl>
      <dl>
        <dt>当サイトを<br>知ったきっかけ</dt>
        <dd>
          <?php
            $params = array(
              "検索",
              "ブログ",
              "その他" => "text",
            );
            $form->inputCheckboxBR("当サイトを知ったきっかけ", $params);
          ?>
          <div class="nest">
            <p>＜検索サイト＞</p>
            <?php
              $params = array(
                "Yahoo",
                "Google",
                "その他" => "text",
              );
              $form->inputCheckboxBR("検索サイト", $params);
            ?>
          </div>
        </dd>
      </dl>
      <dl>
        <dt>添付ファイル</dt>
        <dd>
          <?php $form->inputFile("添付ファイル1", "必須"); ?>
          <?php $form->inputFile("添付ファイル2", "必須"); ?>
          <?php $form->inputFile("添付ファイル3"); ?>
        </dd>
      </dl>
      <dl>
        <dt>お問い合わせ内容</dt>
        <dd><?php $form->textarea("お問い合わせ内容", "必須"); ?></dd>
      </dl>
    <?php $form->end('確認する'); ?>
  </div>

</div>

</body>
</html>
