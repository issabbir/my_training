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
                        <h4 class="card-title">Create Calendar</h4>
                        <hr>
                        @if(Session::has('message'))
                            <div
                                class="alert {{Session::get('m-class') ? Session::get('m-class') : 'alert-danger'}} show"
                                role="alert">
                                {{ Session::get('message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <form enctype="multipart/form-data"
                              @if(isset($calendardata->calender_id)) action="{{route('create-calender.create-calender-update',[$calendardata->calender_id])}}"
                              @else action="{{route('create-calender.create-calender-post')}}" @endif method="post">
                            @csrf
                            @if (isset($calendardata->calender_id))
                                @method('PUT')
                            @endif
                            <div class="row">
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="required">Calendar Name</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="calendar_name"
                                            name="calendar_name"
                                            autocomplete="off"
                                            value="{{old('calendar_name', isset($calendardata->calender_name) ? $calendardata->calender_name :'')}}"
                                            required
                                        >
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label>Description</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="description"
                                            name="description"
                                            autocomplete="off"
                                            value="{{old('description', isset($calendardata->description) ? $calendardata->description :'')}}"
                                        >
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="mb-1 required">Active</label>
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="active_yn" id="active_yes"
                                                       value="{{ \App\Enums\YesNoFlag::YES }}"
                                                       checked
                                                       @if(isset($calendardata->active_yn) && $calendardata->active_yn == "Y") checked @endif/>
                                                <label class="form-check-label">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio"
                                                       name="active_yn" value="{{ \App\Enums\YesNoFlag::NO }}"
                                                       id="active_no"
                                                       @if(isset($calendardata->active_yn) && $calendardata->active_yn == "N") checked @endif/>
                                                <label class="form-check-label">No</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="required">Start Date</label>
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
                                               data-predefined-date="{{old('from_date',isset($calendardata->start_date) ? $calendardata->start_date : $date1 )}}"
                                        >
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label class="required">End Date</label>
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
                                               data-predefined-date="{{old('to_date',isset($calendardata->end_date) ? $calendardata->end_date : $date2 )}}"
                                        >
                                        <input type="hidden" id="training_count" name="training_count"
                                               value="{{isset($allTraining_count) ? $allTraining_count : ''}}">
                                        <input type="hidden" id="all_training" name="all_training"
                                               value="{{isset($allTraining) ? $allTraining : ''}}">
                                    </div>
                                </div>
                            </div>
                            @if($calendardata)
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/create-calender')}}">
                                        <button type="button" id="cancel"
                                                class="btn btn-primary mb-1">
                                            Cancel
                                        </button>
                                    </a>
                                </div>
                            @else
                                <div class="col-md-12 mt-2 text-right" id="add">
                                    <button type="submit" id="add"
                                            class="btn btn-primary mb-1">Save
                                    </button>
                                    <button type="reset" id="reset"
                                            class="btn btn-primary mb-1">Reset
                                    </button>

                                </div>

                            @endif
                        </form>
                    </div>

                </div>
            @include('training.calendar.create_calendar.calendar_list')
        </div>
    </div>

    <div class="modal fade" id="exampleModal" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document" style="min-width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Training Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    @include('training.partials.training-entry')

                </div>
            </div>
        </div>
    </div>




@endsection

@section('footer-script')

    <script type="text/javascript">


        $(".add-training-info").click(function () {
            var myModal = $('#exampleModal');
            myModal.modal({show: true});
            return false;
        });


        calendermstList();
        var dataArray = new Array();

        function convertDate(inputFormat) {
            function pad(s) {
                return (s < 10) ? '0' + s : s;
            }

            var d = new Date(inputFormat)
            return [d.getFullYear(), pad(d.getMonth() + 1), pad(d.getDate())].join('-')
        }

        $("#dtl_training_id").change(function () {
            let dtl_training_id = ($('#dtl_training_id').val());
            if (dtl_training_id !== null) {
                $.ajax({
                    type: 'GET',
                    url: '/get-training-info-data',
                    data: {dtl_training_id: dtl_training_id},
                    success: function (msg) {
                        //console.log(msg)
                        $("#tab_obj").val(msg[0].objectives);
                        $("#tab_duration").val(msg[0].duration);
                        $("#tab_course").val(msg[0].number_of_course);
                        if (msg[0].active_yn == 'Y') {
                            $("#tab_status").val('Active');
                        } else {
                            $("#tab_status").val('In-Active');
                        }

                        $("#train_from_date").val(convertDate(msg[0].form_date));
                        $("#train_to_date").val(convertDate(msg[0].date_to));
                    }
                });
            }
        });

        $(".add-row-exam-result").click(function () {
            let training_id = $("#dtl_training_id option:selected").val();
            let training_name = $("#dtl_training_id option:selected").text();
            let tab_duration = $("#tab_duration").val();
            let tab_course = $("#tab_course").val();
            let train_from_date = new Date($('#train_from_date').val());
            let compare_date = new Date($('#from_date').val());

            let train_to_date = new Date($('#train_to_date').val());
            let compare_to_date = new Date($('#to_date').val());
            //var train_to_date = $("#train_to_date").val();

            if (training_id != '' || tab_duration != '' || tab_course != '' /*|| train_from_date != '' || train_to_date != ''*/) {
                if ($.inArray(training_id, dataArray) > -1) {
                    Swal.fire('Duplicate value not allowed.');
                } else if (compare_date > train_from_date) {
                    Swal.fire({
                        title: 'Training Start Date Not Matched For Calender Date Range!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                } else if (compare_to_date < train_to_date) {
                    Swal.fire({
                        title: 'Training End Date Not Matched For Calender Date Range!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    return false;
                } else {
                    let markup = "<tr><td><input type='checkbox' name='record'>" +
                        "<input type='hidden' name='training_id[]' class='delete_training_id' value='" + training_id + "'>" +
                        /*"<input type='hidden' name='train_from_date[]' value='" + train_from_date + "'>" +
                        "<input type='hidden' name='train_to_date[]' value='" + train_to_date + "'>" +
                        "</td><td>" + training_name + "</td><td>" + tab_duration + "</td><td>" + tab_course + "</td><td>" + train_from_date + "</td><td>" + train_to_date + "</td></tr>";*/
                        "</td><td>" + training_name + "</td><td>" + tab_duration + "</td><td>" + tab_course + "</td></tr>";
                    $("#dtl_training_id").val('').trigger('change');
                    $("#tab_duration").val("");
                    $("#tab_course").val("");
                    $("#train_from_date").val("");
                    $("#train_to_date").val("");
                    $("#tab_obj").val("");
                    $("#tab_status").val("");
                    $("#table-exam-result tbody").append(markup);
                    dataArray.push(training_id);
                }
            } else {
                Swal.fire('Fill required value.');
            }

        });

        $(".delete-row-exam-result").click(function () {
            $("#table-exam-result tbody").find('input[name="record"]').each(function () {
                if ($(this).is(":checked")) {
                    let calender_dtl_id = $(this).closest('tr').find('.calender_dtl_id').val();
                    let training_id = $(this).closest('tr').find('.delete_training_id').val();
                    if (calender_dtl_id !== null) {
                        Swal.fire({
                            title: 'Are you sure?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.value) {
                                $(this).parents("tr").remove();
                                $.ajax({
                                    type: 'GET',
                                    url: '/detail-data-remove',
                                    data: {calender_dtl_id: calender_dtl_id},
                                    success: function (msg) {
                                        for (var i = 0; i < dataArray.length; i++) {
                                            if (dataArray[i] == training_id) {
                                                dataArray.splice(i, 1);
                                                break;
                                            }
                                        }

                                        Swal.fire({
                                            title: 'Entry Successfully Deleted!',
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        }).then(function () {

                                            //location.reload();
                                        });
                                    }
                                });
                            }
                        });
                    } else {
                        $(this).parents("tr").remove();
                    }


                }
            });
        });

        function calendermstList() {
            $('#calendar-mst-list').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: APP_URL + '/create-calender-datatable',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'calender_name', name: 'calender_name', searchable: true},
                    {data: 'start_date', name: 'start_date', searchable: false},
                    {data: 'end_date', name: 'end_date', searchable: false},
                    {data: 'active_yn', name: 'active_yn', searchable: false},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        function call_date_picker(e) {
            datePicker(e);
        }

        $(document).ready(function () {

            if ($('#external_y').is(':checked')) {
                $('#course_fee_yn').show();
            } else {
                $('#course_fee_yn').hide();
            }

            $('#external_y').click(function () {
                $('#course_fee_yn').show();
            });
            $('#external_n').click(function () {
                $('#course_fee_yn').hide();
            });

            dateRangePicker('#from_date', '#to_date');
            dateRangePicker('#train_from_date', '#train_to_date');
            datePicker('#proposed_start_date');
            datePicker('#proposed_end_date');

            var training_count = $("#training_count").val();
            var all_training = $("#all_training").val();
            var arr_allTraining = []
            try {
                arr_allTraining = JSON.parse(all_training);
            } catch (e) {
                console.log("Invalid json")
            }
            if (training_count) {
                var i;
                for (i = 0; i < training_count; i++) {
                    dataArray.push(arr_allTraining[i]);
                }
            }
        });


        //New functions added from
        function checkCpaOutsider() {
            $('#trainee_type').on('change', function () {
                var traineeTypeId = $('#trainee_type').val();

                // if(traineeTypeId){
                //     $.ajax({
                //         type: "GET",
                //         url: APP_URL + '/ajax/trainee-type/' + traineeTypeId,
                //         success: function (data) {
                //             //alert(data.cpa_yn)
                //             // $('#totalDays').val(data.trainee_type);
                //             if((data.cpa_yn) == 'N'){
                //                 $('#course_fee_yn').show();
                //             }
                //             else{
                //                 $('#course_fee_yn').hide();
                //             }
                //
                //         },
                //         error: function (data) {
                //             alert('error');
                //         }
                //     });
                //
                // } else {
                //     $('#course_fee_yn').hide();
                // }
            });
        }

        function editors() {
            $('.ql-editor').on('blur', function () {
                var editorId = $(this).parent("div[id]:first").attr('id');
                if (editorId == 'course_content_editor') {
                    $('#course_content').html(replacePtagToBrTag($('#' + editorId + ' .ql-editor').html()));
                } else if (editorId == 'traininginfo_objectives_editor') {
                    $('#traininginfo_objectives').html(replacePtagToBrTag($('#' + editorId + ' .ql-editor').html()));
                } else if (editorId == 'training_facalities_editor') {
                    $('#training_facalities').html(replacePtagToBrTag($('#' + editorId + ' .ql-editor').html()));
                }
            });

            $('.clearTextEditor').on('click', function () {
                var editorId = $(this).closest("div").nextAll("[id]:first").attr('id');

                if (editorId == 'course_content_editor') {
                    $('#' + editorId + ' .ql-editor').html('');
                    $('#course_content').html('');
                } else if (editorId == 'traininginfo_objectives_editor') {
                    $('#' + editorId + ' .ql-editor').html('');
                    $('#traininginfo_objectives').html('');
                } else if (editorId == 'training_facalities_editor') {
                    $('#' + editorId + ' .ql-editor').html('');
                    $('#training_facalities').html('');
                }
            });
        };


        function calculateDays() {
            $('#from_date, #to_date').on('change.datetimepicker', function () {
                var from_date = $('#from_date').val();
                var to_date = $('#to_date').val();

                if (from_date && to_date) {
                    $.ajax({
                        type: "GET",
                        url: APP_URL + '/ajax/total-days/' + from_date + '/' + to_date,
                        success: function (data) {
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                }
            });
        }

        function yesnoCheck() {
            if (document.getElementById('remuneration_active_y').checked) {
                $("#remuneration_amount").prop("disabled", false);
                $('#remuneration_amount').val('');
            }
        }

        function yesnoCheck1() {
            if (document.getElementById('remuneration_active_n').checked) {
                $("#remuneration_amount").prop("disabled", true);
                $('#remuneration_amount').val('');
            }
        }

        function yesCheckDept() {
            if (document.getElementById('y_dept').checked) {
                $("#dept_name").prop("disabled", false);
            }
        };

        function noCheckDept() {
            if (document.getElementById('n_dept').checked) {
                $('#dept_name').val('').trigger('change');
                $("#dept_name").prop("disabled", true);
            }
        };

        function trainingInfoList() {
            $('#training-info-list').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: APP_URL + '/training-information-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'training_number', name: 'training_number', searchable: false},
                    {data: 'training_title', name: 'training_title', searchable: true},
                    {data: 'duration', name: 'duration', searchable: true},
                    {data: 'course_fee', name: 'course_fee', searchable: false},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        function populateRelatedFields(that, data) {
            $(that).parent().parent().parent().find('#emp_name').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_mbl').val(data.emp_mbl);
            $(that).parent().parent().parent().find('#emp_email').val(data.emp_email);
        }

        $(document).ready(function () {
            selectCpaEmployees('#emp_id', APP_URL + '/ajax/employees', APP_URL + '/ajax/employee/', populateRelatedFields);
            dateRangePicker('#modal_train_from_date', '#modal_train_to_date');
            trainingInfoList();
            editors();
            calculateDays();
            checkCpaOutsider();


            // $('span.select2.select2-container.select2-container--default.select2-container--below.select2-container--focus').css('width', '100%');
            $('.select2-container').css('min-width', '100%');

        });


    </script>



@endsection
