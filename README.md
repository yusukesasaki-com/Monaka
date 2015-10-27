# PHP-OriginalMailForm
カスタマイズ性の高いメールフォームを作成していきます。

    basic_v3
    ファイルを添付できるように改変
    
    
## 使い方

### mailform.php

input(selectやtextarea含む)はname= `項目名[value]` と name= `項目名[params]` が必ずセットで必要です。
[params]はhiddenにします。

    (例)
    <input type="text" name="項目名[value]">
    <input type="hidden" name="項目名[params]">

***

[params] は `名前` と `メール` が必ず必要です。
無い場合はエラーになります。

    (例)
    <input type="text" name="お名前[value]">
    <input type="hidden" name="お名前[params]" value="名前">
    
    <input type="text" name="メールアドレス[value]">
    <input type="hidden" name="メールアドレス[params]" value="メール">

***

必須項目には[params]に `必須` を書きます。
(`名前` と `メール` は自動で必須になるので、`必須` を書く必要はありません。 )

    (例)
    <textarea name="お問い合わせ内容[value]"></textarea>
    <input type="hidden" name="お問い合わせ内容[params]" value="必須">

***

必須ではない項目も[params]を記述する必要があります。

    (例)
    <input type="text" name="年齢[value]">
    <input type="hidden" name="年齢[params]">


### config.php

`$adminMail` に 管理者(送信先)のメールアドレスを書きます。

    (例)
    // 送信先メールアドレス
    $adminMail = "hoge@example.com";


***

`$adminName` に管理者の名前を書きます。

    (例)
    // 送信者名
    $adminName = "YusukeSasaki";


***

`$returnUrl` に送信後の戻るボタンで移動するURLを書きます。

    (例)
    // 送信後に戻るURL
    $returnUrl = "http://yusukesasaki.com/PHP-OriginalMailForm/basic_v2/mailform.php";
    
***

`$completionMessage` に送信直後に表示されるメッセージを書きます。

    (例)
    $completionMessage = <<<EOD
    送信が完了しました。
    ありがとうございます。
    EOD
    ;

***

`$returnMailHeader` に返信メール上部に表示されるメッセージを書きます。

    (例)
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
    
***

`$returnMailFooter` に返信メール下部に表示されるメッセージを書きます。
    
    (例)
    $returnMailFooter = <<<EOD
    ありがとうございます。
    EOD
    ;

***

以上、わかりづらい場合は下記のサンプルページからお問い合わせしてください！
(私に届きます)


## 環境
PHP 5.6

## サンプルページ
http://yusukesasaki.com/PHP-OriginalMailForm/basic_v2/mailform.php
