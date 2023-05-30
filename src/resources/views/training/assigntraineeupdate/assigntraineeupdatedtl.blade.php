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
                    <h4 class="card-title">Trainee Assign</h4>
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
                    <form enctype="multipart/form-data"
                          @if(isset($mstData->nominated_trainee_mst_id)) action="{{route('assign-trainee-update.assign-trainee-update-update',[$mstData->nominated_trainee_mst_id])}}"
                          @else action="{{route('assign-trainee-update.assign-trainee-update-post')}}" @endif method="post" onsubmit="return chkTable()">
                        @csrf
                        @if (isset($mstData->nominated_trainee_mst_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Training Name.</label>
                                    <input
                                        disabled
                                        type="text"
                                        class="form-control"
                                        id="training_name"
                                        name="training_name"
                                        autocomplete="off"
                                        value="{{old('training_name',isset($mstData->training_name) ? $mstData->training_name : '')}}"
                                    >
                                    <input type="hidden" id="assignment_mst_id" name="assignment_mst_id" value="{{old('training_name',isset($mstData->assignment_mst_id) ? $mstData->assignment_mst_id : '')}}">
                                    <input type="hidden" id="training_id" name="training_id" value="{{old('training_id',isset($mstData->training_id) ? $mstData->training_id : '')}}">
                                    <input type="hidden" id="trinee_count" name="trinee_count"
                                           value="{{isset($allTrainee_count) ? $allTrainee_count : ''}}">
                                    <input type="hidden" id="all_trainee" name="all_trainee"
                                           value="{{isset($allTrainee) ? $allTrainee : ''}}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Course Co-Ordinator Name</label>
                                    <input
                                        disabled
                                        type="text"
                                        class="form-control"
                                        id="coordination_name"
                                        name="coordination_name"
                                        autocomplete="off"
                                        value="{{old('coordination_name',isset($mstData->coordination_name) ? $mstData->coordination_name : '')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="">Mobile No</label>
                                    <input
                                        disabled
                                        type="number"
                                        class="form-control"
                                        id="coordination_cell"
                                        autocomplete="off"
                                        name="coordination_cell"
                                        value="{{old('coordination_cell',isset($mstData->coordination_cell) ? $mstData->coordination_cell : '')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="">Email</label>
                                    <input
                                        disabled
                                        type="text"
                                        class="form-control"
                                        id="coordination_email"
                                        autocomplete="off"
                                        name="coordination_email"
                                        value="{{old('coordination_email',isset($mstData->coordination_email) ? $mstData->coordination_email : '')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required"> Department Assign Date</label>
                                    <input
                                        disabled
                                        type="text"
                                        class="form-control"
                                        id="assignment_date"
                                        autocomplete="off"
                                        name="assignment_date"
                                        value="{{old('assignment_date',isset($mstData->dept_assignment_date) ? $mstData->dept_assignment_date : '')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Trainee Assign Date</label>
                                    <input
                                        disabled
                                        type="text"
                                        class="form-control"
                                        id="trainee_assign_date"
                                        autocomplete="off"
                                        name="trainee_assign_date"
                                        value="{{old('assignment_date',isset($mstData->trainee_assign_date) ? $mstData->trainee_assign_date : '')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Department Capacity</label>
                                    <input class="form-control" type="text" value="{{ $capacity }}" id="Capacity" readonly>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="">Remaining Capacity</label>
                                    <input class="form-control" type="text" value="{{ $r_capacity }}" id="R-Capacity" readonly>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <div class="form-group row">
                                    <label for="remarks" class="col-md-12 mb-1">Description</label>
                                    <div class="col-md-12"> {!! (isset($mstData->remarks) ? ($mstData->remarks) : '')!!} </div>
                                    <input type="hidden" name="master_remarks" value="{{old('remarks',isset($mstData->remarks) ? $mstData->remarks : '')}}">
                                </div>
                            </div>
                        </div>
                        <fieldset class="border p-1 mt-1 mb-1 col-md-12">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label class="required">Trainee</label>
                                        <select class="custom-select select2" name="emp_id" id="emp_id"
                                                data-emp-id="{{old('emp_id')}}"></select>

{{--                                        <input type="hidden" value="{{ $capacity }}" id="Capacity">--}}

                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Designation</label>
                                        <input disabled type="text" id="emp_designation" name="emp_designation"
                                               class="form-control"
                                               value=""
                                               autocomplete="off">
                                        <input type="hidden" id="designation_id" name="designation_id">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <input disabled type="text" id="emp_department" name="emp_department"
                                               class="form-control"
                                               value=""
                                               autocomplete="off">
                                        <input type="hidden" id="department_id" name="department_id">
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label>Remarks</label>
                                        <input type="text" id="tab_remarks" name="tab_remarks"
                                               class="form-control"
                                               value=""
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-1" align="right">
                                    <div id="start-no-field">
                                        <label for="seat_to1">&nbsp;</label><br/>
                                        <button type="button" id="append"
                                                class="btn btn-primary mb-1 add-row-dept">
                                            ADD
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 mt-1">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-bordered" id="table-dept">
                                        <thead>
                                        <tr>
                                            <th style="height: 25px;text-align: left; width: 5%">Action</th>
                                            <th style="height: 25px;text-align: left; width: 35%">Trainee Name
                                            </th>
                                            <th style="height: 25px;text-align: left; width: 20%">
                                                Department
                                            </th>
                                            <th style="height: 25px;text-align: left; width: 40%">Remarks
                                            </th>
                                        </tr>
                                        </thead>

                                        <tbody id="dept_body">
                                        @if(!empty($dtlData))
                                            @foreach($dtlData as $key=>$value)
                                                <tr>
                                                    <td class="text-center"><input type='checkbox' name='record'>
                                                        <input type="hidden" name="trainee_id[]" value="{{$value->trainee_id}}" class="trainee_id">
                                                        <input type="hidden" name="department_id[]" value="{{$value->department_id}}">
                                                        <input type="hidden" name="tab_remarks[]" value="{{$value->remarks}}">
                                                        <input type="hidden" name="nominated_dtl_id[]" value="{{$value->nominated_dtl_id}}" class="delete_nominated_dtl_id"></td>
                                                    <td>{{$value->emp_name}}</td>
                                                    <td>{{$value->department_name}}</td>
                                                    <td>{{$value->remarks}}</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-start">

                                <button type="button"
                                        class="btn btn-primary mb-1 delete-row-dept">
                                    Delete
                                </button>
                            </div>
                        </fieldset>
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/assign-trainee-update')}}">
                                        <button type="button" id="cancel"
                                                class="btn btn-primary mb-1">
                                            Cancel
                                        </button>
                                    </a>
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
        function chkTable() {
            if ($('#dept_body tr').length == 0) {
                Swal.fire({
                    title: 'Please assign Trainee!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            } else {
                return true;
            }
        }
        var dataArray = new Array();
        $(document).ready(function () {
            selectCpaEmployees('#emp_id', APP_URL + '/ajax/dept-wise-trainee', APP_URL + '/ajax/employee/', populateRelatedFields);
            datePicker('#assignment_date');

            var trainee_count = $("#trinee_count").val();
            var all_trainee = $("#all_trainee").val();
            var arr_all_trainee = []
            try {
                arr_all_trainee = JSON.parse(all_trainee);
            } catch (e){
                console.log("Invalid json")
            }
            if(trainee_count)
            {
                var i;
                for (i = 0; i < trainee_count; i++) {
                    dataArray.push(arr_all_trainee[i]);
                }
            }
        });

        function populateRelatedFields(that, data) {
            $(that).parent().parent().parent().find('#emp_name').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_name_post').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_designation').val(data.designation);
            $(that).parent().parent().parent().find('#emp_department').val(data.department);
            $(that).parent().parent().parent().find('#department_id').val(data.department_id);
            $(that).parent().parent().parent().find('#designation_id').val(data.designation_id);
        }

        $(".add-row-dept").click(function () {

            let capacity = $("#Capacity").val();

            // let r_capacity = parseInt($('#R-capacity').val());
            //
            // $('#R-Capacity').val(r_capacity - 1);

            if(dataArray.length + 1 > capacity) {
                Swal.fire('Department capacity full') ;
            }else {

                let emp_id = $("#emp_id option:selected").text();
                let trainee_name = emp_id.split('(').pop().split(')')[0];
                let trainee_id = $("#emp_id option:selected").val();
                let emp_department = $("#emp_department").val();
                let department_id = $("#department_id").val();
                let tab_remarks = $("#tab_remarks").val();

                if (emp_id) {
                    if ($.inArray(trainee_id, dataArray) > -1) {
                        Swal.fire('Duplicate value not allowed.');
                    } else {
                        var markup = "<tr><td class='text-center'><input type='checkbox' name='record'>" +
                            "<input type='hidden' name='nominated_dtl_id[]' class='delete_nominated_dtl_id'>" +
                            "<input type='hidden' name='trainee_id[]' class='trainee_id' value='" + trainee_id + "'>" +
                            "<input type='hidden' name='department_id[]' value='" + department_id + "'>" +
                            "<input type='hidden' name='tab_remarks[]' value='" + tab_remarks + "'>" +
                            "</td><td>" + trainee_name + "</td><td>" + emp_department + "</td><td>" + tab_remarks + "</td></tr>";
                        $("#table-dept tbody").append(markup);
                        $("#emp_designation").val("");
                        $("#designation_id").val("");
                        $("#emp_department").val("");
                        $("#department_id").val("");
                        $("#trinee_mobile_no").val("");
                        $("#tab_remarks").val("");
                        dataArray.push(trainee_id);
                    }
                } else {
                    Swal.fire('Fill required value.');
                }

            }

        });

        $(".delete-row-dept").click(function () {
            $("#table-dept tbody").find('input[name="record"]').each(function () {
                if ($(this).is(":checked")) {
                    let trainee_id = $(this).closest('tr').find('.trainee_id').val();
                    let nominated_dtl_id = $(this).closest('tr').find('.delete_nominated_dtl_id').val();
                    if (nominated_dtl_id!== null) {
                        Swal.fire({
                            title: 'Are you sure?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Yes, delete it!'
                        }).then((result) => {
                            if (result.value) {
                                $(this).parents("tr").remove();
                                $.ajax({
                                    type: 'GET',
                                    url: '/assign-trainee-data-remove',
                                    data: {nominated_dtl_id: nominated_dtl_id},
                                    success: function (msg) {

                                        for (var i = 0; i < dataArray.length; i++) {
                                            if (dataArray[i] == trainee_id) {
                                                dataArray.splice(i, 1);
                                                break;
                                            }
                                        }

                                        Swal.fire({
                                            title: 'Entry Successfully Deleted!',
                                            icon: 'success',
                                            confirmButtonText: 'OK'
                                        }).then(function () {

                                        });
                                    }
                                });
                            }
                        });

                    } else {
                        $(this).parents("tr").remove();
                    }

                }
            });
        });
    </script>

@endsection
