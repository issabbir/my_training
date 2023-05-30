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
                    <form
                        @if(isset($trainingtype->training_type_id)) action="{{route('training-type.training-type-update',[$trainingtype->training_type_id])}}"
                        @else action="{{route('training-type.training-type-post')}}" @endif method="post">
                        @csrf
                        @if (isset($trainingtype->training_type_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Type Name</label>
                                    <input type="text" id="type_name" name="type_name"
                                           class="form-control" required
                                           value="{{old('type_name',isset($trainingtype->training_type_name) ? $trainingtype->training_type_name : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Active From</label>
                                    <input type="text"
                                           autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           data-toggle="datetimepicker"
                                           id="from_date"
                                           data-target="#from_date"
                                           name="from_date"
                                           value=""
                                           placeholder="YYYY-MM-DD"
                                           required
                                           data-predefined-date="{{old('from_date',isset($trainingtype->activation_start_date) ? $trainingtype->activation_start_date :'')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Active To</label>
                                    <input type="text"
                                           autocomplete="off"
                                           class="form-control datetimepicker-input"
                                           data-toggle="datetimepicker"
                                           id="to_date"
                                           data-target="#to_date"
                                           name="to_date"
                                           value=""
                                           placeholder="YYYY-MM-DD"
                                           required
                                           data-predefined-date="{{old('from_date',isset($trainingtype->activation_end_date) ? $trainingtype->activation_end_date :'')}}"
                                    >
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="mb-1 required">Status</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="requsition_status" id="requsition_status_yes"
                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                   checked
                                                   @if(isset($trainingtype->status_yn) && $trainingtype->status_yn == "Y") checked @endif/>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="requsition_status" value="{{ \App\Enums\YesNoFlag::NO }}"
                                                   id="requsition_status_no"
                                                   @if(isset($trainingtype->status_yn) && $trainingtype->status_yn == "N") checked @endif/>
                                            <label class="form-check-label">In-Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea rows="2" wrap="soft"
                                              name="remarks"
                                              class="form-control"
                                              id="remarks">{{old('remarks',isset($trainingtype->remarks) ? $trainingtype->remarks : '')}}</textarea>
                                </div>
                            </div>
                        </div>
                        @if($trainingtype)
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/training-type')}}">
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
            @include('training.setup.trainingtype.trainingtype_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function trainingtypeList() {
            $('#type-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + '/training-type-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
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


        $(document).ready(function () {
            trainingtypeList();
            dateRangePicker('#from_date', '#to_date');
        });
    </script>

@endsection


