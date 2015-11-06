<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>メールフォーム</title>
    <link rel="stylesheet" href="css/html5reset-1.6.1.css">
    <link rel="stylesheet" href="css/ini.css">
    <link rel="stylesheet" href="css/mailform.css">
    <!--[if lt IE 9]>
    <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <script src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
    <![endif]-->
</head>
<body>
    
<div class="container">
    
    <h1><span>メールフォーム</span></h1>
    
    <div class="mailform">
        <form action="./mailform/confirmation.php" method="post" enctype="multipart/form-data">
            <dl>
                <dt>■お名前</dt>
                <dd>
                    <input type="text" name="お名前[value]">
                    <input type="hidden" name="お名前[params]" value="名前">
                </dd>
                
                <dt>■メールアドレス</dt>
                <dd>
                    <input type="text" name="メールアドレス[value]">
                    <input type="hidden" name="メールアドレス[params]" value="メール">
                </dd>
                
                <dt>■メールアドレス確認</dt>
                <dd>
                    <input type="text" name="メールアドレス確認[value]">
                    <input type="hidden" name="メールアドレス確認[params]" value="再入力">
                </dd>
                
                <dt>■電話番号</dt>
                <dd>
                    <input type="text" name="電話番号[value]">
                    <input type="hidden" name="電話番号[params]" value="電話番号,必須">
                </dd>
                
                <dt>■郵便番号</dt>
                <dd>
                    <input type="text" name="郵便番号[value]">
                    <input type="hidden" name="郵便番号[params]" value="郵便番号">
                </dd>

                <dt>■住所</dt>
                <dd>
                    <input type="text" name="住所[value]">
                    <input type="hidden" name="住所[params]" value="">
                </dd>

                <dt>■折り返しの連絡方法</dt>
                <dd>
                    <select name="折り返しの連絡方法[value]">
                        <option value="">選択してください</option>
                        <option value="メール">メール</option>
                        <option value="電話">電話</option>
                    </select>
                    <input type="hidden" name="折り返しの連絡方法[params]" value="必須">
                </dd>
                
                <dt>■年齢</dt>
                <dd>
                    <input type="text" name="年齢[value]">
                    <input type="hidden" name="年齢[params]" value="">
                </dd>
                
                <dt>■性別</dt>
                <dd>
                    <input type="hidden" name="性別[params]" value="">
                    <label><input type="radio" name="性別[value]" value="男" checked> 男</label><br>
                    <label><input type="radio" name="性別[value]" value="女" > 女</label><br>
                    <label><input type="radio" name="性別[value]" value="その他" > その他</label>　
                    <input type="text" name="性別-その他[value]">　
                    <input type="hidden" name="性別-その他[params]">
                </dd>
                
                <dt>■当サイトを知ったきっかけ</dt>
                <dd>
                    <input type="hidden" name="当サイトを知ったきっかけ[params]" value="">
                    <label><input type="checkbox" name="当サイトを知ったきっかけ[value][]" value="検索"> 検索</label>
                    <label><input type="checkbox" name="当サイトを知ったきっかけ[value][]" value="ブログ"> ブログ</label>
                    <label><input type="checkbox" name="当サイトを知ったきっかけ[value][]" value="その他"> その他</label>
                    <input type="text" name="当サイトを知ったきっかけ-その他[value]">
                    <input type="hidden" name="当サイトを知ったきっかけ-その他[params]">
                    <div class="nest">
                        <p>＜検索サイト＞</p>
                        <input type="hidden" name="検索サイト[params]" value="">
                        <label><input type="checkbox" name="検索サイト[value][]" value="Yahoo"> Yahoo</label><br>
                        <label><input type="checkbox" name="検索サイト[value][]" value="Google"> Google</label><br>
                        <label><input type="checkbox" name="検索サイト[value][]" value="その他"> その他</label>
                        <input type="text" name="検索サイト-その他[value]">
                        <input type="hidden" name="検索サイト-その他[params]"><br>
                    </div>
                </dd>

                <dt>■添付ファイル</dt>
                <dd>
                    <input type="file" name="添付ファイル1"><br>
                    <input type="file" name="添付ファイル2"><br>
                    <input type="file" name="添付ファイル3">
                </dd>
        
                <dt>■お問い合わせ内容</dt>
                <dd>
                    <textarea name="お問い合わせ内容[value]"></textarea>
                    <input type="hidden" name="お問い合わせ内容[params]" value="必須">
                </dd>
                
            </dl>
            
            <div class="submit_area">
                <input type="submit" value="確認">
            </div>
        </form>
    </div>
    
</div>
    
</body>
</html>