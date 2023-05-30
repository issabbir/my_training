{{--<div class="alert @if($lastParams['o_status_code'] == 1) alert-success @else alert-danger @endif" role="alert">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {{$lastParams['o_status_message']}}
</div>--}}

<script>
    $(".alert").fadeTo(20000, 20000).slideUp(500, function(){
        $(".alert").slideUp(500);
    });
</script>
<div class="alert @if($params['o_status_code'] == 1) alert-success @else alert-danger @endif" role="alert">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {{$params['o_status_message']}}
</div>
