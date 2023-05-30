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
                              @if (isset($experienceView->exp_id)) action="{{route('trainer-information.trainer-experience-update',[$trainer_id,$experienceView->exp_id])}}"
                              @else action="{{route('trainer-information.trainer-experience-post',$trainer_id)}}"
                              @endif method="post">
                            @csrf
                            @if (isset($experienceView->exp_id))
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
                                                        <h6 class="pb-50"><b>Experience Details</b></h6>

                                                        @if (isset ($trainerDetails->internal_yn))
                                                            @if($trainerDetails->internal_yn == 'Y')
                                                                <div class="table-responsive">
                                                                    <table width="100%"
                                                                           class="table scroll-horizontal-vertical">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>SL</th>
                                                                            <th>Employer Name</th>
                                                                            <th>Designation</th>
                                                                            <th>Employer Address</th>
                                                                            <th>Responsibility</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @if (isset($empExperienceList))
                                                                            @php $i=1 @endphp
                                                                            @foreach($empExperienceList as $value)
                                                                                <tr>
                                                                                    <td>{{$i++}}</td>
                                                                                    <td>{{ strtolower($value->employer_name)}}</td>
                                                                                    <td>{{strtolower($value->designation)}}</td>
                                                                                    <td>{{strtolower($value->employer_address)}}</td>
                                                                                    <td>{{strtolower ($value->responsibility)}}</td>
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
                                                                            <label for="organization_name"
                                                                                   class="required">Organization
                                                                                Name </label>
                                                                            <input class="form-control"
                                                                                   name="organization_name"
                                                                                   id="organization_name" required
                                                                                   value="{{old('',isset($experienceView->organization_name) ? $experienceView->organization_name : '')}}"
                                                                                   type="text">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="organization_name_bn" class="">Organization
                                                                                Name Bangla</label>
                                                                            <input class="form-control"
                                                                                   name="organization_name_bn"
                                                                                   id="organization_name_bn"
                                                                                   value="{{old('',isset($experienceView->organization_bn) ? $experienceView->organization_bn : '')}}"
                                                                                   type="text">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="designation" class="required">Designation</label>
                                                                            <input class="form-control"
                                                                                   name="designation" id="designation"
                                                                                   required
                                                                                   value="{{old('',isset($experienceView->designation) ? $experienceView->designation : '')}}"
                                                                                   type="text">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="designation_bn" class="">Designation
                                                                                Bangla</label>
                                                                            <input class="form-control"
                                                                                   name="designation_bn"
                                                                                   id="designation_bn"
                                                                                   value="{{old('',isset($experienceView->designation_bn) ? $experienceView->designation_bn : '')}}"
                                                                                   type="text">
                                                                            <small
                                                                                class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="start_date" class="required">Start
                                                                                Date</label>
                                                                            <input type="text"
                                                                                   autocomplete="off"
                                                                                   class="form-control datetimepicker-input"
                                                                                   data-toggle="datetimepicker"
                                                                                   id="start_date"
                                                                                   data-target="#start_date"
                                                                                   name="start_date"
                                                                                   value=""
                                                                                   placeholder="YYYY-MM-DD"
                                                                                   required
                                                                                   data-predefined-date="{{old('from_date',isset($experienceView->start_date) ? $experienceView->start_date :'')}}"
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="end_date" class="required">To
                                                                                Date</label>
                                                                            <input type="text"
                                                                                   autocomplete="off"
                                                                                   class="form-control datetimepicker-input"
                                                                                   data-toggle="datetimepicker"
                                                                                   id="end_date"
                                                                                   data-target="#end_date"
                                                                                   name="end_date"
                                                                                   value=""
                                                                                   placeholder="YYYY-MM-DD"
                                                                                   required
                                                                                   data-predefined-date="{{old('from_date',isset($experienceView->end_date) ? $experienceView->end_date :'')}}"
                                                                            >
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="organization_address"
                                                                                   class="required">Organization
                                                                                Address</label>
                                                                            <textarea required
                                                                                      placeholder="Organization Address"
                                                                                      rows="3" wrap="soft"
                                                                                      name="organization_address"
                                                                                      class="form-control"
                                                                                      id="organization_address">{{old('organization_address', isset($experienceView->organization_address) ? $experienceView->organization_address :'')}}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="organization_address_bn"
                                                                                   class="">Organization Address
                                                                                Bangla</label>
                                                                            <textarea
                                                                                placeholder="Organization Address Bangla"
                                                                                rows="3" wrap="soft"
                                                                                name="organization_address_bn"
                                                                                class="form-control"
                                                                                id="organization_address_bn">{{old('organization_address_bn', isset($experienceView->organization_address_bn) ? $experienceView->organization_address_bn :'')}}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="experience_letter"

                                                                            >Upload Experience Letter</label>
                                                                            <input type="file" class="form-control"
                                                                                   id="experience_letter"
                                                                                   name="experience_letter"

                                                                            />
                                                                        </div>
                                                                        @if(isset($experienceView->exp_letter_photo))
                                                                            <a href="{{ route('trainer-information.trainer-experience-exp-download', [$experienceView->exp_id]) }}"
                                                                               target="_blank">{{$experienceView->exp_letter_photo_name}}</a>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="release_letter"

                                                                            >Upload Release Letter</label>
                                                                            <input type="file" class="form-control"
                                                                                   id="release_letter"
                                                                                   name="release_letter"

                                                                            />
                                                                        </div>
                                                                        @if(isset($experienceView->release_letter_photo))
                                                                            <a href="{{ route('trainer-information.trainer-experience-rel-download', [$experienceView->exp_id]) }}"
                                                                               target="_blank">{{$experienceView->release_letter_p_name}}</a>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="form-group">
                                                                            <label for="current_job" class="required">Current
                                                                                Job</label>
                                                                            <ul class="list-unstyled mb-0">
                                                                                <li class="d-inline-block mr-2 mb-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="radio"
                                                                                                   class="custom-control-input"
                                                                                                   name="current_job_yn"
                                                                                                   id="current_job_y"
                                                                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                                                                {{isset($experienceView->current_job_yn) && ($experienceView->current_job_yn == \App\Enums\YesNoFlag::YES) ? 'checked' : ''}}
                                                                                            >
                                                                                            <label
                                                                                                class="custom-control-label"
                                                                                                for="current_job_y">Yes</label>
                                                                                        </div>
                                                                                    </fieldset>
                                                                                </li>
                                                                                <li class="d-inline-block mr-2 mb-1">
                                                                                    <fieldset>
                                                                                        <div
                                                                                            class="custom-control custom-radio">
                                                                                            <input type="radio"
                                                                                                   class="custom-control-input"
                                                                                                   name="current_job_yn"
                                                                                                   id="current_job_n"
                                                                                                   value="{{\App\Enums\YesNoFlag::NO}}"
                                                                                                {{!isset($experienceView->current_job_yn) ? 'checked' : ''}}
                                                                                                {{isset($experienceView->current_job_yn) && ($experienceView->current_job_yn == \App\Enums\YesNoFlag::NO) ? 'checked' : ''}}
                                                                                            >
                                                                                            <label
                                                                                                class="custom-control-label"
                                                                                                for="current_job_n">No</label>
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
                                                                        <label for="organization_name" class="required">Organization
                                                                            Name </label>
                                                                        <input class="form-control"
                                                                               name="organization_name"
                                                                               id="organization_name" required
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="organization_name_bn" class="">Organization
                                                                            Name Bangla</label>
                                                                        <input class="form-control"
                                                                               name="organization_name_bn"
                                                                               id="organization_name_bn"
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="designation" class="required">Designation</label>
                                                                        <input class="form-control" name="designation"
                                                                               id="designation" required
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="designation_bn" class="">Designation
                                                                            Bangla</label>
                                                                        <input class="form-control"
                                                                               name="designation_bn" id="designation_bn"
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="start_date" class="required">Start
                                                                            Date</label>
                                                                        <input type="text"
                                                                               autocomplete="off"
                                                                               class="form-control datetimepicker-input"
                                                                               data-toggle="datetimepicker"
                                                                               id="start_date"
                                                                               data-target="#start_date"
                                                                               name="start_date"
                                                                               value=""
                                                                               placeholder="YYYY-MM-DD"
                                                                               required

                                                                        >
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="end_date" class="required">To
                                                                            Date</label>
                                                                        <input type="text"
                                                                               autocomplete="off"
                                                                               class="form-control datetimepicker-input"
                                                                               data-toggle="datetimepicker"
                                                                               id="end_date"
                                                                               data-target="#end_date"
                                                                               name="end_date"
                                                                               value=""
                                                                               placeholder="YYYY-MM-DD"
                                                                               required

                                                                        >
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="organization_address"
                                                                               class="required">Organization
                                                                            Address</label>
                                                                        <textarea required
                                                                                  placeholder="Organization Address"
                                                                                  rows="3" wrap="soft"
                                                                                  name="organization_address"
                                                                                  class="form-control"
                                                                                  id="organization_address"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="organization_address_bn" class="">Organization
                                                                            Address Bangla</label>
                                                                        <textarea
                                                                            placeholder="Organization Address Bangla"
                                                                            rows="3" wrap="soft"
                                                                            name="organization_address_bn"
                                                                            class="form-control"
                                                                            id="organization_address_bn"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="experience_letter" class="">Upload
                                                                            Experience Letter</label>
                                                                        <input type="file" class="form-control"
                                                                               id="experience_letter"
                                                                               name="experience_letter"/>
                                                                    </div>

                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="release_letter" class="">Upload
                                                                            Release Letter</label>
                                                                        <input type="file" class="form-control"
                                                                               id="release_letter"
                                                                               name="release_letter"/>
                                                                    </div>

                                                                </div>
                                                                <div class="col-sm-3">
                                                                    <div class="form-group">
                                                                        <label for="current_job" class="required">Current
                                                                            Job</label>
                                                                        <ul class="list-unstyled mb-0">
                                                                            <li class="d-inline-block mr-2 mb-1">
                                                                                <fieldset>
                                                                                    <div
                                                                                        class="custom-control custom-radio">
                                                                                        <input type="radio"
                                                                                               class="custom-control-input"
                                                                                               name="current_job_yn"
                                                                                               id="current_job_y"
                                                                                               value="{{ \App\Enums\YesNoFlag::YES }}"
                                                                                        >
                                                                                        <label
                                                                                            class="custom-control-label"
                                                                                            for="current_job_y">Yes</label>
                                                                                    </div>
                                                                                </fieldset>
                                                                            </li>
                                                                            <li class="d-inline-block mr-2 mb-1">
                                                                                <fieldset>
                                                                                    <div
                                                                                        class="custom-control custom-radio">
                                                                                        <input type="radio"
                                                                                               class="custom-control-input"
                                                                                               name="current_job_yn"
                                                                                               id="current_job_n"
                                                                                               value="{{\App\Enums\YesNoFlag::NO}}"
                                                                                            {{!isset($experienceView->current_job_yn) ? 'checked' : ''}}
                                                                                        >
                                                                                        <label
                                                                                            class="custom-control-label"
                                                                                            for="current_job_n">No</label>
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
            @include('training.trainerinfo.trainerexperience_list')
        </div>
    </div>


@endsection

@section('footer-script')
    <script type="text/javascript">

        $(document).ready(function () {
            dateRangePicker('#start_date', '#end_date')
        });

    </script>

@endsection
