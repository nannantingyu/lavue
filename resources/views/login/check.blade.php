@if($check)
    <script>
        var con = window.confim("验证成功！");
        if(con || !con) {
            window.location.href = "/";
        }
    </script>
@else
    <script>
        var con = window.confim("验证失败！{{ $msg }}}");
        if(con || !con) {
            window.location.href = "/";
        }
    </script>
@endif