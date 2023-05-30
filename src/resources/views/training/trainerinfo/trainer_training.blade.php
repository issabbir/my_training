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
                    <h4 class="card-title">Trainer Information</h4><!---->
                    <hr>
                    @if(Session::has('message'))
                        <div class="alert {{Session::get('m-class') ? Session::get('m-class') : 'alert-danger'}} show" role="alert">
                            {{ Session::get('message') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                @endif
                <!-- vertical Wizard start-->
                    <section id="vertical-wizard">
                        <form enctype="multipart/form-data" @if (isset($trainingView->trainer_training_id)) action="{{route('trainer-information.trainer-training-update',[$trainer_id,$trainingView->trainer_training_id])}}" @else action="{{route('trainer-information.trainer-training-post',$trainer_id)}}" @endif method="post">
                            @csrf
                            @if (isset($trainingView->trainer_training_id))
                                @method('PUT')
                            @endif
                            <div class="card">
                                <div class="card-content">
                                    <div class="row pills-stacked">
                                        <div class="col-md-3 col-sm-12 border-right pr-md-0">
                                            @include('tab')
                                        </div>
                                        <div class="col-md-9 col-sm-12">
                                            <div class="tab-content bg-transparent shadow-none">
                                                <div role="tabpanel" class="tab-pane active" id="vertical-pill-1"
                                                     aria-labelledby="stacked-pill-1"
                                                     aria-expanded="true">
                                                    <fieldset class="pt-0">
                                                        <h6 class="pb-50"><b>Training Details</b></h6>

                                                        @if (isset ($trainerDetails->internal_yn))
                                                            @if($trainerDetails->internal_yn == 'Y')
                                                                <div class="table-responsive">
                                                                    <table width="100%"  class="table scroll-horizontal-vertical">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>SL</th>
                                                                            <th>Training Name</th>
                                                                            <th>Training Institute</th>
                                                                            <th>Training type</th>
                                                                            <th>Training Description</th>
                                                                            <th>Training Duration</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @if (isset($empTrainingList))
                                                                            @php $i=1 @endphp
                                                                            @foreach($empTrainingList as $value)
                                                                                <tr>
                                                                                    <td>{{$i++}}</td>
                                                                                    <td>{{ strtolower ($value->training_name) }}</td>
                                                                                    <td>{{strtolower($value->training_institute)}}</td>
                                                                                    <td>{{strtolower($value->training_type)}}</td>
                                                                                    <td>{{strtolower($value->training_description)}}</td>
                                                                                    <td>{{strtolower($value->training_duration)}}</td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @endif
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @else
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="course_name" class="required">Course Name </label>
                                                                            <input class="form-control" name="course_name" id="course_name" required
                                                                                   value="{{old('course_name',isset($trainingView->training_name) ? $trainingView->training_name : '')}}" type="text">
                                                                            <small class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="course_name_bn" class="">Course Name Bangla</label>
                                                                            <input class="form-control" name="course_name_bn" id="course_name_bn"
                                                                                   value="{{old('course_name_bn',isset($trainingView->training_name_bn) ? $trainingView->training_name_bn : '')}}" type="text">
                                                                            <small class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="institute" class="required">Institute Name</label>
                                                                            <input class="form-control" name="institute" id="institute" required
                                                                                   value="{{old('institute',isset($trainingView->institute_name) ? $trainingView->institute_name : '')}}" type="text">
                                                                            <small class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="institute_bn" class="">Institute Name Bangla</label>
                                                                            <input class="form-control" name="institute_bn" id="institute_bn"
                                                                                   value="{{old('institute_bn',isset($trainingView->institute_bn) ? $trainingView->institute_bn : '')}}" type="text">
                                                                            <small class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="institute_address" class="required">Institute Address</label>
                                                                            <textarea required placeholder="Institute Address"
                                                                                      rows="3" wrap="soft"
                                                                                      name="institute_address"
                                                                                      class="form-control"
                                                                                      id="institute_address">{{old('institute_address', isset($trainingView->institute_address) ? $trainingView->institute_address :'')}}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="Institute_address_bn" class="">Institute Address Bangla</label>
                                                                            <textarea  placeholder="Institute Address Bangla"
                                                                                       rows="3" wrap="soft"
                                                                                       name="Institute_address_bn"
                                                                                       class="form-control"
                                                                                       id="Institute_address_bn">{{old('Institute_address_bn', isset($trainingView->institute_address_bn) ? $trainingView->institute_address_bn :'')}}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="training_duration" class="">Training Duration</label>
                                                                            <input class="form-control" name="training_duration" id="training_duration"
                                                                                   value="{{old('training_duration',isset($trainingView->training_duration) ? $trainingView->training_duration : '')}}" type="text">
                                                                            <small class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="training_attachment" class="">Training Attachment</label>
                                                                            <input type="file" class="form-control" id="training_attachment" name="training_attachment" />
                                                                        </div>
                                                                        @if(isset($trainingView->training_attachment))
                                                                            <a href="{{ route('trainer-information.trainer-training-attach-download', [$trainingView->trainer_training_id]) }}" target="_blank">{{$trainingView->training_attachment_name}}</a>
                                                                        @endif
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="sponsor" class="">Sponsor</label>
                                                                            <input class="form-control" name="sponsor" id="sponsor"
                                                                                   value="{{old('sponsor',isset($trainingView->sponsor) ? $trainingView->sponsor : '')}}" type="text">
                                                                            <small class="text-muted form-text"> </small>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="coverage" class="required">Coverage</label>
                                                                            <textarea required placeholder="Coverage"
                                                                                      rows="3" wrap="soft"
                                                                                      name="coverage"
                                                                                      class="form-control"
                                                                                      id="coverage">{{old('coverage', isset($trainingView->coverage) ? $trainingView->coverage :'')}}</textarea>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="trainer_id" value="{{$trainer_id}}">
                                                                    <div class="col-12 d-flex justify-content-end">
                                                                        <button type="submit" class="btn btn-primary mr-1 mb-1 mt-1">Save</button>
                                                                        <button type="reset" class="btn btn-light-secondary mb-1 mt-1">Reset
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @else
                                                            <div class="row">
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="course_name" class="required">Course Name </label>
                                                                        <input class="form-control" name="course_name" id="course_name" required
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="course_name_bn" class="">Course Name Bangla</label>
                                                                        <input class="form-control" name="course_name_bn" id="course_name_bn"
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="institute" class="required">Institute Name</label>
                                                                        <input class="form-control" name="institute" id="institute" required
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="institute_bn" class="">Institute Name Bangla</label>
                                                                        <input class="form-control" name="institute_bn" id="institute_bn"
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="institute_address" class="required">Institute Address</label>
                                                                        <textarea required placeholder="Institute Address"
                                                                                  rows="3" wrap="soft"
                                                                                  name="institute_address"
                                                                                  class="form-control"
                                                                                  id="institute_address"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="Institute_address_bn" class="">Institute Address Bangla</label>
                                                                        <textarea  placeholder="Institute Address Bangla"
                                                                                   rows="3" wrap="soft"
                                                                                   name="Institute_address_bn"
                                                                                   class="form-control"
                                                                                   id="Institute_address_bn">{{old('Institute_address_bn', isset($trainingView->institute_address_bn) ? $trainingView->institute_address_bn :'')}}</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="training_duration" class="">Training Duration</label>
                                                                        <input class="form-control" name="training_duration" id="training_duration"
                                                                               type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="training_attachment" class="">Training Attachment</label>
                                                                        <input type="file" class="form-control" id="training_attachment" name="training_attachment" />
                                                                    </div>

                                                                </div>
                                                                <div class="col-sm-6">
                                                                    <div class="form-group">
                                                                        <label for="sponsor" class="">Sponsor</label>
                                                                        <input class="form-control" name="sponsor" id="sponsor"
                                                                                type="text">
                                                                        <small class="text-muted form-text"> </small>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="coverage" class="required">Coverage</label>
                                                                        <textarea required placeholder="Coverage"
                                                                                  rows="3" wrap="soft"
                                                                                  name="coverage"
                                                                                  class="form-control"
                                                                                  id="coverage"></textarea>
                                                                    </div>
                                                                </div>
                                                                <input type="hidden" name="trainer_id" value="{{$trainer_id}}">
                                                                <div class="col-12 d-flex justify-content-end">
                                                                    <button type="submit" class="btn btn-primary mr-1 mb-1 mt-1">Save</button>
                                                                    <button type="reset" class="btn btn-light-secondary mb-1 mt-1">Reset
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif


                                                    </fieldset>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                    <!-- vertical Wizard end-->

                </div>
                <!-- Table End -->
            </div>
            @include('training.trainerinfo.trainertraining_list')
        </div>
    </div>


@endsection

@section('footer-script')
    <script type="text/javascript">

        $(document).ready(function() {
            // script code
        });

    </script>

@endsection
