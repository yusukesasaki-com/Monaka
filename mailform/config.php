<?php

// 送信先メールアドレス
$adminMail = "";


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


// リターンメールのヘッダーメッセージ
$returnMailHeader = <<<EOD
お問い合わせフォームよりお問い合わせをいただきありがとうございます。
	
お問い合わせ内容を確認の上、ご返信先メールアドレスへ回答いたしますので、
しばらくお待ちくださいますようお願いいたします。


なお、お問い合わせから48時間経過しましても回答がない場合、
サポートにてお問い合わせが受信できていない可能性がございます。

大変お手数ですが、「{$adminMail}」まで
再度お問い合わせくださいますようお願いいたします。 

EOD
;


// リターンメールのフッターメッセージ
$returnMailFooter = <<<EOD

ありがとうございます。

EOD
;