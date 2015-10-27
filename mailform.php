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
        <form action="./mailform/confirmation.php" method="post">
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
                
                <dt>■年齢</dt>
                <dd>
                    <label><input type="radio" name="年齢[value]" value="～20" checked> ～20</label>　
                    <label><input type="radio" name="年齢[value]" value="20代"> 20代</label>　
                    <label><input type="radio" name="年齢[value]" value="30代"> 30代</label>　
                    <label><input type="radio" name="年齢[value]" value="40代"> 40代</label>　
                    <label><input type="radio" name="年齢[value]" value="50～"> 50～</label>
                    </select>
                    <input type="hidden" name="年齢[params]" value="">
                </dd>
                
                <dt>■当サイトを知ったきっかけ</dt>
                <dd>
                    <label><input type="checkbox" name="当サイトを知ったきっかけ[value][]" value="検索"> 検索</label><br>
                    <label><input type="checkbox" name="当サイトを知ったきっかけ[value][]" value="ブログ"> ブログ</label><br>
                    <label><input type="checkbox" name="当サイトを知ったきっかけ[value][]" value="その他"> その他</label>　
                    <input type="hidden" name="当サイトを知ったきっかけ[params]" value="">
                    
                    <input type="text" name="当サイトを知ったきっかけ-その他[value]">
                    <input type="hidden" name="当サイトを知ったきっかけ-その他[params]" value="">
                    
                    <div class="nest">
                        <p>&lt;検索サイト&gt;</p>
                        <label><input type="checkbox" name="検索サイト[value][]" value="Yahoo"> Yahoo</label>
                        <label><input type="checkbox" name="検索サイト[value][]" value="Google"> Google</label><br>
                        <label><input type="checkbox" name="検索サイト[value][]" value="その他"> その他</label>　
                        <input type="hidden" name="検索サイト[params]" value="">
                        
                        <input type="text" name="検索サイト-その他[value]">
                        <input type="hidden" name="検索サイト-その他[params]" value="">
                    </div>
                    
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