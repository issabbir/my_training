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
                    <form
                        @if(isset($assigntraineetmst[0]->assignment_mst_id)) action="{{route('assign-trainee.assign-trainee-update',[$assigntraineetmst[0]->assignment_mst_id])}}"
                        @endif method="post" onsubmit="return chkTable()">
                        @csrf
                        @if (isset($assigntraineetmst[0]->assignment_mst_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            {{--                            @dd($capacity)--}}
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
                                        value="{{old('training_name',isset($assigntraineetmst[0]->training_name) ? $assigntraineetmst[0]->training_name : '')}}"
                                    >
                                    <input type="hidden" id="assignment_mst_id" name="assignment_mst_id"
                                           value="{{old('training_name',isset($assigntraineetmst[0]->assignment_mst_id) ? $assigntraineetmst[0]->assignment_mst_id : '')}}">
                                    <input type="hidden" id="training_id" name="training_id"
                                           value="{{old('training_id',isset($assigntraineetmst[0]->training_id) ? $assigntraineetmst[0]->training_id : '')}}">
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
                                        value="{{old('coordination_name',isset($assigntraineetmst[0]->coordination_name) ? $assigntraineetmst[0]->coordination_name : '')}}"
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
                                        value="{{old('coordination_cell',isset($assigntraineetmst[0]->coordination_cell) ? $assigntraineetmst[0]->coordination_cell : '')}}"
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
                                        value="{{old('coordination_email',isset($assigntraineetmst[0]->coordination_email) ? $assigntraineetmst[0]->coordination_email : '')}}"
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
                                        value="{{old('assignment_date',isset($assigntraineetmst[0]->assignment_date) ? $assigntraineetmst[0]->assignment_date : '')}}"
                                    >
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Capacity For Selected Department:</label>
                                    <table style="border: 1px solid;">
                                        <thead>
                                        <tr>
                                            <th style="border: 1px solid;" scope="col">Department</th>
                                            <th style="border: 1px solid;" scope="col">Capacity</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($department as $value)
                                            <tr>
                                                <td style="border: 1px solid;"
                                                    scope="row">{{$value->department_name}}</td>
                                                <td scope="row"
                                                    style="text-align: center; border: 1px solid;">{{$value->department_capacity}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            {{--<div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Department Capacity</label>
                                    <input class="form-control" type="text" value="{{ $capacity }}" id="Capacity"
                                           readonly>
                                </div>
                            </div>--}}

                            <div class="col-md-12">
                                <div class="form-group row">
                                    <label for="remarks" class="col-md-12 mb-1">Description</label>
                                    <div
                                        class="col-md-12">{!! (isset($assigntraineetmst[0]->remarks) ? $assigntraineetmst[0]->remarks : '')!!}</div>
                                    <input type="hidden" name="master_remarks"
                                           value="{!! (isset($assigntraineetmst[0]->remarks) ? $assigntraineetmst[0]->remarks : '')!!}">
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

                                        {{--                                        @dd($capacity)--}}
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
                                        {{--@if(!empty($assigndept))
                                            @foreach($assigndept as $key=>$value)
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
                                        @endif--}}
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
                                    Assign
                                </button>
                                <a href="{{url('/assign-trainee')}}">
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

        let dataArray = new Array();
        let chkData = [];

        $(document).ready(function () {
            selectCpaEmployees('#emp_id', APP_URL + '/ajax/dept-wise-trainee-dtl/'+$("#assignment_mst_id").val(), APP_URL + '/ajax/employee/', populateRelatedFields);
            datePicker('#assignment_date');
        });

        function populateRelatedFields(that, data) {
            $(that).parent().parent().parent().find('#emp_name').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_name_post').val(data.emp_name);
            $(that).parent().parent().parent().find('#emp_designation').val(data.designation);
            $(that).parent().parent().parent().find('#emp_department').val(data.department);
            $(that).parent().parent().parent().find('#department_id').val(data.department_id);
            $(that).parent().parent().parent().find('#designation_id').val(data.designation_id);
        }

        function getOccurrence(array, value) {
            var count = 1;
            array.forEach((v) => (v === value && count++));
            return count;
            /*let count = array.reduce(function (obj, b) {
                obj[b] = ++obj[b] || value;
                return obj;
            }, {});
            return count;*/
        }

        $(".add-row-dept").click(function () {

            let capacity = $("#Capacity").val();
            let emp_id = $("#emp_id option:selected").text();
            let trainee_name = emp_id.split('(').pop().split(')')[0];
            let trainee_id = $("#emp_id option:selected").val();
            let emp_department = $("#emp_department").val();
            let department_id = $("#department_id").val();
            let tab_remarks = $("#tab_remarks").val();
            $.ajax({
                type: 'GET',
                url: '/check-dept-capacity',
                data: {assignment_mst_id: $("#assignment_mst_id").val(), department_id: department_id},
                success: function (msg) {console.log(getOccurrence(chkData, department_id))
                    if (getOccurrence(chkData, department_id) > msg) {
                        Swal.fire('Department Capacity Full.');
                        return;
                    }else{
                        if (emp_id) {
                            var markup = "<tr><td class='text-center'><input type='checkbox' name='record'>" +
                                "<input type='hidden' name='nominated_dtl_id[]' class='delete_nominated_dtl_id'>" +
                                "<input type='hidden' name='trainee_id[]' class='trainee_id' value='" + trainee_id + "'>" +
                                "<input type='hidden' name='department_id[]' class='department_id' value='" + department_id + "'>" +
                                "<input type='hidden' name='tab_remarks[]' value='" + tab_remarks + "'>" +
                                "</td><td>" + trainee_name + "</td><td>" + emp_department + "</td><td>" + tab_remarks + "</td></tr>";
                            $("#table-dept tbody").append(markup);
                            $("#emp_designation").val("");
                            $("#designation_id").val("");
                            $("#emp_department").val("");
                            $("#department_id").val("");
                            $("#emp_id").empty('');
                            $("#trinee_mobile_no").val("");
                            $("#tab_remarks").val("");
                            dataArray.push(trainee_id);
                            chkData.push(department_id);
                        } else {
                            Swal.fire('Fill required value.');
                        }
                    }
                }
            });

            /*if (dataArray.length + 1 > capacity) {
                Swal.fire('Department capacity full');
            } else {
                /!*$.ajax({
                    type: 'GET',
                    url: '/check-dept-capacity',
                    data: {assignment_mst_id: $("#assignment_mst_id").val(), department_id: department_id},
                    success: function (msg) {
                        //alert(chkData);
                        if(getOccurrence(chkData, department_id) === msg){
                            Swal.fire('Department Capacity Full.');
                            return;
                        }else{*!/
                        if (emp_id) {
                            /!*if ($.inArray(trainee_id, dataArray) > -1) {
                                Swal.fire('Duplicate value not allowed.');
                            } else {*!/

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
                            $("#emp_id").empty('');
                            $("#trinee_mobile_no").val("");
                            $("#tab_remarks").val("");
                            dataArray.push(trainee_id);
                            chkData.push(department_id);

                            //}
                        } else {
                            Swal.fire('Fill required value.');
                        }
                        //}
                    /!*}
                });*!/


            }*/


        });

        $(".delete-row-dept").click(function () {
            /*Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                Swal.fire({
                    title: 'Entry Successfully Deleted!',
                    icon: 'success',
                    confirmButtonText: 'OK'
                }).then(function () {
                    /!*let trainee_id = $(this).closest('tr').find('.trainee_id').val();alert(trainee_id);
                    for (var i = 0; i < dataArray.length; i++) {
                        if (dataArray[i] == trainee_id) {
                            dataArray.splice(i, 1);
                            break;
                        }
                    }*!/
                    $('td input:checked').closest('tr').remove();
                });
            });*/
            $("#table-dept tbody").find('input[name="record"]').each(function () {
                if ($(this).is(":checked")) {
                    let trainee_id = $(this).closest('tr').find('.trainee_id').val();
                    let department_id = $(this).closest('tr').find('.department_id').val();
                    if (trainee_id !== null) {
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
                                for (var i = 0; i < dataArray.length; i++) {
                                    if (dataArray[i] == trainee_id) {
                                        dataArray.splice(i, 1);
                                        break;
                                    }
                                }

                                for (var i = 0; i < chkData.length; i++) {
                                    if (chkData[i] == department_id) {
                                        chkData.splice(i, 1);
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

                    } else {
                        $(this).parents("tr").remove();
                    }

                }
            });
        });
    </script>

@endsection
