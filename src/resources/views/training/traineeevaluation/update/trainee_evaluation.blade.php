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
                    <h4 class="card-title">Trainee Evaluation Update</h4>
                    <hr>
                    {{--@if(Session::has('message'))
                        <div class="alert {{Session::get('m-class') ? Session::get('m-class') : 'alert-danger'}} show"
                             role="alert">
                            {{ Session::get('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif--}}
                    <form method="POST" id="search-form" name="search-form">
                        {{ csrf_field() }}
                        <div class="row">
                            {{--<div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Schedule Id</label>
                                    <select required class="custom-select form-control select2" id="schedule_id"
                                            name="schedule_id">
                                        <option value="">Select One</option>
                                        @foreach($trainingschedule as $value)
                                            <option value="{{$value->schedule_id}}">{{$value->schedule_id}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>--}}
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="required">Batch No</label>
                                    <select required class="custom-select form-control select2" id="batch_id"
                                            name="batch_id">
                                        <option value="">Select One</option>
                                        @foreach($trainingschedule as $value)
                                            <option value="{{$value->batch_id}}">{{$value->batch_id.' ('.$value->training_info->training_title.')'}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="trainer_id" class="required">Exam Type</label>
                                    <select class="custom-select form-control select2" id="exam_type"
                                            name="exam_type">
                                        <option value="">Select One</option>
                                        {{--@foreach($examType as $value)
                                            <option
                                                value="{{$value->exam_type_id}}">{{$value->exam_type_name}}</option>
                                        @endforeach--}}
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 mt-2">
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
                <div class="card-body"><h4 class="card-title">Evaluation Update List</h4>
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
                                    <th>Name</th>
                                    <th>Organization</th>
                                    <th>Exam</th>
                                    <th>Total Score</th>
                                    <th>Passing Score</th>
                                    <th>Exam Score</th>
                                    <th>Remark</th>
                                </tr>
                                </thead>
                                <tbody> </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end mt-2">
                            <button type="submit" class="btn btn btn-dark shadow btn-secondary" name="final-results-submission" id="final-results-submission">Approve Current Page</button>&nbsp;
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('footer-script')
    <script type="text/javascript">
        var oTable = $('#final-results').DataTable({
            processing: true,
            serverSide: true,
            bDestroy : true,
            pageLength: 20,
            bFilter: true,
            lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'All']],
            ajax: {
                url: APP_URL+"/trainee-evaluation-update-datatable",
                'type': 'POST',
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function (d) {
                    //d.schedule_id = $('#schedule_id').val();
                    d.batch_id = $('#batch_id').val();
                    d.exam_type = $('#exam_type').val();
                }
            },
            "columns": [
                {"data": 'DT_RowIndex', "name": 'DT_RowIndex'},
                {"data": "trainee_name"},
                {"data": "organization_name"},
                {"data": "exam_type_name"},
                {"data": "total_marks"},
                {"data": "passing_score"},
                {"data": "exam_score"},
                {"data": "remark"},

            ]
        });

        $(document).ready(function () {

            $('#search-form').on('submit', function(e) {
                e.preventDefault();
                oTable.draw();
            });

            $('#final-results-form').on('submit', function(e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: "{{route('trainee-evaluation-update.trainee-evaluation-update-post')}}",
                    data: $(this).serialize(),
                    success: function (data) {
                       // $('#final-selection-message').html(data.message);
                        $('#final-selection-message').html(data.html);
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            });
        });

        $('#batch_id').on('change', function (e) {
            e.preventDefault();
            let batch_id = $(this).val();
            if (batch_id){
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/get-exam-type/' + batch_id,
                    success: function (data) {
                        $('#exam_type').html(data.html);
                    },
                    error: function (err) {
                        alert('error');
                    }
                });
            }
            else{
                $('#exam_type').html('');
            }
        });

        $(document).on("keyup",'.exam-score', function (e) {
            e.preventDefault();
            let exam_score = $(this).val();

            let pass_mark = $(this).parents("table").find("td:eq(5)").text();
            let tr = $(this).closest('tr');
            //console.log(pass_mark+' '+ exam_score);

            if(Number(pass_mark) > Number(exam_score)){
                tr.find('input[name="remark[]"]').val('Fail');
            }else{
                tr.find('input[name="remark[]"]').val('Pass');
            }
        });
    </script>

@endsection
