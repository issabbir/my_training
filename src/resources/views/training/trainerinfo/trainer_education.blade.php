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
                    <h4 class="card-title">Trainer Information</h4><!---->
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
                <!-- vertical Wizard start-->
                    <section id="vertical-wizard">
                        <form enctype="multipart/form-data"
                              @if (isset($educationView->education_id)) action="{{route('trainer-information.trainer-education-update',[$trainer_id,$educationView->education_id])}}"
                              @else action="{{route('trainer-information.trainer-education-post',$trainer_id)}}"
                              @endif method="post">
                            @csrf
                            @if (isset($educationView->education_id))
                                @method('PUT')
                            @endif
                            <div class="card">
                                <div class="card-content">
                                    <div class="row pills-stacked">
                                        <div class="col-md-3 col-sm-12 border-right pr-md-0">
                                            @include('tab')
                                        </div>
                                        <div class="col-md-9 col-sm-12">
                                            <div class="tab-content bg-transparent shadow-none">
                                                <div role="tabpanel" class="tab-pane active" id="vertical-pill-1"
                                                     aria-labelledby="stacked-pill-1"
                                                     aria-expanded="true">
                                                    <fieldset class="pt-0">
                                                        <h6 class="pb-50"><b>Education Details</b></h6>

                                                        @if (isset ($trainerDetails->internal_yn))
                                                            @if($trainerDetails->internal_yn == 'Y')
                                                                <div class="table-responsive">
                                                                    <table width="100%"
                                                                           class="table nowrap scroll-horizontal-vertical">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>SL</th>
                                                                            <th>Exam Name</th>
                                                                            <th>Exam Body</th>
                                                                            <th>Exam Result</th>
                                                                            <th>Passing Year</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @if (isset($empEducationList))
                                                                            @php $i=1 @endphp
                                                                            @foreach($empEducationList as $value)
                                                                                <tr>
                                                                                    <td>{{$i++}}</td>
                                                                                    <td>{{$value->exam_name}}</td>
                                                                                    <td>{{$value->exam_body_name}}</td>
                                                                                    <td>{{$value->exam_result}}</td>
                                                                                    <td>{{$value->passing_year}}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="trainer_exam" class="required">Exam </label>
                                                                            <select
                                                                                class="custom-select form-control select2"
                                                                                required
                                                                                id="trainer_exam" name="trainer_exam">
                                                                                <option value="">Select One</option>
                                                                                @foreach($exams as $exam)
                                                                                    <option value="{{$exam->exam_id}}"
                                                                                        {{isset($educationView->exam_id) && $educationView->exam_id == $exam->exam_id ? 'selected' : ''}}
                                                                                    >{{$exam->exam_name}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="trainer_exam_body"
                                                                                   class="required">Exam Body</label>
                                                                            <select
                                                                                class="custom-select form-control select2"
                                                                                required
                                                                                id="trainer_exam_body"
                                                                                name="trainer_exam_body">
                                                                                <option value="">Select One</option>
                                                                                @foreach($examBody as $value)
                                                                                    <option
                                                                                        value="{{$value->exam_body_id}}"
                                                                                        {{isset($educationView->exam_body_id) && $educationView->exam_body_id == $value->exam_body_id ? 'selected' : ''}}
                                                                                    >{{$value->exam_body_name}}</option>
                                                                                @endforeach
                                                                            </select>
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="subject" class="required">Subject </label>
                                                                            <input class="form-control" name="subject"
                                                                                   id="subject" required
                                                                                   value="{{old('',isset($educationView->subject) ? $educationView->subject : '')}}"
                                                                                   type="text">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="subject_bn" class="">Subject
                                                                                Bangla</label>
                                                                            <input class="form-control"
                                                                                   name="subject_bn" id="subject_bn"
                                                                                   value="{{old('',isset($educationView->subject_bn) ? $educationView->subject_bn : '')}}"
                                                                                   type="text">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="pass_year" class="required">Pass
                                                                                Year</label>
                                                                            <input
                                                                                class="form-control global-number-validation"
                                                                                name="pass_year" id="pass_year" required
                                                                                type="number"
                                                                                oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                                maxlength="4"
                                                                                value="{{old('',isset($educationView->pass_year) ? $educationView->pass_year : '')}}"
                                                                                type="text">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="exam_result" class="required">Exam
                                                                                Result</label>
                                                                            <input class="form-control"
                                                                                   name="exam_result" id="exam_result"
                                                                                   required
                                                                                   value="{{old('',isset($educationView->exam_result) ? $educationView->exam_result : '')}}"
                                                                                   type="text">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="certificate"

                                                                            >Upload Certificate</label>
                                                                            <input type="file" class="form-control"
                                                                                   id="certificate" name="certificate"

                                                                            />
                                                                        </div>
                                                                        @if(isset($educationView->certifficte_photo))
                                                                            <a href="{{ route('trainer-information.trainer-education-cert-download', [$educationView->education_id]) }}"
                                                                               target="_blank">{{$educationView->certificate_name}}</a>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="transcript"

                                                                            >Upload Transcript</label>
                                                                            <input type="file" class="form-control"
                                                                                   id="transcript" name="transcript"

                                                                            />
                                                                        </div>
                                                                        @if(isset($educationView->transcript_photo))
                                                                            <a href="{{ route('trainer-information.trainer-education-trans-download', [$educationView->education_id]) }}"
                                                                               target="_blank">{{$educationView->transcript_name}}</a>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="form-group">
                                                                            <label for="other_exam" class="required">Other
                                                                                Exam</label>
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-inline-block mr-2 mb-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="radio"
                                                                                                   class="custom-control-input"
                                                                                                   name="other_exam_yn"
                                                                                                   id="other_exam_y"
                                                                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                                                                {{isset($educationView->other_exam_body_yn) && ($educationView->other_exam_body_yn == \App\Enums\YesNoFlag::YES) ? 'checked' : ''}}
                                                                                            >
                                                                                            <label
                                                                                                class="custom-control-label"
                                                                                                for="other_exam_y">Yes</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-2 mb-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="radio"
                                                                                                   class="custom-control-input"
                                                                                                   name="other_exam_yn"
                                                                                                   id="other_exam_n"
                                                                                                   value="{{\App\Enums\YesNoFlag::NO}}"
                                                                                                {{!isset($educationView->other_exam_body_yn) ? 'checked' : ''}}
                                                                                                {{isset($educationView->other_exam_body_yn) && ($educationView->other_exam_body_yn == \App\Enums\YesNoFlag::NO) ? 'checked' : ''}}
                                                                                            >
                                                                                            <label
                                                                                                class="custom-control-label"
                                                                                                for="other_exam_n">No</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="form-group">
                                                                            <label for="foreign_exam" class="required">Foreign
                                                                                Exam</label>
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-inline-block mr-2 mb-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="radio"
                                                                                                   class="custom-control-input"
                                                                                                   name="foreign_exam_yn"
                                                                                                   id="foreign_exam_y"
                                                                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                                                                {{isset($educationView->exam_body_foreign_yn) && ($educationView->exam_body_foreign_yn == \App\Enums\YesNoFlag::YES) ? 'checked' : ''}}
                                                                                            >
                                                                                            <label
                                                                                                class="custom-control-label"
                                                                                                for="foreign_exam_y">Yes</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-2 mb-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="radio"
                                                                                                   class="custom-control-input"
                                                                                                   name="foreign_exam_yn"
                                                                                                   id="foreign_exam_n"
                                                                                                   value="{{\App\Enums\YesNoFlag::NO}}"
                                                                                                {{!isset($educationView->exam_body_foreign_yn) ? 'checked' : ''}}
                                                                                                {{isset($educationView->exam_body_foreign_yn) && ($educationView->exam_body_foreign_yn == \App\Enums\YesNoFlag::NO) ? 'checked' : ''}}
                                                                                            >
                                                                                            <label
                                                                                                class="custom-control-label"
                                                                                                for="foreign_exam_n">No</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>

                                                                    <input type="hidden" name="trainer_id"
                                                                           value="{{$trainer_id}}">

                                                                    <div class="col-12 d-flex justify-content-end">
                                                                        <button type="submit"
                                                                                class="btn btn-primary mr-1 mb-1 mt-1">
                                                                            Save
                                                                        </button>
                                                                        <button type="reset"
                                                                                class="btn btn-light-secondary mb-1 mt-1">
                                                                            Reset
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="trainer_exam"
                                                                               class="required">Exam </label>
                                                                        <select
                                                                            class="custom-select form-control select2"
                                                                            required
                                                                            id="trainer_exam" name="trainer_exam">
                                                                            <option value="">Select One</option>
                                                                            @foreach($exams as $exam)
                                                                                <option value="{{$exam->exam_id}}"

                                                                                >{{$exam->exam_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="trainer_exam_body" class="required">Exam
                                                                            Body </label>
                                                                        <select
                                                                            class="custom-select form-control select2"
                                                                            required
                                                                            id="trainer_exam_body"
                                                                            name="trainer_exam_body">
                                                                            <option value="">Select One</option>
                                                                            @foreach($examBody as $value)
                                                                                <option value="{{$value->exam_body_id}}"

                                                                                >{{$value->exam_body_name}}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="subject"
                                                                               class="required">Subject </label>
                                                                        <input class="form-control" name="subject"
                                                                               id="subject" required
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="subject_bn" class="">Subject
                                                                            Bangla</label>
                                                                        <input class="form-control" name="subject_bn"
                                                                               id="subject_bn"
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="pass_year" class="required">Pass
                                                                            Year</label>
                                                                        <input
                                                                            class="form-control global-number-validation"
                                                                            name="pass_year" id="pass_year" required
                                                                            type="number"
                                                                            oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                            maxlength="4"
                                                                            type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="exam_result" class="required">Exam
                                                                            Result</label>
                                                                        <input class="form-control" name="exam_result"
                                                                               id="exam_result" required
                                                                               {{--value="{{old('',isset($educationView->exam_result) ? $educationView->exam_result : '')}}"--}} type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="certificate" class="">Upload
                                                                            Certificate</label>
                                                                        <input type="file" class="form-control"
                                                                               id="certificate" name="certificate"/>
                                                                    </div>

                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="transcript" class="">Upload
                                                                            Transcript</label>
                                                                        <input type="file" class="form-control"
                                                                               id="transcript" name="transcript"/>
                                                                    </div>

                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="other_exam" class="required">Other
                                                                            Exam</label>
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="d-inline-block mr-2 mb-1">
                                                                                <fieldset>
                                                                                    <div
                                                                                        class="custom-control custom-radio">
                                                                                        <input type="radio"
                                                                                               class="custom-control-input"
                                                                                               name="other_exam_yn"
                                                                                               id="other_exam_y"
                                                                                               value="{{ \App\Enums\YesNoFlag::YES }}"

                                                                                        >
                                                                                        <label
                                                                                            class="custom-control-label"
                                                                                            for="other_exam_y">Yes</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-2 mb-1">
                                                                                <fieldset>
                                                                                    <div
                                                                                        class="custom-control custom-radio">
                                                                                        <input type="radio"
                                                                                               class="custom-control-input"
                                                                                               name="other_exam_yn"
                                                                                               id="other_exam_n"
                                                                                               value="{{\App\Enums\YesNoFlag::NO}}"
                                                                                            {{!isset($educationView->other_exam_body_yn) ? 'checked' : ''}}

                                                                                        >
                                                                                        <label
                                                                                            class="custom-control-label"
                                                                                            for="other_exam_n">No</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="foreign_exam" class="required">Foreign
                                                                            Exam</label>
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="d-inline-block mr-2 mb-1">
                                                                                <fieldset>
                                                                                    <div
                                                                                        class="custom-control custom-radio">
                                                                                        <input type="radio"
                                                                                               class="custom-control-input"
                                                                                               name="foreign_exam_yn"
                                                                                               id="foreign_exam_y"
                                                                                               value="{{ \App\Enums\YesNoFlag::YES }}"

                                                                                        >
                                                                                        <label
                                                                                            class="custom-control-label"
                                                                                            for="foreign_exam_y">Yes</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-2 mb-1">
                                                                                <fieldset>
                                                                                    <div
                                                                                        class="custom-control custom-radio">
                                                                                        <input type="radio"
                                                                                               class="custom-control-input"
                                                                                               name="foreign_exam_yn"
                                                                                               id="foreign_exam_n"
                                                                                               value="{{\App\Enums\YesNoFlag::NO}}"
                                                                                            {{!isset($educationView->exam_body_foreign_yn) ? 'checked' : ''}}

                                                                                        >
                                                                                        <label
                                                                                            class="custom-control-label"
                                                                                            for="foreign_exam_n">No</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                </div>

                                                                <input type="hidden" name="trainer_id"
                                                                       value="{{$trainer_id}}">

                                                                <div class="col-12 d-flex justify-content-end">
                                                                    <button type="submit"
                                                                            class="btn btn-primary mr-1 mb-1 mt-1">Save
                                                                    </button>
                                                                    <button type="reset"
                                                                            class="btn btn-light-secondary mb-1 mt-1">
                                                                        Reset
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif

                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                    <!-- vertical Wizard end-->

                </div>
                <!-- Table End -->
            </div>
            @include('training.trainerinfo.trainereducation_list')
        </div>
    </div>


@endsection

@section('footer-script')
    <script type="text/javascript">

    </script>

@endsection
