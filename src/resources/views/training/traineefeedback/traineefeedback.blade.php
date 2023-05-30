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
                    <h4 class="card-title">Trainee Feedback</h4>
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
                    <form onsubmit="return checkForm()"
                        @if(isset($traineefeedback->feedback_id)) action="{{route('trainee-feedback.trainee-feedback-update',[$traineefeedback->feedback_id])}}"
                        @else action="{{route('trainee-feedback.trainee-feedback-post')}}" @endif method="post">
                        @csrf
                        @if (isset($traineefeedback->feedback_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Batch No</label>
                                    <select required class="custom-select form-control select2" id="schedule_id"
                                            name="schedule_id">
                                        <option value="">Select One</option>
                                        @foreach($schedule as $value)
                                            <option value="{{$value->schedule_id}}">{{$value->batch_id.' ('.$value->training_info->training_title.')'}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" id="feed_back_count" value="{{isset($feed_back_count) ? $feed_back_count :''}}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Trainee</label>
                                    <select required class="custom-select form-control select2" id="trainee_id"
                                            name="trainee_id">
                                        <option value="">Select One</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="">Improvement Suggestion</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="training_sugg"
                                        name="training_sugg"
                                        autocomplete="off"
                                        value="{{old('training_fee', isset($trainingschedule->batch_id) ? $trainingschedule->batch_id :'')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="mb-1 required">Overall Satisfaction</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="overall_sat_yn" id="overall_sat_yes"
                                                   value="{{ \App\Enums\YesNoFlag::YES }}" checked/>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="overall_sat_yn" value="{{ \App\Enums\YesNoFlag::NO }}"
                                                   id="overall_sat_no"/>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <fieldset class="border p-2 mt-2 mb-2 col-sm-12">
                                <legend class="w-auto required" style="font-size: 18px;">Feedback</legend>
                                <div class="table-responsive">
                                    <table id="foreign-tour-list"
                                           class="table table-sm table-bordered table-striped">
                                        <thead>
                                        <tr class="text-center">
                                            <th >Criteria</th>
                                            <th>Excellent</th>
                                            <th>Good</th>
                                            <th>Fair</th>
                                            <th>Okay</th>
                                            <th>Bad</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($feedback as $key=>$data)
                                            <tr class="text-center">
                                                <td class="text-left"><b>{{$data->feedback_for}}</b></td>
                                                <td>
                                                    <input class="text-center" type="checkbox" class="excellent" name="rating[]" value="{{$data->feedback_type_id.'-'.'5'}}" id="excellent">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="good" name="rating[]" value="{{$data->feedback_type_id.'-'.'4'}}" id="good">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="fair" name="rating[]" value="{{$data->feedback_type_id.'-'.'3'}}" id="fair">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="okay" name="rating[]" value="{{$data->feedback_type_id.'-'.'2'}}" id="okay">
                                                </td>
                                                <td>
                                                    <input type="checkbox" class="bad" name="rating[]" value="{{$data->feedback_type_id.'-'.'1'}}" id="bad">
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </fieldset>

                        </div>

                        <div class="row">
                            <div class="col-md-12 text-right" id="add">
                                <button type="submit" id="add"
                                        class="btn btn-primary mb-1">Submit
                                </button>

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
        $(document).ready(function () {
            $(':checkbox').on('change', function() {
                $(this).closest('tr').find(':checkbox').prop('checked', false);
                $(this).prop('checked', true);
            });
        });

        function checkForm()
        {
            var feed_back_count = $("#feed_back_count").val();
            var array = []
            var checkboxes = document.querySelectorAll('input[type=checkbox]:checked')

            for (var i = 0; i < checkboxes.length; i++) {
                array.push(checkboxes[i].value)
            }
            if(feed_back_count>array.length)
            {
                Swal.fire('Please fill up all information.');
                return false;
            }
            return true;
        }

        $('#schedule_id').on('change', function (e) {
            e.preventDefault();
            let schedule_id = $(this).val();
            if (schedule_id){
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/all-batch-wise-feed-back-trainee/' + schedule_id,
                    success: function (data) {
                        $('#trainee_id').html(data.html);
                    },
                    error: function (err) {
                        alert('error');
                    }
                });
            }
            else{
                $('#trainee_id').html('');
            }
        });
    </script>

@endsection
