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
                    <h4 class="card-title">Training Media Entry</h4>
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
                        @if(isset($trainingmedia->training_media_id)) action="{{route('training-media.training-media-update',[$trainingmedia->training_media_id])}}"
                        @else action="{{route('training-media.training-media-post')}}" @endif method="post">
                        @csrf
                        @if (isset($trainingmedia->training_media_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Media Name</label>
                                    <input type="text" id="media_name" name="media_name"
                                           class="form-control" required
                                           value="{{old('media_name',isset($trainingmedia->media_name) ? $trainingmedia->media_name : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Media Name (Bangla)</label>
                                    <input type="text" id="media_name_bn" name="media_name_bn"
                                           class="form-control"
                                           value="{{old('media_name_bn',isset($trainingmedia->media_name_bn) ? $trainingmedia->media_name_bn : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="mb-1">Status</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="active_status" id="active_status_yes"
                                                   value="{{ \App\Enums\YesNoFlag::YES }}"
                                                   checked
                                                   @if(isset($trainingmedia->active_yn) && $trainingmedia->active_yn == "Y") checked @endif/>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio"
                                                   name="active_status" value="{{ \App\Enums\YesNoFlag::NO }}"
                                                   id="active_status_no"
                                                   @if(isset($trainingmedia->active_yn) && $trainingmedia->active_yn == "N") checked @endif/>
                                            <label class="form-check-label">In-Active</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea
                                              rows="3" wrap="soft"
                                              name="remarks"
                                              class="form-control"
                                              id="remarks">{{old('remarks',isset($trainingmedia->remarks) ? $trainingmedia->remarks : '')}}</textarea>
                                </div>
                            </div>
                        </div>
                        @if($trainingmedia)
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/training-media')}}">
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
            @include('training.setup.trainingmedia.trainingmedia_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function trainingmediaList() {
            $('#training-media-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + '/training-media-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'media_name', name: 'media_name', searchable: true},
                    {data: 'active_yn', name: 'active_yn', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function () {
            trainingmediaList();
        });
    </script>

@endsection


