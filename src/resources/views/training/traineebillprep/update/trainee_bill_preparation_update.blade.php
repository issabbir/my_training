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
                    <h4 class="card-title">Trainee Bill Preparation Update</h4>
                    <hr>
                    <form method="POST" id="search-form" name="search-form">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-sm-5">
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
                            <div class="col-md-3">
                                <label class="required">Batch No</label>
                                <select name="batch_no" id="batch_no" class="form-control select2" required>
                                    <option value="">Select One</option>
                                </select>
                            </div>
                            <div class="col-md-3 mt-2">
                                <div class="d-flex justify-content-start col">
                                    <button type="submit" class="btn btn btn-dark shadow mb-1 btn-secondary">
                                        Search
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
                                    <th>Bill Date</th>
                                    <th>Duration</th>
                                    <th>Previous Rate</th>
                                    <th>Previous Bill Amount</th>
                                    <th width="10%">Bill Rate</th>
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
            datePicker('#bill_date');
            $('#search-form').on('submit', function (e) {
                e.preventDefault();
                oTable.draw();
                billCalculate(".bill-rate");
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
                        url: "{{route('bill-preparation-update.bill-preparation-update-post')}}",
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

        var oTable = $('#final-results').DataTable({
            processing: true,
            serverSide: true,
            bDestroy: true,
            pageLength: 20,
            bFilter: true,
            lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'All']],
            ajax: {
                async:false,
                url: APP_URL + "/bill-preparation-update-datatable",
                'type': 'POST',
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function (d) {
                    d.training_no = $('#training_no').val();
                    d.schedule_id = $('#batch_no').val();
                }
            },
            "columns": [
                {"data": 'DT_RowIndex', "name": 'DT_RowIndex'},
                {"data": "training_info"},
                {"data": "trainee_name"},
                {"data": "organization_name"},
                {"data": "dept_name"},
                {"data": "bill_date"},
                {"data": "duration"},
                {"data": "rate"},
                {"data": "total_amount"},
                {"data": "bill_rate" , "width": "10%"},
                {"data": "bill_amount"},

            ]
        });

        /*$(document).on("keyup",'.bill-rate', function (e) {
            e.preventDefault();
            let bill_rate = $(this).val();
            let duration = $(this).parents("table").find("td:eq(6)").text();
            let tr = $(this).closest('tr');

            if(bill_rate){
                tr.find('input[name="bill_amount[]"]').val(Number(duration) * Number(bill_rate));
            }else{
                tr.find('input[name="bill_amount[]"]').val('');
            }
        });*/

        $(document).on("change",'.bill-rate', function (e) {
            e.preventDefault();
            billCalculate(this);
        });

        function billCalculate(selector) {
            let bill_rate = $(selector).val();
            // console.log(bill_rate)
            let duration = $(selector).parents("table").find("td:eq(6)").text();
            let tr = $(selector).closest('tr');

            if (bill_rate) {
                tr.find('input[name="bill_amount[]"]').val(Number(duration) * Number(bill_rate));
            } else {
                tr.find('input[name="bill_amount[]"]').val('');
            }
        }

    </script>

@endsection
