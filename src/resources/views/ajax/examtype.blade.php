@if($examType)
    <option value="">Select One</option>
    @foreach($examType as $data)
        <option value="{{$data->exam_type_id}}">{{$data->exam_type_name}}</option>
    @endforeach
@endif
