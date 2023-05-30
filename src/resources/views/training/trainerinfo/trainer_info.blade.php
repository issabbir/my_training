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
                              @if (isset($trainerInfo->trainer_id))  action="{{route('trainer-information.trainer-info-update',[$trainerInfo->trainer_id])}}"
                              @else action="{{route('trainer-information.trainer-info-post')}}" @endif method="post">
                            @csrf
                            @if (isset($trainerInfo->trainer_id))
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
                                                        <h6 class="pb-50"><b>Enter Trainer Info Details</b></h6>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="trainer_no" class="required">Trainer
                                                                        No</label>
                                                                    <input type="text" class="form-control"
                                                                           id="trainer_no"
                                                                           required
                                                                           name="trainer_no"
                                                                           value="{{old('trainer_no',isset($trainerInfo->trainer_no) ? $trainerInfo->trainer_no : '')}}"
                                                                           placeholder="Enter Trainer No">
                                                                    <small class="text-muted form-text"> </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label class="mb-1">Trainer from</label>
                                                                    <div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                   name="active_yn1" id="yn_outsider"
                                                                                   value="{{\App\Enums\YesNoFlag::NO}}"
                                                                                   onclick="javascript:yesNoCheckOutSider();"
                                                                                   checked
                                                                                   @if(isset($trainerInfo->internal_yn) && $trainerInfo->internal_yn == \App\Enums\YesNoFlag::NO) checked @endif/>
                                                                            <label
                                                                                class="form-check-label">External</label>
                                                                        </div>
                                                                        <div class="form-check form-check-inline">
                                                                            <input class="form-check-input" type="radio"
                                                                                   name="active_yn1"
                                                                                   value="{{\App\Enums\YesNoFlag::YES}}"
                                                                                   onclick="javascript:yesNoCheckCpa();"
                                                                                   id="yn_cpa"
                                                                                   @if(isset($trainerInfo->internal_yn) && $trainerInfo->internal_yn == \App\Enums\YesNoFlag::YES) checked @endif/>
                                                                            <label class="form-check-label">CPA</label>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @if(isset($trainerInfo->internal_yn) && $trainerInfo->internal_yn == \App\Enums\YesNoFlag::NO)
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="required" for="emp_id">Emp
                                                                            Code</label>
                                                                        <select disabled
                                                                                class="custom-select form-control select2"
                                                                                name="emp_id" id="emp_id">
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="emp_name" class="required">Trainer
                                                                            Name</label>
                                                                        <input type="text" id="emp_name" name="emp_name"
                                                                               required
                                                                               class="form-control" placeholder="Name"
                                                                               value="{{old('emp_name',isset($trainerInfo->trainer_name) ? $trainerInfo->trainer_name : '')}}"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="emp_name_bng">Trainer Name
                                                                            Bangla</label>
                                                                        <input type="text" id="emp_name_bng"
                                                                               name="emp_name_bng"
                                                                               class="form-control" placeholder="Name"
                                                                               value="{{old('emp_name',isset($trainerInfo->trainer_name_bn) ? $trainerInfo->trainer_name_bn : '')}}"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="emp_designation">Designation</label>
                                                                        <input type="text" id="emp_designation"
                                                                               name="emp_designation"
                                                                               class="form-control" placeholder="Designation"
                                                                               value="{{old('emp_designation',isset($trainerInfo->trainer_designation) ? $trainerInfo->trainer_designation : '')}}"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="mobile_no" class="required">Mobile
                                                                            No</label>
                                                                        <input type="number" class="form-control"
                                                                               id="mobile_no"
                                                                               name="mobile_no"
                                                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                               maxlength="13"
                                                                               value="{{old('mobile_no',isset($trainerInfo->mobile_no) ? $trainerInfo->mobile_no : '')}}"
                                                                               placeholder="Enter Mobile No">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="email_address" class="required">Email
                                                                            Address</label>
                                                                        <input type="email" class="form-control"
                                                                               id="email_address"
                                                                               name="email_address"
                                                                               value="{{old('email_address',isset($trainerInfo->email_add) ? $trainerInfo->email_add : '')}}"
                                                                               placeholder="Enter Email Address">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="nid" class="required">NID</label>
                                                                        <input type="number"
                                                                               class="form-control global-number-validation"
                                                                               id="nid"
                                                                               name="nid"
                                                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                               maxlength="17"
                                                                               value="{{old('nid',isset($trainerInfo->nid) ? $trainerInfo->nid : '')}}"
                                                                               placeholder="NID">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="emergency_cell_No" class="">Emergency
                                                                            cell No</label>
                                                                        <input type="number" class="form-control"
                                                                               id="emergency_cell_No"
                                                                               name="emergency_cell_No"
                                                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                               maxlength="13"
                                                                               value="{{old('emergency_cell_No',isset($trainerInfo->emg_cell_number) ? $trainerInfo->emg_cell_number : '')}}"
                                                                               placeholder="Emergency cell No">
                                                                    </div>
                                                                </div>
                                                            @elseif(isset($trainerInfo->internal_yn) && $trainerInfo->internal_yn == \App\Enums\YesNoFlag::YES)
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="required" for="emp_id">Emp
                                                                            Code</label>
                                                                        <select
                                                                            class="custom-select form-control select2"
                                                                            name="emp_id" id="emp_id"
                                                                            data-emp-id="@if($trainerInfo && $trainerInfo->emp_id) {{$trainerInfo->emp_id}} @endif"
                                                                        >
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="emp_name">Trainer Name</label>
                                                                        <input readonly type="text" id="emp_name"
                                                                               name="emp_name" required
                                                                               class="form-control" placeholder="Name"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="emp_name_bng">Trainer Name
                                                                            Bangla</label>
                                                                        <input readonly type="text" id="emp_name_bng"
                                                                               name="emp_name_bng"
                                                                               class="form-control" placeholder="Name"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="emp_designation">Designation
                                                                            Bangla</label>
                                                                        <input readonly type="text" id="emp_designation"
                                                                               name="emp_designation"
                                                                               class="form-control" placeholder="Designation"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="mobile_no" class="">Mobile
                                                                            No</label>
                                                                        <input readonly type="number"
                                                                               class="form-control"
                                                                               id="mobile_no"
                                                                               name="mobile_no"

                                                                               placeholder="Enter Mobile No">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="email_address" class="">Email
                                                                            Address</label>
                                                                        <input readonly type="text" class="form-control"
                                                                               id="email_address"
                                                                               name="email_address"
                                                                               placeholder="Enter Email Address">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="nid" class="">NID</label>
                                                                        <input readonly type="number"
                                                                               class="form-control"
                                                                               id="nid"
                                                                               name="nid"
                                                                               placeholder="NID">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="emergency_cell_No" class="">Emergency
                                                                            cell No</label>
                                                                        <input readonly type="number"
                                                                               class="form-control"
                                                                               id="emergency_cell_No"
                                                                               name="emergency_cell_No"
                                                                               placeholder="Emergency cell No">
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label class="required" for="emp_id">Emp
                                                                            Code</label>
                                                                        <select disabled
                                                                                class="custom-select form-control select2"
                                                                                name="emp_id" id="emp_id">
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="emp_name" class="required">Trainer
                                                                            Name</label>
                                                                        <input type="text" id="emp_name" name="emp_name"
                                                                               required
                                                                               class="form-control" placeholder="Name"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="emp_name_bng">Trainer Name
                                                                            Bangla</label>
                                                                        <input type="text" id="emp_name_bng"
                                                                               name="emp_name_bng"
                                                                               class="form-control" placeholder="Name"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="emp_designation">Designation</label>
                                                                        <input type="text" id="emp_designation"
                                                                               name="emp_designation"
                                                                               class="form-control" placeholder="Designation"
                                                                               autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="mobile_no" class="required">Mobile
                                                                            No</label>
                                                                        <input type="number"
                                                                               class="form-control global-number-validation"
                                                                               id="mobile_no"
                                                                               name="mobile_no"
                                                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                               maxlength="13"
                                                                               placeholder="Enter Mobile No">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="email_address" class="required">Email
                                                                            Address</label>
                                                                        <input type="email" class="form-control"
                                                                               id="email_address"
                                                                               name="email_address"

                                                                               placeholder="Enter Email Address">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="nid" class="required">NID</label>
                                                                        <input type="number"
                                                                               class="form-control global-number-validation"
                                                                               id="nid"
                                                                               required
                                                                               name="nid"
                                                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                               maxlength="17"
                                                                               placeholder="NID">
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="emergency_cell_No" class="">Emergency
                                                                            cell No</label>
                                                                        <input type="number"
                                                                               class="form-control global-number-validation"
                                                                               id="emergency_cell_No"
                                                                               name="emergency_cell_No"
                                                                               oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                               maxlength="11"
                                                                               placeholder="Emergency cell No">
                                                                    </div>
                                                                </div>
                                                            @endif
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="work_place" class="required">Work
                                                                        Place</label>
                                                                    <input type="text" class="form-control"
                                                                           id="work_place"
                                                                           required
                                                                           name="work_place"
                                                                           value="{{old('work_place',isset($trainerInfo->workplace) ? $trainerInfo->workplace : '')}}"
                                                                           placeholder="Enter Work Place">
                                                                    <small class="text-muted form-text"> </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="distinction" class="required">Distinction</label>
                                                                    <input type="text" class="form-control"
                                                                           id="distinction"
                                                                           required
                                                                           name="distinction"
                                                                           value="{{old('distinction',isset($trainerInfo->distinction) ? $trainerInfo->distinction : '')}}"
                                                                           placeholder="Enter Distinction">
                                                                    <small class="text-muted form-text"> </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="remuneration" class="required">Remuneration</label>
                                                                    <input type="number" class="form-control"
                                                                           id="remuneration"
                                                                           required
                                                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                                                           maxlength="6"
                                                                           name="remuneration"
                                                                           value="{{old('remuneration',isset($trainerInfo->remuneration) ? $trainerInfo->remuneration : '')}}"
                                                                           placeholder="Enter Remuneration">
                                                                    <small class="text-muted form-text"> </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="expertise"
                                                                           class="required">Expertise</label>
                                                                    <textarea required placeholder="Expertise"
                                                                              rows="3" wrap="soft"
                                                                              name="expertise"
                                                                              class="form-control"
                                                                              id="expertise">{{old('expertise',isset($trainerInfo->expertise) ? $trainerInfo->expertise : '')}}</textarea>
                                                                    <small class="text-muted form-text"> </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="job_responsibility" class="required">Job
                                                                        Responsibility</label>
                                                                    <textarea required placeholder="Job Responsibility"
                                                                              rows="3" wrap="soft"
                                                                              name="job_responsibility"
                                                                              class="form-control"
                                                                              id="job_responsibility">{{old('job_responsibility', isset($trainerInfo->job_responsibility) ? $trainerInfo->job_responsibility :'')}}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="performance" class="required">Performance</label>
                                                                    <textarea required placeholder="Performance"
                                                                              rows="3" wrap="soft"
                                                                              name="performance"
                                                                              class="form-control"
                                                                              id="remarks">{{old('performance', isset($trainerInfo->performance) ? $trainerInfo->performance :'')}}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="remarks" class="">Remarks</label>
                                                                    <textarea placeholder="Remarks"
                                                                              rows="3" wrap="soft"
                                                                              name="remarks"
                                                                              class="form-control"
                                                                              id="remarks">{{old('remarks', isset($trainerInfo->remarks) ? $trainerInfo->remarks :'')}}</textarea>
                                                                </div>
                                                            </div>

                                                            @if (isset ($trainerInfo->internal_yn))
                                                                @if($trainerInfo->internal_yn == 'N')
                                                                    <div class="col-sm-6" id="upload_photo">
                                                                        <div class="form-group">
                                                                            <label for="trainer_photo" class="">Upload
                                                                                Photo</label>
                                                                            <input type="file" class="form-control"
                                                                                   id="trainer_photo"
                                                                                   name="trainer_photo"/>
                                                                        </div>
                                                                        @if(isset($trainerInfo->trainer_photo))
                                                                            <a href="{{ route('trainer-information.trainer-info-photo-download', [$trainerInfo->trainer_id]) }}"
                                                                               target="_blank">{{$trainerInfo->trainer_photo_name}}</a>
                                                                        @endif
                                                                    </div>
                                                                @else
                                                                    <div class="col-sm-6" id="upload_photo"
                                                                         style="display:none">
                                                                        <div class="form-group">
                                                                            <label for="trainer_photo" class="">Upload
                                                                                Photo</label>
                                                                            <input type="file" class="form-control"
                                                                                   id="trainer_photo"
                                                                                   name="trainer_photo"/>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <div class="col-sm-6" id="upload_photo">
                                                                    <div class="form-group">
                                                                        <label for="trainer_photo" class="">Upload
                                                                            Photo</label>
                                                                        <input type="file" class="form-control"
                                                                               id="trainer_photo" name="trainer_photo"/>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <div class="col-sm-6">
                                                                <label for="trainer_active_yn"
                                                                       class="required">Active?</label>
                                                                <div class="form-group">
                                                                    <div class="form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                               name="trainer_active_yn"
                                                                               id="trainer_active_yes"
                                                                               value="{{\App\Enums\YesNoFlag::YES}}"
                                                                               @if($trainerInfo && $trainerInfo->trainer_active_yn == \App\Enums\YesNoFlag::YES)
                                                                               checked
                                                                               @elseif(!$trainerInfo)
                                                                               checked
                                                                            @endif
                                                                        >
                                                                        <label class="form-check-label" for="active">
                                                                            Yes</label>
                                                                    </div>
                                                                    <div class="form-check-inline">
                                                                        <input class="form-check-input" type="radio"
                                                                               name="trainer_active_yn"
                                                                               id="trainer_active_no"
                                                                               value="{{\App\Enums\YesNoFlag::NO}}"
                                                                               @if($trainerInfo && $trainerInfo->trainer_active_yn != \App\Enums\YesNoFlag::YES)
                                                                               checked
                                                                            @endif
                                                                        >
                                                                        <label class="form-check-label" for="Inactive">
                                                                            No</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </fieldset>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1">Save</button>
                                            <button type="reset" class="btn btn-light-secondary mr-1 mb-1">Reset
                                            </button>
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
        </div>
    </div>


