
@if (isset ($addressList))
    @if(isset($trainerDetails->internal_yn) && $trainerDetails->internal_yn== 'N')
        <div class="card">
            <div class="card-body">
            <section id="horizontal-vertical">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Address List</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <div class="table-responsive">
                                        <table width="100%" class="table nowrap scroll-horizontal-vertical">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Address Type</th>
                                                <th>Address Line</th>
                                                <th>Division</th>
                                                <th>District</th>
                                                <th>Post Office</th>
                                                <th>Post Office Code</th>
                                                <th>Action</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                             @php($i=1)
                                             @foreach($addressList as $value)
                                            <tr>
                                                <td>{{$i++}}</td>
                                                <td>{{strtolower($value->add_type->address_type)}}</td>
                                                <td>{{$value->address_line}}</td>
                                                <td>{{strtolower($value->div_name->geo_division_name)}}</td>
                                                <td>{{strtolower($value->dis_name->geo_district_name)}}</td>
                                                <td>{{$value->post_office}}</td>
                                                <td>{{$value->post_code}}</td>
                                                <td>
                                                    <a href="{{route('trainer-information.trainer-address-get',[$trainer_id,"address_id" => $value->address_id])}}"><i class="bx bx-edit"></i></a>
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

