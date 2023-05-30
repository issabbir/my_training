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
                    <h4 class="card-title">Training Reschedule Information</h4>
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
                        @if(isset($trainingschedule->schedule_id)) action="{{route('training-re-schedule.training-re-schedule-update',[$trainingschedule->schedule_id])}}"
                        @else action="{{route('training-re-schedule.training-re-schedule-post')}}"
                        @endif method="post">
                        @csrf
                        @if (isset($trainingschedule->schedule_id))
                            @method('PUT')
                        @endif
                        <!--Reschedule Date Part Start--->
                            <fieldset class="border pl-1 pr-1 col-sm-12 mb-1" style="display:none" id="res_portion">
                                <legend class="w-auto" style="font-size: 16px;">Rescheduled Date</legend>
                                {{--<div class="row">
                                    <div class="col-md-2"><p><strong>Start Date :</strong></p></div>
                                    <div class="col-md-2" id="re_start_date"></div>
                                    <div class="col-md-2"><p><strong>End Date :</strong></p></div>
                                    <div class="col-md-2" id="re_end_date"></div>
                                    <div class="col-md-2"><p><strong>Start Time :</strong></p></div>
                                    <div class="col-md-2" id="re_start_time"></div>
                                    <div class="col-md-2"><p><strong>End Time :</strong></p></div>
                                    <div class="col-md-2" id="re_end_time"></div>
                                </div>--}}
                                <div class=" table-responsive">
                                    <table class="table table-borderless mb-0">
                                        <thead>
                                        <tr class="border-0">
                                            <th scope="col">Start Date</th>
                                            <th scope="col">End Date</th>
                                            <th scope="col">Start Time</th>
                                            <th scope="col">End Time</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td id="re_start_date"></td>
                                            <td id="re_end_date"></td>
                                            <td id="re_start_time">28</td>
                                            <td id="re_end_time">1</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>
                            <fieldset class="border p-2 col-sm-12 mb-1" style="display:none" id="post_portion">
                                <legend class="w-auto" style="font-size: 16px;">Previous Postpone Date</legend>
                                <div class="row">
                                    <div class="col-md-2"><p><strong>Postpone Date :</strong></p></div>
                                    <div class="col-md-2" id="postpone_date"></div>
                                </div>
                            </fieldset>
                            <div class="row">
                                <div class="col-md-12">
                                    <legend class="w-auto" style="font-size: 16px;">Reschedule Date Entry</legend>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="required">Batch No</label>
                                        <select required class="custom-select form-control select2" id="schedule_id"
                                                name="schedule_id">
                                            <option value="">Select One</option>
                                            @foreach($trainingschedule as $value)
                                                <option value="{{$value->schedule_id}}">{{$value->batch_id}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Course Co-Ordinator Name</label>
                                        <input
                                            readonly
                                            type="text"
                                            class="form-control"
                                            id="coordinator_name"
                                            name="coordinator_name"
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="">Training Start Date</label>
                                        <input type="text" disabled
                                               autocomplete="off"
                                               class="form-control datetimepicker-input"
                                               data-toggle="datetimepicker"
                                               id="from_date"
                                               data-target="#from_date"
                                               name="from_date"
                                               value=""
                                        >
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="">Training End Date</label>
                                        <input type="text" disabled
                                               autocomplete="off"
                                               class="form-control datetimepicker-input"
                                               data-toggle="datetimepicker"
                                               id="to_date"
                                               data-target="#to_date"
                                               name="to_date"
                                               value=""
                                        >
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Training Location</label>
                                        <input
                                            readonly
                                            type="text"
                                            class="form-control"
                                            id="training_location"
                                            name="training_location"
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Capacity</label>
                                        <input
                                            readonly
                                            type="text"
                                            class="form-control"
                                            id="capacity"
                                            name="capacity"
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Calender</label>
                                        <input
                                            readonly
                                            type="text"
                                            class="form-control"
                                            id="calendar"
                                            name="calendar"
                                            autocomplete="off"
                                        >
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="required">Schedule Status</label>
                                        <select required class="custom-select form-control select2" id="schedule_status"
                                                name="schedule_status">
                                            <option value="">Select One</option>
                                            <option value="2">Re-Schedule</option>
                                            <option value="3">Postpone</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div id="re_schedule_active" style="display:none">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="">Re-Schedule Start Date</label>
                                            <input type="text"
                                                   autocomplete="off"
                                                   class="form-control datetimepicker-input"
                                                   data-toggle="datetimepicker"
                                                   id="from_date-re"
                                                   placeholder="YYYY-MM-DD"
                                                   data-target="#from_date-re"
                                                   name="from_date-re"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="">Re-Schedule End Date</label>
                                            <input type="text"
                                                   autocomplete="off"
                                                   class="form-control datetimepicker-input"
                                                   data-toggle="datetimepicker"
                                                   id="to_date-re"
                                                   data-target="#to_date-re"
                                                   name="to_date-re"
                                                   placeholder="YYYY-MM-DD"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="">Re-Schedule Time From</label>
                                            <input type="text"
                                                   autocomplete="off"
                                                   class="form-control datetimepicker-input"
                                                   data-toggle="datetimepicker"
                                                   id="time_from"
                                                   data-target="#time_from"
                                                   name="time_from"
                                                   value=""
                                                   placeholder="HH-MM"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="">Re-Schedule Time To</label>
                                            <input type="text"
                                                   autocomplete="off"
                                                   class="form-control datetimepicker-input"
                                                   data-toggle="datetimepicker"
                                                   id="time_to"
                                                   data-target="#time_to"
                                                   name="time_to"
                                                   value=""
                                                   placeholder="HH-MM"
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="postpone_active" style="display:none">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="">Postpone Date</label>
                                            <input type="text"
                                                   autocomplete="off"
                                                   class="form-control datetimepicker-input"
                                                   data-toggle="datetimepicker"
                                                   id="postphone_date"
                                                   data-target="#postphone_date"
                                                   name="postphone_date"
                                                   value=""
                                            >
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!--Reschedule Date Part Start--->

                        <!--Trainer Reschedule Part Start--->
                            <div class="row border-top-dark mt-1">
                                <input type="hidden" id="batch_id" name="batch_id">
                                <fieldset class="border p-1 mt-2 col-sm-12" style="display:none" id="res_data_portion">
                                    <legend class="w-auto" style="font-size: 16px;">Trainer Previous Reschedule</legend>
                                    <div class="col-sm-12 mt-1 mb-1">
                                        <div class="table-responsive-sm">
                                            <table class="table table-sm datatable mdl-data-table" id="prevRescheduleData">
                                                <thead>
                                                <tr>
                                                    <th>SL</th>
                                                    <th>Trainer</th>
                                                    <th>Prev. Training Date</th>
                                                    <th>Prev. Trainer</th>
                                                    <th>Reschedule Date</th>
                                                    <th>Reschedule In Time</th>
                                                    <th>Reschedule Out Time</th>
                                                </tr>
                                                </thead>
                                                <tbody id="comp_body">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </fieldset>
                                <div class="col-md-12 mt-2">
                                    <legend class="w-auto" style="font-size: 16px;">Trainer Reschedule Entry</legend>
                                    <div class="table-responsive-sm">
                                        <table class="table table-sm datatable mdl-data-table" id="searchResultTable">
                                            <thead>
                                            <tr>
                                                <th>SL</th>
                                                <th>Trainer</th>
                                                <th>Training Date</th>
                                                <th>Reschedule Trainer</th>
                                                <th>Reschedule Date</th>
                                                <th>Reschedule In Time</th>
                                                <th>Reschedule Out Time</th>
                                            </tr>
                                            </thead>
                                            <tbody id="comp_body">
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <!--Trainer Reschedule Part End--->
                        <div class="row mt-3 mb-2">
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary mb-2 mt-2 mr-1">Submit</button>
                                <button type="reset" class="btn btn-primary mb-2 mb-2 mt-2">Reset</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function convertDate(inputFormat) {
            function pad(s) {
                return (s < 10) ? '0' + s : s;
            }

            var d = new Date(inputFormat)
            return [d.getFullYear(), pad(d.getMonth() + 1), pad(d.getDate())].join('-')
        }

        function convertTime(divTimeStr) {
            var tmstr = divTimeStr.toString().split(' '); //'21-01-2013 PM 3:20:24'
            var dt = tmstr[0].split('/');
            var str = dt[2] + "/" + dt[1] + "/" + dt[0] + " " + tmstr[1]; //+ " " + tmstr[1]//'2013/01/20 3:20:24 pm'
            var time = new Date(str);
            if (time == "Invalid Date") {
                time = new Date(divTimeStr);
            }
            return time;
        }

        $("#schedule_id").change(function () {
            let schedule_id = ($('#schedule_id').val());
            if (schedule_id) {
                $.ajax({
                    type: 'GET',
                    url: '/get-schedule-data',
                    data: {schedule_id: schedule_id},
                    success: function (msg) {
                        $("#coordinator_name").val(msg.coordinator_name);
                        $("#from_date").val(convertDate(msg.training_start_date));
                        $("#to_date").val(convertDate(msg.training_end_date));
                        $("#training_location").val(msg.location_name);
                        $("#capacity").val(msg.training_capacity);
                        $("#calendar").val(msg.calender_name);
                        if (msg.re_schedule_start_date != null) {
                            $('#res_portion').show();
                            $('#res_data_portion').show();
                            $("#re_start_date").html(convertDate(msg.re_schedule_start_date));
                            $("#re_end_date").html(convertDate(msg.re_schedule_end_date));
                            $("#re_start_time").html(msg.re_schedule_start_time);
                            $("#re_end_time").html(msg.re_schedule_end_time);
                        } else {
                            $('#res_portion').hide();
                            $('#res_data_portion').hide();
                        }
                        if (msg.postponed_date != null) {
                            $('#post_portion').show();
                            $("#postpone_date").html(convertDate(msg.postponed_date));
                        } else {
                            $('#post_portion').hide();
                        }
                    }
                });
            }else
                {
                    $('#res_portion').hide();
                    $('#res_data_portion').hide();
                    $("#coordinator_name").val('');
                    $("#from_date").val('');
                    $("#to_date").val('');
                    $("#training_location").val('');
                    $("#capacity").val('');
                    $("#calendar").val('');
                }
            trainerList(schedule_id);
            trainerListReschedule(schedule_id);
        });

        function trainerList(schedule_id) {
            var tblPreventivi = $('#searchResultTable').DataTable({
                processing: true,
                serverSide: true,
                bDestroy: true,
                pageLength: 20,
                bFilter: true,
                lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'All']],
                /*serverSide: true,
                bDestroy: true,*/
                ajax: {
                    url: APP_URL + '/get-schedule-dtl-data',
                    data: {schedule_id: schedule_id},
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                "columns": [
                    {"data": 'DT_RowIndex', "name": 'DT_RowIndex'},
                    {"data": "trainer_name"},
                    {"data": "training_date"},
                    {"data": "reschedule_trainer"},
                    {"data": "reschedule_date"},
                    {"data": "in_time"},
                    {"data": "out_time"},
                ],
                drawCallback: function() {
                    $('.select2').select2();
                }
            });
        }

        function trainerListReschedule(schedule_id) {
            var tblReschedule = $('#prevRescheduleData').DataTable({
                /*processing: true,
                serverSide: true,
                bDestroy: false,*/
                /*pageLength: 20,
                bFilter: false,
                lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'All']],*/
                //bPaginate: false, //hide pagination
                /*searching: false,
                bFilter: false, //hide Search bar
                bInfo: false, // hide showing entries*/
                processing: true,
                serverSide: true,
                bDestroy: true,
                bFilter: false,
                bInfo: false,
                bPaginate: false,
                ajax: {
                    url: APP_URL + '/get-reschedule-dtl-data',
                    data: {schedule_id: schedule_id},
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                "columns": [
                    {"data": 'DT_RowIndex', "name": 'DT_RowIndex'},
                    {"data": "trainer_name"},
                    {"data": "training_date"},
                    {"data": "re_schedule_trainer"},
                    {"data": "re_schedule_date"},
                    {"data": "re_schedule_start_time"},
                    {"data": "re_schedule_end_time"},
                ],
            });
        }


        $('#schedule_status').on('change', function (e) {
            e.preventDefault();
            let statusId = $(this).val();
            /*if(statusId == '1'){
                $('#re_schedule_active').hide('');
                $('#postpone_active').hide('');
                $('#closed_active').hide('');
            }
            else*/
            if (statusId == '2') {
                $('#re_schedule_active').show();
                $('#postpone_active').hide();
                $('#closed_active').hide();
            } else if (statusId == '3') {
                $('#postpone_active').show();
                $('#re_schedule_active').hide();
                $('#closed_active').hide();
            }
            else {
                $('#re_schedule_active').hide('');
                $('#postpone_active').hide('');
                $('#closed_active').hide('');
            }
        });

        function call_date_picker(e) {
            datePicker(e);
        }

        function call_time_picker(e) {
            timePicker(e);
        }

        function call_time_picker2(e) {
            timePicker(e);
        }

        $(document).ready(function () {
            dateRangePicker('#from_date-re', '#to_date-re');
            timePicker('#time_from');
            timePicker('#time_to');
            datePicker('#postphone_date');
            // $('.select2').select2();
        });


        $("#schedule_id").change(function () {
            $("#batch_id").val($(this).find(":selected").text());
        });
    </script>

@endsection
