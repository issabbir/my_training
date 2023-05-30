@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
    <style type="text/css">
        .display-none{
            display: none;
        }
        .display-block{
            display: flex;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <!-- Table Start -->
                <div class="card-body">
                    <h4 class="card-title">Training Schedule</h4>
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
                    <form enctype="multipart/form-data"
                          @if(isset($trainingschedule->schedule_id)) action="{{route('training-schedule.training-schedule-update',[$trainingschedule->schedule_id])}}"
                          @else action="{{route('training-schedule.training-schedule-post')}}" @endif method="post">
                        @csrf
                        @if (isset($trainingschedule->schedule_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Batch No.</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="batch_id"
                                        name="batch_id"
                                        autocomplete="off"
                                        value="{{old('batch_id', isset($trainingschedule->batch_id) ? $trainingschedule->batch_id :'')}}"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Training No.</label>
                                    <select class="custom-select form-control select2" required id="training_id"
                                            name="training_id"
                                            data-training-id="{{old('training_id',isset($trainingschedule->training_id) ? $trainingschedule->training_id :'')}}"
                                    >
                                        <option value="">Select One</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Course Co-Ordinator Name</label>
                                    <input
                                        readonly
                                        type="text"
                                        class="form-control"
                                        id="coordinator_name"
                                        name="coordinator_name"
                                        autocomplete="off"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="">Mobile No</label>
                                    <input
                                        readonly
                                        type="number"
                                        class="form-control"
                                        id="mobile_no"
                                        autocomplete="off"
                                        name="mobile_no"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="">Email</label>
                                    <input
                                        readonly
                                        type="email"
                                        class="form-control"
                                        id="email"
                                        autocomplete="off"
                                        name="email"
                                    >
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="">Training Start Date</label>
                                    <input type="date"
                                           autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           id="from_date"
                                           data-target="#from_date"
                                           name="from_date"
                                           value=""
                                           placeholder="YYYY-MM-DD"

                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="">Training End Date</label>
                                    <input type="date"
                                           autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           id="to_date"
                                           data-target="#to_date"
                                           name="to_date"
                                           value=""
                                           placeholder="YYYY-MM-DD"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Course Fee</label>
                                    <input readonly
                                           type="number"
                                           class="form-control"
                                           id="training_fee"
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           maxlength="5"
                                           name="training_fee"
                                           autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="training_location_id" class="required">Training Location</label>
                                    <select class="custom-select form-control select2" required id="training_location"
                                            name="training_location">
                                        @foreach($lTrainingLocation as $value)
                                            <option value="{{$value->location_id}}"
                                                {{isset($trainingschedule->location_id) && $trainingschedule->location_id == $value->location_id ? 'selected' : ''}}
                                            >{{$value->location_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">No Of Participants</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="training_capacity"
                                        name="training_capacity"
                                        autocomplete="off"
                                        value="{{old('training_facilities', isset($trainingschedule->training_capacity) ? $trainingschedule->training_capacity :'')}}"
                                        required
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="mb-1">Allowance</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                   name="allowance_yn" id="allowance_yes"
                                                   onclick="javascript:yesnoCheck3();" checked
                                                   @if(isset($trainingschedule->allowance_yn) && $trainingschedule->allowance_yn == "Y") checked @endif/>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="allowance_yn" value="{{ \App\Enums\YesNoFlag::NO }}"
                                                   onclick="javascript:yesnoCheck2();"
                                                   id="allowance_no"
                                                   @if(isset($trainingschedule->allowance_yn) && $trainingschedule->allowance_yn == "N") checked @endif/>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
{{--                            <div class="col-sm-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="required">Calendar</label>--}}
{{--                                    <select class="custom-select form-control select2" required id="calender_id"--}}
{{--                                            name="calender_id">--}}
{{--                                        <option value="">Select One</option>--}}
{{--                                        @foreach($trainingcalender as $value)--}}
{{--                                            <option value="{{$value->calender_id}}"--}}
{{--                                                {{isset($trainingschedule->calender_id) && $trainingschedule->calender_id == $value->calender_id ? 'selected' : ''}}--}}
{{--                                            >{{$value->calender_name}}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Calendar</label>
                                    <input type="hidden" required id="calender_id" name="calender_id" value="{{$trainingcalender->calender_id}}"/>
                                    <input readonly
                                           type="text"
                                           class="form-control"
                                           id="calender_name"
                                           name="calender_name"
                                           autocomplete="off"
                                           value="{{$trainingcalender->calender_name}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="mb-1">Discount</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="discount_yn" id="disc_yes"
                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                   onclick="javascript:disk_amt_yes_chk();"
                                                   @if(isset($trainingschedule->discount_yn) && $trainingschedule->discount_yn == \App\Enums\YesNoFlag::YES) checked @endif/>
                                            <label class="form-check-label">Yes</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="discount_yn" id="disc_no"
                                                   value="{{ \App\Enums\YesNoFlag::NO }}"
                                                   onclick="javascript:disk_amt_no_chk();"
                                                   {{!isset($trainingschedule->discount_yn) ? 'checked' : ''}}
                                                   @if(isset($trainingschedule->discount_yn) && $trainingschedule->discount_yn == \App\Enums\YesNoFlag::NO) checked @endif/>
                                            <label class="form-check-label">No</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Discount Amount</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        id="disc_amt"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        maxlength="5"
                                        autocomplete="off"
                                        name="disc_amt"
                                        value="{{old('disc_amt', isset($trainingschedule->discount_amount) ? $trainingschedule->discount_amount :'')}}"

                                    >
                                </div>
                            </div>

                            @if(isset ($trainingschedule->training_total_cost))
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="">Per Perticipant Cost</label>
                                        <input readonly
                                               type="number"
                                               class="form-control"
                                               autocomplete="off"
                                               id="update_tot_cost"
                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                               maxlength="5"
                                               name="total_cost"
                                               value="{{old('total_cost', isset($trainingschedule->training_total_cost) ? $trainingschedule->training_total_cost :'')}}"
                                        >
                                    </div>
                                </div>
                            @else
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="">Per Perticipant Cost</label>
                                        <input readonly
                                               type="number"
                                               class="form-control"
                                               autocomplete="off"
                                               id="total_cost"
                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                               maxlength="5"
                                               name="total_cost"
                                        >
                                    </div>
                                </div>
                            @endif

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Course Director</label>
                                    {{--<select class="custom-select select2" name="course_director_id" id="course_director_id" required
                                            data-emp-id="@if($trainingschedule && $trainingschedule->course_director_id) {{$trainingschedule->course_director_id}} @endif" >
                                    </select>--}}
                                    <select class="custom-select select2 form-control course_director_id" required
                                            name="course_director_id">
                                        @if(isset($trainingschedule->course_director_id))
                                            <option
                                                value="{{$trainingschedule->course_director_id}}">{{'('.$drictor->emp_code.')'.$drictor->emp_name}}</option>
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea rows="3" wrap="soft"
                                              name="remarks"
                                              class="form-control"
                                              id="remarks">{{old('remarks',isset($trainingschedule->remarks) ? $trainingschedule->remarks : '')}}</textarea>
                                </div>
                            </div>
                            @if(isset ($trainingschedule->schedule_status_id))
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label class="required">Schedule Status</label>
                                        <select required class="custom-select form-control select2"
                                                id="schedule_status_id"
                                                name="schedule_status_id">
                                            <option
                                                value="1" @if(!empty($trainingschedule)) @if($trainingschedule->schedule_status_id=='1') {{'selected="selected"'}} @endif @endif>
                                                Active
                                            </option>
                                            <option
                                                value="4" @if(!empty($trainingschedule)) @if($trainingschedule->schedule_status_id=='4') {{'selected="selected"'}} @endif @endif>
                                                Closed
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <fieldset class="border p-1 mt-1 mb-1 col-sm-12">
                                <legend class="w-auto" style="font-size: 18px;">Trainer Assign</legend>
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label for="trainer_id" class="required">Trainer</label>
                                            <select class="custom-select form-control select2" id="dtl_trainer"
                                                    name="dtl_trainer">
                                                <option value="">Select One</option>
                                                @foreach($trainerlist as $value)
                                                    <option
                                                        value="{{$value->trainer_id}}">{{$value->trainer_name. " (".$value->code.")" }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="required">Subject</label>
                                            <input type="text"
                                                   autocomplete="off"
                                                   class="form-control"
                                                   id="subject"
                                                   name="subject"
                                                   value=""
                                                   placeholder="Subject"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="required">Training Date</label>
                                            <input type="text"
                                                   autocomplete="off"
                                                   class="form-control datetimepicker-input"
                                                   data-toggle="datetimepicker"
                                                   id="training_date"
                                                   data-target="#training_date"
                                                   name="training_date"
                                                   value=""
                                                   placeholder="YYYY-MM-DD"
                                            >
                                        </div>
                                    </div>

                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            <label class="required">Time From</label>
                                            <input type="text"
                                                   autocomplete="off"
                                                   class="form-control datetimepicker-input"
                                                   data-toggle="datetimepicker"
                                                   id="dtl_time_from"
                                                   data-target="#dtl_time_from"
                                                   name="dtl_time_from"
                                                   value=""
                                                   placeholder="HH-MM"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div class="form-group">
                                            <label class="required">Time To</label>
                                            <input type="text"
                                                   autocomplete="off"
                                                   class="form-control datetimepicker-input"
                                                   data-toggle="datetimepicker"
                                                   id="dtl_time_to"
                                                   data-target="#dtl_time_to"
                                                   name="dtl_time_to"
                                                   value=""
                                                   placeholder="HH-MM"
                                            >
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div id="start-no-field" class="form-group">
                                            <label>Remarks</label>
                                            <input type="text" id="tab_remrks" name="tab_remrks"
                                                   class="form-control" value="">
                                        </div>
                                    </div>

                                    <div class="col-sm-1" align="right">
                                        <div id="start-no-field">
                                            <label for="seat_to1">&nbsp;</label><br/>
                                            <button type="button" id="append"
                                                    class="btn btn-primary mb-1 add-row-trainer-assign">
                                                ADD
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-1">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered"
                                               id="table-exam-result">
                                            <thead>
                                            <tr>
                                                <th role="columnheader" scope="col"
                                                    aria-colindex="1" class="" width="5%">Action
                                                </th>
                                                <th role="columnheader" scope="col"
                                                    aria-colindex="2" class="" width="13%">Trainer
                                                </th>
                                                <th role="columnheader" scope="col"
                                                    aria-colindex="4" class="" width="13%">Subject
                                                </th>
                                                <th role="columnheader" scope="col"
                                                    aria-colindex="5" class="" width="25%">Training-Date
                                                </th>
                                                <th role="columnheader" scope="col"
                                                    aria-colindex="6" class="" width="17%">Time From
                                                </th>
                                                <th role="columnheader" scope="col"
                                                    aria-colindex="7" class="" width="17%">Time To
                                                </th>
                                                <th role="columnheader" scope="col"
                                                    aria-colindex="8" class="" width="10%">Remarks
                                                </th>
                                            </tr>
                                            </thead>

                                            <tbody role="rowgroup" id="comp_body">
                                            @if(!empty($trainingscheduledtl))
                                                @foreach($trainingscheduledtl as $key=>$value)
                                                    <tr role="row">
                                                        <td aria-colindex="1" role="cell" class="text-center">
                                                            <input type='checkbox' name='record' value="{{$value->trainer_id.'+'.$value->schedule_dtl_id}}">
                                                            <input type="hidden" name="schedule_dtl_id[]"
                                                                   value="{{$value->schedule_dtl_id}}"
                                                                   class="schedule_dtl_id">
                                                            <input type="hidden" name="delete_trainer_id[]"
                                                                   value="{{$value->trainer_id}}"
                                                                   class="delete_trainer_id">
                                                            <input type="hidden" name="schedule_id[]"
                                                                   value="{{$value->schedule_id}}">
                                                        </td>
                                                        <td aria-colindex="2" role="cell">
                                                            <select class="custom-select form-control select2"
                                                                    id="dtl_trainer_listabc_{{$key + 1}}"
                                                                    name="dtl_trainer_id[]">
                                                                <option value="">Select One</option>
                                                                @foreach($trainerlist as $values)
                                                                    <option value="{{$values->trainer_id}}"
                                                                        {{isset($value->trainer_id) && $value->trainer_id == $values->trainer_id ? 'selected' : ''}}
                                                                    >{{$values->trainer_name." (".$values->code.")"}}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td aria-colindex="4" role="cell">
                                                            <input type="text" class="form-control"
                                                                   name="subject[]"
                                                                   value="{{$value->subject}}">
                                                        </td>
                                                        <td aria-colindex="5" role="cell"
                                                            id="training_date_pick_{{$key + 1}}"
                                                            onclick="call_date_picker(this)"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                   autocomplete="off"
                                                                   class="form-control datetimepicker-input"
                                                                   data-toggle="datetimepicker"
                                                                   data-target="#training_date_pick_{{$key + 1}}"
                                                                   name="training_date[]"
                                                                   value="{{date('Y-m-d', strtotime($value->training_date))}}"
                                                                   data-predefined-date=""
                                                            >
                                                        </td>
                                                        <td aria-colindex="6" role="cell"
                                                            id="training_start_pick_{{$key + 1}}"
                                                            onclick="call_time_picker(this)"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                   autocomplete="off"
                                                                   class="form-control datetimepicker-input"
                                                                   data-toggle="datetimepicker"
                                                                   data-target="#training_start_pick_{{$key + 1}}"
                                                                   name="dtl_time_from[]"
                                                                   value="{{date('h:i a', strtotime($value->training_start_time))}}"
                                                                   data-predefined-date=""
                                                            >
                                                        </td>
                                                        <td aria-colindex="7" role="cell"
                                                            id="training_end_pick_{{$key + 1}}"
                                                            onclick="call_time_picker(this)"
                                                            data-target-input="nearest">
                                                            <input type="text"
                                                                   autocomplete="off"
                                                                   class="form-control datetimepicker-input"
                                                                   data-toggle="datetimepicker"
                                                                   data-target="#training_end_pick_{{$key + 1}}"
                                                                   name="dtl_time_to[]"
                                                                   value="{{date('h:i a', strtotime($value->training_end_time))}}"
                                                                   data-predefined-date=""
                                                            >
                                                        </td>
                                                        <td aria-colindex="8" role="cell">
                                                            <input type="text" class="form-control"
                                                                   name="tab_remrks[]"
                                                                   value="{{$value->remarks}}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-start">

                                    <button type="button"
                                            class="btn btn-primary mb-1 delete-row-trainer-assign">
                                        Delete
                                    </button>
                                </div>
                            </fieldset>

                            <fieldset class="border p-1 mt-1 mb-1 col-md-12">
                                <legend class="w-auto" style="font-size: 18px;">Trainee Assign</legend>
                                <div class="row {{ (isset($traineeAttendance) == null)  ? '': 'display-none'}}">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="required">Trainee</label>
                                            {{--<select class="custom-select form-control select2" name="emp_id"
                                                    id="emp_id">
                                                <option value="">Select One</option>
                                                @foreach($traineeList as $value)
                                                    <option value="{{$value->trainee_id}}">
                                                        {{$value->trainee_name.'-'.$value->organization_name.'-'.$value->dept_name.'-'.$value->trainee_code}}
                                                    </option>
                                                @endforeach
                                            </select>--}}
                                            <select class="custom-select form-control select2" name="trainee_id"
                                                    id="trainee_id">
                                                <option value="">Select One</option>
                                            </select>
                                            <input type="hidden" id="trainee_count" name="trainee_count"
                                                   value="{{isset($traineeCount) ? $traineeCount : ''}}">
                                            <input type="hidden" id="all_trainee" name="all_trainee"
                                                   value="{{isset($allTrainee) ? $allTrainee : ''}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Organization</label>
                                            <input readonly type="text" id="organization" name="organization"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-1" align="right">
                                        <div id="start-no-field">
                                            <label for="seat_to1">&nbsp;</label><br/>
                                            <button type="button" id="append"
                                                    class="btn btn-primary mb-1 add-row-trainee">
                                                ADD
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-1">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered" id="table-emp">
                                            <thead>
                                            <tr>
                                                <th style="height: 25px;text-align: left; width: 5%">Action</th>
                                                <th style="height: 25px;text-align: left; width: 35%">Employee
                                                </th>
                                                <th style="height: 25px;text-align: left; width: 20%">
                                                    Organization
                                                </th>
                                            </tr>
                                            </thead>

                                            <tbody id="emp_body">
                                            @if(!empty($TraineeAssignmentSchedule))
                                                @foreach($TraineeAssignmentSchedule as $key=>$value)
                                                    <tr>
                                                        <td class="text-center">
                                                            <input type='checkbox' name='record' value="{{$value->trainee_id.'+'.$value->assignment_id}}">
                                                            <input type="hidden" name="assignment_id[]"
                                                                   value="{{$value->assignment_id}}"
                                                                   class="assignment_id">
                                                            <input type="hidden" name="dtl_trainee_id[]"
                                                                   value="{{$value->trainee_id}}"
                                                                   class="delete_trainee_id">
                                                            <input type="hidden" name="dtl_schedule_mst_id[]"
                                                                   value="{{$value->schedule_mst_id}}"></td>
                                                        <td>{{"(".$value->trainee_code.") ".$value->trainee_name}}</td>
                                                        <td>{{$value->organization_name}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-start">

                                    <button type="button"
                                            class="btn btn-primary mb-1 delete-row-trainee {{ (isset($traineeAttendance) == null)  ? '': 'display-none'}}">
                                        Delete
                                    </button>
                                </div>
                            </fieldset>

                            <fieldset class="border p-1 mt-1 mb-1 col-sm-12">
                                <legend class="w-auto" style="font-size: 18px;">Trainee Exam Assign
                                </legend>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label class="required">Exam Type</label>
                                            <select class="custom-select form-control select2" name="exam_type"
                                                    id="exam_type">
                                                <option value="">Select One</option>
                                                @foreach($examType as $value)
                                                    <option
                                                        value="{{$value->exam_type_id}}">{{$value->exam_type_name}}</option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" id="examtype_count" name="examtype_count"
                                                   value="{{isset($allExamtype_count) ? $allExamtype_count : ''}}">
                                            <input type="hidden" id="all_examtype" name="all_examtype"
                                                   value="{{isset($allExamtype) ? $allExamtype : ''}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="required">Total Marks</label>
                                            <input type="number" id="total_marks" name="total_marks"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                            <label class="required">Pass Marks</label>
                                            <input type="number" id="pass_marks" name="pass_marks"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label>Remark</label>
                                            <input type="text" id="remarks_exam_type" name="remarks_exam_type"
                                                   class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div id="start-no-field">
                                            <label for="seat_to1">&nbsp;</label><br/>
                                            <button type="button" id="append"
                                                    class="btn btn-primary mb-1 add-row-exam-type">
                                                ADD
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-1">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered"
                                               id="table-exam-type">
                                            <thead>
                                            <tr>
                                                <th style="height: 25px;text-align: left; width: 5%">Action</th>
                                                <th style="height: 25px;text-align: left; width: 25%">Exam Type</th>
                                                <th style="height: 25px;text-align: left; width: 20%">Total Mark
                                                </th>
                                                <th style="height: 25px;text-align: left; width: 20%">Pass Mark</th>
                                                <th style="height: 25px;text-align: left; width: 30%">Remark</th>
                                            </tr>
                                            </thead>

                                            <tbody id="exam-type">

                                            @if(!empty($TraineeExamType))
                                                @foreach($TraineeExamType as $key=>$value)
                                                    <tr>
                                                        <td class="text-center">
                                                            <input type='checkbox' name='record' value="{{$value->exam_type_id.'+'.$value->trainee_exam_sch_id}}">
                                                            <input type="hidden" name="trainee_exam_sch_id[]"
                                                                   value="{{$value->trainee_exam_sch_id}}"
                                                                   class="trainee_exam_sch_id">
                                                            <input type="hidden" name="schedule_mst_id[]"
                                                                   value="{{$value->schedule_mst_id}}">
                                                            <input type="hidden" name="exam_type_id[]"
                                                                   value="{{$value->exam_type_id}}"
                                                                   class="delete_exam_type_id"></td>
                                                        <input type="hidden" name="exam_type_name[]"
                                                               value="{{$value->exam_type_name}}"></td>
                                                        <td>{{$value->exam_type_name}}</td>
                                                        <td><input type="number" class="form-control"
                                                                   name="total_marks[]"
                                                                   value="{{$value->total_marks}}"></td>
                                                        <td><input type="number" class="form-control"
                                                                   name="pass_marks[]"
                                                                   value="{{$value->pass_marks}}"></td>
                                                        <td><input type="text" class="form-control"
                                                                   name="remarks_exam_type[]"
                                                                   value="{{$value->remarks}}"></td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-start">

                                    <button type="button"
                                            class="btn btn-primary mb-1 delete-row-exam-type">
                                        Delete
                                    </button>
                                </div>
                            </fieldset>

                            <fieldset class="border p-1 mt-1 mb-1 col-sm-12">
                                <legend class="w-auto" style="font-size: 18px;">Supporting Stuff Assign
                                </legend>
                                <div class="row">
                                    <div class="col-md-3" id="show_emp">
                                        <div class="form-group">
                                            <label class="required">Emp Code</label>
                                            <select class="custom-select select2" name="emp_id" id="emp_id">

                                            </select>
                                            <input type="hidden" id="supprotingStuff_count" name="supprotingStuff_count"
                                                   value="{{isset($supprotingStuff_count) ? $supprotingStuff_count : ''}}">
                                            <input type="hidden" id="allSupprotingStuff" name="allSupprotingStuff"
                                                   value="{{isset($allSupprotingStuff) ? $allSupprotingStuff : ''}}">
                                        </div>
                                    </div>
                                    <div class="col-sm-1">
                                        <div id="start-no-field">
                                            <label for="seat_to1">&nbsp;</label><br/>
                                            <button type="button" id="append"
                                                    class="btn btn-primary mb-1 add-row-supp-stuff">
                                                ADD
                                            </button>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-12 mt-1">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped table-bordered"
                                               id="table-supp-stuff">
                                            <thead>
                                            <tr>
                                                <th style="height: 25px;text-align: left; width: 5%">Action</th>
                                                <th style="height: 25px;text-align: left; width: 25%">Stuff</th>
                                            </tr>
                                            </thead>

                                            <tbody id="supp-stuff">
                                            @if(!empty($supprotingStuff))
                                                @foreach($supprotingStuff as $key=>$value)
                                                    <tr>
                                                        <td class="text-center">
                                                            <input type='checkbox' name='record' value="{{$value->emp_id.'+'.$value->support_member_id}}">
                                                        </td>
                                                        <td>{{'('.$value->emp_code.') '.$value->emp_name}}</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                            </tbody>
                                        </table>

                                    </div>
                                </div>
                                <div class="col-12 d-flex justify-content-start">

                                    <button type="button"
                                            class="btn btn-primary mb-1 delete-row-supp-stuff">
                                        Delete
                                    </button>
                                </div>
                            </fieldset>

                            @if($trainingschedule)

                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/training-schedule')}}">
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
                        </div>
                    </form>
                </div>

            </div>
            @include('training.trainingschedule.trainingschedule_list')
        </div>
    </div>
@endsection

@section('footer-script')

    <script type="text/javascript">
        let employees = APP_URL + '/ajax/employees';
        $('.course_director_id').select2({
            placeholder: "Select one",
            ajax: {
                url: employees,
                data: function (params) {
                    if (params.term) {
                        if (params.term.trim().length < 1) {
                            return false;
                        }
                    } else {
                        return false;
                    }

                    return params;
                },
                dataType: 'json',
                processResults: function (data) {
                    var formattedResults = $.map(data, function (obj, idx) {
                        obj.id = obj.emp_id;
                        obj.text = '('+obj.emp_code+')'+obj.emp_name;
                        return obj;
                    });
                    return {
                        results: formattedResults,
                    };
                }
            }
        });

        let trainerArray = new Array();
        let dateArray = new Array();
        let timeArray = new Array();
        let dataArray = new Array();
        let dataArray2 = new Array();
        let dataArray3 = new Array();
        let dataArray4 = new Array();

        function dateCheck(from,to,check) {

            var fDate,lDate,cDate;
            fDate = Date.parse(from);
            lDate = Date.parse(to);
            cDate = Date.parse(check);

            if((cDate <= lDate && cDate >= fDate)) {
                return true;
            }
            return false;
        }

        function convertDate(inputFormat) {
            function pad(s) {
                return (s < 10) ? '0' + s : s;
            }

            var d = new Date(inputFormat)
            return [d.getFullYear(), pad(d.getMonth() + 1), pad(d.getDate())].join('-')
        }

        function pre_req_yes_chk() {
            if (document.getElementById('pre_req_yes').checked) {
                $("#pre_training_id").prop("disabled", false);
            }
        }

        function pre_req_no_chk() {
            if (document.getElementById('pre_req_no').checked) {
                $("#pre_training_id").prop("disabled", true);
                $("#pre_training_id").val('').trigger('change');
            }
        }

        function disk_amt_yes_chk() {
            if (document.getElementById('disc_yes').checked) {
                $("#disc_amt").prop("disabled", false);
            }
        }

        function disk_amt_no_chk() {
            if (document.getElementById('disc_no').checked) {
                $("#disc_amt").prop("disabled", true);
                $('#disc_amt').val('');
                $("#total_cost").val(Number($("#training_fee").val()));
                $("#update_tot_cost").val(Number($("#training_fee").val()));
            }
        }

        $("#training_capacity").change(function () {
            var capacity_chk = $("#training_capacity").val();
            if(capacity_chk < dataArray2.length){
                Swal.fire('Delete Extra Trainee.');
                $("#training_capacity").val(dataArray2.length);
            }
        });

        $(".add-row-trainer-assign").click(function () {

            let dtl_trainer_id = $("#dtl_trainer option:selected").val();
            let dtl_trainer_name = $("#dtl_trainer option:selected").text();

            let dtl_subject = $("#subject").val();

            let dtl_training_id = $("#dtl_training_id option:selected").val();
            let dtl_training_name = $("#dtl_training_id option:selected").text();

            let tab_remrks = $("#tab_remrks").val();
            let training_date = $("#training_date").val();
            let from_date = $("#from_date").val();
            let to_date = $("#to_date").val();
            if(from_date){
                if(!dateCheck(from_date,to_date,training_date)){
                    Swal.fire('Please select date between Training Start and End Date.');
                    return false;
                }
            }else{
                Swal.fire('Please select training');
                return false;
            }




            let dtl_time_from = $("#dtl_time_from").val();
            let dtl_time_to = $("#dtl_time_to").val();

            if ($.inArray(dtl_trainer_id, trainerArray) > -1) {
                if ($.inArray(training_date, dateArray) > -1) {
                    if ($.inArray(dtl_time_from, timeArray) > -1) {
                        Swal.fire('Duplicate Value Not allowed.');
                        return false;
                    }
                }
            }

            if (dtl_trainer_id != '' && dtl_training_id != '' && dtl_subject != '' && training_date != '' && dtl_time_from != '' && dtl_time_to != '') {

                let markup = "<tr role='row'>" +
                    "<td aria-colindex='1' role='cell' class='text-center'>" +
                    "<input type='checkbox' name='record' value='" + dtl_trainer_id  + "+" + "" + "'>" +
                    "<input type='hidden' name='dtl_trainer_id[]' value='" + dtl_trainer_id + "'>" +
                    "<input type='hidden' name='dtl_training_id[]' value='" + dtl_training_id + "'>" +
                    "<input type='hidden' name='dtl_subject[]' value='" + dtl_subject + "'>" +
                    "<input type='hidden' name='subject[]' value='" + dtl_subject + "'>" +
                    "<input type='hidden' name='training_date[]' value='" + training_date + "'>" +
                    "<input type='hidden' name='dtl_time_from[]' value='" + dtl_time_from + "'>" +
                    "<input type='hidden' name='dtl_time_to[]' value='" + dtl_time_to + "'>" +
                    "<input type='hidden' name='tab_remrks[]' value='" + tab_remrks + "'>" +
                    "</td><td aria-colindex='2' role='cell'>" + dtl_trainer_name + "</td>" +
                    "<td aria-colindex='4' role='cell'>" + dtl_subject + "</td>" +
                    "<td aria-colindex='5' role='cell'>" + training_date + "</td>" +
                    "<td aria-colindex='6' role='cell'>" + dtl_time_from + "</td>" +
                    "<td aria-colindex='7' role='cell'>" + dtl_time_to + "</td>" +
                    "<td aria-colindex='8' role='cell'>" + tab_remrks + "</td></tr>";
                $("#dtl_training_id").val('').trigger('change');
                $("#dtl_trainer").val('').trigger('change');
                $("#subject").val("");
                $("#tab_remrks").val("");
                $("#training_date").val("");
                $("#dtl_time_from").val("");
                $("#dtl_time_to").val("");
                $("#table-exam-result tbody").append(markup);

                trainerArray.push(dtl_trainer_id);
                dateArray.push(training_date);
                timeArray.push(dtl_time_from);
                //alert(trainerArray);
                //alert(dateArray);
                //alert(timeArray);

            } else {
                Swal.fire('Fill required value.');
            }
        });

        $(".delete-row-trainer-assign").click(function () {
            let arr_stuff = [];
            let dtl_trainer_id = [];
            let schedule_dtl_id = [];
            $(':checkbox:checked').each(function(i){
                arr_stuff[i] = $(this).val();
                let sd = arr_stuff[i].split('+');
                dtl_trainer_id.push(sd[0]);
                if(sd[1]){
                    schedule_dtl_id.push(sd[1]);
                }
            });

            if(schedule_dtl_id.length != 0){
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: 'GET',
                            url: '/trainer-data-remove',
                            data: {schedule_dtl_id: schedule_dtl_id},
                            success: function (msg) {
                                // console.log(msg)
                                if (msg == 0) {
                                    Swal.fire({
                                        title: 'Can not remove data. Attedeance process ongoing for this schedule.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                    //return false;
                                }else{
                                    Swal.fire({
                                        title: 'Entry Successfully Deleted!',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(function () {
                                        $('td input:checked').closest('tr').remove();
                                    });
                                }
                            }
                        });
                    }
                });
            }else{
                Swal.fire({
                    title: 'Entry Successfully Deleted!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function () {
                    $('td input:checked').closest('tr').remove();
                });
            }
        });

        $(".add-row-trainee").click(function () {
            var dtl_trainee_id = $("#trainee_id option:selected").val();
            var dtl_trainee_name = $("#trainee_id option:selected").text();
            var organization_name = $("#organization").val();
            var capacity_chk = $("#training_capacity").val();

            if (dtl_trainee_id) {
                if ($.inArray(dtl_trainee_id, dataArray2) > -1) {
                    Swal.fire('Duplicate value not allowed.');
                } else {
                    if (capacity_chk > dataArray2.length) {
                        if(dtl_trainee_id == 'all'){
                            var training_id = $("#training_id option:selected").val();
                            if (training_id){
                                $.ajax({
                                    type: "GET",
                                    url: APP_URL + '/ajax/get-training-wise-emp-for-all/' + training_id,
                                    success: function (data) {
                                        $.each(data, function(key, value) {
                                            // alert(capacity_chk);
                                            if ($.inArray(value.trainee_id, dataArray2) > -1) {
                                                Swal.fire('Duplicate value not allowed.');
                                            } else if (capacity_chk <= dataArray2.length) {
                                                Swal.fire('Capacity Full.');
                                            }else{
                                                var trainee_name = value.trainee_name+'-'+value.organization_name+'-'+value.dept_name+'-'+value.trainee_code;
                                                var markup = "<tr><td class='text-center'>" +
                                                    "<input type='checkbox' name='record' value='" + value.trainee_id  + "+" + "" + "'>" +
                                                    "<input type='hidden' name='dtl_trainee_id[]' class='delete_trainee_id' value='" + value.trainee_id + "'>" +
                                                    "</td><td>" + trainee_name + "</td><td>" + value.organization_name + "</td></tr>";
                                                $("#trainee_id").val('').trigger('change');
                                                $("#organization").val("");
                                                $("#table-emp tbody").append(markup);
                                                dataArray2.push(value.trainee_id);
                                            }
                                        });
                                    },
                                    error: function (err) {
                                        alert('error');
                                    }
                                });
                            }
                            else{
                                Swal.fire('Training no. not given.');
                            }
                        } else{
                            var markup = "<tr><td class='text-center'>" +
                                "<input type='checkbox' name='record' value='" + dtl_trainee_id  + "+" + "" + "'>" +
                                "<input type='hidden' name='dtl_trainee_id[]' class='delete_trainee_id' value='" + dtl_trainee_id + "'>" +
                                "</td><td>" + dtl_trainee_name + "</td><td>" + organization_name + "</td></tr>";
                            $("#trainee_id").val('').trigger('change');
                            $("#organization").val("");
                            $("#table-emp tbody").append(markup);
                            dataArray2.push(dtl_trainee_id);
                        }
                    } else {
                        Swal.fire('Capacity Full.');
                    }
                }
            } else {
                Swal.fire('Fill required value.');
            }

        });

        $(".delete-row-trainee").click(function () {
            let arr_stuff = [];
            let trainee_id = [];
            let assignment_id = [];
            $(':checkbox:checked').each(function(i){
                arr_stuff[i] = $(this).val();
                let sd = arr_stuff[i].split('+');
                trainee_id.push(sd[0]);
                if(sd[1]){
                    assignment_id.push(sd[1]);
                }
            });
            if(assignment_id.length != 0){
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: 'GET',
                            url: '/trainee-data-remove',
                            data: {assignment_id: assignment_id},
                            success: function (msg) {
                                if (msg == 0) {
                                    Swal.fire({
                                        title: 'Can not remove data. Attendance process ongoing for this schedule.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                    //return false;
                                }else{
                                    Swal.fire({
                                        title: 'Entry Successfully Deleted!',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(function () {
                                        for( var i =dataArray2.length - 1; i>=0; i--){
                                            for( var j=0; j<trainee_id.length; j++){
                                                if(dataArray2[i] === trainee_id[j]){
                                                    dataArray2.splice(i, 1);
                                                }
                                            }
                                        }
                                        $('td input:checked').closest('tr').remove();
                                    });
                                }
                            }
                        });
                    }
                });
            }else{
                for( var i =dataArray2.length - 1; i>=0; i--){
                    for( var j=0; j<trainee_id.length; j++){
                        if(dataArray2[i] === trainee_id[j]){
                            dataArray2.splice(i, 1);
                        }
                    }
                }
                Swal.fire({
                    title: 'Entry Successfully Deleted!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function () {
                    $('td input:checked').closest('tr').remove();
                });
            }
        });

        $(".add-row-exam-type").click(function () {

            var exam_type_id = $("#exam_type option:selected").val();
            var exam_type_name = $("#exam_type option:selected").text();
            var total_marks = $("#total_marks").val();
            var pass_marks = $("#pass_marks").val();
            var remarks_exam_type = $("#remarks_exam_type").val();

            if (exam_type_id != '' && total_marks != '' && pass_marks != '') {
                if ($.inArray(exam_type_id, dataArray3) > -1) {
                    Swal.fire('Duplicate value not allowed.');
                } else {
                    var markup = "<tr><td class='text-center'>" +
                        "<input type='checkbox' name='record' value='" + exam_type_id  + "+" + "" + "'>" +
                        "<input type='hidden' name='exam_type_id[]' class='delete_exam_type_id' value='" + exam_type_id + "'>" +
                        "<input type='hidden' name='exam_type_name[]' value='" + exam_type_name + "'>" +
                        "<input type='hidden' name='total_marks[]' value='" + total_marks + "'>" +
                        "<input type='hidden' name='pass_marks[]' value='" + pass_marks + "'>" +
                        "<input type='hidden' name='remarks_exam_type[]' value='" + remarks_exam_type + "'>" +
                        "</td><td>" + exam_type_name + "</td><td>" + total_marks + "</td><td>" + pass_marks + "</td><td>" + remarks_exam_type + "</td></tr>";
                    $("#exam_type").val('').trigger('change');
                    $("#total_marks").val("");
                    $("#pass_marks").val("");
                    $("#remarks_exam_type").val("");
                    $("#table-exam-type tbody").append(markup);

                    dataArray3.push(exam_type_id);
                }
            } else {
                Swal.fire('Fill required value.');
            }

        });

        $(".delete-row-exam-type").click(function () {
            let arr_stuff = [];
            let exam_type_id = [];
            let trainee_exam_sch_id = [];
            $(':checkbox:checked').each(function(i){
                arr_stuff[i] = $(this).val();
                let sd = arr_stuff[i].split('+');
                exam_type_id.push(sd[0]);
                if(sd[1]){
                    trainee_exam_sch_id.push(sd[1]);
                }
            });

            if(trainee_exam_sch_id.length != 0){
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: 'GET',
                            url: '/exam-type-data-remove',
                            data: {trainee_exam_sch_id: trainee_exam_sch_id},
                            success: function (msg) {
                                if (msg == 0) {
                                    Swal.fire({
                                        title: 'Can not remove data. Attedeance process ongoing for this schedule.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                    //return false;
                                }else{
                                    Swal.fire({
                                        title: 'Entry Successfully Deleted!',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(function () {
                                        for( var i =dataArray3.length - 1; i>=0; i--){
                                            for( var j=0; j<exam_type_id.length; j++){
                                                if(dataArray3[i] === exam_type_id[j]){
                                                    dataArray3.splice(i, 1);
                                                }
                                            }
                                        }
                                        $('td input:checked').closest('tr').remove();
                                    });
                                }
                            }
                        });
                    }
                });
            }else{
                for( var i =dataArray3.length - 1; i>=0; i--){
                    for( var j=0; j<exam_type_id.length; j++){
                        if(dataArray3[i] === exam_type_id[j]){
                            dataArray3.splice(i, 1);
                        }
                    }
                }
                Swal.fire({
                    title: 'Entry Successfully Deleted!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function () {
                    $('td input:checked').closest('tr').remove();
                });
            }
        });

        $(".add-row-supp-stuff").click(function () {

            var emp_id = $("#emp_id option:selected").val();
            var emp_name = $("#emp_id option:selected").text();

            if (emp_id) {
                if ($.inArray(emp_id, dataArray4) > -1) {
                    Swal.fire('Duplicate value not allowed.');
                } else {
                    let markup = "<tr role='row'>" +
                        "<td aria-colindex='1' role='cell' class='text-center'>" +
                        "<input type='checkbox' name='record' value='" + emp_id  + "+" + "" + "'>" +
                        "<input type='hidden' name='emp_id[]' value='" + emp_id + "'>" +
                        "</td><td aria-colindex='2' role='cell'>" + emp_name + "</td></tr>";
                    $("#emp_id").empty('');
                    $("#table-supp-stuff tbody").append(markup);
                    dataArray4.push(emp_id);
                }
            } else {
                Swal.fire('Fill required value.');
            }
        });

        $(".delete-row-supp-stuff").click(function () {
            let arr_stuff = [];
            let emp_id = [];
            let support_member_id = [];
            $(':checkbox:checked').each(function(i){
                arr_stuff[i] = $(this).val();
                let sd = arr_stuff[i].split('+');
                emp_id.push(sd[0]);
                if(sd[1]){
                    support_member_id.push(sd[1]);
                }
            });
            if(support_member_id.length != 0){
                Swal.fire({
                    title: 'Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: 'GET',
                            url: '/support-member-remove',
                            data: {support_member_id: support_member_id},
                            success: function (msg) {
                                if (msg == 0) {
                                    Swal.fire({
                                        title: 'Can not remove data. Attedeance process ongoing for this schedule.',
                                        icon: 'error',
                                        confirmButtonText: 'OK'
                                    });
                                    //return false;
                                }else{
                                    Swal.fire({
                                        title: 'Entry Successfully Deleted!',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(function () {
                                        for( var i =dataArray4.length - 1; i>=0; i--){
                                            for( var j=0; j<emp_id.length; j++){
                                                if(dataArray4[i] === emp_id[j]){
                                                    dataArray4.splice(i, 1);
                                                }
                                            }
                                        }
                                        $('td input:checked').closest('tr').remove();
                                    });
                                }
                            }
                        });
                    }
                });
            }else{
                for( var i =dataArray4.length - 1; i>=0; i--){
                    for( var j=0; j<emp_id.length; j++){
                        if(dataArray4[i] === emp_id[j]){
                            dataArray4.splice(i, 1);
                        }
                    }
                }
                Swal.fire({
                    title: 'Entry Successfully Deleted!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function () {
                    $('td input:checked').closest('tr').remove();
                });
            }
        });

        function trainingScheduleList() {
            $('#training-schedule-list').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: APP_URL + '/training-schedule-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'batch_id', name: 'batch_id', searchable: false},
                    {data: 'coordinator_name', name: 'coordinator_name', searchable: true},
                    {data: 'training_location', name: 'training_location', searchable: true},
                    {data: 'training_capacity', name: 'training_capacity', searchable: true},
                    {data: 'training_start_date', name: 'training_start_date', searchable: false},
                    {data: 'training_end_date', name: 'training_end_date', searchable: false},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        function populateRelatedFields(that, data) {
            $(that).parent().parent().parent().find('#coordinator_name').val(data.coordination_name);
            $(that).parent().parent().parent().find('#mobile_no').val(data.coordination_cell);
            $(that).parent().parent().parent().find('#email').val(data.coordination_email);
            $(that).parent().parent().parent().find('#from_date').val(convertDate(data.form_date));
            $(that).parent().parent().parent().find('#to_date').val(convertDate(data.date_to));
            $(that).parent().parent().parent().find('#training_fee').val(data.course_fee);
            $(that).parent().parent().parent().find('#total_cost').val(data.course_fee);

            var training_id = $("#training_id option:selected").val();
            if (training_id){
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/get-training-wise-emp/' + training_id,
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
        }

        $(function () {
            $("#training_fee, #disc_amt").on("keydown keyup", sum);

            function sum() {
                $("#total_cost").val(Number($("#training_fee").val()) - Number($("#disc_amt").val()));
                $("#update_tot_cost").val(Number($("#training_fee").val()) - Number($("#disc_amt").val()));
            }
        });

        $(document).ready(function () {
            // for trainee addition table START
            var trainee_count = $("#trainee_count").val();
            var all_trainee = $("#all_trainee").val();
            var arr_allTrainee = []
            try {
                arr_allTrainee = JSON.parse(all_trainee);
            } catch (e) {
                console.log("Invalid json")
            }
            if (trainee_count) {
                var i;
                for (i = 0; i < trainee_count; i++) {
                    dataArray2.push(arr_allTrainee[i]);
                }
            }
            // for trainee addition table END


            // for exam type table START
            var examtype_count = $("#examtype_count").val();
            var all_examtype = $("#all_examtype").val();
            var arr_allexamtype = []
            try {
                arr_allexamtype = JSON.parse(all_examtype);
            } catch (e) {
                console.log("Invalid json")
            }
            if (examtype_count) {
                var i;
                for (i = 0; i < examtype_count; i++) {
                    dataArray3.push(arr_allexamtype[i]);
                }
            }
            // for exam type table END


            // for supporting stuff table START
            var supprotingStuff_count = $("#supprotingStuff_count").val();
            var allSupprotingStuff = $("#allSupprotingStuff").val();
            var arr_allSupprotingStuff = []
            try {
                arr_allSupprotingStuff = JSON.parse(allSupprotingStuff);
            } catch (e) {
                console.log("Invalid json")
            }
            if (supprotingStuff_count) {
                var i;
                for (i = 0; i < supprotingStuff_count; i++) {
                    dataArray4.push(arr_allSupprotingStuff[i]);
                }
            }
            // for supporting stuff table END

            selectTraining('#training_id', APP_URL + '/ajax/training-no', APP_URL + '/ajax/training-details/', populateRelatedFields);
            //dateRangePicker('#from_date', '#to_date');
            timeRangePicker('#time_from', '#time_to');
            timePicker('#dtl_time_from');
            timePicker('#dtl_time_to');
            //datePicker('#from_date');
            //datePicker('#to_date');
            datePicker('#training_date');
            datePicker('#training_date_pick');
            datePicker('#training_start_pick');
            datePicker('#training_end_pick');
            trainingScheduleList();
            disk_amt_yes_chk();
            disk_amt_no_chk();
            selectCpaEmployees('#emp_id', APP_URL + '/ajax/employees', APP_URL + '/ajax/employee/', populateRelatedFieldsEmp);
            selectCpaEmployees('#course_director_id', APP_URL + '/ajax/employees', APP_URL + '/ajax/employee/', populateRelatedFieldsEmp);
        });

        function populateRelatedFieldsEmp(that, data) {
        }

        function call_date_picker(e) {
            datePicker(e);
        }

        function call_time_picker(e) {
            timePicker(e);
        }


        $("#trainee_id").change(function () {

            let emp_id = $("#trainee_id option:selected").val();
            if (emp_id !== null) {
                $.ajax({
                    type: 'GET',
                    url: '/get-trainee-data',
                    data: {emp_id: emp_id},
                    success: function (msg) {
                        $("#organization").val(msg[0].organization_name);

                    }
                });
            }
        });

    </script>

@endsection
