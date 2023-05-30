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
                    <h4 class="card-title">Department Assign</h4>
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
                          @if(isset($assigndeptmst->assignment_mst_id)) action="{{route('assign-dept.assign-dept-update',[$assigndeptmst->assignment_mst_id])}}"
                          @else action="{{route('assign-dept.assign-dept-post')}}" @endif method="post"
                          onsubmit="return chkTable()">
                        @csrf
                        @if (isset($assigndeptmst->assignment_mst_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Training No.</label>
                                    <select class="custom-select form-control select2" required id="training_id"
                                            name="training_id"
                                            data-training-id="{{old('training_id',isset($assigndeptmst->training_id) ? $assigndeptmst->training_id :'')}}"
                                    >
                                        <option value="">Select One</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Course Co-Ordinator Name</label>
                                    <input
                                        readonly
                                        type="text"
                                        class="form-control"
                                        id="coordinator_name"
                                        name="coordinator_name"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                            <div class="col-md-3">
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
                            <div class="col-md-3">
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
                            <div class="col-md-6 col-sm-12 mb-5">
                                <label class="required" for="assign_dept_description">Description</label>
                                <div id="assign_dept_desc_editor"
                                     class="text-editor">{!! (isset($assigndeptmst->remarks) ? $assigndeptmst->remarks : '')!!}</div>
                                <textarea required rows="1" wrap="soft" name="assign_dept_description"
                                          class="form-control customize-text-editor"
                                          id="assign_dept_description">{{(isset($assigndeptmst->remarks) ? $assigndeptmst->remarks : '')}}</textarea>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Assignment Date</label>
                                    <input type="text"
                                           autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           data-toggle="datetimepicker"
                                           id="assignment_date"
                                           data-target="#assignment_date"
                                           name="assignment_date"
                                           value=""
                                           required
                                           placeholder="YYYY-MM-DD"
                                           data-predefined-date="{{old('assignment_date',isset($assigndeptmst->assignment_date) ? $assigndeptmst->assignment_date :'')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="">Reference</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="reference_no"
                                        autocomplete="off"
                                        name="reference_no"
                                        value="{{old('reference_no',isset($assigndeptmst->reference) ? $assigndeptmst->reference : '')}}"
                                    >
                                </div>
                            </div>
                            {{--<div class="col-md-6">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea placeholder="Remarks"
                                              rows="3" wrap="soft"
                                              name="remarks"
                                              class="form-control"
                                              id="remarks">{{(isset($assigndeptmst->remarks) ? $assigndeptmst->remarks : '')}}</textarea>
                                </div>
                            </div>--}}
                        </div>

                        <fieldset class="border p-1 mt-5 mb-1 col-md-12">
                            <div class="row">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label class="required">Department</label>
                                        <select class="custom-select form-control select2" name="dept_id"
                                                id="dept_id">
                                            <option value="">Select One</option>
                                            <option value="all">ALL</option>
                                            @foreach($department as $value)
                                                <option value="{{$value->department_id}}"
                                                >{{$value->department_name}}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" id="dept_count" name="dept_count"
                                               value="{{isset($allDept_count) ? $allDept_count : ''}}">
                                        <input type="hidden" id="all_dept" name="all_dept"
                                               value="{{isset($allDept) ? $allDept : ''}}">
                                    </div>
                                </div>
                                {{--                                <div class="col-md-2">--}}
                                {{--                                    <div class="form-group">--}}
                                {{--                                        <label class="required">Department Head</label>--}}
                                {{--                                        <select class="custom-select select2" name="emp_id" id="emp_id"--}}
                                {{--                                                data-emp-id="{{old('emp_id')}}"></select>--}}
                                {{--                                        <select class="custom-select form-control select2" id="emp_id"--}}
                                {{--                                                name="emp_id">--}}
                                {{--                                            <option value="">Select One</option>--}}
                                {{--                                        </select>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="required">Department Head</label>
                                        <input
                                            readonly
                                            type="text"
                                            class="form-control"
                                            id="emp_id"
                                            autocomplete="off"
                                            name="emp_id"
                                        >
                                        <input type="hidden" id="dept_head_id" name="dept_head_id"/>
                                        <input type="hidden" id="dept_head_name" name="dept_head_name"/>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="">Department Capacity</label>
                                        <input
                                            type="number"
                                            class="form-control"
                                            id="department_capacity"
                                            autocomplete="off"
                                            name="department_capacity"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label class="">Remarks</label>
                                        <input
                                            type="text"
                                            class="form-control"
                                            id="tab_remarks"
                                            autocomplete="off"
                                            name="tab_remarks"
                                        >
                                    </div>
                                </div>
                                <div class="col-md-1" align="right">
                                    <div id="start-no-field">
                                        <label for="seat_to1">&nbsp;</label><br/>
                                        <button type="button" id="append"
                                                class="btn btn-primary mb-1 add-row-dept">
                                            ADD
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-1">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-bordered" id="table-dept">
                                        <thead>
                                        <tr>
                                            <th style="height: 25px;text-align: left; width: 5%">Action</th>
                                            {{--<th style="height: 25px;text-align: left; width: 20%">Training No
                                            </th>--}}
                                            <th style="height: 25px;text-align: left; width: 15%">Department
                                            </th>
                                            <th style="height: 25px;text-align: left; width: 20%">
                                                Department Head
                                            </th>
                                            <th style="height: 25px;text-align: left; width: 30%">Department Capacity
                                            </th>
                                            <th style="height: 25px;text-align: left; width: 30%">Remarks
                                            </th>
                                        </tr>
                                        </thead>

                                        <tbody id="dept_body">
                                        @if(!empty($assigndept))
                                            @foreach($assigndept as $key=>$value)
                                                <tr>
                                                    <td class="text-center"><input type='checkbox'
                                                                                   name='record'>
                                                        <input type="hidden" name="assignment_mst_id[]"
                                                               class='assignment_mst_id'
                                                               value="{{$value->assignment_mst_id}}">
                                                        <input type="hidden" name="dept_id[]"
                                                               value="{{$value->assign_dept_id}}"
                                                               class='delete_dept_id'>
                                                        <input type="hidden" name="dept_head_id[]" class='dept_head_id'
                                                               value="{{$value->assign_to}}">
                                                        <input type="hidden" name="department_capacity[]"
                                                               value="{{$value->department_capacity}}">
                                                        <input type="hidden" name="tab_remarks[]"
                                                               value="{{$value->remarks}}">
                                                        <input type="hidden" name="assignment_dtl_id[]"
                                                               value="{{$value->assignment_dtl_id}}"
                                                               class="assignment_dtl_id"></td>
                                                    <td>{{$value->department_name}}</td>
                                                    <td>{{$value->emp_name}}</td>
                                                    <td>{{$value->department_capacity}}</td>
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
                        @if($assigndeptmst)
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/assign-dept')}}">
                                        <button type="button" id="cancel"
                                                class="btn btn-primary mb-1">
                                            Cancel
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="row">
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
                    </form>
                </div>

            </div>
            @include('training.assigndept.assigndept_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">
        var dataArray = new Array();

        function chkTable() {
            if ($('#dept_body tr').length == 0) {
                Swal.fire({
                    title: 'Please assign Department!',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return false;
            } else {
                return true;
            }
        }

        $(document).ready(function () {
            selectTraining('#training_id', APP_URL + '/ajax/training-no', APP_URL + '/ajax/training-details/', populateRelatedFields);
            //selectCpaEmployees('#emp_id', APP_URL + '/ajax/employees', APP_URL + '/ajax/employee/', populateRelatedFields);
            assignDeptList();
            datePicker('#assignment_date');
            editors();
            customSelectAssDpt();


            var dept_count = $("#dept_count").val();
            var all_dept = $("#all_dept").val();
            var arr_all_dept = []
            try {
                arr_all_dept = JSON.parse(all_dept);
            } catch (e) {
                console.log("Invalid json")
            }
            if (dept_count) {
                var i;
                for (i = 0; i < dept_count; i++) {
                    dataArray.push(arr_all_dept[i]);
                }
            }
        });

        $('#dept_id').on('change', function (e) {
            e.preventDefault();
            let dept_id = $(this).val();
            if (dept_id) {
                if (dept_id == 'all') {
                    $('#emp_id').val('');
                    $('#dept_head_name').val('');
                    $('#dept_head_id').val('all');
                } else {
                    $.ajax({
                        type: "GET",
                        url: APP_URL + '/ajax/get-dept-head/' + dept_id,
                        success: function (data) {
                            // $('#emp_id').html(data.html);
                            $('#emp_id').val('');
                            $('#dept_head_id').val('');
                            $('#dept_head_name').val('');
                            $('#emp_id').val(data[0].emp_code + ' (' + data[0].emp_name + ')');
                            $('#dept_head_id').val(data[0].emp_id);
                            $('#dept_head_name').val(data[0].emp_name);
                        },
                        error: function (err) {
                            alert('error');
                        }
                    });
                }
            } else {
                $('#emp_id').html('');
            }
        });

        function populateRelatedFields(that, data) {
            $(that).parent().parent().parent().find('#coordinator_name').val(data.coordination_name);
            $(that).parent().parent().parent().find('#mobile_no').val(data.coordination_cell);
            $(that).parent().parent().parent().find('#email').val(data.coordination_email);
        }

        function assignDeptList() {
            $('#assign-dept-list').DataTable({
                processing: true,
                serverSide: true,
                // order: [],
                order: [ 1, 'desc' ],
                ajax: {
                    url: APP_URL + '/assign-dept-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'training_number', name: 'training_number', searchable: true},
                    {data: 'training_title', name: 'training_title', searchable: true},
                    {data: 'assignment_date', name: 'assignment_date', searchable: true},
                    {data: 'view_status', name: 'view_status'},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(".add-row-dept").click(function () {
            let training_id = $("#training_id option:selected").val();
            let training_id_split = $("#training_id option:selected").text();
            let training_id_name = training_id_split.split('(').pop().split(')')[0];
            let dept_id = $("#dept_id option:selected").val();
            let dept_name = $("#dept_id option:selected").text();

            if (dept_id == 'all') {
                $('#emp_id').val('');
                $('#dept_head_name').val('');
                $('#dept_head_id').val('all');
            }
            // let emp_id = $("#emp_id option:selected").text();
            // let dept_head_name = emp_id.split('(').pop().split(')')[0];
            // let dept_head_id = $("#emp_id option:selected").val();
            let dept_head_name = $("#dept_head_name").val();
            let dept_head_id = $("#dept_head_id").val();
            let department_capacity = $("#department_capacity").val();
            let tab_remarks = $("#tab_remarks").val();

            if (dept_id && training_id && dept_head_id) {
                // alert(dataArray)
                if ($.inArray(dept_id, dataArray) > -1) {
                    Swal.fire('Duplicate Value Not Allowed!');
                } else if (dept_id == 'all') {
                    var id = null;
                    if(dataArray.length === 0)
                    {
                        var arrEmpty = true;
                    }
                    $.ajax({
                        type: "GET",
                        url: APP_URL + '/ajax/get-dept-head/' + id,
                        success: function (data) {
                            $.each(data, function (key, value) {
                                // alert(value.dpt_id);
                                if ($.inArray(value.dpt_id, dataArray) > -1) {
                                    if(arrEmpty)
                                    {
                                        Swal.fire('All Department Added!');
                                    }
                                    else
                                    {
                                        Swal.fire('Duplicate Value Not Allowed!');
                                    }
                                } else {
                                    var markup = "<tr><td class='text-center'><input type='checkbox' name='record'>" +
                                        "<input type='hidden' name='assignment_mst_id[]' class='assignment_mst_id' value=''>" +
                                        "<input type='hidden' name='assignment_dtl_id[]' class='assignment_dtl_id' value=''>" +
                                        "<input type='hidden' name='dept_id[]' class='delete_dept_id' value='" + value.dpt_id + "'>" +
                                        "<input type='hidden' name='dept_head_id[]' class='dept_head_id' value='" + value.emp_id + "'>" +
                                        "<input type='hidden' name='department_capacity[]' value='" + department_capacity + "'>" +
                                        "<input type='hidden' name='tab_remarks[]' value='" + tab_remarks + "'>" +
                                        "</td><td>" + value.department_name + "</td><td>" + value.emp_name + "</td><td>" + department_capacity + "</td><td>" + tab_remarks + "</td></tr>";
                                    $("#table-dept tbody").append(markup);
                                    $("#department_capacity").val("");
                                    $("#tab_remarks").val("");

                                    $("#emp_id").val("");
                                    // $("#dept_id").empty();
                                    $("#dept_head_id").val("");
                                    $("#dept_head_name").val("");
                                    dataArray.push(value.dpt_id);
                                }
                            });
                        },
                        error: function (err) {
                            alert('error');
                        }
                    });
                } else {
                    var markup = "<tr><td class='text-center'><input type='checkbox' name='record'>" +
                        "<input type='hidden' name='assignment_mst_id[]' class='assignment_mst_id' value=''>" +
                        "<input type='hidden' name='assignment_dtl_id[]' class='assignment_dtl_id' value=''>" +
                        "<input type='hidden' name='dept_id[]' class='delete_dept_id' value='" + dept_id + "'>" +
                        "<input type='hidden' name='dept_head_id[]' class='dept_head_id' value='" + dept_head_id + "'>" +
                        "<input type='hidden' name='department_capacity[]' value='" + department_capacity + "'>" +
                        "<input type='hidden' name='tab_remarks[]' value='" + tab_remarks + "'>" +
                        "</td><td>" + dept_name + "</td><td>" + dept_head_name + "</td><td>" + department_capacity + "</td><td>" + tab_remarks + "</td></tr>";
                    $("#table-dept tbody").append(markup);
                    $("#department_capacity").val("");
                    $("#tab_remarks").val("");

                    $("#emp_id").val("");
                    // $("#dept_id").empty();
                    $("#dept_head_id").val("");
                    $("#dept_head_name").val("");
                    dataArray.push(dept_id);
                }
            } else {
                Swal.fire('Fill Required Value!');
            }

        });

        $(".delete-row-dept").click(function () {
            Swal.fire({
                async: false,
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                // alert('then');
                if (result.value) {
                    $("#table-dept tbody").find('input[name="record"]').each(function () {
                        if ($(this).is(":checked")) {
                            let dept_id = $(this).closest('tr').find('.delete_dept_id').val();
                            // alert(dept_id)
                            let assignment_dtl_id = $(this).closest('tr').find('.assignment_dtl_id').val();
                            let assignment_mst_id = $(this).closest('tr').find('.assignment_mst_id').val();
                            let assign_to = $(this).closest('tr').find('.dept_head_id').val();
                            if (assignment_dtl_id !== null) {
                                        // alert(result.value);
                                        // $(this).parents("tr").remove();
                                        $.ajax({
                                            type: 'GET',
                                            url: '/assign-dept-data-remove',
                                            data: {
                                                assignment_mst_id: assignment_mst_id,
                                                assignment_dtl_id: assignment_dtl_id,
                                                assign_to: assign_to,
                                                dept_id: dept_id
                                            },
                                            success: function (msg) {
                                                for (var i = 0; i < dataArray.length; i++) {
                                                    if (dataArray[i] == dept_id) {
                                                        dataArray.splice(i, 1);
                                                        // break;
                                                    }
                                                }
                                                $('td input:checked').closest('tr').remove();
                                            }
                                        });
                            } else {
                                $(this).parents("tr").remove();
                            }
                            Swal.fire({
                                title: 'Entry Successfully Deleted!',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(function () {
                                // $('td input:checked').closest('tr').remove();
                            });
                        }
                    });
                }
            });
        });

        function editors() {
            $('.ql-editor').on('blur', function () {
                var editorId = $(this).parent("div[id]:first").attr('id');
                if (editorId == 'assign_dept_desc_editor') {
                    $('#assign_dept_description').html(replacePtagToBrTag($('#' + editorId + ' .ql-editor').html()));
                }
            });

            $('.clearTextEditor').on('click', function () {
                var editorId = $(this).closest("div").nextAll("[id]:first").attr('id');

                if (editorId == 'assign_dept_desc_editor') {
                    $('#' + editorId + ' .ql-editor').html('');
                    $('#assign_dept_description').html('');
                }
            });
        };

        function customSelectAssDpt() {
            $('select#dept_id, select#emp_id').select2({
                width: '100%'
            });
        }
    </script>

@endsection
