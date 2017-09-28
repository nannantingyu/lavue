@if($check)
    <script>
        var con = window.confirm("验证成功！");
        if(con || !con) {
            window.location.href = "/";
        }
    </script>
@else
    <script>
        var con = window.confirm("验证失败！{{ $msg }}}");
        if(con || !con) {
            window.location.href = "/";
        }
    </script>
@endif