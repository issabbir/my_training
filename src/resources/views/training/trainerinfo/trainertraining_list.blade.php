@if (isset ($trainingList))
    @if(isset($trainerDetails->internal_yn) && $trainerDetails->internal_yn== 'N')
    <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Trainer Training List</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="table-responsive">
                                    <table width="100%"  class="table scroll-horizontal-vertical">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Training Name</th>
                                            <th>Institute Name</th>
                                            <th>Institute Address</th>
                                            <th>Training Duration</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                         @php($i=1)
                                         @foreach($trainingList as $value)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$value->training_name}}</td>
                                            <td>{{$value->institute_name}}</td>
                                            <td>{{$value->institute_address}}</td>
                                            <td>{{$value->training_duration}}</td>
                                            <td>
                                                <a href="{{route('trainer-information.trainer-training-get',[$trainer_id,"trainer_training_id" => $value->trainer_training_id])}}"><i class="bx bx-edit"></i></a>
                                            </td>
                                        </tr>
                                        @endforeach


                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    @endif
@endif

