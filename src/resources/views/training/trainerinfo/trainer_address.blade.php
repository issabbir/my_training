@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Table Start -->
                <div class="card-body">
                    <h4 class="card-title">Trainer Information</h4><!---->
                    <hr>
                    @if(Session::has('message'))
                        <div class="alert {{Session::get('m-class') ? Session::get('m-class') : 'alert-danger'}} show"
                             role="alert">
                            {{ Session::get('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                @endif
                <!-- vertical Wizard start-->
                    <section id="vertical-wizard">
                        <form
                            @if (isset($addressView->address_id)) action="{{route('trainer-information.trainer-address-update',[$trainer_id,$addressView->address_id])}}"
                            @else action="{{route('trainer-information.trainer-address-post',$trainer_id)}}"
                            @endif method="post">
                            @csrf
                            @if (isset($addressView->address_id))
                                @method('PUT')
                            @endif
                            <div class="card">
                                <div class="card-content">
                                    <div class="row pills-stacked">
                                        <div class="col-md-3 col-sm-12 border-right pr-md-0">
                                            @include('tab')
                                        </div>
                                        <div class="col-md-9 col-sm-12">
                                            <div class="tab-content bg-transparent shadow-none">
                                                <div role="tabpanel" class="tab-pane active" id="vertical-pill-1"
                                                     aria-labelledby="stacked-pill-1"
                                                     aria-expanded="true">
                                                    <fieldset class="pt-0">
                                                        <h6 class="pb-50"><b>Address Details</b></h6>
                                                        @if (isset ($trainerDetails->internal_yn))
                                                            @if($trainerDetails->internal_yn == 'Y')
                                                                <div class="table-responsive">
                                                                    <table width="100%"
                                                                           class="table nowrap scroll-horizontal-vertical">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>SL</th>
                                                                            <th>Address Type</th>
                                                                            <th>Address Line</th>
                                                                            <th>Division</th>
                                                                            <th>District</th>
                                                                            <th>Post Office Code</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @if (isset($empAddressList))
                                                                            @php $i=1; @endphp
                                                                            @foreach($empAddressList as $value)
                                                                                <tr>
                                                                                    <td>{{$i++}}</td>
                                                                                    <td>{{strtolower($value->address_type)}}</td>
                                                                                    <td>{{$value->address_line_1}}</td>
                                                                                    <td>{{strtolower($value->geo_division_name)}}</td>
                                                                                    <td>{{strtolower($value->geo_district_name)}}</td>
                                                                                    <td>{{$value->post_code}}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="address_type_id"
                                                                                   class="required">Address Type </label>
                                                                            <select class="custom-select form-control"
                                                                                    required
                                                                                    id="address_type_id"
                                                                                    name="address_type_id">
                                                                                <option value="">Select One</option>
                                                                                @foreach($address_type as $address)
                                                                                    <option
                                                                                        value="{{$address->address_type_id}}"
                                                                                        {{isset($addressView->address_type_id) && $addressView->address_type_id == $address->address_type_id ? 'selected' : ''}}
                                                                                    >{{$address->address_type}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-12">
                                                                        <div class="form-group">
                                                                            <label for="address_line" class="required">Address
                                                                                Line</label>
                                                                            <input type="text" class="form-control"
                                                                                   id="address_line"
                                                                                   required
                                                                                   name="address_line"
                                                                                   value="{{old('',isset($addressView->address_line) ? $addressView->address_line : '')}}"
                                                                                   placeholder="Enter Address">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="geo_division_id"
                                                                                   class="required">Division </label>
                                                                            <select
                                                                                class="custom-select form-control select2"
                                                                                required
                                                                                id="geo_division_id"
                                                                                name="geo_division_id">
                                                                                <option value="">Select One</option>
                                                                                @foreach($divisions as $division)
                                                                                    <option
                                                                                        value="{{$division->geo_division_id}}"
                                                                                        {{isset($addressView->geo_division_id) && $addressView->geo_division_id == $division->geo_division_id ? 'selected' : ''}}
                                                                                    >{{$division->geo_division_name}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="geo_district_id"
                                                                                   class="required">District </label>
                                                                            <select
                                                                                class="custom-select form-control select2"
                                                                                required
                                                                                id="geo_district_id"
                                                                                name="geo_district_id">
                                                                                <option value="">Select One</option>
                                                                                @if ($addressView)
                                                                                    @php $districts = \App\Helpers\HelperClass::findDistrictByDivision($addressView->geo_division_id) @endphp
                                                                                    @foreach($districts as $district)
                                                                                        <option
                                                                                            value="{{$district->geo_district_id}}" {{isset($addressView->geo_district_id) && $addressView->geo_district_id == $district->geo_district_id ? 'selected' : ''}}>{{$district->geo_district_name}}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="geo_thana_id" class="required">Thana</label>
                                                                            <select
                                                                                class="custom-select form-control select2"
                                                                                required
                                                                                id="geo_thana_id" name="geo_thana_id">
                                                                                <option value="">Select One</option>
                                                                                @if ($addressView)
                                                                                    @php $thanas = \App\Helpers\HelperClass::findDivisionByThana($addressView->geo_district_id) @endphp
                                                                                    @foreach($thanas as $thana)
                                                                                        <option
                                                                                            value="{{$thana->geo_thana_id}}" {{isset($addressView->geo_thana_id) && $addressView->geo_thana_id == $thana->geo_thana_id ? 'selected' : ''}}>{{$thana->geo_thana_name}}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="post_office" class="required">Post
                                                                                Office </label>
                                                                            <input class="form-control"
                                                                                   name="post_office" id="post_office"
                                                                                   required
                                                                                   value="{{old('',isset($addressView->post_office) ? $addressView->post_office : '')}}"
                                                                                   type="text">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="post_code" class="required">Post
                                                                                Office Code</label>
                                                                            <input type="number" class="form-control"
                                                                                   id="post_code"
                                                                                   name="post_code"
                                                                                   oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                                   maxlength="4"
                                                                                   required
                                                                                   value="{{old('',isset($addressView->post_code) ? $addressView->post_code : '')}}"
                                                                                   placeholder="Enter Post Office Code">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="trainer_id"
                                                                           value="{{$trainer_id}}">

                                                                    <div class="col-12 d-flex justify-content-end">
                                                                        <button type="submit"
                                                                                class="btn btn-primary mr-1 mb-1 mt-1">
                                                                            Save
                                                                        </button>
                                                                        <button type="reset"
                                                                                class="btn btn-light-secondary mb-1 mt-1">
                                                                            Reset
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="address_type_id" class="required">Address
                                                                            Type </label>
                                                                        <select class="custom-select form-control"
                                                                                required
                                                                                id="address_type_id"
                                                                                name="address_type_id">
                                                                            <option value="">Select One</option>
                                                                            @foreach($address_type as $address)
                                                                                <option
                                                                                    value="{{$address->address_type_id}}"
                                                                                >{{$address->address_type}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-12">
                                                                    <div class="form-group">
                                                                        <label for="address_line" class="required">Address
                                                                            Line</label>
                                                                        <input type="text" class="form-control"
                                                                               id="address_line"
                                                                               required
                                                                               name="address_line"
                                                                               placeholder="Enter Address">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="geo_division_id" class="required">Division </label>
                                                                        <select
                                                                            class="custom-select form-control select2"
                                                                            required
                                                                            id="geo_division_id" name="geo_division_id">
                                                                            <option value="">Select One</option>
                                                                            @foreach($divisions as $division)
                                                                                <option
                                                                                    value="{{$division->geo_division_id}}"
                                                                                >{{$division->geo_division_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="geo_district_id" class="required">District </label>
                                                                        <select
                                                                            class="custom-select form-control select2"
                                                                            required
                                                                            id="geo_district_id" name="geo_district_id">
                                                                            <option value="">Select One</option>
                                                                            @foreach($districts as $district)
                                                                                <option
                                                                                    value="{{$district->geo_district_id}}">{{$district->geo_district_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="geo_thana_id"
                                                                               class="required">Thana</label>
                                                                        <select
                                                                            class="custom-select form-control select2"
                                                                            required
                                                                            id="geo_thana_id" name="geo_thana_id">
                                                                            <option value="">Select One</option>
                                                                            @foreach($thanas as $thana)
                                                                                <option
                                                                                    value="{{$thana->geo_thana_id}}">{{$thana->geo_thana_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="post_office" class="required">Post
                                                                            Office </label>
                                                                        <input class="form-control" name="post_office"
                                                                               id="post_office" required
                                                                               value="{{old('',isset($addressView->post_office) ? $addressView->post_office : '')}}"
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="post_code" class="required">Post
                                                                            Office Code</label>
                                                                        <input type="number" class="form-control"
                                                                               id="post_code"
                                                                               name="post_code"
                                                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                               maxlength="4"
                                                                               required
                                                                               placeholder="Enter Post Office Code">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="trainer_id"
                                                                       value="{{$trainer_id}}">

                                                                <div class="col-12 d-flex justify-content-end">
                                                                    <button type="submit"
                                                                            class="btn btn-primary mr-1 mb-1 mt-1">Save
                                                                    </button>
                                                                    <button type="reset"
                                                                            class="btn btn-light-secondary mb-1 mt-1">
                                                                        Reset
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </fieldset>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                    <!-- vertical Wizard end-->

                </div>
                <!-- Table End -->
            </div>
            @include('training.trainerinfo.traineraddress_list')
        </div>
    </div>


@endsection

@section('footer-script')
    <script type="text/javascript">

        $('#geo_division_id').on('change', function (e) {
            e.preventDefault();
            let divisionId = $(this).val();
            if (divisionId) {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/districts/' + divisionId,
                    success: function (data) {
                        $('#geo_district_id').html(data.html);
                    },
                    error: function (err) {
                        alert('error');
                    }
                });
            } else {
                $('#geo_district_id').html('');
                $('#geo_thana_id').html('');
            }
        });

        $('#geo_district_id').on('change', function (e) {
            e.preventDefault();
            let districtId = $(this).val();
            if (districtId) {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/thanas/' + districtId,
                    success: function (data) {
                        $('#geo_thana_id').html(data.html);
                    },
                    error: function (err) {
                        alert('error');
                    }
                });
            } else {
                $('#geo_thana_id').html('');
            }
        });

    </script>

@endsection
