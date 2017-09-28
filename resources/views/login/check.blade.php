@if($check)
    <script>
        alert("验证成功！");
    </script>
@else
    <script>
        alert("验证失败！{{ $msg }}}" );
    </script>
@endif