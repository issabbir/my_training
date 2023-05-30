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
                    <h4 class="card-title">Trainer Bill Preparation Entry</h4>
                    <hr>
                    <form method="POST" id="search-form" name="search-form">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-2">
                                <div class="form-group">
                                    <label class="required">Training No</label>
                                    <select required class="custom-select form-control select2" id="training_no"
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
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="required">Bill Date</label>
                                    <input type="text"
                                           autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           data-toggle="datetimepicker"
                                           id="bill_date"
                                           data-target="#bill_date"
                                           name="bill_date"
                                           required
                                           placeholder="YYYY-MM-DD"
                                    >
                                </div>
                            </div>
                            <div class="col-md-3 billGenerated1" style="display: none;">
                                <span class="badge badge-success form-control billGenerated"  style="padding: 10px; margin-top: 20px; height: 40px;display: none">Bill Approved</span>
                            </div>
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
                <div class="card-body">

                    <div class="row d-flex justify-content-between align-items-center">
                        <div class="col"><h4 class="card-title">Bill List</h4></div>
                        <div class="col-sm-3 d-flex justify-content-end">
                            <div class="form-group">
                                <label for="vat_rate_change">Vat Amount</label>
                                <input type="number" id="vat_rate_change" class="form-control" placeholder="Change vat amount" value="15">
                            </div>
                        </div>
                    </div>


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
                                    <th>Trainer Name</th>
                                    <th>No of Session</th>
                                    <th>Bill Rate</th>
                                    <th>Total  Amount</th>
                                    <th>Income Tax</th>
                                    <th>Vat AMT.</th>
                                    <th>TIN</th>
                                    <th>Bank A/C</th>
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

            $('#vat_rate_change').on('keyup', function (e) {
                $(".vat-change").val($(this).val());
            });

            customSelect2();
            datePicker('#bill_date');
            $('#search-form').on('submit', function (e) {
                e.preventDefault();
                oTable.draw();
                // billCalculate(".bill-rate");
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
                        url: "{{route('trainer-bill-preparation.trainer-bill-preparation-post')}}",
                        data: $(this).serialize(),
                        async: false,
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

        $('#training_no').on('change', function (e) {
            e.preventDefault();
            let trainingInfoId = $(this).val();
            if (trainingInfoId) {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/training-batch-no/' + trainingInfoId,
                    success: function (data) {
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
                url: APP_URL + "/trainer-bill-preparation-datatable",
                async:false,
                'type': 'POST',
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function (d) {
                    d.training_no = $('#training_no').val();
                    d.schedule_id = $('#batch_no').val();
                    d.bill_date = $('#bill_date').val();
                }
            },
            "columns": [
                {"data": 'DT_RowIndex', "name": 'DT_RowIndex'},
                {"data": "emp"},
                {"data": "total_days"},
                {"data": "bill_rate"},
                {"data": "bill_amount"},
                {"data": "income_tax"},
                {"data": "vat"},
                {"data": "tin"},
                {"data": "bank_acc"},

            ]
        });

        $(document).on("change",'.bill-rate', function (e) {
            e.preventDefault();
            billCalculate(this);
        });

        function billCalculate(selector) {
            let bill_rate = $(selector).val();
            // let duration = $(selector).parents("table").find("td:eq(2)").text();
            let duration = $(selector).closest('tr').find("td:eq(2)").text();
            // console.log(duration)
            let tr = $(selector).closest('tr');

            if (bill_rate) {
                tr.find('input[name="bill_amount[]"]').val(Number(duration) * Number(bill_rate));
            } else {
                tr.find('input[name="bill_amount[]"]').val('');
            }
        }

    </script>

@endsection
