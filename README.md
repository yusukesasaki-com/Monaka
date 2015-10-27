# PHP-OriginalMailForm
カスタマイズ性の高いメールフォームを作成していきます。

    basic_v2
    mailform.phpで項目を増やせるように改変
    
    
## 使い方

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


## 環境
PHP 5.6

## サンプルページ
http://yusukesasaki.com/PHP-OriginalMailForm/basic_v2/mailform.php
