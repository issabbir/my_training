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
                    <h4 class="card-title">Location Entry</h4>
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
                        @if(isset($locationentry->location_id)) action="{{route('location-entry.location-entry-update',[$locationentry->location_id])}}"
                        @else action="{{route('location-entry.location-entry-post')}}" @endif method="post">
                        @csrf
                        @if (isset($locationentry->location_id))
                            @method('PUT')
                        @endif
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Location Name</label>
                                    <input type="text" id="location_name" name="location_name"
                                           class="form-control" required
                                           value="{{old('location_name',isset($locationentry->location_name) ? $locationentry->location_name : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Location Name (Bangla)</label>
                                    <input type="text" id="location_name_bng" name="location_name_bng"
                                           class="form-control"
                                           value="{{old('location_name_bng',isset($locationentry->location_bng) ? $locationentry->location_bng : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Building Name</label>
                                    <input type="text" id="building_name" name="building_name"
                                           class="form-control" required
                                           value="{{old('building_name',isset($locationentry->building_name) ? $locationentry->building_name : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Building Name (Bangla)</label>
                                    <input type="text" id="building_name_bng" name="building_name_bng"
                                           class="form-control"
                                           value="{{old('building_name_bng',isset($locationentry->building_name_bng) ? $locationentry->building_name_bng : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Floor Name</label>
                                    <input type="text" id="floor_name" name="floor_name"
                                           class="form-control" required
                                           value="{{old('floor_name',isset($locationentry->floor_name) ? $locationentry->floor_name : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label>Floor Name (Bangla)</label>
                                    <input type="text" id="floor_name_bng" name="floor_name_bng"
                                           class="form-control"
                                           value="{{old('floor_name_bng',isset($locationentry->floor_name_bng) ? $locationentry->floor_name_bng : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Location Address</label>
                                    <input type="text" id="location_address" name="location_address"
                                           class="form-control" required
                                           value="{{old('location_address',isset($locationentry->location_address) ? $locationentry->location_address : '')}}"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label class="required">Country</label>
                                    <select required class="custom-select select2" name="country_name"
                                            id="country_name">
                                        <option value="">Select One</option>
                                        @foreach($countrylist as $value)
                                            <option value="{{$value->country_id}}"
                                                {{isset($locationentry->country_id) && $locationentry->country_id == $value->country_id ? 'selected' : ''}}
                                            >{{$value->country}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <textarea rows="2" wrap="soft"
                                              name="remarks"
                                              class="form-control"
                                              id="remarks">{{old('remarks',isset($locationentry->remarks) ? $locationentry->remarks : '')}}</textarea>
                                </div>
                            </div>
                        </div>
                        @if($locationentry)
                            <div class="row">
                                <div class="col-md-12 text-right" id="cancel">
                                    <button type="submit" id="update"
                                            class="btn btn-primary mb-1">
                                        Update
                                    </button>
                                    <a href="{{url('/location-entry')}}">
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
            @include('training.setup.locationentry.locationentry_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">

        function locationentryList() {
            $('#location-entry-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + '/location-entry-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'location_name', name: 'location_name', searchable: true},
                    {data: 'building_name', name: 'building_name', searchable: true},
                    {data: 'floor_name', name: 'floor_name', searchable: true},
                    {data: 'location_address', name: 'location_address', searchable: true},
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function () {
            locationentryList();
        });
    </script>

@endsection


