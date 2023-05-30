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
                    <h4 class="card-title">Training Type Entry</h4>
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
                    <form @if(isset($trainingtype->training_type_id)) action="{{route('training-type.training-type-update',[$trainingtype->training_type_id])}}"
                          @else action="{{route('training-type.training-type-post')}}" @endif method="post">
                        @csrf
                        @if (isset($trainingtype->training_type_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-md-3" >
                                <div class="form-group">
                                    <label>Type Name</label>
                                    <input type="text" id="type_name" name="type_name"
                                           class="form-control" placeholder="Type Name"
                                           value="{{old('type_name',isset($trainingtype->training_type_name) ? $trainingtype->training_type_name : '')}}"
                                           autocomplete="off">
                                    @if (isset($trainingtype->training_type_id))<input type="hidden" id="training_type_id" name="training_type_id" value="{{isset($trainingtype->training_type_id) ? $trainingtype->training_type_id : ''}}">@endif
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Active From</label>
                                    <div class="input-group date" id="activefrom_dateDP" data-target-input="nearest">
                                        <input required type="text"
                                               value="{{($trainingtype)?date('d-m-Y', strtotime($trainingtype->activation_start_date)):''}}"
                                               class="form-control datetimepicker-input"
                                               data-toggle="datetimepicker" data-target="#activefrom_dateDP"
                                               id="active_from"
                                               name="active_from"
                                               placeholder="Active From"
                                               autocomplete="off"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Active To</label>
                                    <div class="input-group date" id="activeto_dateDP" data-target-input="nearest">
                                        <input required type="text"
                                               value="{{($trainingtype)?date('d-m-Y', strtotime($trainingtype->activation_end_date)):''}}"
                                               class="form-control datetimepicker-input"
                                               data-toggle="datetimepicker" data-target="#activeto_dateDP"
                                               id="active_to"
                                               name="active_to"
                                               placeholder="Active To"
                                               autocomplete="off"
                                        />
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="required">Status</label>
                                    <select required class="custom-select select2" name="requsition_status" id="requsition_status">
                                        <option value="">-- Please select an option --</option>
                                        <option value="Y" @if(!empty($trainingtype)) @if($trainingtype->status_yn=='Y') {{'selected="selected"'}} @endif @endif>Active</option>
                                        <option value="N" @if(!empty($trainingtype)) @if($trainingtype->status_yn=='N') {{'selected="selected"'}} @endif @endif>In-Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if($trainingtype)
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">
                                        Update
                                    </button>
                                    <a href="{{url('/training-type')}}">
                                        <button type="button" id="cancel"
                                                class="btn btn btn-outline shadow mb-1 btn-secondary">
                                            Cancel
                                        </button>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="row">
                                <div class="col-md-12 text-right" id="add">
                                    <button type="submit" id="add"
                                            class="btn btn btn-dark shadow mr-1 mb-1 btn-secondary">Save
                                    </button>
                                    <button type="reset" id="reset"
                                            class="btn btn btn-outline shadow mb-1 btn-secondary">Reset
                                    </button>

                                </div>
                            </div>
                        @endif

                    </form>
                </div>

            </div>
            @include('training.trainingtype.trainingtype_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function trainingtypeList() {
            $('#type-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: APP_URL + '/training-type-datatable-list',
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'training_type_name', name: 'training_type_name', searchable: true},
                    {data: 'activation_start_date', name: 'activation_start_date', searchable: false},
                    {data: 'activation_end_date', name: 'activation_end_date', searchable: false},
                    {data: 'status_yn', name: 'status_yn', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $('#activefrom_dateDP').datetimepicker({
            format: 'DD-MM-YYYY',
            // format: 'L',
            icons: {
                time: 'bx bx-time',
                date: 'bx bxs-calendar',
                up: 'bx bx-up-arrow-alt',
                down: 'bx bx-down-arrow-alt',
                previous: 'bx bx-chevron-left',
                next: 'bx bx-chevron-right',
                today: 'bx bxs-calendar-check',
                clear: 'bx bx-trash',
                close: 'bx bx-window-close'
            }
        });

        $('#activeto_dateDP').datetimepicker({
            format: 'DD-MM-YYYY',
            // format: 'L',
            icons: {
                time: 'bx bx-time',
                date: 'bx bxs-calendar',
                up: 'bx bx-up-arrow-alt',
                down: 'bx bx-down-arrow-alt',
                previous: 'bx bx-chevron-left',
                next: 'bx bx-chevron-right',
                today: 'bx bxs-calendar-check',
                clear: 'bx bx-trash',
                close: 'bx bx-window-close'
            }
        });

        $(document).ready(function() {
            trainingtypeList();
        });
    </script>

@endsection


