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
                <div class="card-body" id="top-div">
                    <h4 class="card-title">Trainee Attendance Update</h4>
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
                                    <label class="required">Batch Id</label>
                                    <select required class="custom-select form-control select2" id="batch_id"
                                            name="batch_id">
                                        <option value="">Select One</option>
                                        @foreach($trainingschedule as $value)
                                            <option value="{{$value->batch_id.'||||'.$value->schedule_id}}">
                                                {{$value->batch_id}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
{{--                            <div class="col-sm-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="required">Attendance Date</label>--}}
{{--                                    <input type="text"--}}
{{--                                           autocomplete="off"--}}
{{--                                           class="form-control datetimepicker-input"--}}
{{--                                           data-toggle="datetimepicker"--}}
{{--                                           id="exam_date"--}}
{{--                                           name="exam_date"--}}
{{--                                           disabled--}}
{{--                                    >--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="col-sm-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="required">Total Day</label>--}}
{{--                                    <input type="text"--}}
{{--                                           autocomplete="off"--}}
{{--                                           class="form-control"--}}
{{--                                           id="total_day"--}}
{{--                                           name="total_day"--}}
{{--                                           readonly--}}
{{--                                    >--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Training Held (Days)</label>
                                    <input type="text"
                                           autocomplete="off"
                                           class="form-control"
                                           id="total_training_days"
                                           name="total_training_days"
                                           readonly
                                    >
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="d-flex justify-content-start col">
                                    <button type="submit" class="btn btn btn-dark shadow mt-2 btn-secondary">
                                        Search
                                    </button>
                                </div>
                            </div>

                        </div>
                        <input type="hidden"
                               id="total_day"
                               name="total_day">
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body"><h4 class="card-title">Attendance Update List</h4>
                    <hr>
                    <div class="row">
                        <div class="col" id="final-selection-message"></div>
                    </div>
                    <form method="post" name="final-results-form" id="final-results-form">
                        {{csrf_field()}}
                        <div class="table-responsive">
                            <input type="hidden" name="date_post" id="date_post"/>
                            <table class="table table-sm datatable mdl-data-table" id="final-results">
                                <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Name</th>
                                    <th>Organization</th>
                                    <th>Attendance</th>
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
        var oTable = $('#final-results').DataTable({
            processing: true,
            serverSide: true,
            bDestroy: true,
            autoWidth: false,
            pageLength: 100,
            bFilter: true,
            // lengthMenu: [[5, 10, 20, -1], [5, 10, 20, 'All']],
            ajax: {
                url: APP_URL + "/trainee-attendance-update-datatable-list",
                'type': 'POST',
                'headers': {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: function (d) {
                    d.schedule_id = $('#schedule_id').val();
                    d.batch_id = $('#batch_id').val();
                    d.exam_date = $('#exam_date').val();
                }
            },
            "columns": [
                {"data": 'DT_RowIndex', "name": 'DT_RowIndex'},
                {"data": "student"},
                /*{"data": "trainee_name"},*/
                {"data": "organization_name"},
                /*{"data": "in_time_v"},
                {"data": "out_time_v"},*/
                {"data": "selected"},

            ]
        });

        function convertDate(inputFormat) {
            function pad(s) {
                return (s < 10) ? '0' + s : s;
            }

            var d = new Date(inputFormat)
            return [d.getFullYear(), pad(d.getMonth() + 1), pad(d.getDate())].join('-')
        }

        $('#batch_id').on('change', function (e) {
            e.preventDefault();
            let data = $(this).val();

            var fields = data.split('||||');

            var batch_id = fields[0];
            var schedule_id = fields[1];

            if (batch_id) {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/select-date',
                    data: {'batch_id' : batch_id, 'schedule_id' : schedule_id },
                    success: function (data) {
                        // $('#exam_date').val(convertDate(data[0].attendance_date));
                        let fields = data.split('+');

                        let total_day = fields[0];
                        let total_training_days = fields[1];
                        $('#total_day').val(total_day);
                        $('#total_training_days').val(total_training_days);
                    },
                    error: function (err) {
                        alert('error');
                    }
                });
            } else {
                // $('#exam_date').val('');
            }
        });


        $(document).ready(function () {
            datePicker('#exam_date');

            $('#search-form').on('submit', function (e) {
                e.preventDefault();
                oTable.draw();
                $('#date_post').val($('#exam_date').val());
            });

            $('#final-results-form').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    type: 'POST',
                    url: "{{route('trainee-attendance-update.trainee-attendance-update-post')}}",
                    data: $(this).serialize(),
                    success: function (data) {
                        $('html, body').animate({
                            scrollTop: $("#top-div").offset().top
                        }, 500);

                        $('#final-selection-message').html(data.html);
                    },
                    error: function (data) {
                        alert('error');
                    }
                });
            });


        });
    </script>

@endsection
