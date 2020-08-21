@extends('admin.layouts.layouts')
@section('content')

<?php
    $test = '<p style="text-align:center">
    <span style="font-size:12pt"><span style="font-size:14.0pt">行政院人事行政總處公務人力發展學院　研習通知</span></span></p>
<p>';
?>

    <?php $_menu = 'transfer_training_letter';?>

    <div class="content">
        <div class="container-fluid">

            <!-- 頁面標題 -->
            <div class="row pc_show">
                <div class="col-sm-12">
                    <h4 class="pull-left page-title">調訓函</h4>
                    <ol class="breadcrumb pull-right">
                        <li><a href="/admin">首頁</a></li>
                        <li class="active">調訓函</li>
                    </ol>
                </div>
            </div>

            <!-- 提示訊息 -->
            @include('admin/layouts/alert')

            <!-- 列表 -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fa fa-list pr-2"></i>調訓函</h3>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                <!-- <form method="post" action="/admin/transfer_training_letter/export" id="search_form">
                                        {{ csrf_field() }} -->
                                        {!! Form::open([ 'method'=>'post', 'name'=>'form', 'id'=>'form']) !!}
                                        <div class="form-group row">
                                            <label class="col-1 control-label text-md-right pt-2">班別</label>
                                            <div class="col-6">
                                                <div class="input-group bootstrap-touchspin number_box">
                                                    <select id="classes" name="classes" class="select2 form-control select2-single input-max" onchange="getTerms();">
                                                            <option value="0">請選擇</option>
                                                        <?php foreach ($classArr as $key => $va) { ?>
                                                            <option value='<?=$va->class?>'><?=$va->class?>-<?=$va->name?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <label class="col-1 control-label text-md-right pt-2">期別</label>
                                                <div class="col-3">
                                                    <div class="input-group bootstrap-touchspin number_box">
                                                        <select id="terms" name="terms" class="select2 form-control select2-single input-max" onchange="changeTerms();" >
                                                            <option value="0">請選擇</option>
                                                            <?php foreach ($termArr as $key => $va) { ?>
                                                                <option value='<?=$va->term?>'><?=$va->term?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                        </div>


                                        <div class="form-group row align-items-center justify-content-center">
                                            <button type="button" class="btn mobile-100 mr-2" onclick="setdva()"><i class="fas fa-file-export fa-lg pr-1"></i>Email通知服務機關及學員</button>
                                            <button type="button" class="btn mobile-100 mr-2" onclick="setdva()"><i class="fas fa-file-export fa-lg pr-1"></i>Email通知學員</button>
                                            <button type="button" class="btn mobile-100" onclick="setdvb()"><i class="fas fa-file-export fa-lg pr-1"></i>天然災害處理原則通知</button>
                                        </div>

                                        <div id="dvmail" style="visibility:hidden">
                                            <div class="form-group row ">
                                                <label class="col-1 control-label text-md-right pt-2">收件者：</label>
                                                <div class="col-11" style="padding-top: 3px;">
                                                    <a id="mlink" href="/admin/transfer_training_letter/list/{{ $class_data['class'] }}_{{ $class_data['term'] }}_{{ $subject }}">
                                                        <button type="button" class="btn btn-info"> 挑選收件者</button>
                                                    </a>
                                                </div>
                                                <label class="col-1 control-label text-md-right pt-2">主旨：</label>
                                                <div class="col-11" style="padding-top: 3px;">
                                                    <input type="text" style="width: 700px;" class="form-control" maxlength="50" autocomplete="off" id="subject" name="subject"  value="{{ $subject }}">
                                                </div>
                                            </div>  


                                            <div class="form-group row ">
                                                <textarea name="editor" id="editor" cols="20" rows="5"></textarea>
                                            </div>                        
                                            <div class="form-group row ">
                                             附件：
                                                <div class="col-sm-8" style="padding-top: 18px;">
                                                    <input name="attached[]" type="checkbox" value="1" /> <label>學員名冊</label>
                                                    <input name="attached[]" type="checkbox" value="2">   <label>課程表</label>
                                                    <input name="attached[]" type="checkbox" value="3">   <label>停車卡</label>
                                                </div>
                                            </div>         
                                            
                                            <div class="form-group row align-items-center justify-content-center">
                                                <button type="button" class="btn mobile-100 mr-2" onclick="submitform();" ><i class="fas fa-file-export fa-lg pr-1"></i>寄出</button>
                                                <button type="button" class="btn mobile-100" onclick="submittome();"><i class="fas fa-file-export fa-lg pr-1"></i>寄送範本給我</button>
                                            </div>
                                        </div>
                                        {!! Form::close() !!}
                                    <!-- </form>    -->
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 刪除確認視窗  -->
    @include('admin/layouts/list/del_modol')

@endsection

@section('js')
<script>

    if("<?php echo ($result); ?>" != ""){
        alert("<?php echo ($result); ?>");
    }

    $(document).ready(function(){
        <?php echo ($js); ?>
    }); 


    CKEDITOR.replace( 'editor',{height:200 } );

    function submitform(){
        window.document.form.action='/admin/transfer_training_letter/mail';
        submitForm('#form');
    }
    
    function submittome(){
        window.document.form.action='/admin/transfer_training_letter/mailtome';
        submitForm('#form');
    }

    function getTerms()	{
        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/transfer_training_letter/getTerms",
            data: { classes: $('#classes').val()},
            success: function(data){
            let dataArr = JSON.parse(data);
            let tempHTML = "";
            // console.log(dataArr);
            for(let i=0; i<dataArr.length; i++) 
            {
                tempHTML += "<option value='"+dataArr[i].term+"'>"+dataArr[i].term+"</option>";                     
            }
            $("#terms").html(tempHTML);
            changeTerms();
            },
            error: function() {
                console.log('Ajax Error');
            }
        });
    };

    function setdva(){        
        document.getElementById("dvmail").style.visibility="visible";
        $("#subject").val("行政院人事行政總處公務人力發展學院-"+$('#classes').find("option:selected").text().substr(7)+"第"+$('#terms').find("option:selected").text().replace(/\b(0+)/gi,"")+"期-研習通知");   

        $.ajax({
            type: 'post',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            dataType: "html",
            url:"/admin/transfer_training_letter/getContent",
            data: { classes: $('#classes').val(),term: $('#terms').val()},
            success: function(data){
                if(data == '1'){
                    CKEDITOR.instances.editor.setData('<p align="center">\
    行政院人事行政總處公務人力發展學院　研習通知</p>\
<p>\
    親愛的學員，歡迎您參加107年初任簡任官等主管職務人員研究班第1期研習，以下相關事項請參閱：</p>\
<p>\
    <strong>※<strong>為因應嚴重特殊傳染性肺炎（COVID-19，簡稱武漢肺炎）防疫措施，若有下列情形之一者，請勿到訓，並請通知貴機關人事單位進行換員或email填覆未到訓通知單至本學院：</strong></strong></p>\
<p style="margin-left:24.45pt;">\
    <strong>一、依中央流行疫情指揮中心公布之「</strong><a href="https://www.cdc.gov.tw/Category/MPage/IRvJdHilZERpzIaEHWKAUg" target="_blank"><strong>具感染風險民眾追蹤管理機制</strong></a><strong>」，需「居家隔離」、「居家檢疫」及「自主健康管理」，且尚未期滿者。</strong></p>\
<p style="margin-left:24.45pt;">\
    <strong>二、14天內曾與返國親友接觸者。</strong></p>\
<p style="margin-left:25.9pt;">\
    <strong>三、有發燒、乾咳、倦怠、四肢無力、呼吸急促、上呼吸道症狀(咳嗽、喉嚨痛、打噴嚔、呼吸困難等)、肌肉痛、頭痛、腹瀉、嗅覺或味覺喪失（或異常）等症狀者。</strong></p>\
<p>\
    <strong>防疫工作人人有責，學員在本學院研習期間需自備口罩並配戴；更多資訊請詳閱衛生福利部疾病管制署網站（</strong><a href="https://www.cdc.gov.tw/">https://www.cdc.gov.tw/</a><strong>），或撥打防疫專線1922。</strong></p>\
<p style="margin-left:28.0pt;">\
    一、研習目標：培育初任簡任官等主管職務人員策略性、創造性及宏觀視野之領導管理才能，強化其決策及領導統御能力。</p>\
<p style="margin-left:28.0pt;">\
    二、研習時間：民國107年5月21日至107年5月25日止，為期5天。</p>\
<p style="margin-left:28.0pt;">\
    三、報到時間：民國107年5月21日上午08時40分至09時10分。</p>\
<p style="margin-left:28.0pt;">\
    四、報到及研習地點：行政院人事行政總處公務人力發展學院（臺北院區）教學棟5樓501教室（臺北市大安區新生南路3段30號）。</p>\
<p style="margin-left:28.0pt;">\
    五、研習期間膳宿供應情形係依各委託機關所填參訓報名表之需求辦理（詳見參訓人員名冊）；參訓費用皆不含提前住宿及結訓日（週五）晚餐費用，登記住宿者請務必攜帶身分證，逕至本學院（臺北院區）1樓大廳福華國際文教會館(以下簡稱會館)櫃檯辦理住宿手績，如有開車者請將入場停車晶片交予櫃檯；研習期間可憑房卡免消磁進出停車場。大廳櫃檯提供行李寄放服務，請先放置行李後，再前往報到地點。另周日需提前住宿之學員請自行事先向會館訂房，將以公務人員優惠房價計費。</p>\
<p style="margin-left:28.0pt;">\
    六、有關本研習<a href="http://www.hrd.gov.tw/index.asp?type=1&amp;class=09800801">參訓人員名冊</a>、<a href="http://www.hrd.gov.tw/index.asp?type=3">學員須知</a>等相關資訊，請至本學院全球資訊網（網址：<a href="https://www.hrd.gov.tw/">https://www.hrd.gov.tw/</a>）點選「學員」身分後登入查閱。</p>\
<p style="margin-left:28.0pt;">\
    七、參訓經費收據另函寄送各經費支付機關，並請於民國107年5月14日前撥入本學院指定帳戶或以支票逕寄本學院（臺北院區）。</p>\
<p style="margin-left:28.0pt;">\
    八、本學院（臺北院區）室內場所及住宿房間全面禁菸，另為顧及個人衛生及配合環保政策，受訓期間請自行攜帶水杯備用。</p>\
<p style="margin-left:28.0pt;">\
    九、本案承辦人：培育發展組鄭淑真；E-Mail：winnie@hrd.gov.tw；電話：（02）83691399轉8203；傳真：（02）83695611。研習期間如需聯絡研習人員請撥學員專線：02-83691399轉6232，傳真：02-83695808。</p>\
<p style="margin-left:28.0pt;">\
    十、學員研習期間得依據<a href="https://lib.rad.gov.tw/libweb/service.htm">「行政院人事行政總處公務人力發展學院臺北院區自學中心使用須知」</a>使用相關圖書服務，惟研習上課時間不提供在班學員使用，自學中心介紹請參閱附檔（<a href="http://mediab.hrd.gov.tw/courses/tmp/%E8%87%BA%E5%8C%97%E9%99%A2%E5%8D%80%E8%87%AA%E5%AD%B8%E4%B8%AD%E5%BF%83%E4%BB%8B%E7%B4%B91080601.pdf">自學中心介紹</a>），另臺北院區得依據<a href="http://mediab.hrd.gov.tw/courses/tmp/%E5%85%AC%E5%8B%99%E4%BA%BA%E5%8A%9B%E7%99%BC%E5%B1%95%E5%AD%B8%E9%99%A2%E7%A6%8F%E8%8F%AF%E5%9C%8B%E9%9A%9B%E6%96%87%E6%95%99%E6%9C%83%E9%A4%A8%E5%95%86%E5%8B%99%E4%B8%AD%E5%BF%83%E4%BD%BF%E7%94%A8%E9%A0%88%E7%9F%A5.pdf">「福華文教會館商務中心使用須知」</a>使用電腦（含上網服務）、借用視聽媒體及書刊。</p>\
<p style="margin-left:28.0pt;">\
    十一、本學院（臺北院區）地下1樓提供游泳池、按摩池、桌球、撞球、健身設施等，免費使用時段為：上午6時至8時，中午12時至下午1時30分，下午4時30分至6時30分，請以個人身分證至會館休閒中心櫃檯登錄，並請自備所需之服裝及用具（如：游泳衣帽、桌球器具，使用健身設施請著運動鞋）。</p>\
<p>\
    <strong>PS.</strong><strong>因下列學員於報名時，未附e-mail郵件信箱，請服務機關特別注意提醒當事人，本學院不另通知：<br />\
    1.陳敏森（金融監督管理委員會檢查局）</strong></p>\
<p>\
    <strong>謝謝</strong></p>\
');
                } else if(data == '2'){
                    CKEDITOR.instances.editor.setData('<p style="text-align:center">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">行政院人事行政總處公務人力發展學院　研習通知</span></span></p>\
<p>\
    <span style="font-size:12pt"><span style="font-size:14.0pt">親愛的學員，歡迎您參加107年環境洞察研習班第1期研習，以下相關事項請參閱：</span></span></p>\
<p style="text-align:justify">\
    <span style="font-size:12pt"><strong><span style="font-size:13.0pt">※<strong>為因應嚴重特殊傳染性肺炎（COVID-19，簡稱武漢肺炎）防疫措施，若<span style="color:red">有下列情形之一者，請勿到訓，</span>並請通知貴機關人事單位進行換員或email填覆未到訓通知單至本學院：</strong></span></strong></span></p>\
<p style="margin-left:33px; text-align:justify">\
    <span style="font-size:12pt"><strong><span style="font-size:13.0pt"><span style="color:red">一、依中央流行疫情指揮中心公布之「</span></span></strong><a data-cke-saved-href="https://www.cdc.gov.tw/Category/MPage/IRvJdHilZERpzIaEHWKAUg" href="https://www.cdc.gov.tw/Category/MPage/IRvJdHilZERpzIaEHWKAUg" style="color: blue;" target="_blank"><strong><span style="color:red">具感染風險民眾追蹤管理機制</span></strong></a><strong><span style="font-size:13.0pt"><span style="color:red">」，需「居家隔離」、「居家檢疫」及「自主健康管理」，且尚未期滿者。</span></span></strong></span></p>\
<p style="margin-left:33px; text-align:justify">\
    <span style="font-size:12pt"><strong><span style="font-size:13.0pt"><span style="color:red">二、14天內曾與返國親友接觸者。</span></span></strong></span></p>\
<p style="margin-left:35px; text-align:justify">\
    <span style="font-size:12pt"><strong><span style="font-size:13.0pt"><span style="color:red">三、有發燒、乾咳、倦怠、四肢無力、呼吸急促、上呼吸道症狀(咳嗽、喉嚨痛、打噴嚔、呼吸困難等)、肌肉痛、頭痛、腹瀉、嗅覺或味覺喪失（或異常）等症狀者。</span></span></strong></span></p>\
<p style="text-align:justify">\
    <span style="font-size:12pt"><strong><span style="font-size:13.0pt">防疫工作人人有責，<span style="color:red">學員在本學院研習期間需自備口罩並配戴</span>；更多資訊請詳閱衛生福利部疾病管制署網站（</span></strong><a data-cke-saved-href="https://www.cdc.gov.tw/" href="https://www.cdc.gov.tw/" style="color: blue;"><span style="font-size:13.0pt">https://www.cdc.gov.tw/</span></a><strong><span style="font-size:13.0pt">），或撥打防疫專線1922。</span></strong></span></p>\
<p style="margin-left:37px">\
    <span style="font-size:11pt"><span style="font-size:14.0pt">一、研習目標：審視我國情勢，並將所觀察之事物轉化為符合我國特定需求。</span></span></p>\
<p style="margin-left:37px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">二、研習時間：民國107年1月24日至107年1月24日止，為期1天。</span></span></p>\
<p style="margin-left:37px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">三、報到時間：民國107年1月24日上午08時30分至09時00分。</span></span></p>\
<p style="margin-left:37px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">四、報到及研習地點：行政院人事行政總處公務人力發展學院（臺北院區）教學棟5樓501教室（臺北市大安區新生南路3段30號）。</span></span></p>\
<p style="margin-left:37px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">五、研習事項說明：</span></span></p>\
<p style="margin-left:66px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（一）為應相關行政資訊作業，於研習期間請攜帶智慧型手機(或平板電腦)及身分證，以利辦理學員報到與問卷填答及臺北院區之用餐、住宿、停車、自學中心、商務中心及休閒設施使用。</span></span></p>\
<p style="margin-left:65px">\
    <span style="font-size:12pt">&nbsp;<span style="font-size:14.0pt">1.</span><span style="font-size:14.0pt">學員報到及問卷填答：報到可使用身分證刷卡或「e等公務園」APP簽到等方式擇一，另各班別「滿意度問卷」請以「e等公務園」APP填答。</span></span></p>\
<p style="margin-left:67px">\
    <span style="font-size:12pt">&nbsp;<span style="font-size:14.0pt">2.</span><span style="font-size:14.0pt">訓前下載並安裝「e等公務園」APP：請於開訓前以智慧型手機(或平板電腦)下載並安裝「e等公務園」APP，有關APP下載、帳號密碼查詢、問卷填答及報到之說明，請參閱</span><a data-cke-saved-href="https://appweb.hrd.gov.tw/base/10001/door/操作手冊/manual_app.pdf" href="https://appweb.hrd.gov.tw/base/10001/door/操作手冊/manual_app.pdf" style="color: blue;"><span style="font-size:14.0pt">連結</span></a><span style="font-size:14.0pt">資料。</span></span></p>\
<p style="margin-left:66px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（二）訓前確認班期、個人資料及資料下載：</span></span></p>\
<p style="margin-left:65px">\
    <span style="font-size:12pt">&nbsp;<span style="font-size:14.0pt">1.</span><span style="font-size:14.0pt">開訓前3天請至本學院全球資訊網(網址：</span><a data-cke-saved-href="https://www.hrd.gov.tw/" href="https://www.hrd.gov.tw/" style="color: blue;">https://www.hrd.gov.tw/</a><span style="font-size:14.0pt">)</span><span style="font-size:14.0pt">，點選「學員」身分後登入「訓練需求及學習服務系統」，查詢課程表資訊並確認公告學員名冊之個人資料（含用餐習慣、住宿登記）是否正確，忘記帳號密碼者請洽機關訓練承辦人協助設定。另學員須知、臺北院區交通位置圖、平面圖及哺乳室使用規則等相關資訊，亦請於該系統或「e等公務園」APP查詢。</span></span></p>\
<p style="margin-left:65px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">2.</span><span style="font-size:14.0pt">為響應節能減碳政策，本學院部分班別不再提供紙本講義，109年實施無紙化措施班別請參閱附檔（</span><a data-cke-saved-href="http://mediab.hrd.gov.tw/courses/tmp/109年人力學院臺北院區無紙化措施班別一覽表.pdf" href="http://mediab.hrd.gov.tw/courses/tmp/109年人力學院臺北院區無紙化措施班別一覽表.pdf" style="color: blue;"><span style="font-size:14.0pt">109</span><span style="font-size:14.0pt">年人力學院【臺北院區】無紙化措施班別一覽表</span></a><span style="font-size:14.0pt">），請參訓學員於開訓前3天至本學院「訓練需求及學習服務系統」下載課程講義，並請遵守著作權法相關規範，講義僅提供當期課程使用，不得任意轉載利用。</span></span></p>\
<p style="margin-left:66px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（三）修正或更換參訓人員：</span></span></p>\
<p style="margin-left:65px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">1.</span><span style="font-size:14.0pt">應於開訓前1上班日上午10時以前聯絡本學院辦班人員，並將更換人員報名表e-mail予辦班人員辦理更正。</span></span></p>\
<p style="margin-left:65px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">2.</span><span style="font-size:14.0pt">學員如未攜帶個人身分證或逾時限更換參訓人員者，屆時需先行墊付臺北院區住宿及用餐等相關費用，為避免造成不便，務請配合辦理。</span></span></p>\
<p style="margin-left:72px">\
    <span style="font-size:12pt">&nbsp;<span style="font-size:14.0pt">六、<span style="color:black">本學院（臺北院區）用餐規定：</span></span></span></p>\
<p style="margin-left:64px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（一）一般年度訓練計畫班期：研習期間僅供應午餐。經核准住宿者，以全日供膳為原則，惟開訓日早餐、結訓日晚餐及例假日均不供膳；另經核准前1天住宿者，得供應開訓日早餐。</span></span></p>\
<p style="margin-left:61px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（二）經核准供應早餐者，除天然災害期間依本學院天然災害期間學員事務處理事項辦理外，學員用餐時間均為上午7時至8時40分，逾時不供應。</span></span></p>\
<p style="margin-left:61px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（三）年度訓練實施計畫已註明不供餐班期，依其規定辦理。</span></span></p>\
<p style="margin-left:94px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（四）委(代)辦班期及特殊班期依核定實施計畫或簽准案辦理。</span></span></p>\
<p style="margin-left:37px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt"><span style="color:black">七、</span></span><span style="font-size:14.0pt">本學院（臺北院區）住宿規定：</span></span></p>\
<p style="margin-left:66px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（一）研習期間不住班，惟遠道者可申請登記住宿；另開訓日第1節課為下午者，不提供提前住宿。</span></span></p>\
<p style="margin-left:66px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（二）經核准提前1天住宿者，請攜帶身分證於當日下午3時至午夜12時辦理住宿手續；開訓當日住宿者，請於當日下午3時至6時辦理住宿手續。逾時本學院不提供住宿。住宿學員請於結訓當日下午1時30分前辦理退房手續。</span></span></p>\
<p style="margin-left:66px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（三）本學院（臺北院區）1樓大廳福華國際文教會館(以下簡稱會館)櫃檯提供行李寄放服務。</span></span></p>\
<p style="margin-left:37px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">八、本學院（臺北院區）停車規定：</span></span></p>\
<p style="margin-left:66px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（一）如有開車者，請妥善保管停車晶片卡，研習期間請憑身分證至地下2樓停車管理室消磁後，於出口處投入停車晶片卡後即可離開停車場。</span></span></p>\
<p style="margin-left:66px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">（二）如不慎遺失停車晶片卡，請憑身分證至地下2樓停車管理室繳交工本費用新臺幣100元。</span></span></p>\
<p style="margin-left:30px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">九、為珍惜學習資源，本學院將每月檢送學員請假及未到訓名單通知各主管機關，學員如未能到訓時，請洽服務機關人事單位更換人員或寄送未到訓通知單；如需請假，請依規定辦理請假手續。另為加強知識擴散，請參訓人員訓後於服務機關（單位）內部進行知識分享，例如採讀書會、簡報、參訓心得寫作等，其方式不拘。</span></span></p>\
<p style="margin-left:37px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">十、本學院（臺北院區）室內場所及住宿房間全面禁煙，另為顧及個人衛生及配合環保政策，研習期間請自行攜帶水杯備用。</span></span></p>\
<p style="margin-left:37px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">十一、辦班人員：培育發展組凌芳琪；E-Mail：nima@hrd.gov.tw；電話：（02）83691399轉8208；傳真：（02）83695611。研習期間如需聯絡學員請撥學員專線：02-83691399轉6232，傳真：02-83695808。</span></span></p>\
<p style="margin-left:57px">\
    <span style="font-size:12pt"><span style="font-size:14.0pt">十二、學員研習期間得依據</span><a data-cke-saved-href="https://lib.rad.gov.tw/libweb/service.htm" href="https://lib.rad.gov.tw/libweb/service.htm" style="color: blue;"><span style="font-size:14.0pt">「行政院人事行政總處公務人力發展學院臺北院區自學中心使用須知」</span></a><span style="font-size:14.0pt"><span style="color:black">使用相關圖書服務，惟研習上課時間不提供在班學員使用，自學中心介紹請參閱附檔（</span></span><a data-cke-saved-href="http://mediab.hrd.gov.tw/courses/tmp/臺北院區自學中心介紹1080601.pdf" href="http://mediab.hrd.gov.tw/courses/tmp/臺北院區自學中心介紹1080601.pdf" style="color: blue;"><span style="font-size:14.0pt">自學中心介紹</span></a><span style="font-size:14.0pt"><span style="color:black">），另臺北院區得依據</span></span><a data-cke-saved-href="http://mediab.hrd.gov.tw/courses/tmp/公務人力發展學院福華國際文教會館商務中心使用須知.pdf" href="http://mediab.hrd.gov.tw/courses/tmp/公務人力發展學院福華國際文教會館商務中心使用須知.pdf" style="color: blue;"><span style="font-size:14.0pt">「福華文教會館商務中心使用須知」</span></a><span style="font-size:14.0pt"><span style="color:black">使用電腦（含上網服務）、借用視</span></span><span style="font-size:14.0pt">聽媒體及書刊。</span></span></p>\
<p>\
    <span style="font-size:14.0pt">十三、本學院（臺北院區）地下1樓提供游泳池、按摩池、桌球、撞球、健身設施等，學員均需使用個人身分證至會館休閒中心櫃檯刷卡，設施開放使用時段為每日上午6時至8時、中午12時至下午1時30分、下午4時30分至6時30分，另學員應自備所需之服裝及用具（如：游泳衣帽、桌球器具，使用健身設施請著運動鞋）。</span></p>\
');
                } else if(data == '3'){
                    CKEDITOR.instances.editor.setData('<p>\
    &nbsp;</p>\
<p align="center">\
    行政院人事行政總處公務人力發展學院　研習通知</p>\
<p>\
    親愛的學員，歡迎您參加107年環境洞察研習班第1期研習，以下相關事項請參閱：</p>\
<p>\
    <strong>※<strong>為因應嚴重特殊傳染性肺炎（COVID-19，簡稱武漢肺炎）防疫措施，若有下列情形之一者，請勿到訓，並請通知貴機關人事單位進行換員或email填覆未到訓通知單至本學院：</strong></strong></p>\
<p style="margin-left:24.45pt;">\
    <strong>一、依中央流行疫情指揮中心公布之「</strong><a href="https://www.cdc.gov.tw/Category/MPage/IRvJdHilZERpzIaEHWKAUg" target="_blank"><strong>具感染風險民眾追蹤管理機制</strong></a><strong>」，需「居家隔離」、「居家檢疫」及「自主健康管理」，且尚未期滿者。</strong></p>\
<p style="margin-left:24.45pt;">\
    <strong>二、14天內曾與返國親友接觸者。</strong></p>\
<p style="margin-left:25.9pt;">\
    <strong>三、有發燒、乾咳、倦怠、四肢無力、呼吸急促、上呼吸道症狀(咳嗽、喉嚨痛、打噴嚔、呼吸困難等)、肌肉痛、頭痛、腹瀉、嗅覺或味覺喪失（或異常）等症狀者。</strong></p>\
<p>\
    <strong>防疫工作人人有責，學員在本學院研習期間需自備口罩並配戴；更多資訊請詳閱衛生福利部疾病管制署網站（</strong><a href="https://www.cdc.gov.tw/">https://www.cdc.gov.tw/</a><strong>），或撥打防疫專線1922。</strong></p>\
<p style="margin-left:28.0pt;">\
    一、研習目標：審視我國情勢，並將所觀察之事物轉化為符合我國特定需求。</p>\
<p style="margin-left:28.0pt;">\
    二、研習時間：民國107年2月5日至107年2月5日止，為期1天。</p>\
<p style="margin-left:28.0pt;">\
    三、報到時間：民國107年2月5日上午08時30分至09時00分。</p>\
<p style="margin-left:28.0pt;">\
    四、報到及研習地點：行政院人事行政總處公務人力發展學院（南投院區）文教大樓7樓701教室（南投市光明路1號）。</p>\
<p style="margin-left:28.0pt;">\
    五、研習事項說明：</p>\
<p style="margin-left:49.35pt;">\
    （一）研習期間請攜帶智慧型手機(或平板電腦)及身分證，以利辦理報到與問卷填答。</p>\
<p style="margin-left:48.3pt;">\
    &nbsp;1.報到及問卷填答：報到可使用身分證刷卡或「e等公務園」APP簽到，「滿意度問卷」請以「e等公務園」APP填答。</p>\
<p style="margin-left:50.4pt;">\
    &nbsp;2.下載並安裝「e等公務園」APP：以智慧型手機(或平板電腦)下載並安裝「e等公務園」APP，有關APP下載、帳號密碼查詢、問卷填答及報到之說明，請參閱<a href="https://appweb.hrd.gov.tw/base/10001/door/%E6%93%8D%E4%BD%9C%E6%89%8B%E5%86%8A/manual_app.pdf">連結</a>資料。</p>\
<p style="margin-left:49.35pt;">\
    （二）訓前確認班期、個人資料及資料下載：</p>\
<p style="margin-left:48.45pt;">\
    &nbsp;1.前3日請至本學院全球資訊網(網址：<a href="https://www.hrd.gov.tw/">https://www.hrd.gov.tw/</a>)，點選「學員」身分後登入「訓練需求及學習服務系統」，查詢課程表資訊並確認公告學員名冊之個人資料（含用餐習慣、住宿登記）是否正確，忘記帳號密碼者請洽機關訓練承辦人協助設定。另學員須知、南投院區交通位置圖、平面圖及哺乳室使用規則等相關資訊，亦請於該系統或「e等公務園」APP查詢。</p>\
<p style="margin-left:48.45pt;">\
    2.本學院部分班別不提供紙本講義，無紙化班別請參閱附檔（<a href="http://mediab.hrd.gov.tw/courses/tmp/109%E5%B9%B4%E4%BA%BA%E5%8A%9B%E5%AD%B8%E9%99%A2%E3%80%90%E5%8D%97%E6%8A%95%E9%99%A2%E5%8D%80%E3%80%91%E7%84%A1%E7%B4%99%E5%8C%96%E6%8E%AA%E6%96%BD%E7%8F%AD%E5%88%A5%E4%B8%80%E8%A6%BD%E8%A1%A8.pdf">109年人力學院【南投院區】無紙化措施班別一覽表</a>），請於開訓前3日至本學院「訓練需求及學習服務系統」下載課程講義，並請遵守著作權法相關規範，講義僅提供當期課程使用，不得任意轉載利用。</p>\
<p style="margin-left:49.35pt;">\
    （三）修正或更換參訓人員：於開訓前1上班日上午10時以前聯絡本學院班務人員，並將更換人員報名表e-mail班務人員辦理更正。</p>\
<p style="margin-left:53.9pt;">\
    &nbsp;六、本學院（南投院區）用餐規定：</p>\
<p style="margin-left:48.3pt;">\
    （一）研習期間全日供膳為原則，但提前住宿者，供應開訓日早餐，結訓日最後一節課未達用餐時間不供膳。</p>\
<p style="margin-left:45.5pt;">\
    （二）早、晚餐以自助式供餐，午餐以桌餐方式提供。</p>\
<p style="margin-left:45.5pt;">\
    （三）早餐：07時至08時30分、午餐：12時至12時45分、晚餐：17時至18時30分。</p>\
<p style="margin-left:28.0pt;">\
    七、本學院（南投院區）住宿規定：</p>\
<p style="margin-left:49.35pt;">\
    （一）研習期間得由本學院南投院區提供住宿之班期學員，於報到時向班務人員領取房卡。</p>\
<p style="margin-left:46.2pt;">\
    （二）結訓日當日、居住或服務機關位於南投市、草屯鎮等鄰近地區者，不提供住宿。</p>\
<p style="margin-left:46.9pt;">\
    （三）研習天數為1日之班期，原則不提供住宿(行動不便及花東、離島、其他偏遠地區交通不便者，得申請提前住宿)。</p>\
<p style="margin-left:49.35pt;">\
    （四）完成報名手續且登記住宿之遠途學員，可於報到前一天下午11時前至本學院「值日室」簽名後領取房卡住宿(入住時間週一至週四下午4時以後，星期日上午10時以後)，並於翌日上午7時至8時逕至文教大樓地下1樓餐廳用餐。報到前一天晚餐請自理（本院區至鄰近商店徒步約需20分鐘）。開訓日第1節課為下午者，不提供提前住宿。</p>\
<p style="margin-left:49.35pt;">\
    （五）寢室房卡使用：房卡輕貼鎖面不動，感應3至5秒，待&rdquo;嗶&rdquo;一聲及開鎖聲音停止後，才可按門把開門，門鎖在啟動開及關閉時切勿按門把，易造成門鎖損壞，房卡請隨身攜帶，結訓時繳回。</p>\
<p style="margin-left:49.35pt;">\
    （六）本學院不再提供水杯、盥洗用品(含牙膏、牙刷、毛巾、香皂)，請學員自備。另提供重複使用之拖鞋，學員如有個人衛生考量，亦請自備。</p>\
<p style="margin-left:28.0pt;">\
    &nbsp;八、本學院（南投院區）停車規定：</p>\
<p style="margin-left:1.0cm;">\
    本院區提供停車位免費停車（建議儘量共乘或搭乘公車），但不負保管責任，請依規定停放，並將停車證放置擋風玻璃前左側，以供查驗；如停車位已滿，請停放於本學院大門外路邊、鄰近之國史館臺灣文獻館對面停車場及中興新村高爾夫球場旁停車場(仍請遵守交通規則，紅線、車體超過白線、或白線寬度未超過15公分處請勿停車)。</p>\
<p style="margin-left:28.0pt;">\
    九、本學院將每月檢送學員請假及未到訓名單通知各主管機關，學員如未能到訓時，請洽服務機關人事單位更換人員或寄送未到訓通知單；如需請假，請依規定辦理請假手續。另為加強知識擴散，請參訓人員訓後於服務機關（單位）內部進行知識分享，例如採讀書會、簡報、參訓心得寫作等，其方式不拘。</p>\
<p style="margin-left:28.0pt;">\
    十、本學院（南投院區）室內場所及住宿房間全面禁煙。</p>\
<p style="margin-left:42.55pt;">\
    十一、班務人員：培育發展組陳嘉祥；E-Mail：jerry@hrd.gov.tw；電話：（049）2332131轉7211；傳真：（049）2370962。研習期間如需聯絡學員請洽各班務人員。</p>\
<p style="margin-left:42.55pt;">\
    十二、有關「報到/住宿」、「交通/停車」、「學員請假規定」等相關事項訊息，請至本學院網站(<a href="https://www.hrd.gov.tw/">https://www.hrd.gov.tw/</a>)「南投院區」「學員服務專區」瀏覽。<span style="font-family: 新細明體, serif; font-size: 14pt;">十三、本學院（南投院區）休閒中心，提供桌球、撞球、跑步機、</span><span lang="EN-US" style="font-family: 新細明體, serif; font-size: 14pt;">KTV</span><span style="font-family: 新細明體, serif; font-size: 14pt;">設施等，開放時間為週一至週四上午</span><span lang="EN-US" style="font-family: 新細明體, serif; font-size: 14pt;">8</span><span style="font-family: 新細明體, serif; font-size: 14pt;">時至下午</span><span lang="EN-US" style="font-family: 新細明體, serif; font-size: 14pt;">9</span><span style="font-family: 新細明體, serif; font-size: 14pt;">時，週五上午</span><span lang="EN-US" style="font-family: 新細明體, serif; font-size: 14pt;">8</span><span style="font-family: 新細明體, serif; font-size: 14pt;">時至下午</span><span lang="EN-US" style="font-family: 新細明體, serif; font-size: 14pt;">5</span><span style="font-family: 新細明體, serif; font-size: 14pt;">時，其中</span><span lang="EN-US" style="font-family: 新細明體, serif; font-size: 14pt;">KTV</span><span style="font-family: 新細明體, serif; font-size: 14pt;">設施需申請使用，開放時間週一至週四下午</span><span lang="EN-US" style="font-family: 新細明體, serif; font-size: 14pt;">6</span><span style="font-family: 新細明體, serif; font-size: 14pt;">時至</span><span lang="EN-US" style="font-family: 新細明體, serif; font-size: 14pt;">9</span><span style="font-family: 新細明體, serif; font-size: 14pt;">時，週五不開放。如使用健身設施請著運動鞋。</span></p>\
<p>\
    &nbsp;</p>\
<body style="position: absolute; top: 12px; width: 1px; height: 1px; overflow: hidden; left: -1000px;">\
    <br />\
</body>\
<br />\
');
                }
           
           
            },
            error: function() {
                console.log('Ajax Error');
            }
        });

        
        changeTerms();
    };

    function setdvb(){
       document.getElementById("dvmail").style.visibility="visible";
       $("#subject").val("行政院人事行政總處公務人力發展學院-"+$('#classes').find("option:selected").text().substr(7)+"第"+$('#terms').find("option:selected").text().replace(/\b(0+)/gi,"")+"期-天然災害處理原則通知"); 

       CKEDITOR.instances.editor.setData('<p>\
    各位學長：</p>\
<p>\
    &nbsp;&nbsp;&nbsp; 有關您將於民國107年5月21日至107年5月25日止，為期5天。參加本學院（臺北院區）107年初任簡任官等主管職務人員研究班第1期，因適逢颱風影響期間，本研習班之課程進行將依據本學院天然災害停課處理原則（如後附）辦理，敬請參閱。</p>\
<p>\
    &nbsp;&nbsp;&nbsp;</p>\
<p>\
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;行政院人事行政總處公務人力發展學院&nbsp; 敬啟</p>\
<p>\
    &nbsp;</p>\
<p>\
    &nbsp;</p>\
<p align="center">\
    <strong>行政院人事行政總處公務人力發展學院</strong></p>\
<p align="center">\
    <strong>天然災害停課處理原則</strong></p>\
<table cellpadding="0" cellspacing="0" style="border-style:none" width="100%">\
    <tbody>\
        <tr style="border-style:none">\
            <td style="width:51.7%;height:41px;border-style:none">\
                <p>\
                    &nbsp;</p>\
            </td>\
            <td style="width:48.3%;height:41px;border-style:none">\
                <p>\
                    106年7月7日發布<br />\
                    108年4月23日人發綜字第1080300458號函修正<br />\
                    108年7月18日人發綜字第1080300840號函修正</p>\
            </td>\
        </tr>\
    </tbody>\
</table>\
<p>\
    一、依據天然災害停止上班及上課作業辦法，本學院所在地之地方政府(臺北院區為臺北市政府、南投院區為南投縣政府)發布停止上班時，所在地院區各班期均比照停課且不另行補課。前項停課班期於恢復上班時，如尚有未實施之課程，仍應依各班期課程表繼續辦理。但有例外情形，將另行通知。</p>\
<p>\
    二、學員於停課期間，已提前住宿或住宿者，仍可留宿，並由本學院供膳，膳宿費用由本學院支應。</p>\
<p>\
    三、學員遇有天然災害停止上班及上課作業辦法第13條至第15條所規定之情形，得自行決定停止參訓，並由服務機關通報本學院。</p>\
<p>\
    四、本學院所在地之地方政府發布停止上班時，所在地院區圖書還書期限順延。</p>\
<p>\
    五、停課期間，本學院臺北院區留宿學員如需使用福華國際文教會館(以下簡稱會館)各項服務，請逕洽詢會館大廳櫃檯服務人員；南投院區留宿學員膳宿服務，請洽詢各班班務人員。</p>\
<p>\
    六、其他臨時事項，請參閱本學院全球資訊網站最新消息相關公告訊息。</p>\
');

       changeTerms();
   };

   function changeTerms(){
        $("#mlink").attr("href","/admin/transfer_training_letter/list/"+$('#classes').val()+"_"+$("#terms").val()+"_"+$("#subject").val());
    };

</script>
@endsection