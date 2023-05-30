{{--<div class="card">
    <div class="card-body">
        <section id="horizontal-vertical">--}}
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
                                            <th>Expertise Name</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                         @php($i=1)
                                         @foreach($expertiseList as $value)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$value->l_exper->expertise_name}}</td>
                                            <td>{{$value->remarks}}</td>
                                            <td>{{$value->active_yn}}</td>
                                            <td>
                                                <a href="{{route('trainer-information.trainer-expertise-get',[$trainer_id,"trainer_exp_id" => $value->trainer_exp_id])}}"><i class="bx bx-edit"></i></a>
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
        {{--</section>
    </div>
</div>--}}
