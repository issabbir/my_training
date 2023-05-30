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
                    <h4 class="card-title">Pre-Requisite Entry</h4>
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
                        @if(isset($preRequisiteEntry->requsit_id)) action="{{route('pre-requisite-entry.pre-requisite-entry-update',[$preRequisiteEntry->requsit_id])}}"
                        @else action="{{route('pre-requisite-entry.pre-requisite-entry-post')}}" @endif method="post">
                        @csrf
                        @if (isset($preRequisiteEntry->requsit_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="required">Pre-Requisite Name</label>
                                    <input type="text" id="pre_requisite_name" name="pre_requisite_name"
                                       class="form-control" required
                                       value="{{old('pre_requisite_name',isset($preRequisiteEntry->pre_requsit_name) ? $preRequisiteEntry->pre_requsit_name : '')}}"
                                       autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="remarks">Remarks</label>
                                    <textarea placeholder="Remarks"
                                          rows="3" wrap="soft"
                                          name="remarks"
                                          class="form-control"
                                          id="remarks">{{old('remarks', isset($preRequisiteEntry->remarks) ? $preRequisiteEntry->remarks :'')}}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="active" class="required">Active</label>
                                    <ul class="list-unstyled mb-0">
                                        <li class="d-inline-block mr-2 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="active_yn"
                                                           id="active_y"
                                                           value="{{ \App\Enums\YesNoFlag::YES }}"
                                                        {{!isset($preRequisiteEntry->active_yn) ? 'checked' : ''}}
                                                        {{isset($preRequisiteEntry->active_yn) && ($preRequisiteEntry->active_yn == \App\Enums\YesNoFlag::YES) ? 'checked' : ''}}
                                                    >
                                                    <label class="custom-control-label" for="active_y">Yes</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                        <li class="d-inline-block mr-2 mb-1">
                                            <fieldset>
                                                <div class="custom-control custom-radio">
                                                    <input type="radio" class="custom-control-input" name="active_yn"
                                                           id="active_n"
                                                           value="{{\App\Enums\YesNoFlag::NO}}"
                                                        {{isset($preRequisiteEntry->active_yn) && ($preRequisiteEntry->active_yn == \App\Enums\YesNoFlag::NO) ? 'checked' : ''}}
                                                    >
                                                    <label class="custom-control-label" for="active_n">No</label>
                                                </div>
                                            </fieldset>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-12 d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary mb-1 mr-1">Submit</button>
                            </div>

                        </div>
                    </form>
                </div>

            </div>
            @include('training.setup.pre-requisiteentry.pre-requisite_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function preRequisiteEntryList() {
            $('#pre-requisite-entry-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + '/pre-requisite-entry-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'pre_requsit_name', name: 'pre_requsit_name', searchable: true},
                    {data: 'active_yn', name: 'active_yn', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function () {
            preRequisiteEntryList();
        });
    </script>

@endsection


