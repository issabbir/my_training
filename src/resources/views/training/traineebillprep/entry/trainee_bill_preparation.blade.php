@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
    <style type="text/css">
        .display-none{
            display: none;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Table Start -->
                <div class="card-body">
                    <h4 class="card-title">Trainee Bill Preparation Entry</h4>
                    <hr>
                    <form method="POST" id="search-form" name="search-form">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="required">Training No</label>
                                    <select required class="custom-select form-control select2 training_no1" id="training_no"
                                            name="training_no">
                                        <option value="">Select One</option>
                                        @if($trainingInfo)
                                            @foreach($trainingInfo as $training)
                                                <option
                                                    value="{{$training->training_id}}">{{$training->training_number.' ('.$training->training_title.')'}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="required">Batch No</label>
                                <select name="batch_no" id="batch_no" class="form-control select2" required>
                                    <option value="">Select One</option>
                                </select>
                            </div>
                            <div class="col-md-2 billGenerated1" style="display: none">
                                    <span class="badge badge-success form-control billGenerated"  style="padding: 10px; margin-top: 20px; height: 40px;display: none">Bill Approved</span>
                            </div>
{{--                            <div class="col-md-2">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="required">Training Session</label>--}}
{{--                                    <input--}}
{{--                                        type="text"--}}
{{--                                        class="form-control"--}}
{{--                                        id="training_duration"--}}
{{--                                        autocomplete="off"--}}
{{--                                        name="training_duration"--}}
{{--                                        required--}}
{{--                                    >--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-md-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="required">Bill Date</label>--}}
{{--                                    <input type="text"--}}
{{--                                           autocomplete="off"--}}
{{--                                           class="form-control datetimepicker-input"--}}
{{--                                           data-toggle="datetimepicker"--}}
{{--                                           id="bill_date"--}}
{{--                                           data-target="#bill_date"--}}
{{--                                           name="bill_date"--}}
{{--                                           required--}}
{{--                                           placeholder="YYYY-MM-DD"--}}
{{--                                    >--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="col-sm-3 mt-2">
                                <div class="d-flex justify-content-start col">
                                    <button type="submit" class="btn btn btn-dark shadow mb-1 btn-secondary">
                                        Bill Generation
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body"><h4 class="card-title">Bill List</h4>
                    <hr>
                    <div class="row">
                        <div class="col" id="final-selection-message"></div>
                    </div>
                    <form method="post" name="final-results-form" id="final-results-form">
                        {{csrf_field()}}
                        <div class="table-responsive">
                            <table class="table table-sm datatable mdl-data-table" id="final-results">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Training Name</th>
                                    <th>Trainee Name</th>
                                    <th>Organization</th>
                                    <th>Department</th>
                                    <th>Day</th>
                                    <th>Bill Rate</th>
                                    <th>Bill Amount</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn btn-dark shadow btn-secondary"
                                    name="final-results-submission" id="final-results-submission">Approve Current Page
                            </button>&nbsp;
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer-script')
    <script type="text/javascript">

        $(document).ready(function () {
            customSelect2();
            datePicker('#bill_date');
            $('#search-form').on('submit', function (e) {
                e.preventDefault();
                oTable.draw();
            });



            $('#final-results-form').on('submit', function (e) {
                e.preventDefault();
                var answer = confirm('Are you confirm to submit this bill?');

                if(answer==true) {
                    $.ajax({
                        type: 'POST',
                        'headers': {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        url: "{{route('bill-preparation.bill-preparation-post')}}",
                        data: $(this).serialize(),
                        success: function (data) {
                            $('#final-selection-message').html(data.html);
                        },
                        error: function (data) {
                            alert('error');
                        }
                    });
                } else {
                    $('#final-selection-message').html('');
                }
            });
        });

        $('.training_no1').on('change', function (e) {
            e.preventDefault();
            let trainingInfoId = $(this).val();
            if (trainingInfoId) {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/training-batch-no/' + trainingInfoId,
                    success: function (data) {
                        console.log(data)
                        $('#batch_no').html(data.batchNoHtml);
                    },
                    error: function (err) {
                        alert('error', err);
                    }
                });
            } else {
                $('#batch_no').html('');
            }
        });

        $('#batch_no').on('change', function (e) {
            e.preventDefault();
            let batchNO = $(this).val();
            if (batchNO) {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/training-exist-no/' + batchNO,
                    success: function (data) {
                        console.log(data)
                        if (data.bill.length > 0)
                        {
                            $('.billGenerated').show();
                            $('.billGenerated1').show();
                        }else {
                            $('.billGenerated').hide();
                            $('.billGenerated1').hide();
                        }
                    },

                });
            } else {
                $('#batch_no').html('');
            }
        });

        var oTable = $('#final-results').DataTable({
            processing: true,
            serverSide: true,
            bDestroy: true,
            //pageLength: 10,
            paging: false,
            bFilter: true,
            lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'All']],
            ajax: {
                async:false,
                url: APP_URL + "/bill-preparation-datatable",
                'type': 'POST',
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function (d) {
                    d.training_no = $('#training_no').val();
                    d.schedule_id = $('#batch_no').val();
                    d.training_duration = $('#training_duration').val();
                    d.bill_date = $('#bill_date').val();
                }
            },
            "columns": [
                {"data": 'DT_RowIndex', "name": 'DT_RowIndex'},
                {"data": "training_info"},
                {"data": "trainee_name"},
                {"data": "organization_name"},
                {"data": "dept_name"},
                {"data": "tot_days"},
                {"data": "bill_rate"},
                {"data": "bill_amount"},

            ]
        });


        $(document).on("change",'.bill-rate', function (e) {
            e.preventDefault();
            billCalculate(this);
        });

        function billCalculate(selector) {
            let bill_rate = $(selector).val();
            // let tot_days = $(selector).parents("tr").find("td:eq(5)").text();
            let tot_days = $(selector).parents("tr").find('input[name="tot_days[]"]').val();
            let tr = $(selector).closest('tr');
            if (bill_rate) {
                tr.find('input[name="bill_amount[]"]').val(Number(tot_days) * Number(bill_rate));
            } else {
                tr.find('input[name="bill_amount[]"]').val('');
            }
        }


    </script>

@endsection