@endsection

@section('footer-script')
    <script type="text/javascript">

        function yesNoCheckCpa() {
            if (document.getElementById('yn_cpa').checked) {

                $('#emp_name').val('');
                $('#emp_name_bng').val('');
                $('#emp_designation').val('');
                $('#mobile_no').val('');
                $('#email_address').val('');
                $('#nid').val('');
                $('#emergency_cell_No').val('');

                $("#emp_name").prop("readonly", true);
                $("#emp_name_bng").prop("readonly", true);
                $("#emp_designation").prop("readonly", true);
                $("#mobile_no").prop("readonly", true);
                $("#email_address").prop("readonly", true);
                $("#nid").prop("readonly", true);
                $("#emergency_cell_No").prop("readonly", true);
                $('#upload_photo').hide();

                $("#emp_id").prop("disabled", false);

            }
        }

        function yesNoCheckOutSider() {
            if (document.getElementById('yn_outsider').checked) {
                $('#emp_name').val('');
                $('#emp_name_bng').val('');
                $('#emp_designation').val('');
                $('#mobile_no').val('');
                $('#email_address').val('');
                $('#nid').val('');
                $('#emergency_cell_No').val('');
                $('#emp_id').empty();

                $("#emp_name").prop("readonly", false);
                $("#emp_name_bng").prop("readonly", false);
                $("#emp_designation").prop("readonly", false);
                $("#mobile_no").prop("readonly", false);
                $("#email_address").prop("readonly", false);
                $("#nid").prop("readonly", false);
                $("#emergency_cell_No").prop("readonly", false);
                $('#upload_photo').show();

                $("#emp_name").prop("disabled", false);
                $("#emp_name_bng").prop("disabled", false);
                $("#emp_designation").prop("disabled", false);
                $("#mobile_no").prop("disabled", false);
                $("#email_address").prop("disabled", false);
                $("#nid").prop("disabled", false);
                $("#emergency_cell_No").prop("disabled", false);

                $("#emp_id").prop("disabled", true);
            }
        }

        function populateRelatedFields(that, data) {
            $(that).parent().parent().parent().find('#emp_name').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_name_bng').val(data.emp_name_bng);
            $(that).parent().parent().parent().find('#emp_designation').val(data.designation);
            $(that).parent().parent().parent().find('#mobile_no').val(data.emp_mbl);
            $(that).parent().parent().parent().find('#email_address').val(data.emp_email);
            $(that).parent().parent().parent().find('#nid').val(data.nid_no);
            $(that).parent().parent().parent().find('#emergency_cell_No').val(data.emp_emergency_contact_mobile);
        }

        $(document).ready(function () {
            selectCpaEmployees('#emp_id', APP_URL + '/ajax/employees', APP_URL + '/ajax/employee/', populateRelatedFields);
        });
    </script>

@endsection
