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
                    <h4 class="card-title">Trainee Type Entry</h4>
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
                        @if(isset($traineetype->trainee_type_id)) action="{{route('trainee-type.trainee-type-update',[$traineetype->trainee_type_id])}}"
                        @else action="{{route('trainee-type.trainee-type-post')}}" @endif method="post">
                        @csrf
                        @if (isset($traineetype->trainee_type_id))
                            @method('PUT')
                        @endif
                        <div class="row">
{{--                            <div class="col-sm-3">--}}
{{--                                <div class="form-group">--}}
{{--                                    <label class="required">Trainee Type No</label>--}}
{{--                                    <input type="text" id="trainee_type_no" name="trainee_type_no"--}}
{{--                                           class="form-control" required--}}
{{--                                           value="{{old('trainee_type_no',isset($traineetype->trainee_type_no) ? $traineetype->trainee_type_no : '')}}"--}}
{{--                                           autocomplete="off">--}}
{{--                                </div>--}}
{{--                            </div>--}}
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Trainee Type Name</label>
                                    <input type="text" id="trainee_type_name" name="trainee_type_name"
                                           class="form-control" required
                                           value="{{old('trainee_type_name',isset($traineetype->trainee_type) ? $traineetype->trainee_type : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Trainee Type Name (Bangla)</label>
                                    <input type="text" id="trainee_type_name_bn" name="trainee_type_name_bn"
                                           class="form-control"
                                           value="{{old('trainee_type_name_bn',isset($traineetype->trainee_type_bn) ? $traineetype->trainee_type_bn : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="mb-1 required">Trainee Type</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="trainee_type_yn"
                                                   id="trainee_type_yes"
                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                {{!isset($traineetype->cpa_yn) ? 'checked' : ''}}
                                                {{isset($traineetype->cpa_yn) && ($traineetype->cpa_yn == \App\Enums\YesNoFlag::YES) ? 'checked' : ''}}
                                            />
                                            <label class="form-check-label">CPA</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="trainee_type_yn"
                                                   id="trainee_type_no"
                                                   value="{{\App\Enums\YesNoFlag::NO}}"
                                                {{isset($traineetype->cpa_yn) && ($traineetype->cpa_yn == \App\Enums\YesNoFlag::NO) ? 'checked' : ''}}
                                            />
                                            <label class="form-check-label">Outsider</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="mb-1 required">Status</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="active_status" id="active_status_yes"
                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                   checked
                                                   @if(isset($traineetype->active_yn) && $traineetype->active_yn == "Y") checked @endif/>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="active_status" value="{{ \App\Enums\YesNoFlag::NO }}"
                                                   id="active_status_no"
                                                   @if(isset($traineetype->active_yn) && $traineetype->active_yn == "N") checked @endif/>
                                            <label class="form-check-label">In-Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($traineetype)
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/trainee-type')}}">
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
            @include('training.setup.traineetype.traineetype_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function traineetypeList() {
            $('#trainee-type-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + '/trainee-type-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'trainee_type', name: 'trainee_type', searchable: true},
                    {data: 'active_yn', name: 'active_yn', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function () {
            traineetypeList();
        });
    </script>

@endsection


