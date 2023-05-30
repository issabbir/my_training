@if($emplist)
    <option value="">Select One</option>
    @foreach($emplist as $data)
        <option value="{{$data->emp_id}}">{{$data->emp_code.' ('.$data->emp_name.')'}}</option>
    @endforeach
@endif
