<?php

require_once __DIR__ . '/Monaka/lib/functions.php';

$configPath = __DIR__ . '/Monaka/config/config.php';
$alreadyInstalled = file_exists($configPath);

if (isset($_GET['selfDelete']) && $_GET['selfDelete'] === '1' && $alreadyInstalled) {
  @unlink(__FILE__);
  header('Location: mailform.php');
  exit;
}

$errors = array();
$success = false;

$input = array(
  'adminMail' => '',
  'adminName' => '',
  'returnUrl' => '',
  'completionMessage' => "送信が完了しました。\nありがとうございます。",
  'returnMailTitle' => 'お問い合わせを受け付けました。',
  'returnMailHeader' => "お問い合わせフォームよりお問い合わせをいただきありがとうございます。\n\nお問い合わせ内容を確認の上、ご返信先メールアドレスへ回答いたしますので、\nしばらくお待ちくださいますようお願いいたします。",
  'returnMailFooter' => "ありがとうございます。",
  'extDenied' => true,
  'extAllows' => 'jpg,jpeg,gif,pdf',
  'maxmemory' => true,
  'max' => 3000,
  'smtpEnabled' => false,
  'smtpHost' => '',
  'smtpPort' => 587,
  'smtpUser' => '',
  'smtpPass' => '',
  'smtpSecure' => 'tls',
);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$alreadyInstalled) {
  $input['adminMail'] = trim($_POST['adminMail'] ?? '');
  $input['adminName'] = trim($_POST['adminName'] ?? '');
  $input['returnUrl'] = trim($_POST['returnUrl'] ?? '');
  $input['completionMessage'] = trim($_POST['completionMessage'] ?? '');
  $input['returnMailTitle'] = trim($_POST['returnMailTitle'] ?? '');
  $input['returnMailHeader'] = trim($_POST['returnMailHeader'] ?? '');
  $input['returnMailFooter'] = trim($_POST['returnMailFooter'] ?? '');
  $input['extDenied'] = isset($_POST['extDenied']);
  $input['extAllows'] = trim($_POST['extAllows'] ?? '');
  $input['maxmemory'] = isset($_POST['maxmemory']);
  $input['max'] = (int) ($_POST['max'] ?? 0);
  $input['smtpEnabled'] = isset($_POST['smtpEnabled']);
  $input['smtpHost'] = trim($_POST['smtpHost'] ?? '');
  $input['smtpPort'] = (int) ($_POST['smtpPort'] ?? 0);
  $input['smtpUser'] = trim($_POST['smtpUser'] ?? '');
  $input['smtpPass'] = trim($_POST['smtpPass'] ?? '');
  $input['smtpSecure'] = trim($_POST['smtpSecure'] ?? '');

  if ($input['adminMail'] === '') {
    $errors[] = '送信先メールアドレスを入力してください。';
  } else {
    foreach (explode(',', $input['adminMail']) as $addr) {
      if (!preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', trim($addr))) {
        $errors[] = '送信先メールアドレスの形式が正しくありません: ' . trim($addr);
      }
    }
  }

  if ($input['adminName'] === '') {
    $errors[] = '送信者名を入力してください。';
  }

  $extAllowsList = array_values(array_filter(array_map('trim', explode(',', $input['extAllows']))));
  if ($input['extDenied'] && count($extAllowsList) === 0) {
    $errors[] = '拡張子制限を有効にする場合は、許可する拡張子を1つ以上入力してください。';
  }

  if ($input['smtpEnabled'] && $input['smtpHost'] === '') {
    $errors[] = 'SMTP送信を有効にする場合はSMTPホストを入力してください。';
  }

  if (empty($errors)) {
    $lines = array();
    $lines[] = '<?php';
    $lines[] = '';
    $lines[] = '// このファイルはinstall.phpによって自動生成されました。';
    $lines[] = '// 再設定する場合はこのファイルを削除してから、再度install.phpにアクセスしてください。';
    $lines[] = '';
    $lines[] = '$adminMail = ' . var_export($input['adminMail'], true) . ';';
    $lines[] = '$adminArray = explode(",", $adminMail);';
    $lines[] = '';
    $lines[] = '$adminName = ' . var_export($input['adminName'], true) . ';';
    $lines[] = '';
    $lines[] = '$returnUrl = ' . var_export($input['returnUrl'], true) . ';';
    $lines[] = '';
    $lines[] = '$completionMessage = ' . var_export($input['completionMessage'], true) . ';';
    $lines[] = '';
    $lines[] = '$returnMailTitle = ' . var_export($input['returnMailTitle'], true) . ';';
    $lines[] = '';
    $lines[] = '$returnMailHeader = ' . var_export($input['returnMailHeader'], true) . ';';
    $lines[] = '';
    $lines[] = '$returnMailFooter = ' . var_export($input['returnMailFooter'], true) . ';';
    $lines[] = '';
    $lines[] = '$ext_denied = ' . var_export($input['extDenied'] ? 1 : 0, true) . ';';
    $lines[] = '$EXT_ALLOWS = ' . var_export($extAllowsList, true) . ';';
    $lines[] = '';
    $lines[] = '$maxmemory = ' . var_export($input['maxmemory'] ? 1 : 0, true) . ';';
    $lines[] = '$max = ' . var_export($input['max'], true) . ';';
    $lines[] = '';
    $lines[] = '$smtpEnabled = ' . var_export($input['smtpEnabled'], true) . ';';
    $lines[] = '$smtpHost = ' . var_export($input['smtpHost'], true) . ';';
    $lines[] = '$smtpPort = ' . var_export($input['smtpPort'], true) . ';';
    $lines[] = '$smtpUser = ' . var_export($input['smtpUser'], true) . ';';
    $lines[] = '$smtpPass = ' . var_export($input['smtpPass'], true) . ';';
    $lines[] = '$smtpSecure = ' . var_export($input['smtpSecure'], true) . ';';
    $lines[] = '';
    $lines[] = 'require_once(__DIR__ . "/../lib/functions.php");';
    $lines[] = 'require_once(__DIR__ . "/../../vendor/autoload.php");';
    $lines[] = '';
    $lines[] = 'session_start();';
    $lines[] = '';
    $configBody = implode("\n", $lines);

    if (@file_put_contents($configPath, $configBody) === false) {
      $errors[] = 'config.phpの書き込みに失敗しました。Monaka/config ディレクトリの書き込み権限を確認してください。';
    } else {
      $success = true;
      $alreadyInstalled = true;
    }
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Monaka 設置ウィザード</title>
  <link rel="stylesheet" href="Monaka/css/reset.css">
  <link rel="stylesheet" href="Monaka/css/common.css">
  <style>
    body { padding: 20px; max-width: 720px; margin: 0 auto; }
    fieldset { margin-bottom: 24px; padding: 16px; }
    legend { font-weight: bold; padding: 0 8px; }
    label { display: block; margin-top: 12px; font-weight: bold; }
    input[type="text"], input[type="email"], input[type="number"], textarea {
      width: 100%; box-sizing: border-box; padding: 6px; margin-top: 4px;
    }
    .checkbox-row { font-weight: normal; }
    .checkbox-row input { width: auto; margin-right: 6px; }
    .errors { background: #fee; border: 1px solid #c00; padding: 12px; margin-bottom: 16px; }
    .errors li { color: #c00; }
    .notice { background: #eef; border: 1px solid #66c; padding: 12px; margin-bottom: 16px; }
    .success { background: #efe; border: 1px solid #2a2; padding: 12px; margin-bottom: 16px; }
    .hint { font-weight: normal; color: #666; font-size: 0.9em; }
  </style>
</head>
<body>

<h1>Monaka 設置ウィザード</h1>

<?php if ($alreadyInstalled) : ?>

  <?php if ($success) : ?>
    <div class="success">
      <p>設定が完了しました。<code>Monaka/config/config.php</code> を生成しました。</p>
    </div>
  <?php else : ?>
    <div class="notice">
      <p>既に設置済みです(<code>Monaka/config/config.php</code> が存在します)。</p>
      <p>設定をやり直す場合は、サーバー上の <code>Monaka/config/config.php</code> を削除してから、再度このページにアクセスしてください。</p>
    </div>
  <?php endif; ?>

  <p><strong>セキュリティのため、設定が完了したら install.php を削除してください。</strong></p>
  <p><a href="install.php?selfDelete=1">今すぐ install.php を削除する</a></p>
  <p><a href="mailform.php">フォームを開く</a> / <a href="check.php">環境チェックを実行する</a></p>

<?php else : ?>

  <?php if (!empty($errors)) : ?>
    <div class="errors">
      <ul>
        <?php foreach ($errors as $error) : ?>
          <li><?php echo h($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <fieldset>
      <legend>基本設定</legend>

      <label for="adminMail">送信先メールアドレス(必須)<br><span class="hint">複数指定する場合はカンマ区切り</span></label>
      <input type="text" id="adminMail" name="adminMail" value="<?php echo h($input['adminMail']); ?>" required>

      <label for="adminName">送信者名(必須)</label>
      <input type="text" id="adminName" name="adminName" value="<?php echo h($input['adminName']); ?>" required>

      <label for="returnUrl">送信後に戻るURL</label>
      <input type="text" id="returnUrl" name="returnUrl" value="<?php echo h($input['returnUrl']); ?>">

      <label for="completionMessage">送信完了メッセージ</label>
      <textarea id="completionMessage" name="completionMessage" rows="3"><?php echo h($input['completionMessage']); ?></textarea>

      <label for="returnMailTitle">自動返信メールのタイトル</label>
      <input type="text" id="returnMailTitle" name="returnMailTitle" value="<?php echo h($input['returnMailTitle']); ?>">

      <label for="returnMailHeader">自動返信メールの本文(冒頭)</label>
      <textarea id="returnMailHeader" name="returnMailHeader" rows="5"><?php echo h($input['returnMailHeader']); ?></textarea>

      <label for="returnMailFooter">自動返信メールの本文(末尾)</label>
      <textarea id="returnMailFooter" name="returnMailFooter" rows="3"><?php echo h($input['returnMailFooter']); ?></textarea>
    </fieldset>

    <fieldset>
      <legend>添付ファイル設定</legend>

      <label class="checkbox-row">
        <input type="checkbox" name="extDenied" <?php echo $input['extDenied'] ? 'checked' : ''; ?>>
        拡張子を制限する
      </label>

      <label for="extAllows">許可する拡張子<br><span class="hint">カンマ区切り(例: jpg,jpeg,gif,pdf)</span></label>
      <input type="text" id="extAllows" name="extAllows" value="<?php echo h($input['extAllows']); ?>">

      <label class="checkbox-row">
        <input type="checkbox" name="maxmemory" <?php echo $input['maxmemory'] ? 'checked' : ''; ?>>
        アップロード容量を制限する
      </label>

      <label for="max">最大容量(KB)</label>
      <input type="number" id="max" name="max" value="<?php echo h($input['max']); ?>">
    </fieldset>

    <fieldset>
      <legend>SMTP設定<span class="hint">(未入力の場合はサーバーのmail()関数で送信します)</span></legend>

      <label class="checkbox-row">
        <input type="checkbox" name="smtpEnabled" <?php echo $input['smtpEnabled'] ? 'checked' : ''; ?>>
        SMTP経由で送信する
      </label>

      <label for="smtpHost">SMTPホスト</label>
      <input type="text" id="smtpHost" name="smtpHost" value="<?php echo h($input['smtpHost']); ?>">

      <label for="smtpPort">SMTPポート</label>
      <input type="number" id="smtpPort" name="smtpPort" value="<?php echo h($input['smtpPort']); ?>">

      <label for="smtpUser">SMTP認証ユーザー名</label>
      <input type="text" id="smtpUser" name="smtpUser" value="<?php echo h($input['smtpUser']); ?>">

      <label for="smtpPass">SMTP認証パスワード</label>
      <input type="text" id="smtpPass" name="smtpPass" value="<?php echo h($input['smtpPass']); ?>">

      <label for="smtpSecure">暗号化方式<br><span class="hint">tls / ssl / 暗号化しない場合は空欄</span></label>
      <input type="text" id="smtpSecure" name="smtpSecure" value="<?php echo h($input['smtpSecure']); ?>">
    </fieldset>

    <div class="submit_area">
      <input type="submit" value="この内容で設置する">
    </div>
  </form>

<?php endif; ?>

</body>
</html>
