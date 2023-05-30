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
                    <h4 class="card-title">Course Entry</h4>
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
                          @if(isset($courseentry->course_id)) action="{{route('course-entry.course-entry-update',[$courseentry->course_id])}}"
                          @else action="{{route('course-entry.course-entry-post')}}" @endif method="post">
                        @csrf
                        @if (isset($courseentry->course_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Course Name</label>
                                    <input type="text" id="course_name" name="course_name"
                                           class="form-control" required
                                           value="{{old('course_name',isset($courseentry->course_name) ? $courseentry->course_name : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Course Name (Bangla)</label>
                                    <input type="text" id="course_name_bn" name="course_name_bn"
                                           class="form-control"
                                           value="{{old('course_name_bn',isset($courseentry->course_name_bn) ? $courseentry->course_name_bn : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Description</label>
                                    <input type="text" id="description" name="description"
                                           class="form-control" required
                                           value="{{old('description',isset($courseentry->description) ? $courseentry->description : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Duration</label>
                                    <input type="number" id="course_duration" name="course_duration"
                                           class="form-control" required
                                           oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"
                                           maxlength = "3"
                                           value="{{old('course_duration',isset($courseentry->course_duration) ? $courseentry->course_duration : '')}}"
                                           autocomplete="off">
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
                                                   @if(isset($courseentry->active_yn) && $courseentry->active_yn == "Y") checked @endif/>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="active_status" value="{{ \App\Enums\YesNoFlag::NO }}"
                                                   id="active_status_no"
                                                   @if(isset($courseentry->active_yn) && $courseentry->active_yn == "N") checked @endif/>
                                            <label class="form-check-label">In-Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if(isset($courseentry->course_file))
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="attachment" class="">Attachment</label>
                                        <input type="file" class="form-control" id="attachment" name="attachment"/>
                                    </div>
                                    <a href="{{ route('course-entry.course-entry-file-download', [$courseentry->course_id]) }}"
                                       target="_blank">{{$courseentry->course_file_name}}</a>

                                </div>
                            @else
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <label for="attachment" class="">Attachment</label>
                                        <input type="file" class="form-control" id="attachment" name="attachment"/>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @if($courseentry)
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/course-entry')}}">
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
            @include('training.setup.courseentry.courseentry_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function courseentryList() {
            $('#course-entry-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + '/course-entry-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'course_name', name: 'course_name', searchable: true},
                    {data: 'course_duration', name: 'course_duration', searchable: true},
                    {data: 'active_yn', name: 'active_yn', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function () {
            courseentryList();
        });
    </script>

@endsection


