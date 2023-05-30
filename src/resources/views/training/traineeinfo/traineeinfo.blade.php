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
                    <h4 class="card-title">Outsider Trainee Information</h4>
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
                    <form id="trainee_info"
                        @if(isset($traineeinfo[0]->trainee_id)) action="{{route('trainee-information.trainee-information-update',[$traineeinfo[0]->trainee_id])}}"
                        @else action="{{route('trainee-information.trainee-information-post')}}" @endif method="post">
                        @csrf
                        @if (isset($traineeinfo[0]->trainee_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Trainee Name</label>
                                    <input required type="text" id="emp_name" name="emp_name"
                                           class="form-control"
                                           value="{{old('emp_name', isset($traineeinfo[0]->trainee_name) ? $traineeinfo[0]->trainee_name :'')}}"
                                           autocomplete="off">

                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Designation</label>
                                    <input type="text" id="emp_designation" name="emp_designation"
                                           class="form-control" required
                                           value="{{old('emp_designation', isset($traineeinfo[0]->designation_name) ? $traineeinfo[0]->designation_name :'')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Department</label>
                                    <input type="text" id="emp_department" name="emp_department"
                                           class="form-control"
                                           value="{{isset($traineeinfo[0]->department_name) ? $traineeinfo[0]->department_name : ''}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Organization</label>
                                    <input required
                                           type="text"
                                           class="form-control"
                                           id="organization"
                                           name="organization"
                                           autocomplete="off"
                                           value="{{old('organization', isset($traineeinfo[0]->organization_name) ? $traineeinfo[0]->organization_name :'')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Contact No</label>
                                    <input
                                        type="number" required
                                        class="form-control global-number-validation"
                                        id="contact_no"
                                        name="contact_no"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        maxlength="13"
                                        value="{{old('contact_no', isset($traineeinfo[0]->cell_number) ? $traineeinfo[0]->cell_number :'')}}"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label">Email</label>
                                    <input type="email" id="email_add" name="email_add"
                                           class="form-control"
                                           value="{{old('email_add', isset($traineeinfo[0]->email) ? $traineeinfo[0]->email :'')}}"
                                           autocomplete="off">

                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Emergency Contact No</label>
                                    <input
                                        type="number"
                                        class="form-control global-number-validation"
                                        id="emrg_contact_no"
                                        name="emrg_contact_no"
                                        oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                        maxlength="11"
                                        autocomplete="off"
                                        value="{{old('emrg_contact_no', isset($traineeinfo[0]->emergency_cell_number) ? $traineeinfo[0]->emergency_cell_number :'')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="">Previous Training</label>
                                    <select class="form-control select2" id="prev_training_id"
                                            name="prev_training_id">
                                        <option value="">Select One</option>
                                        @foreach($traininginfo as $value)
                                            <option value="{{$value->training_id}}"
                                                {{isset($traineeinfo[0]->previous_training) && $traineeinfo[0]->previous_training == $value->training_id ? 'selected' : ''}}
                                            >{{$value->training_title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <input type="bank_name" id="bank_name" name="bank_name"
                                           class="form-control"
                                           value="{{old('bank_name', isset($traineeinfo[0]->bank_name) ? $traineeinfo[0]->bank_name :'')}}"
                                           autocomplete="off">

                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Branch Name</label>
                                    <input type="branch_name" id="branch_name" name="branch_name"
                                           class="form-control"
                                           value="{{old('branch_name', isset($traineeinfo[0]->branch_name) ? $traineeinfo[0]->branch_name :'')}}"
                                           autocomplete="off">

                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Account No</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="acc_no"
                                        name="acc_no"
                                        value="{{old('acc_no', isset($traineeinfo[0]->acc_no) ? $traineeinfo[0]->acc_no :'')}}"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea placeholder="Address"
                                              rows="3" wrap="soft"
                                              name="trainee_address"
                                              class="form-control"
                                              id="trainee_address">{{old('trainee_address',isset($traineeinfo[0]->contact_address) ? $traineeinfo[0]->contact_address : '')}}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea rows="3" wrap="soft"
                                              name="remarks"
                                              class="form-control"
                                              id="remarks">{{old('remarks',isset($traineeinfo[0]->remarks) ? $traineeinfo[0]->remarks : '')}}</textarea>
                                </div>
                            </div>

                            @if($traineeinfo)
                                <div class="col-12 d-flex justify-content-end">
                                    <div class="col-md-12 text-right" id="cancel">
                                        <button type="submit" id="update"
                                                class="btn btn-primary mb-1">
                                            Update
                                        </button>
                                        <a href="{{url('/trainee-information')}}">
                                            <button type="button" id="cancel"
                                                    class="btn btn-primary mb-1">
                                                Cancel
                                            </button>
                                        </a>
                                    </div>
                                </div>
                            @else
                                <div class="col-12 d-flex justify-content-end">
                                    <div class="col-md-12 text-right" id="add">
                                        <button type="submit" id="add"
                                                class="btn btn-primary mb-1">Save
                                        </button>
                                        <button type="reset" id="reset"
                                                class="btn btn-primary mb-1">Reset
                                        </button>

                                    </div>
                                </div>
                            @endif
                        </div>
                    </form>
                </div>

            </div>
            @include('training.traineeinfo.traineeinfo_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        $("#reset").click(function () {
            $("#trainee_info").trigger('reset');
            $('.select2').val('').trigger('change');
        });

        function traineeInfoList() {
            $('#trainee-info-list').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: APP_URL + '/trainee-information-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'trainee_name', name: 'trainee_name', searchable: true},
                    {data: 'organization_name', name: 'organization_name', searchable: true},
                    {data: 'cell_number', name: 'cell_number', searchable: true},
                    {data: 'email', name: 'email', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function () {
            traineeInfoList();
        });

    </script>

@endsection
