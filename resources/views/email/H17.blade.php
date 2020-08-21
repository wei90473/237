<title>行政院人事行政總處公務人力發展學院　鐘點費入帳通知</title>
<center>
    <br>
            <table style="border-collapse:collapse;" width="420">
                <tr>
                    <td align="center" >
                        鐘點費入帳通知<br><br>
                    </td>
                </tr>
                <tr>
                    <td>
                    
                        1.  講座姓名：{{$data['cname']}} <br>
                        2.  上課班期：{{$data['class_name_term']}} <br>
                        3.  授課時間：{{$data['period']}}{{$data['lecthr']}} <br>
                        4.  鐘點費：{{$data['lectamt']}}元 <br>
                        5.  稿費：{{$data['noteamt']}}元 <br>
                        6.  講演費：{{$data['speakamt']}}元 <br>
                        7.  交通費：{{$data['tratot']}}元 <br>
                        8.  扣取補充保險費：{{$data['insuretot']}}元 <br>
                        9.  扣繳稅額：{{$data['deductamt']}}元 <br>
                        10. 劃撥郵局或金融機構代理：{{$data['totalpay']}} <br>
                        11. 匯款日期：{{$data['tdate']}} <br><br>

                        
                    </td>
                </tr>
                <tr>
                    <td align="right" >
                    行政院人事行政總處公務人力發展學院　　敬啟<br>
                    {{$data['ndate']}}<br>
                    </td>
                </tr>
            </table>
    <br>
</center>
