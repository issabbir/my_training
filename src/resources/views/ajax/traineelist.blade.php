@if($emplist)
    <option value="">Select One</option>
    <option value="all">ALL</option>
    @foreach($emplist as $value)
        <option value="{{$value->trainee_id}}">
            {{$value->trainee_name.'-'.$value->organization_name.'-'.$value->dept_name.'-'.$value->trainee_code}}
        </option>
    @endforeach
@endif
