<?php

// ----------基本設定開始---------- //

// 送信先メールアドレス
$adminMail = "";


// 送信先メールアドレスを配列化(編集しないでください)
$adminArray = array();
$adminArray = explode(',', $adminMail);


// 送信者名
$adminName = "";


// 送信後に戻るURL
$returnUrl = "";


// 送信完了メッセージ
$completionMessage = <<<EOD
送信が完了しました。
ありがとうございます。
EOD
;

// リターンメールのメールタイトル
$returnMailTitle = "お問い合わせを受け付けました。";

// リターンメールのヘッダーメッセージ
$returnMailHeader = <<<EOD
お問い合わせフォームよりお問い合わせをいただきありがとうございます。

お問い合わせ内容を確認の上、ご返信先メールアドレスへ回答いたしますので、
しばらくお待ちくださいますようお願いいたします。


なお、お問い合わせから48時間経過しましても回答がない場合、
サポートにてお問い合わせが受信できていない可能性がございます。

大変お手数ですが、「{$adminArray[0]}」まで
再度お問い合わせくださいますようお願いいたします。

EOD
;


// リターンメールのフッターメッセージ
$returnMailFooter = <<<EOD

ありがとうございます。

EOD
;

// ----------基本設定終了---------- //




// ----------添付ファイル設定開始---------- //

//参照URL：http://www.k-sugi.sakura.ne.jp/php/2300/

// 拡張子制限（0=しない・1=する）
$ext_denied = 1;
// 許可する拡張子リスト
$ext_allow1 = "jpg";
$ext_allow2 = "jpeg";
$ext_allow3 = "gif";
$ext_allow4 = "pdf";
// 配列に格納しておく
$EXT_ALLOWS = array($ext_allow1, $ext_allow2, $ext_allow3, $ext_allow4);

// アップロード容量制限（0=しない・1=する）
$maxmemory = 1;
// 最大容量（KB）
$max = 3000;

// ----------添付ファイル設定終了---------- //
