@extends('layouts.default')

@section('title')

@endsection

@section('header-style')
    <!--Load custom style link or css-->
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            @include('training.assigntrainee.assigntrainee_list')
        </div>
    </div>
@endsection

@section('footer-script')

    <script type="text/javascript">
        $(document).ready(function () {
            assignTraineeList();
        });

        function assignTraineeList() {
            $('#assign-dept-list').DataTable({
                processing: true,
                serverSide: true,
                // order: [],
                order: [ 2, 'desc' ],
                ajax: {
                    url: APP_URL + '/assign-trainee-datatable-list',
                    'type': 'POST',
                    'headers': {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false},
                    {data: 'training_name', name: 'training_name', searchable: true},
                    {data: 'department_name', name: 'department_name', searchable: true},
                    {data: 'assignment_date', name: 'assignment_date', searchable: true},
                    {data: 'notification_step_name', name: 'notification_step_name', searchable: true},
                   /* {data: 'remarks', name: 'remarks', searchable: true},*/
                    {data: 'action', name: 'action', searchable: false},
                ]
            });
        };
    </script>

@endsection
