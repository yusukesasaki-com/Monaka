<?php

require_once __DIR__ . '/Monaka/lib/functions.php';

$checks = array();

$phpOk = version_compare(PHP_VERSION, '8.4.0', '>=');
$checks[] = array(
  'label' => 'PHPバージョン',
  'ok' => $phpOk,
  'detail' => PHP_VERSION . ($phpOk ? ' (OK)' : ' (8.4以上を推奨します)'),
);

$requiredExtensions = array('mbstring', 'openssl', 'fileinfo', 'json');
foreach ($requiredExtensions as $ext) {
  $loaded = extension_loaded($ext);
  $checks[] = array(
    'label' => "PHP拡張機能: {$ext}",
    'ok' => $loaded,
    'detail' => $loaded ? '有効です' : '無効です。サーバー管理者にご相談ください。',
  );
}

$vendorExists = file_exists(__DIR__ . '/vendor/autoload.php');
$checks[] = array(
  'label' => '依存ライブラリ(vendor/autoload.php)',
  'ok' => $vendorExists,
  'detail' => $vendorExists ? '存在します' : '見つかりません。配布用zipを使用しているか、composer installを実行したか確認してください。',
);

$configPath = __DIR__ . '/Monaka/config/config.php';
$configExists = file_exists($configPath);
$checks[] = array(
  'label' => '設定ファイル(Monaka/config/config.php)',
  'ok' => $configExists,
  'detail' => $configExists ? '存在します' : '未設置です。install.phpにアクセスして設定してください。',
);

if ($configExists && $vendorExists) {
  require $configPath;

  $adminMailOk = !empty($adminMail ?? '');
  $checks[] = array(
    'label' => '送信先メールアドレス設定',
    'ok' => $adminMailOk,
    'detail' => $adminMailOk ? h($adminMail) : '未設定です。install.phpで再設定してください。',
  );

  if (!empty($smtpEnabled ?? false)) {
    $errno = 0;
    $errstr = '';
    $conn = @fsockopen($smtpHost ?? '', (int) ($smtpPort ?? 0), $errno, $errstr, 5);
    $smtpOk = $conn !== false;
    if ($conn) {
      fclose($conn);
    }
    $checks[] = array(
      'label' => "SMTP接続確認({$smtpHost}:{$smtpPort})",
      'ok' => $smtpOk,
      'detail' => $smtpOk ? '接続できました(実際のメール送信は行っていません)' : "接続できませんでした({$errstr})",
    );
  } else {
    $mailFuncOk = function_exists('mail');
    $disabled = array_map('trim', explode(',', (string) ini_get('disable_functions')));
    if (in_array('mail', $disabled, true)) {
      $mailFuncOk = false;
    }
    $checks[] = array(
      'label' => 'mail()関数',
      'ok' => $mailFuncOk,
      'detail' => $mailFuncOk ? '利用可能です' : '無効化されています。SMTP送信の利用を検討してください。',
    );
  }
} elseif ($configExists && !$vendorExists) {
  $checks[] = array(
    'label' => '設定ファイルの読み込み',
    'ok' => false,
    'detail' => 'vendor/autoload.phpが無いため読み込めません。',
  );
}

$allOk = true;
foreach ($checks as $check) {
  if ($check['ok'] === false) {
    $allOk = false;
  }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <title>Monaka 環境チェック</title>
  <link rel="stylesheet" href="Monaka/css/reset.css">
  <link rel="stylesheet" href="Monaka/css/common.css">
  <style>
    body { padding: 20px; max-width: 720px; margin: 0 auto; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; }
    th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; vertical-align: top; }
    .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-weight: bold; font-size: 0.85em; }
    .badge-ok { background: #dfd; color: #070; }
    .badge-ng { background: #fdd; color: #900; }
    .notice { background: #eef; border: 1px solid #66c; padding: 12px; margin-top: 20px; }
  </style>
</head>
<body>

<h1>Monaka 環境チェック</h1>

<p>このページはサーバー環境がMonakaの動作要件を満たしているかを自己診断します。実際のメール送信は行いません。</p>

<table>
  <thead>
    <tr><th>項目</th><th>状態</th><th>詳細</th></tr>
  </thead>
  <tbody>
    <?php foreach ($checks as $check) : ?>
      <tr>
        <td><?php echo h($check['label']); ?></td>
        <td>
          <?php if ($check['ok'] === true) : ?>
            <span class="badge badge-ok">OK</span>
          <?php elseif ($check['ok'] === false) : ?>
            <span class="badge badge-ng">要確認</span>
          <?php else : ?>
            <span class="badge">-</span>
          <?php endif; ?>
        </td>
        <td><?php echo h($check['detail']); ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div class="notice">
  <p>確認が終わったら、このページ(check.php)はサーバー情報の外部への開示を避けるため、削除するかアクセス制限をかけることをおすすめします。</p>
</div>

</body>
</html>
