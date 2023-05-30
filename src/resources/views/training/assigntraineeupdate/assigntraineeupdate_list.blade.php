<div class="card">
    <div class="card-body">
        <section id="horizontal-vertical">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Trainee Assign List</h4>
                            <hr>
                            @if(Session::has('message'))
                                <div class="mb-0 alert {{Session::get('m-class') ? Session::get('m-class') : 'alert-danger'}} show"
                                     role="alert">
                                    {{ Session::get('message') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                        </div>
                        <div class="card-content">
                            <div class="card-body card-dashboard">
                                <div class="table-responsive">
                                    <table id="assign-dept-list"
                                           class="table table-sm datatable mdl-data-table dataTable">
                                        <thead>
                                        <tr>
                                        <tr>
                                            <th>#</th>
                                            <th>Training Name</th>
                                            <th>Department Assign Date</th>
                                            <th>Trainee Assign Date</th>
                                            {{--<th>Remarks</th>--}}
                                            <th>Action</th>
                                        </tr>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
