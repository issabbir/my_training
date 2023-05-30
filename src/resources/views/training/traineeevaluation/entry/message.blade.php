{{--<div class="alert alert-warning" role="alert">
    {{$params['o_status_message']}}
</div>--}}

<script>
    $(".alert").fadeTo(2000, 2000).slideUp(500, function(){
        $(".alert").slideUp(500);
    });
</script>
<div class="alert @if($params['o_status_code'] == 1) alert-success @else alert-danger @endif" role="alert">
    <button type="button" class="close" data-dismiss="alert">×</button>
    {{$params['o_status_message']}}
</div>

