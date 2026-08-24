[![CI](https://github.com/yusukesasaki-com/Monaka/actions/workflows/ci.yml/badge.svg)](https://github.com/yusukesasaki-com/Monaka/actions/workflows/ci.yml)

# Monaka

カスタマイズ性の高いメールフォームを目指して作成しました。

## バージョン

v3.0.0

## 環境

PHP 8.4以上

## 設置方法(非エンジニア向け・推奨)

PHPのコードを一切編集せずに設置できます。

1. [Releases](https://github.com/yusukesasaki-com/Monaka/releases) から最新の `monaka-vX.X.X.zip` をダウンロードして展開する
   (Composerの依存ライブラリ`vendor/`が同梱されているため、サーバー上でcomposerを実行する必要はありません)
2. 展開したファイル一式をFTP/SFTPなどでサーバーにアップロードする
3. ブラウザで `https://あなたのドメイン/install.php` にアクセスし、送信先メールアドレスなどをフォームに入力して「この内容で設置する」を押す
   (`Monaka/config/config.php` が自動生成されます)
4. 設置完了後に表示される「install.phpを削除する」リンクをクリックし、`install.php` を削除する
5. `https://あなたのドメイン/check.php` にアクセスし、環境チェックがすべてOKになっていることを確認する
   (確認後はこのファイルも削除、またはアクセス制限をかけることを推奨します)
6. `https://あなたのドメイン/mailform.php` がメールフォームです

## 設置方法(開発者向け)

```
git clone https://github.com/yusukesasaki-com/Monaka.git
cd Monaka
composer install
```

`Monaka/config/config-sample.php` を `Monaka/config/config.php` にコピーして設定値を編集するか、上記の `install.php` を使って設置してください。

## フォーム項目のカスタマイズ

フォーム項目は `Monaka/config/fields-sample.json` を `Monaka/config/fields.json` としてコピーし、JSONを編集するだけで追加・削除・並び替えができます(PHPの編集は不要です)。

```json
{"type": "text", "label": "住所"}
```

より柔軟なレイアウトが必要な場合は、`mailform.php` 内で `Monaka\Form` クラスの各メソッド(`inputName()` / `inputMail()` / `select()` など)を直接呼び出す従来の書き方も引き続き利用できます。

## メール送信方式

既定ではサーバーの `mail()` 関数で送信しますが、`Monaka/config/config.php` (または`install.php`)でSMTP送信に切り替えることもできます。共有サーバーやモダンなホスティング環境で `mail()` が届きにくい場合はSMTP送信をご利用ください。

## テスト

```
composer install
composer test
```

## ライセンス

MIT
This software is released under the MIT License, see License.txt.
