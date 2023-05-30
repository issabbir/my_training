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
            <div class="card-body">

                <h4 class="card-title">Training Information</h4>
                <hr>

            @include('training.partials.training-entry')

            </div>
        </div>
            @include('training.traininginfo.traininginfo_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">
        // function checkCpaOutsider() {
        //     $('#trainee_type').on('change', function () {
        //         var traineeTypeId = $('#trainee_type').val();
        //
        //         if(traineeTypeId){
        //             $.ajax({
        //                 type: "GET",
        //                 url: APP_URL + '/ajax/trainee-type/' + traineeTypeId,
        //                 success: function (data) {
        //                     //alert(data.cpa_yn)
        //                     // $('#totalDays').val(data.trainee_type);
        //                     if((data.cpa_yn) == 'N'){
        //                         $('#course_fee_yn').show();
        //                     }
        //                     else{
        //                         $('#course_fee_yn').hide();
        //                     }
        //                 },
        //                 error: function (data) {
        //                     alert('error');
        //                 }
        //             });
        //
        //         } else {
        //             $('#course_fee_yn').hide();
        //         }
        //     });
        // }

        $(document).ready(function () {

            if ($('#external_y').is(':checked')) {
                $('#course_fee_yn').show();
            }else{
                $('#course_fee_yn').hide();
            }

            $('#external_y').click(function () {
                $('#course_fee_yn').show();
            });
            $('#external_n').click(function () {
                $('#course_fee_yn').hide();
            });

        });

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
            dateRangePicker('#from_date', '#to_date');
            dateRangePicker('#modal_train_from_date', '#modal_train_to_date');
            trainingInfoList();
            editors();
            calculateDays();
            checkCpaOutsider();
        });
    </script>

@endsection
