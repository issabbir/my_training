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
                    <h4 class="card-title">Foreign Tour</h4>
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
                    <form enctype="multipart/form-data"
                          @if(isset($foreigntour->f_tour_id)) action="{{route('foreign-tour.foreign-tour-update',[$foreigntour->f_tour_id])}}"
                          @else action="{{route('foreign-tour.foreign-tour-post')}}" @endif method="post">
                        @csrf
                        @if (isset($foreigntour->f_tour_id))
                            @method('PUT')
                        @endif
                        <div class="row">

                            @if(isset($foreigntour->emp_id))
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="required">Emp Code</label>
                                        <select class="custom-select form-control select2" name="emp_id" id="emp_id"
                                                data-emp-id="@if($foreigntour && $foreigntour->emp_id) {{$foreigntour->emp_id}} @endif">
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="required">Emp Code</label>
                                        <select class="custom-select select2" name="emp_id" id="emp_id">

                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input disabled type="text" id="emp_name" name="emp_name"
                                           class="form-control" placeholder="Name"
                                           value="{{old('emp_name',isset($foreigntour->emp_name) ? $foreigntour->emp_name : '')}}"
                                           autocomplete="off">
                                    <input type="hidden" id="emp_name_post" name="emp_name_post"
                                           value="{{isset($foreigntour->emp_name) ? $foreigntour->emp_name : ''}}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Designation</label>
                                    <input disabled type="text" id="emp_designation" name="emp_designation"
                                           class="form-control" placeholder="Designation"
                                           value=""
                                           autocomplete="off">
                                    <input type="hidden" id="designation_id" name="designation_id"
                                           value="{{isset($foreigntour->designation_id) ? $foreigntour->designation_id : ''}}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input disabled type="text" id="emp_department" name="emp_department"
                                           class="form-control" placeholder="Department"
                                           value=""
                                           autocomplete="off">
                                    <input type="hidden" id="department_id" name="department_id"
                                           value="{{isset($foreigntour->department_id) ? $foreigntour->department_id : ''}}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="emp_mbl" class="">Contact No</label>
                                    <input type="text" id="emp_mbl" name="emp_mbl"
                                           class="form-control"
                                           autocomplete="off"
                                           readonly
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="emp_email" class="">Email Address</label>
                                    <input type="email" id="emp_email" name="emp_email"
                                           class="form-control"
                                           autocomplete="off"
                                           readonly
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Tour Type</label>
                                    <select required class="custom-select form-control select2" id="tour_type"
                                            name="tour_type">
                                        <option value="">Select One</option>
                                        @foreach($tourtypes as $value)
                                            <option value="{{$value->tour_type_id}}"
                                                {{isset($foreigntour->tour_type_id) && $foreigntour->tour_type_id == $value->tour_type_id ? 'selected' : ''}}
                                            >{{$value->tour_type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Tour Details</label>
                                    <input required type="text" id="tour_details" name="tour_details"
                                           class="form-control"
                                           value="{{old('tour_details',isset($foreigntour->tour_details) ? $foreigntour->tour_details : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Tour Start Date</label>
                                    <input type="text"
                                           autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           data-toggle="datetimepicker"
                                           id="from_date"
                                           data-target="#from_date"
                                           name="from_date"
                                           value=""
                                           placeholder="YYYY-MM-DD"
                                           required
                                           data-predefined-date="{{old('from_date',isset($foreigntour->tour_star_date) ? $foreigntour->tour_star_date :'')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Tour End Date</label>
                                    <input type="text"
                                           autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           data-toggle="datetimepicker"
                                           id="to_date"
                                           data-target="#to_date"
                                           name="to_date"
                                           value=""
                                           placeholder="YYYY-MM-DD"
                                           required
                                           data-predefined-date="{{old('to_date',isset($foreigntour->tour_end_date) ? $foreigntour->tour_end_date :'')}}"
                                    >
                                </div>
                            </div>
                            @if(isset($foreigntour->tour_approval_date))
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="required">Tour Approval Date</label>
                                        <input type="text"
                                               autocomplete="off"
                                               class="form-control datetimepicker-input"
                                               data-toggle="datetimepicker"
                                               id="approve_date"
                                               data-target="#approve_date"
                                               name="approve_date"
                                               value=""
                                               placeholder="YYYY-MM-DD"
                                               disabled
                                               data-predefined-date="{{old('to_date',isset($foreigntour->tour_approval_date) ? $foreigntour->tour_approval_date :'')}}"
                                        >
                                    </div>
                                </div>
                            @else
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="required">Tour Approval Date</label>
                                        <input type="text"
                                               autocomplete="off"
                                               class="form-control datetimepicker-input"
                                               data-toggle="datetimepicker"
                                               id="approve_date"
                                               data-target="#approve_date"
                                               name="approve_date"
                                               value=""
                                               placeholder="YYYY-MM-DD"
                                               required
                                               data-predefined-date="{{old('to_date',isset($foreigntour->tour_approval_date) ? $foreigntour->tour_approval_date :'')}}"
                                        >
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Country</label>
                                    <select required class="custom-select select2" name="country_id"
                                            id="country_id">
                                        <option value="">Select One</option>
                                        @foreach($countrylist as $value)
                                            <option value="{{$value->country_id}}"
                                                {{isset($foreigntour->country_id) && $foreigntour->country_id == $value->country_id ? 'selected' : ''}}
                                            >{{$value->country}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Referance No</label>
                                    <input required type="text" id="ref_no" name="ref_no"
                                           class="form-control"
                                           value="{{old('email_address',isset($foreigntour->referecne_no) ? $foreigntour->referecne_no : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Tour Sponsor</label>
                                    {{--<input required type="text" id="tour_sponser" name="tour_sponser"
                                           class="form-control"
                                           value="{{old('tour_sponser',isset($foreigntour->tour_sponsor) ? $foreigntour->tour_sponsor : '')}}"
                                           autocomplete="off">--}}
                                    <select required class="custom-select form-control select2" id="tour_sponsor_id"
                                            name="tour_sponsor_id">
                                        <option value="">Select One</option>
                                        @foreach($tourSponsorList as $value)
                                            <option value="{{$value->tour_sponser_id}}"
                                                {{isset($foreigntour->tour_sponsor_id) && $foreigntour->tour_sponsor_id == $value->tour_sponser_id ? 'selected' : ''}}
                                            >{{$value->sponser_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Office Order No</label>
                                    <input required type="text" id="office_order_no" name="office_order_no"
                                           class="form-control"
                                           value="{{old('office_order_no',isset($foreigntour->office_order_no) ? $foreigntour->office_order_no : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="order_attachment" class="">Order Attachment</label>
                                    <input type="file" class="form-control" id="order_attachment"
                                           name="order_attachment"/>
                                </div>
                                @if(isset($foreigntour->order_attachment))
                                    <a href="{{ route('foreign-tour.foreign-tour-file-download', [$foreigntour->f_tour_id]) }}"
                                       target="_blank">{{$foreigntour->order_attachment_name}}</a>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Approver Note</label>
                                    <textarea rows="2" wrap="soft"
                                              name="approver_note"
                                              class="form-control"
                                              id="approver_note">{{old('approver_note',isset($foreigntour->approver_note) ? $foreigntour->approver_note : '')}}</textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea rows="2" wrap="soft"
                                              name="remarks"
                                              class="form-control"
                                              id="remarks">{{old('remarks',isset($foreigntour->remarks) ? $foreigntour->remarks : '')}}</textarea>
                                </div>
                            </div>

                        </div>
                        @if($foreigntour)
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/foreign-tour')}}">
                                        <button type="button" id="cancel"
                                                class="btn btn-primary mb-1">
                                            Cancel
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-12 text-right" id="add">
                                    <button type="submit" id="add"
                                            class="btn btn-primary mb-1">Save
                                    </button>
                                    <button type="reset" id="reset"
                                            class="btn btn-primary mb-1">Reset
                                    </button>

                                </div>
                            </div>
                        @endif

                    </form>
                </div>

            </div>
            @include('training.foreigntour.foreigntour_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function populateRelatedFields(that, data) {
            $(that).parent().parent().parent().find('#emp_name').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_name_post').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_designation').val(data.designation);
            $(that).parent().parent().parent().find('#emp_department').val(data.department);
            $(that).parent().parent().parent().find('#department_id').val(data.department_id);
            $(that).parent().parent().parent().find('#designation_id').val(data.designation_id);
            $(that).parent().parent().parent().find('#emp_mbl').val(data.emp_mbl);
            $(that).parent().parent().parent().find('#emp_email').val(data.emp_email);
        }

        function foreigntourList() {
            $('#foreign-tour-list').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: APP_URL + '/foreign-tour-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'f_tour_id', name: 'f_tour_id', searchable: true},
                    {data: 'emp_name', name: 'emp_name', searchable: true},
                    {data: 'country_name', name: 'country_name', searchable: true},
                    {data: 'tour_star_date', name: 'tour_star_date', searchable: true},
                    {data: 'tour_end_date', name: 'tour_end_date', searchable: true},
                    {data: 'sponser_name', name: 'sponser_name', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function () {
            selectCpaEmployees('#emp_id', APP_URL + '/ajax/employees', APP_URL + '/ajax/employee/', populateRelatedFields);
            dateRangePicker('#from_date', '#to_date');
            datePicker('#approve_date');
            foreigntourList();
        });
    </script>

@endsection
