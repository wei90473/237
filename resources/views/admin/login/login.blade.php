<!DOCTYPE html>
<html lang="zh-Hant">
<head>

<meta property="og:site_name" content="行政院人事行政總處公務人力發展學院 - 教育訓練資訊系統"/>
<meta property="og:type" content="website" />
<meta property="og:title" content="行政院人事行政總處公務人力發展學院 - 教育訓練資訊系統"/>
<meta property="og:image" content=""/>
<meta property="og:url" content="" />
<meta property="og:description" content="" />

<title>{{ config('app.login_title') }}</title>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
<meta name="keywords" content="公務人力發展學院、公務人力、洽公、場地、訓練人員、人資發展" />
<meta name="description" content="" />
<link rel="icon" href="images/favicon.png" type="image/x-icon" />
<!--[if lt IE 9]>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
<link href="/backend/homepage/css/animate.css" rel="stylesheet">
<link href="/backend/homepage/css/style.css" rel="stylesheet">
<script src="/backend/homepage/js/jquery-3.3.1.min.js"></script>
<script src="/backend/homepage/js/main.js"></script>
<script src='https://www.google.com/recaptcha/api.js?hl=zh-TW'></script>
</head>

<body class="login login2">
  <header>
      <div class="container">

        <h1 title="行政院人事行政總處公務人力發展學院">
            <a href="/" title="行政院人事行政總處公務人力發展學院">
              <img src="/backend/homepage/images/logo.png" alt="行政院人事行政總處公務人力發展學院">
            </a>
            <span>教育訓練資訊系統</span>
        </h1>

      </div>
  </header>


  <section class="wrapper">
      <div class="container">
      <form  method="post" class="form-horizontal m-t-20">
                {{ csrf_field() }}
            <div class="login-wrap">
                <div class="column col-icon">
                    <h2>目前登入的身份為</h2>
                    <div class="icon-focus">
                      <h3>學院同仁</h3>
                      <span class="cycle"><img src="/backend/homepage/images/icon_identity_01.png" alt="icon_identity_01"></span>
                    </div>
                </div>
                <!--/.column col-icon-->
                <div class="column col-type">
                    <h2>登入方式</h2>
                    <ul>
                      <li><div class="radio"><input id="radio-1" type="radio" name="radio" value="1"  onclick="radio1();"><label for="radio-1" class="radio-label">AD 登入</label></div></li>
                      <li><div class="radio"><input id="radio-2" type="radio" name="radio" value="2"  onclick="radio2();" checked><label for="radio-2" class="radio-label">帳號密碼登入</label></div></li>
                      <li><div class="radio"><input id="radio-3" type="radio" name="radio" value="3"  onclick="radio3();"><label for="radio-3" class="radio-label">E等公務園+帳號登入</label></div></li>
                    </ul>
                </div>

     
                <!--/.column col-type-->
                <div class="column col-form">
                <div id="login_div" ></div>
               
                      <ul id="login_ul" >
                      <li><input type="text" name="userid"  required placeholder="帳號"></li>
                      <li><input type="password" name="password" required placeholder="密碼"></li>
                      <li>
                      <div id="g-recaptcha" class="g-recaptcha" data-sitekey="6LddJsAUAAAAAHkdATPXZgTBqaeLmhpRFWyfvIH9"></div>

                      </li>
                      <li><button type="submit" class="btn-submit">登入</button></li>
                      <!-- <li class="last"><a href="">忘記帳號</a><a href="">忘記密碼</a></li> -->
                    </ul>
                   
                    <!-- 提示訊息 -->
                @include('admin/layouts/alert')
                </div>
                </form>
                <!--/.column col-form-->
            </div>
            <!--/.login-wrap-->
            <div class="tabs-wrap">

                <div class="abgne-tab">
                  <ul class="tabs">
                    <li><a href="#tab1">系統登入方式</a></li>
                    <li><a href="#tab2">登入問題排除</a></li>
                    <li><a href="#tab3">系統操作說明</a></li>
                  </ul>
                  <div class="tab-container">
                    <div id="tab1" class="tab-content">
                      <h3>使用AD登入：</h3>
                      <p>輸入帳號及密碼，系統驗證成功即自動登入。</p>
                      <h3>使用帳號密碼登入：</h3>
                      <p>輸入帳號及密碼，系統驗證成功即自動登入。</p>
                      <h3>使用e等公務園+帳號登入：</h3>
                      <p>點選前往e等公務園登入，系統會導轉至e等公務園+學習平臺的登入頁，請使用任一方式登入後，系統驗證通過即自動導轉回本系統，完成登入。</p>
                      <p></p>
                    </div>
                    <div id="tab2" class="tab-content">
                      <p>如有任何登入的問題，請洽數位組 林如崧#7413</p>
                    </div>
                    <div id="tab3" class="tab-content">
                      <p>請參閱操作說明檔(<a href="#" target="_blank" >點此查看或下載</a>)。</p>
                    </div>

                  </div>
                </div>

            </div>
            <!--/.tabs-wrap-->


      </div>
  </section>
  <!--/.wrapper-->

  <footer>
    <div class="container-fluid">
       <!-- <p class="text-center">本學院聯絡人：綜合規劃組林詩兒小姐(分機8106)及陳芊卉小姐(分機8006)。<p> -->
    </div>
  </footer>



<!-- JS -->
</body>

<script>

function radio1() {
  var html = '';
      $('#login_ul').remove();
      html += '<ul id="login_ul"  >';
      html += '<li><input type="text" name="userid"  required placeholder="帳號"></li>';
      html += '<li><input type="password" name="password" required placeholder="密碼"></li>';
      html += '<li><button type="submit" class="btn-submit">登入</button></li>';
      html += '</ul>';
      $('#login_div').after(html);

}

function radio2() {

    var html = '';
      $('#login_ul').remove();
      html += '<ul id="login_ul"  >';
      html += '<li><input type="text" name="userid"  required placeholder="帳號"></li>';
      html += '<li><input type="password" name="password" required placeholder="密碼"></li>';
      html += '<li><button type="submit" class="btn-submit">登入</button></li>';
      html += '</ul>';
      $('#login_div').after(html);


}
function radio3() {
 

      var html = '';
      $('#login_ul').remove();
      $('#g-recaptcha').remove();
      html += '<ul id="login_ul" style="margin-top: 120px;" >';
      html += '<li><a target="_blank" href="https://elearn.hrd.gov.tw/mooc/co_login_dialog.php?code=csdi_02&type=1" ><button type="button" class="btn-submit">前往e等公務園登入</button></a></li>';
      html += '</ul>';
      $('#login_div').after(html);


}


</script>

</html>


