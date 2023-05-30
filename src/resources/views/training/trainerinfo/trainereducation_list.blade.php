@if (isset ($educationList))
    @if(isset($trainerDetails->internal_yn) && $trainerDetails->internal_yn== 'N')
        <div class="card">
    <div class="card-body">
        <section id="horizontal-vertical">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Trainer Education List</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="table-responsive">
                                    <table width="100%" class="table nowrap scroll-horizontal-vertical">
                                        <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Subject</th>
                                            <th>Pass Year</th>
                                            <th>Exam Result</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                         @php($i=1)
                                         @foreach($educationList as $value)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$value->subject}}</td>
                                            <td>{{$value->pass_year}}</td>
                                            <td>{{$value->exam_result}}</td>
                                            <td>
                                                <a href="{{route('trainer-information.trainer-education-get',[$trainer_id,"education_id" => $value->education_id])}}"><i class="bx bx-edit"></i></a>
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
        </section>
    </div>
</div>
    @endif
@endif

