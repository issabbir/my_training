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
                        <form @if (isset($expertiseView->trainer_exp_id)) action="{{route('trainer-information.trainer-expertise-update',[$trainer_id,$expertiseView->trainer_exp_id])}}"
                              @else action="{{route('trainer-information.trainer-expertise-post',$trainer_id)}}" @endif method="post">
                            @csrf
                            @if (isset($expertiseView->trainer_exp_id))
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
                                                        <h6 class="pb-50"><b>Trainer Expertise Details</b></h6>
                                                        <div class="row">
                                                            <div class="col-sm-6">
                                                                <div class="form-group">
                                                                    <label for="expertise_id" class="required">Expertise Name</label>
                                                                    <select class="custom-select select2 form-control" required
                                                                            id="expertise_id" name="expertise_id">
                                                                        <option value="">Select One</option>
                                                                        @foreach($lExpertise as $value)
                                                                            <option value="{{$value->expertise_id}}"
                                                                                {{isset($expertiseView->expertise_id) && $expertiseView->expertise_id == $value->expertise_id ? 'selected' : ''}}
                                                                            >{{$value->expertise_name}}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <small class="text-muted form-text"> </small>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="remarks" class="required">Description</label>
                                                                    <textarea required placeholder="Description"
                                                                      rows="3" wrap="soft"
                                                                      name="remarks"
                                                                      class="form-control"
                                                                      id="coverage">{{old('remarks', isset($expertiseView->remarks) ? $expertiseView->remarks :'')}}</textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-6">
                                                                <label for="training_active_yn" class="required">Active?</label>
                                                                <div class="form-group">
                                                                    <div class="form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="training_active_yn" id="training_active_yes" value="{{\App\Enums\YesNoFlag::YES}}"
                                                                           @if($expertiseView && $expertiseView->active_yn == \App\Enums\YesNoFlag::YES)
                                                                               checked
                                                                               @elseif(!$expertiseView)
                                                                               checked
                                                                            @endif
                                                                        >
                                                                        <label class="form-check-label" for="active"> Yes</label>
                                                                    </div>
                                                                    <div class="form-check-inline">
                                                                        <input class="form-check-input" type="radio" name="training_active_yn" id="training_active_no" value="{{\App\Enums\YesNoFlag::NO}}"
                                                                           @if($expertiseView && $expertiseView->active_yn != \App\Enums\YesNoFlag::YES)
                                                                               checked
                                                                            @endif
                                                                        >
                                                                        <label class="form-check-label" for="Inactive"> No</label>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="trainer_id" value="{{$trainer_id}}">
                                                        </div>
                                                    </fieldset>

                                                </div>

                                            </div>
                                        </div>
                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary mr-1 mb-1">Save</button>
                                            <button type="reset" class="btn btn-light-secondary mr-1 mb-1">Reset
                                            </button>
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
            @include('training.trainerinfo.trainerexpertise_list')
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
