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
                    <h4 class="card-title">Exam Type Entry</h4>
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
                        @if(isset($examtype->exam_type_id)) action="{{route('exam-type.exam-type-update',[$examtype->exam_type_id])}}"
                        @else action="{{route('exam-type.exam-type-post')}}" @endif method="post">
                        @csrf
                        @if (isset($examtype->exam_type_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="required">Exam Type Name</label>
                                    <input type="text" id="exam_type_name" name="exam_type_name"
                                           class="form-control" required
                                           value="{{old('exam_type_name',isset($examtype->exam_type_name) ? $examtype->exam_type_name : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Exam Type Name (Bangla)</label>
                                    <input type="text" id="exam_type_name_bn" name="exam_type_name_bn"
                                           class="form-control"
                                           value="{{old('exam_type_name_bn',isset($examtype->exam_type_bn) ? $examtype->exam_type_bn : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label class="mb-1 required">Status</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="active_status" id="active_status_yes"
                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                   checked
                                                   @if(isset($examtype->active_yn) && $examtype->active_yn == "Y") checked @endif/>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="active_status" value="{{ \App\Enums\YesNoFlag::NO }}"
                                                   id="active_status_no"
                                                   @if(isset($examtype->active_yn) && $examtype->active_yn == "N") checked @endif/>
                                            <label class="form-check-label">In-Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea rows="2" wrap="soft"
                                              name="remarks"
                                              class="form-control"
                                              id="remarks">{{old('remarks',isset($examtype->remarks) ? $examtype->remarks : '')}}</textarea>
                                </div>
                            </div>
                        </div>
                        @if($examtype)
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/exam-type')}}">
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
            @include('training.setup.examtype.examtype_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function examtypeList() {
            $('#exam-type-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + '/exam-type-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'exam_type_name', name: 'exam_type_name', searchable: true},
                    {data: 'active_yn', name: 'active_yn', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function () {
            examtypeList();
        });
    </script>

@endsection


