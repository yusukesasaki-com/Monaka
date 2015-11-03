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