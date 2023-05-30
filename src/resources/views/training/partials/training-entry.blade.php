@if(Session::has('message'))
    <div class="alert {{Session::get('m-class') ? Session::get('m-class') : 'alert-danger'}} show"
         role="alert">
        {{ Session::get('message') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
@endif


{{--@dd($trainingInfo)--}}




<form enctype="multipart/form-data"
      @if(isset($trainingInfo->training_id)) action="{{route('training-information.training-information-update',[$trainingInfo->training_id])}}"
      @else action="{{route('training-information.training-information-post')}}"
      @endif method="post">
    @csrf
    @if (isset($trainingInfo->training_id))
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-sm-3">
            <div class="form-group">
                <label for="training_type" class="required">Course Title</label>
                <select class="custom-select form-control select2" required id="training_type"
                        name="training_type">
                    <option value="">Select One</option>
                    @foreach($trainingType as $value)
                        <option value="{{$value->training_type_id}}"
                            {{isset($trainingInfo->training_type_id) && $trainingInfo->training_type_id == $value->training_type_id ? 'selected' : ''}}
                        >{{$value->training_type_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @if (isset($trainingInfo->training_number))
            <div class="col-sm-3">
                <div class="form-group">
                    <label for="training_no" class="">Training No</label>
                    <input disabled
                           type="text"
                           class="form-control"
                           id="training_no"
                           name="training_no"
                           value="{{old('training_no', isset($trainingInfo->training_number) ? $trainingInfo->training_number :'')}}"
                    >
                </div>
            </div>
        @endif
        <div class="col-sm-3">
            <div class="form-group">
                <label for="training_title" class="required">Training Title</label>
                <input
                    type="text"
                    class="form-control"
                    id="training_title"
                    name="training_title"
                    value="{{old('training_no', isset($trainingInfo->training_title) ? $trainingInfo->training_title :'')}}"
                    required
                >
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="training_title_bn" class="">Training Title Bangla</label>
                <input
                    type="text"
                    class="form-control"
                    id="training_title_bn"
                    name="training_title_bn"
                    value="{{old('training_title_bn', isset($trainingInfo->training_title_bn) ? $trainingInfo->training_title_bn :'')}}"

                >
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="trainee_type" class="required">For Whom</label>
                <select class="custom-select form-control select2" multiple="multiple" required id="trainee_type"
                        name="trainee_type[]">
                    <option value="">Select One</option>
                    @foreach($traineeType as $value)
                        <option value="{{$value->trainee_type_id}}"
                        @if(!empty($whom_ids))
                            @foreach($whom_ids as $id)
                                @if(isset($trainingInfo->trainee_type_id) && $value->trainee_type_id == $id) {{'selected="selected"'}} @endif
                            @endforeach
                        @endif >
                        {{$value->trainee_type}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="training_media_type" class="required">Training Media Type</label>
                <select class="custom-select form-control select2" required id="training_media_type"
                        name="training_media_type">
                    <option value="">Select One</option>
                    @foreach($traineeMedia as $value)
                        <option value="{{$value->training_media_id}}"
                            {{isset($trainingInfo->training_media_type_id) && $trainingInfo->training_media_type_id == $value->training_media_id ? 'selected' : ''}}
                        >{{$value->media_name}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="from_date" class="required">From Date</label>
                <input type="text"
                       autocomplete="off"
                       class="form-control datetimepicker-input"
                       data-toggle="datetimepicker"
                       id="modal_train_from_date"
                       data-target="#modal_train_from_date"
                       name="from_date"
                       value=""
                       placeholder="YYYY-MM-DD"
                       required
                       data-predefined-date="{{old('from_date',isset($trainingInfo->form_date) ? $trainingInfo->form_date :'')}}"
                >
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="to_date" class="required">To Date</label>
                <input type="text"
                       autocomplete="off"
                       class="form-control datetimepicker-input"
                       data-toggle="datetimepicker"
                       id="modal_train_to_date"
                       data-target="#modal_train_to_date"
                       name="to_date"
                       value=""
                       placeholder="YYYY-MM-DD"
                       required
                       data-predefined-date="{{old('to_date',isset($trainingInfo->date_to) ? $trainingInfo->date_to :'')}}"
                >
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label class="required d-block" for="emp_id">Coordinator Emp Code</label>
                <select class="w-100 custom-select form-control select2 " required name="emp_id" id="emp_id"
                        data-emp-id="{{old('emp_id', isset($trainingInfo->coordination_emp_id) ? $trainingInfo->coordination_emp_id :'')}}">
                </select>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label>Coordinator Name</label>
                <input type="text" id="emp_name" name="emp_name"
                       class="form-control" placeholder="Name"
                       value=""
                       autocomplete="off"
                       readonly
                >
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="emp_mbl" class="">Coordinator Mobile</label>
                <input type="text" id="emp_mbl" name="emp_mbl"
                       class="form-control"
                       autocomplete="off"
                       readonly
                >
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="emp_email" class="">Coordinator Email</label>
                <input type="email" id="emp_email" name="emp_email"
                       class="form-control"
                       autocomplete="off"
                       readonly
                >
            </div>
        </div>
        <div class="col-sm-3">
            <div class="form-group">
                <label for="no_of_course" class="required">No Of Course</label>
                <input
                    type="number"
                    class="form-control global-number-validation"
                    id="no_of_course"
                    name="no_of_course"
                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                    maxlength="3"
                    value="{{old('no_of_course', isset($trainingInfo->number_of_course) ? $trainingInfo->number_of_course :'')}}"
                    required
                >
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                <label for="duration" class="required">Duration (day)</label>
                <input
                    type="number"
                    autocomplete="off"
                    class="form-control global-number-validation"
                    id="duration"
                    name="duration"
                    maxlength="3"
                    value="{{old('duration', isset($trainingInfo->duration) ? $trainingInfo->duration :'')}}"
                    required
                >
            </div>
        </div>

        @if (isset($trainingInfo->course_fee))
            <div class="col-sm-3" id="course_fee_yn">
                <div class="form-group">
                    <label for="course_fee" class="">Course Fee</label>
                    <input
                        type="number"
                        class="form-control global-number-validation"
                        id="course_fee"
                        name="course_fee"
                        autocomplete="off"
                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                        maxlength="5"
                        value="{{old('course_fee', isset($trainingInfo->course_fee) ? $trainingInfo->course_fee :'')}}"
                    >
                </div>
            </div>
        @else
        <div class="col-sm-3" id="course_fee_yn" style="display:none">
            <div class="form-group">
                <label for="course_fee" class="">Course Fee</label>
                <input
                    type="number"
                    class="form-control global-number-validation"
                    id="course_fee"
                    name="course_fee"
                    autocomplete="off"
                    oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                    maxlength="5"
                    value=""
                >
            </div>
        </div>
        @endif

        <div class="col-sm-3">
            <div class="form-group">
                <label for="attachment" class="">Attachment</label>
                <input type="file" class="form-control" id="attachment" name="attachment"/>
            </div>
            @if(isset($trainingInfo->attachment))
                <a href="{{ route('training-information.training-info-file-download', [$trainingInfo->training_id]) }}"
                   target="_blank">{{$trainingInfo->attachment_name}}</a>
            @endif
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                <label for="file_no" class="required">File No</label>
                <input
                    type="text"
                    class="form-control"
                    id="file_no"
                    name="file_no"
                    value="{{old('file_no', isset($trainingInfo->file_no) ? $trainingInfo->file_no :'')}}"
                    required
                >
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                <label for="accepted_participant">Number Of Participant</label>
                <input type="number"
                       class="form-control global-number-validation"
                       id="accepted_participant"
                       name="accepted_participant"
                       oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                       maxlength="3"
                       placeholder="Accepted Participant"
                       value="{{old('accepted_participant', isset($trainingInfo->accepted_participants) ? $trainingInfo->accepted_participants :'')}}"
                >
            </div>
        </div>

    </div>


    <div class="row">


        <div class="col-sm-3">
            <div class="form-group">
                <label for="active" class="required">Active</label>
                <ul class="list-unstyled mb-0">
                    <li class="d-inline-block mr-2 mb-1">
                        <fieldset>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="active_yn"
                                       id="active_y"
                                       value="{{ \App\Enums\YesNoFlag::YES }}"
                                    {{!isset($trainingInfo->active_yn) ? 'checked' : ''}}
                                    {{isset($trainingInfo->active_yn) && ($trainingInfo->active_yn == \App\Enums\YesNoFlag::YES) ? 'checked' : ''}}
                                >
                                <label class="custom-control-label" for="active_y">Yes</label>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2 mb-1">
                        <fieldset>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="active_yn"
                                       id="active_n"
                                       value="{{\App\Enums\YesNoFlag::NO}}"
                                    {{isset($trainingInfo->active_yn) && ($trainingInfo->active_yn == \App\Enums\YesNoFlag::NO) ? 'checked' : ''}}
                                >
                                <label class="custom-control-label" for="active_n">No</label>
                            </div>
                        </fieldset>
                    </li>
                </ul>
            </div>
        </div>


        <div class="col-sm-3">
            <div class="form-group">
                <label for="external" class="required">External</label>
                <ul class="list-unstyled mb-0" id="ex_val">
                    <li class="d-inline-block mr-2 mb-1">
                        <fieldset>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="external_yn"
                                       id="external_y"
                                       value="Y"
                                    {{isset($trainingInfo->external_yn) && ($trainingInfo->external_yn == 'Y') ? 'checked' : ''}}
                                >
                                <label class="custom-control-label" for="external_y">Yes</label>
                            </div>
                        </fieldset>
                    </li>
                    <li class="d-inline-block mr-2 mb-1">
                        <fieldset>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input" name="external_yn"
                                       id="external_n"
                                       @if(!isset($trainingInfo->external_yn)) {{ 'checked' }} @endif
                                       value="N"
                                    {{isset($trainingInfo->external_yn) && ($trainingInfo->external_yn == 'N') ? 'checked' : ''}}
                                >
                                <label class="custom-control-label" for="external_n">No</label>
                            </div>
                        </fieldset>
                    </li>
                </ul>
            </div>
        </div>


    </div>



<div class="row">

        <div class="col-md-12">
            <div class="form-group">
                <label class="">Prerequisite</label>
                <select class="custom-select select2" multiple="multiple" name="chk_court[]"
                        id="chk_court[]">
                    @foreach($lPreRequsit as $value)
                        <option value="{{$value->requsit_id}}"
                        @if(!empty($trainingprereq))
                            @foreach($trainingprereq as $prereq)
                                @if($prereq->requsit_id == $value->requsit_id && $prereq->active_yn == 'Y') {{'selected="selected"'}}
                                    @endif
                                @endforeach
                            @endif >
                            {{$value->pre_requsit_name}}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6 col-sm-12 mb-2">
            <label class="required" for="incidence_description">Course Content</label>
            <div id="course_content_editor"
                 class="text-editor">{!! (isset($trainingInfo->course_content) ? $trainingInfo->course_content : '')!!}</div>
            <textarea required rows="1" wrap="soft" name="course_content"
                      class="form-control customize-text-editor"
                      id="course_content">{{(isset($trainingInfo->course_content) ? $trainingInfo->course_content : '')}}</textarea>
        </div>
        <div class="col-md-6 col-sm-12 mb-2">
            <label class="required" for="incidence_description_bn">Objectives</label>
            <div id="traininginfo_objectives_editor"
                 class="text-editor">{!! (isset($trainingInfo->objectives) ? $trainingInfo->objectives : '')!!}</div>
            <textarea required rows="1" wrap="soft" name="objectives"
                      class="form-control customize-text-editor"
                      id="traininginfo_objectives">{{(isset($trainingInfo->objectives) ? $trainingInfo->objectives : '')}}</textarea>
        </div>
    </div>
    <div class="row mt-5">
        <div class="col-md-6 col-sm-12 mb-2">
            <label class="required" for="objectives">Training Facilities</label>
            <div id="training_facalities_editor"
                 class="text-editor">{!! (isset($trainingInfo->training_facalities) ? $trainingInfo->training_facalities : '')!!}</div>
            <textarea required rows="1" wrap="soft" name="training_facilities"
                      class="form-control customize-text-editor"
                      id="training_facalities">{{(isset($trainingInfo->training_facalities) ? $trainingInfo->training_facalities : '')}}</textarea>
        </div>
        <div class="col-md-6 mb-2">
            <div class="form-group">
                <label for="remarks" class="">Remarks</label>
                <textarea rows="3" wrap="soft"
                          name="remarks"
                          class="form-control"
                          id="remarks">{{old('remarks', isset($trainingInfo->remarks) ? $trainingInfo->remarks :'')}}</textarea>
            </div>
        </div>
    </div>
    @if($trainingInfo)
        <div class="row mt-5">
            <div class="col-md-12 text-right" id="cancel">
                <button type="submit" id="update"
                        class="btn btn-primary mb-1">
                    Update
                </button>
                <a href="{{url('/training-information')}}">
                    <button type="button" id="cancel"
                            class="btn btn-primary mb-1">
                        Cancel
                    </button>
                </a>
            </div>
        </div>
    @else
        <div class="row mt-5">
            <div class="col-md-12 mt-2 text-right" id="add">
                <button type="submit" id="add"
                        class="btn btn-primary mb-1">Save
                </button>
                <button type="reset" id="reset"
                        class="btn btn-primary mb-1">Reset
                </button>

            </div>
        </div>
    @endif
</form>





