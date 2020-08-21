
<title>{!! $title !!}</title>
<center>
    <br><br><br>
    <table width="700px" height="100px;" style="min-width: 332px; max-width: 600px; border: 1px solid #F0F0F0; border-top: 0;">
        <tr>

            <td colspan="3" bgcolor="#3c8dbc" style="color:white" width="100%" border="0" cellspacing="0" cellpadding="0" >
                <center>
                    <h2>
                        <br><br>
                        系統通知信
                    </h2>
                </center>
            </td>
        </tr>

        <!-- content -->
        <tr style="border-collapse:collapse;">
            <table style="border-collapse:collapse;" width="595">
                <tr>
                    <td width="32px" bgcolor="#FAFAFA"></td>
                    <td bgcolor="#FAFAFA">
                        <br>
                        <div>{!! $content !!}</div>

                    </td>
                    </td>
                </tr>
            </table>
        </tr>

        <!-- footer -->
        <tr style="border-collapse:collapse;">
            <table style="border-collapse:collapse;" width="595">
                <tr>
                    <td width="120px" bgcolor="#FAFAFA"></td>
                    <td bgcolor="#FAFAFA" style="font-size: 10px;color:#B9B9B9;">
                        <br><br><br><br><br><br>
                        此信件由系統發送，請勿直接回覆Email，詳情請造訪
                        <a href="{{ config('app.url') }}" target="_blank" style="text-decoration: none;color: #4285F4;">{{ config('app.email_service_form') }}</a>
                        <br><br>
                    </td>
                </tr>
            </table>
        </tr>
    </table>
    <br><br><br><br><br><br><br>
</center>
