@if (isset ($experienceList))
    @if(isset($trainerDetails->internal_yn) && $trainerDetails->internal_yn== 'N')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Trainer Experience List</h4>
                </div>
                <div class="card-content">
                    <div class="card-body card-dashboard">
                        <div class="table-responsive">
                            <table width="100%"  class="table scroll-horizontal-vertical">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Organization Name</th>
                                    <th>Designation</th>
                                    <th>Organization Address</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                 @php($i=1)
                                 @foreach($experienceList as $value)
                                <tr>
                                    <td>{{$i++}}</td>
                                    <td>{{$value->organization_name}}</td>
                                    <td>{{$value->designation}}</td>
                                    <td>{{$value->organization_address}}</td>
                                    <td>
                                        <a href="{{route('trainer-information.trainer-experience-get',[$trainer_id,"exp_id" => $value->exp_id])}}"><i class="bx bx-edit"></i></a>
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
