<title>行政院人事行政總處公務人力發展學院　鐘點費入帳通知</title>
<center>
    <br>
            <table style="border-collapse:collapse;" width="420">
               <!--  <tr>
                    <td align="center" >
                        鐘點費入帳通知<br><br>
                    </td>
                </tr> -->
                <tr>
                    <td>
                    
                        授課期間：{{$data['period']}} <br><br><br>
                        上課班期名稱：{{$data['class_name_term']}} <br><br><br>
                        授課時間：{{$data['period']}}{{$data['lecthr']}} <br><br><br>
                        鐘點費：{{$data['lectamt']}}元 <br><br><br>
                        交通費：{{$data['tratot']}}元 <br><br><br>

                        <?php if($data['otheramt'] > 0) {?>
                        住宿費：{{$data['otheramt']}}元 <br><br><br>
                        <?php } ?>

                        <?php if($data['review_total'] > 0) {?>
                        評閱費：：{{$data['review_total']}}元 <br><br><br>
                        <?php } ?>

                        <?php if($data['other_salary'] > 0) {?>
                        其他薪資所得：{{$data['other_salary']}}元 <br><br><br>
                        <?php } ?>

                        <?php if($data['insuretot'] > 0) {?>
                        扣取補充保險費：{{$data['insuretot']}}元 <br><br><br>
                        <?php } ?>

                        <?php if($data['deductamt'] > 0) {?>
                        扣繳稅額：{{$data['deductamt']}}元 <br><br><br>
                        <?php } ?>

                        劃撥郵局或金融機構代理：{{$data['post_bank']}} <br><br><br>
                        匯款日期：{{$data['tdate']}} <br><br><br>

                        
                    </td>
                </tr>
                <tr>
                    <td align="right" >
                    行政院人事行政總處公務人力發展學院　　敬啟<br><br><br><br>
                    </td>
                </tr>
                <tr>
                    <td>
                    本通知係由系統自動發送，講座如不願意再收到本學院<br><br><br>
                    </td>
                </tr>
                <tr>
                    <td>
                    入帳通知訊息，請直接回覆此郵件，爾後將不再發送。<br><br><br>
                    </td>
                </tr>
            </table>
    <br>
</center>
