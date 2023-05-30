@if($traineeInfo)
    <option value="">Select One</option>
    @foreach($traineeInfo as $data)
        <option value="{{$data->trainee_id}}">{{$data->trainee_name.' ('.$data->organization_name.')'}}</option>
    @endforeach
@endif
