@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <section id="horizontal-vertical">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Trainer Information List</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body card-dashboard">
                                    <div class="table-responsive">
                                        <table id="trainer-info-list" class="table table-sm datatable mdl-data-table dataTable">
                                            <thead>
                                            <tr>
                                            <tr>
                                                <th>#</th>
                                                <th>Trainer No</th>
                                                <th>Trainer Name</th>
                                                <th>Contact No</th>
                                                <th>Email</th>
                                                <th>Designation</th>
                                                <th>NID</th>
                                                <th>Workplace</th>
                                                {{--<th>Expertise</th>--}}
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
@endsection

@section('footer-script')
    <script type="text/javascript">

        function trainerInfoList() {
            $('#trainer-info-list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: APP_URL + '/trainer-info-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'trainer_no', name: 'trainer_no', searchable: true},
                    {data: 'trainer_name', name: 'trainer_name', searchable: true},
                    {data: 'mobile_no', name: 'mobile_no', searchable: true},
                    {data: 'email_add', name: 'email_add', searchable: true},
                    {data: 'trainer_designation', name: 'trainer_designation', searchable: true},
                    {data: 'nid', name: 'nid', searchable: true},
                    {data: 'workplace', name: 'workplace', searchable: true},
                   /* {data: 'expertise', name: 'expertise', searchable: true},*/
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };

        $(document).ready(function() {
            trainerInfoList();
        });
    </script>

@endsection

