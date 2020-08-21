<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="utf-8" />
    <title>Token Mismatch</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- Custom Files -->
    <link href="/backend/assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/backend/assets/css/icons.css" rel="stylesheet" type="text/css" />
    <link href="/backend/assets/css/style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<section>
    <div class="container-alt container">
        <div class="row">
            <div class="col-sm-12 text-center">
                <div class="home-wrapper">
                    <h1 class="icon-main text-danger"><i class="fa fa-warning"></i></h1>
                    <h1 class="home-text text-uppercase">您的Token已經過期</h1>
                    <h4>
                        為了防止跨站請求偽造(Cross-site request forgery)攻擊，網站必須檢查您的Token(訪問令牌)<br>
                        造成Token過期的原因，可能是您在同一個畫面閒置太久，以至於Token失效過期。<br>
                        點擊<a style="color:#007bff;cursor:pointer;" onclick="history.go(-1)">回上一頁</a>並<span>重新整理頁面</span>。
                    </h4>

                </div>
            </div>
        </div>
    </div>
</section>
</body>
</html>