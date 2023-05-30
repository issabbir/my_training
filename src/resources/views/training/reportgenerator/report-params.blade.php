<div class="col-12">
    <div class="row">
        @if($report)
            @if($report->params)
                @foreach($report->params as $reportParam)
                    @if($reportParam->component == 'training_batch_no')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control select2"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                <option value="">Select One</option>
                                @if($trainingInfo)
                                    @foreach($trainingInfo as $training)
                                        <option value="{{$training->training_id}}">{{$training->training_number.' ('.$training->training_title.')'}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="batch_no">Batch No</label>
                            <select name="p_schedule_id" id="batch_no" class="form-control select2"></select>
                        </div>
                    @elseif($reportParam->component == 'emp_no')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                            </select>
                        </div>
                    @elseif($reportParam->component == 'emp_code')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control custom-select select2"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                <option value="">Select One</option>
                                <option value="">All</option>
{{--                                @if($employee)--}}
{{--                                    @foreach($employee as $value)--}}
{{--                                        <option value="{{$value->emp_code}}">{{$value->emp_code}}</option>--}}
{{--                                    @endforeach--}}
{{--                                @endif--}}
                            </select>
                        </div>
                    @elseif($reportParam->component == 'dept_name')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control custom-select select2"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                <option value="">Select One</option>
                                <option value="">All</option>
                                @if($lDept)
                                    @foreach($lDept as $value)
                                        <option value="{{$value->department_id}}">{{$value->department_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @elseif($reportParam->component == 'designation_name')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control custom-select select2"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                <option value="">Select One</option>
                                <option value="">All</option>
                                @if($lDesignation)
                                    @foreach($lDesignation as $value)
                                        <option value="{{$value->designation_id}}">{{$value->designation}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @elseif($reportParam->component == 'country')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control custom-select select2"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                <option value="">Select One</option>
                                <option value="">All</option>
                                @if($lCountry)
                                    @foreach($lCountry as $value)
                                        <option value="{{$value->country_id}}">{{$value->country}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @elseif($reportParam->component == 'training_no')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                            </select>
                        </div>
                    @elseif($reportParam->component == 'trainee_name')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control select2"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                <option value="">Select One</option>
                                @if($traineeList)
                                    @foreach($traineeList as $trainee)
                                        <option value="{{$trainee->trainee_id}}">{{$trainee->trainee_name.'- ('.$trainee->trainee_code.')'}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @elseif($reportParam->component == 'trainer_name')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                            </select>
                        </div>
                    @elseif($reportParam->component == 'cal_name')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control custom-select select2"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                <option value="">Select One</option>
                                @if($trainingCalMst)
                                    @foreach($trainingCalMst as $value)
                                        <option value="{{$value->calender_id}}">{{$value->calender_name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    @elseif($reportParam->component == 'batch_trainee_name')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control select2"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                                <option value="">Select One</option>
                                @if($scheduleMst)
                                    @foreach($scheduleMst as $value)
                                        <option value="{{$value->schedule_id}}">{{$value->batch_id}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="trainee_id" class="required">Trainee Name</label>
                            <select name="p_trainee_id" id="trainee_id" class="form-control select2" required></select>
                        </div>
                    @elseif($reportParam->component == 'schedule_no')
                        <div class="col-md-3">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <select name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                    class="form-control"
                                    @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif>
                            </select>
                        </div>
                    @elseif($reportParam->component == 'date_range')
                        <div class="col-md-3">
                            <label for="p_start_date"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">From
                                Date</label>
                            <div class="input-group date datePiker" id="p_start_date"
                                 data-target-input="nearest">
                                <input type="text" autocomplete="off"
                                       class="form-control datetimepicker-input"
                                       value="" name="p_start_date"
                                       data-toggle="datetimepicker"
                                       data-target="#p_start_date"
                                       @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required
                                       @endif onautocomplete="off"/>
                                <div class="input-group-append" data-target="#p_start_date"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="bx bxs-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="p_end_date"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">To
                                Date</label>
                            <div class="input-group date datePiker" id="p_end_date"
                                 data-target-input="nearest">
                                <input type="text" autocomplete="off"
                                       class="form-control datetimepicker-input"
                                       value="" name="p_end_date"
                                       data-toggle="datetimepicker"
                                       data-target="#p_end_date"
                                       @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required
                                       @endif onautocomplete="off"/>
                                <div class="input-group-append" data-target="#p_end_date"
                                     data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="bx bxs-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="col">
                            <label for="{{$reportParam->param_name}}"
                                   class="@if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif">{{$reportParam->param_label}}</label>
                            <input type="text" name="{{$reportParam->param_name}}" id="{{$reportParam->param_name}}"
                                   class="form-control"
                                   @if($reportParam->requied_yn==\App\Enums\YesNoFlag::YES) required @endif />
                        </div>
                    @endif
                @endforeach
            @endif
            <div class="col-md-3">
                <label for="type">Report Type</label>
                <select name="type" id="type" class="form-control">
                    <option value="pdf">PDF</option>
                    <option value="xlsx">Excel</option>
                </select>
                <input type="hidden" value="{{$report->report_xdo_path}}" name="xdo"/>
                <input type="hidden" value="{{$report->report_id}}" name="rid"/>
                <input type="hidden" value="{{$report->report_name}}" name="filename"/>
            </div>
            <div class="col-md-3 mt-2">
                <button type="submit" class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">Generate Report</button>
            </div>
        @endif
    </div>
</div>

{{--<link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/css/forms/select/select2.min.css')}}">--}}
{{--<script src="{{asset('assets/vendors/js/forms/select/select2.full.min.js')}}"></script>--}}

<script src="{{asset('assets/js/scripts/forms/select/form-select2.min.js')}}"></script>
<script type="text/javascript">

    $('.datePiker').datetimepicker({
        format: 'YYYY-MM-DD',
        widgetPositioning: {
            horizontal: 'left',
            vertical: 'bottom'
        },
        icons: {
            date: 'bx bxs-calendar',
            previous: 'bx bx-chevron-left',
            next: 'bx bx-chevron-right'
        }
    });

    function trainingBatchNo(){
        $('#p_training_no').on('change', function (e) {
            e.preventDefault();
            let trainingInfoId = $(this).val();
            if (trainingInfoId)
            {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/training-batch-no/' + trainingInfoId,
                    success: function (data) {
                        $('#batch_no').html(data.batchNoHtml);
                    },
                    error: function (err) {
                        alert('error', err);
                    }
                });
            }
            else{
                $('#batch_no').html('');
            }
        });
    }

    /*function deptName()
    {
        $('#p_dept_id').select2({
            placeholder: "Select",
            allowClear: true,
            ajax: {
                url: APP_URL+'/ajax/dept-name',
                data: function (params) {
                    if(params.term) {
                        if (params.term.trim().length  < 1) {
                            return false;
                        }
                    } else {
                        return false;
                    }

                    return params;
                },
                dataType: 'json',
                processResults: function(data) {
                    var formattedResults = $.map(data, function(obj, idx) {
                        obj.id = obj.department_id;
                        obj.text = obj.department_name;
                        return obj;
                    });
                    return {
                        results: formattedResults,
                    };
                }
            }
        });
    }*/

    function trainingNo()
    {
        $('#p_training_id').select2({
            placeholder: "Select",
            allowClear: true,
            ajax: {
                url: APP_URL+'/ajax/training-no',
                data: function (params) {
                    if(params.term) {
                        if (params.term.trim().length  < 1) {
                            return false;
                        }
                    } else {
                        return false;
                    }

                    return params;
                },
                dataType: 'json',
                processResults: function(data) {
                    var formattedResults = $.map(data, function(obj, idx) {
                        obj.id = obj.training_id;
                        obj.text = obj.training_number+' ('+obj.training_title+')';
                        return obj;
                    });
                    return {
                        results: formattedResults,
                    };
                }
            }
        });
    }

    function empCode()
    {
        $('#P_EMP_CODE').select2({
            placeholder: "Select",
            allowClear: true,
            ajax: {
                url: APP_URL+'/ajax/emp-code',
                data: function (params) {
                    if(params.term) {
                        if (params.term.trim().length  < 1) {
                            return false;
                        }
                    } else {
                        return false;
                    }

                    return params;
                },
                dataType: 'json',
                processResults: function(data) {
                    var formattedResults = $.map(data, function(obj, idx) {
                        obj.id = obj.emp_code;
                        obj.text = obj.emp_code;
                        return obj;
                    });
                    return {
                        results: formattedResults,
                    };
                }
            }
        });
    }

    function trainerName()
    {
        $('#p_trainer_id').select2({
            placeholder: "Select",
            allowClear: true,
            ajax: {
                url: APP_URL+'/ajax/trainer-name',
                data: function (params) {
                    if(params.term) {
                        if (params.term.trim().length  < 1) {
                            return false;
                        }
                    } else {
                        return false;
                    }

                    return params;
                },
                dataType: 'json',
                processResults: function(data) {
                    var formattedResults = $.map(data, function(obj, idx) {
                        obj.id = obj.trainer_id;
                        obj.text = obj.trainer_name;
                        return obj;
                    });
                    return {
                        results: formattedResults,
                    };
                }
            }
        });
    }

    function batchNo()
    {
        $('#p_schedule_id').select2({
            placeholder: "Select",
            allowClear: true,
            ajax: {
                url: APP_URL+'/ajax/schedule-no',
                data: function (params) {
                    if(params.term) {
                        if (params.term.trim().length  < 1) {
                            return false;
                        }
                    } else {
                        return false;
                    }

                    return params;
                },
                dataType: 'json',
                processResults: function(data) {
                    var formattedResults = $.map(data, function(obj, idx) {
                        obj.id = obj.schedule_id;
                        obj.text = obj.batch_id;
                        return obj;
                    });
                    return {
                        results: formattedResults,
                    };
                }
            }
        });
    }

    function batchTraineeName(){
        $('#p_schedule_no').on('change', function (e) {
            e.preventDefault();
            let scheduleId = $(this).val();
            if (scheduleId)
            {
                $.ajax({
                    type: "GET",
                    url: APP_URL + '/ajax/batch-trainee-name/' + scheduleId,
                    success: function (data) {
                        $('#trainee_id').html(data.traineeInfoHtml);
                    },
                    error: function (data) {
                        alert('error', data);
                    }
                });
            }
            else{
                $('#trainee_id').html('');
            }
        });
    }

    /*function scheduleNo()
    {
        $('#p_schedule_id').select2({
            placeholder: "Select",
            allowClear: true,
            ajax: {
                url: APP_URL+'/ajax/schedule-no',
                data: function (params) {
                    if(params.term) {
                        if (params.term.trim().length  < 1) {
                            return false;
                        }
                    } else {
                        return false;
                    }

                    return params;
                },
                dataType: 'json',
                processResults: function(data) {
                    var formattedResults = $.map(data, function(obj, idx) {
                        obj.id = obj.schedule_id;
                        obj.text = obj.schedule_id;
                        return obj;
                    });
                    return {
                        results: formattedResults,
                    };
                }
            }
        });
    }*/

    $(document).ready(function() {
        /*deptName();*/
        trainingNo();
        empCode();
        trainerName();
        batchNo();
        trainingBatchNo();
        batchTraineeName();
        //scheduleNo();
        selectCpaEmployees('#p_emp_id', APP_URL+'/ajax/employees', APP_URL+'/ajax/employee/');
    });

</script>
