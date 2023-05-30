@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            @include('training.assigntraineeupdate.assigntraineeupdate_list')
        </div>
    </div>
@endsection

@section('footer-script')
    <script type="text/javascript">
        $(document).ready(function () {
            assignTraineeUpdateList();
        });

        function assignTraineeUpdateList() {
            $('#assign-dept-list').DataTable({
                processing: true,
                serverSide: true,
                order: [],
                ajax: {
                    url: APP_URL + '/assign-trainee-update-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'training_name', name: 'training_name', searchable: true},
                    {data: 'assignment_date', name: 'assignment_date', searchable: true},
                    {data: 'trainee_assign_date', name: 'trainee_assign_date', searchable: true},
                   /* {data: 'remarks', name: 'remarks', searchable: true},*/
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };
    </script>

@endsection
