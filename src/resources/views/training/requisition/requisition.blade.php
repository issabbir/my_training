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
                    <h4 class="card-title">Training Requisition</h4>
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

                    <form
                        @if(isset($requisition->traning_req_id)) action="{{route('training-requisition.training-requisition-update',[$requisition->traning_req_id])}}"
                        @else action="{{route('training-requisition.training-requisition-post')}}" @endif method="post">
                        @csrf
                        @if (isset($requisition->traning_req_id))
                            @method('PUT')
                        @endif
                        <div class="row">
{{--                            <div class="col-sm-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="mb-1">Trainee from</label>--}}
{{--                                    <div>--}}
{{--                                        <div class="form-check form-check-inline">--}}
{{--                                            <input class="form-check-input" type="radio"--}}
{{--                                                   name="cpa_yn" id="reporter_outsider_no1"--}}
{{--                                                   onclick="javascript:yesnoCheck3();" checked--}}
{{--                                                   value="{{ \App\Enums\YesNoFlag::NO }}"--}}
{{--                                                   @if(isset($requisition->cpa_emp_yn) && $requisition->cpa_emp_yn == "N") checked @endif/>--}}
{{--                                            <label class="form-check-label">Outside</label>--}}
{{--                                        </div>--}}
{{--                                        <div class="form-check form-check-inline">--}}
{{--                                            <input class="form-check-input" type="radio"--}}
{{--                                                   name="cpa_yn"--}}
{{--                                                   onclick="javascript:yesnoCheck2();"--}}
{{--                                                   id="reporter_cpa_no1" value="{{ \App\Enums\YesNoFlag::YES }}"--}}
{{--                                                   @if(isset($requisition->cpa_emp_yn) && $requisition->cpa_emp_yn == "Y") checked @endif/>--}}
{{--                                            <label class="form-check-label">CPA</label>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}




                            @if(Auth::user()->hasPermission('CAN_CREATE_REQUISITION_FOR_ALL'))
                                    @if(isset($requisition->cpa_emp_yn) && $requisition->cpa_emp_yn == "N")
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="required">Emp Code</label>

                                                <select class="custom-select select2" name="emp_id" id="emp_id"></select>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input type="text" id="emp_name" name="emp_name"
                                                       class="form-control" placeholder="Name"
                                                       value="{{old('emp_name',isset($requisition->emp_name) ? $requisition->emp_name : '')}}"
                                                       autocomplete="off">

                                            </div>
                                        </div>

                                    @elseif(isset($requisition->cpa_emp_yn) && $requisition->cpa_emp_yn == "Y")
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="required">Emp Code</label>
                                                <select class="custom-select form-control select2" name="emp_id" id="emp_id"
                                                        data-emp-id="@if($requisition && $requisition->emp_id) {{$requisition->emp_id}} @endif">
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Name</label>
                                                <input readonly type="text" id="emp_name" name="emp_name"
                                                       class="form-control" placeholder="Name"
                                                       value="{{old('emp_name',isset($requisition->emp_name) ? $requisition->emp_name : '')}}"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                    @else
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="required">Emp Code</label>

                                                    <select class="custom-select select2" name="emp_id" id="emp_id"></select>

                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label class="required">Name</label>
                                                <input type="text" id="emp_name" name="emp_name" required
                                                       class="form-control" placeholder="Name" value="{{ isset($myInfo->emp_name) ? $myInfo->emp_name : '' }}"
                                                       autocomplete="off">
                                            </div>
                                        </div>
                                    @endif


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Designation</label>
                                    <input disabled type="text" id="emp_designation" name="emp_designation"
                                           class="form-control" placeholder="Designation"
                                           value=""
                                           autocomplete="off">
                                    <input type="hidden" id="designation_id" name="designation_id"
                                           value="{{isset($requisition->designation_id) ? $requisition->designation_id : ''}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input disabled type="text" id="emp_department" name="emp_department"
                                           class="form-control" placeholder="Department"
                                           value=""
                                           autocomplete="off">
                                    <input type="hidden" id="department_id" name="department_id"
                                           value="{{isset($requisition->department_id) ? $requisition->department_id : ''}}">
                                </div>
                            </div>

                            @else
                                <input type="hidden" name="emp_id"  @if(isset($loggedUser->emp_id)) value="{{ $loggedUser->emp_id }}" @endif>

                                <input type="hidden" id="emp_name" name="emp_name" required
                                       class="form-control" placeholder="Name" value="{{ isset($myInfo->emp_name) ? $myInfo->emp_name : '' }}"
                                       autocomplete="off">

                                <input type="hidden" name="designation_id"
                                       value="{{ isset($myInfo->designation_id) ? $myInfo->designation_id : '' }}">

                                <input type="hidden" name="department_id"
                                       value="{{ isset($myInfo->dpt_department_id) ? $myInfo->dpt_department_id : '' }}">
                            @endif

                            @if(isset($requisition->traning_req_id))
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Req Id.</label>
                                        <input readonly type="text" id="req_id" name="req_id"
                                               class="form-control"
                                               value="{{old('req_id',isset($requisition->traning_req_id) ? $requisition->traning_req_id : '')}}"
                                               autocomplete="off">

                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="training_type" class="required">Training Category</label>
                                    <select class="custom-select form-control select2" required id="training_category"
                                            name="training_category">
                                        <option value="">Select One</option>
                                        @foreach($trainingType as $value)
                                            <option value="{{$value->training_type_id}}"
                                                {{isset($requisition->training_type_id) && $requisition->training_type_id == $value->training_type_id ? 'selected' : ''}}
                                            >{{$value->training_type_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Course Name</label>
                                    <input type="text" id="course_name" name="course_name" required
                                           class="form-control" placeholder="Course Name"
                                           value="{{old('course_name',isset($requisition->course_name) ? $requisition->course_name : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>

                            {{--<div class="col-sm-3">
                                <div class="form-group">
                                    <label>Requsition Date</label>
                                    <input type="text"
                                           autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           data-toggle="datetimepicker"
                                           id="req_date"
                                           data-target="#req_date"
                                           name="req_date"
                                           value=""
                                           placeholder="YYYY-MM-DD"
                                           data-predefined-date="{{old('req_date',isset($requisition->requsition_date) ? $requisition->requsition_date :'')}}"
                                    >
                                </div>
                            </div>--}}
                            @if(isset($requisition->requsition_date))
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Requsition Date</label>
                                        <input type="text"
                                               disabled
                                               autocomplete="off"
                                               class="form-control datetimepicker-input"
                                               data-toggle="datetimepicker"
                                               id="req_date"
                                               data-target="#req_date"
                                               name="req_date"
                                               value=""
                                               placeholder="YYYY-MM-DD"
                                               data-predefined-date="{{old('req_date',isset($requisition->requsition_date) ? $requisition->requsition_date :'')}}"
                                        >
                                        <input type="hidden" name="req_date"
                                               value="{{isset($requisition->requsition_date) ? $requisition->requsition_date :''}}">
                                    </div>
                                </div>
                            @else
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="required">Requisition Date</label>
                                        <input type="text"
                                               autocomplete="off"
                                               class="form-control datetimepicker-input"
                                               data-toggle="datetimepicker"
                                               id="req_date"
                                               data-target="#req_date"
                                               name="req_date"
                                               value=""
                                               placeholder="YYYY-MM-DD"
                                               required
                                        >
                                    </div>
                                </div>
                            @endif
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="trainee_type" class="required">For Whom</label>
                                    <select class="custom-select form-control select2" required id="for_whom"
                                            name="for_whom[]"  multiple="multiple">
                                        <option value="">Select One</option>

                                        @foreach($traineeType as $value)
                                            <option value="{{$value->trainee_type_id}}"
                                            @if(!empty($whom_ids))
                                                @foreach($whom_ids as $id)
                                                    @if(isset($requisition->trainee_type_id) && $value->trainee_type_id == $id) {{'selected="selected"'}} @endif
                                                @endforeach
                                            @endif >
                                                {{$value->trainee_type}}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="training_location_id" class="required">Location</label>
                                    <select class="custom-select form-control select2" required
                                            id="training_location_id"
                                            name="training_location_id">
                                        @foreach($lTrainingLocation as $value)
                                            <option value="{{$value->location_id}}"
                                                {{isset($requisition->location_id) && $requisition->location_id == $value->location_id ? 'selected' : ''}}
                                            >{{$value->location_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

{{--                            <div class="col-md-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="required">Requisition Status</label>--}}
{{--                                    <select required class="custom-select select2" name="requsition_status"--}}
{{--                                            id="requsition_status">--}}
{{--                                        <option--}}
{{--                                            value="Y" @if(!empty($requisition)) @if($requisition->requsition_status=='Y') {{'selected="selected"'}} @endif @endif>--}}
{{--                                            Approved--}}
{{--                                        </option>--}}
{{--                                        <option--}}
{{--                                            value="N" @if(!empty($requisition)) @if($requisition->requsition_status=='N') {{'selected="selected"'}} @endif @endif>--}}
{{--                                            Incomplete--}}
{{--                                        </option>--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                        </div>
                        <div class="row">
                            <div class="col-md-6 col-sm-12 mb-md-0 mb-sm-2">
                                <label class="required" for="objectives">Objectives</label>
                                <div id="objectives_editor"
                                     class="text-editor">{!! (isset($requisition->objectives) ? $requisition->objectives : '')!!}</div>
                                <textarea required rows="1" wrap="soft" name="objectives"
                                          class="form-control customize-text-editor"
                                          id="objectives">{{(isset($requisition->objectives) ? $requisition->objectives : '')}}</textarea>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea
                                        rows="3" wrap="soft"
                                        name="remarks"
                                        class="form-control"
                                        id="remarks">{{old('remarks',isset($requisition->remarks) ? $requisition->remarks : '')}}</textarea>
                                </div>
                            </div>
                        </div>

                        @if($requisition)
                            <div class="row mt-5">
                                <div class="col-md-12 text-right" id="cancel">
                                    @if((Auth::user()->hasPermission('CAN_APPROVE_REQUISITION')) && ($requisition->requsition_status == 'N'))
                                    <button type="submit" id="approve" name="approve"
                                            class="btn btn-primary mb-1">
                                        Approve
                                    </button>
                                    @endif
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/requisition')}}">
                                        <button type="button" id="cancel"
                                                class="btn btn-danger mb-1">
                                            Cancel
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="row mt-5">
                                <div class="col-md-12 mt-2 text-right" id="add">
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
            @include('training.requisition.requisition_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        // function yesnoCheck2() {
            // if (document.getElementById('reporter_cpa_no1').checked) {
            //     $("#emp_name").prop("readonly", true);
            //     $("#emp_id").prop("disabled", false);
            //     $("#emp_designation").prop("disabled", true);
            //     $("#emp_department").prop("disabled", true);
            //
            //     $('#emp_name').val('');
            //     $('#emp_designation').val('');
            //     $('#emp_department').val('');

            // }
        // }

        // function yesnoCheck3() {
        //     if (document.getElementById('reporter_outsider_no1').checked) {
        //         $("#emp_name").prop("readonly", false);
        //         $("#emp_id").prop("disabled", true);
        //         $("#emp_designation").prop("disabled", true);
        //         $("#emp_department").prop("disabled", true);
        //
        //         $('#emp_name').val('');
        //         $('#emp_designation').val('');
        //         $('#emp_department').val('');
        //         $('#designation_id').val('');
        //         $('#department_id').val('');
        //
        //         $('#emp_id').empty();
        //     }
        // }

        function populateRelatedFields(that, data) {
            $(that).parent().parent().parent().find('#emp_name').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_name_post').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_designation').val(data.designation);
            $(that).parent().parent().parent().find('#emp_department').val(data.department);
            $(that).parent().parent().parent().find('#department_id').val(data.department_id);
            $(that).parent().parent().parent().find('#designation_id').val(data.designation_id);
        }

        function requisitionList() {
            $('#requisition-list').DataTable({
                processing: true,
                order: [ 6, 'desc' ],
                serverSide: true,
                ajax: {
                    url: APP_URL + '/requisition-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'traning_req_id', name: 'traning_req_id', searchable: true},
                    {data: 'emp_name', name: 'emp_name', searchable: true},
                    {data: 'course_name', name: 'course_name', searchable: true},
                    {data: 'l_location_name', name: 'l_location_name', searchable: true},
                    {data: 'status', name: 'status', searchable: true},
                    {data: 'req_status', name: 'req_status', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        function editors() {
            $('.ql-editor').on('blur', function () {
                var editorId = $(this).parent("div[id]:first").attr('id');
                if (editorId == 'objectives_editor') {
                    $('#objectives').html(replacePtagToBrTag($('#' + editorId + ' .ql-editor').html()));
                }
            });

            $('.clearTextEditor').on('click', function () {
                var editorId = $(this).closest("div").nextAll("[id]:first").attr('id');
                if (editorId == 'objectives_editor') {
                    $('#' + editorId + ' .ql-editor').html('');
                    $('#objectives').html('');
                }
            });
        };

        $(document).ready(function () {

            @if(Auth::user()->hasPermission('CAN_CREATE_REQUISITION_FOR_ALL'))
                selectCpaEmployees('#emp_id', APP_URL + '/ajax/employees', APP_URL + '/ajax/employee/', populateRelatedFields);
                $('#emp_name').val('');
                $('#emp_designation').val('');
                $('#emp_department').val('');
            @endif
            // selectCpaEmployees('#emp_idd', APP_URL + '/ajax/employees', APP_URL + '/ajax/employee/', populateRelatedFields);
            requisitionList();
            datePicker('#req_date');
            editors();

            $("#emp_name").prop("readonly", true);
            $("#emp_id").prop("disabled", false);
            // $("#emp_idd").prop("disabled", false);
            $("#emp_designation").prop("disabled", true);
            $("#emp_department").prop("disabled", true);


        });
    </script>

@endsection
