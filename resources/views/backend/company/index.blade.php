@extends('backend.layouts.master')

@section('main-content')
<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="row">
        <div class="col-md-12">
            @include('backend.layouts.notification')
        </div>
    </div>
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary float-left">Company List</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="user-dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Photo</th>
                        <th>Tax ID</th>
                        <th>No. of Employees</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>S.N.</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Photo</th>
                        <th>Tax ID</th>
                        <th>No. of Employees</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($users as $key => $user)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $user->company->company_name ?? '-' }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            @if(!empty($user->company->company_logo))
                                <img src="{{ asset($user->company->company_logo) }}" class="img-fluid rounded-circle" style="max-width:50px" alt="logo">
                            @else
                                <img src="{{ asset('backend/img/avatar.png') }}" class="img-fluid rounded-circle" style="max-width:50px" alt="default-logo">
                            @endif
                        </td>
                        <td>{{ $user->company->tax_identification_number ?? '-' }}</td>
                        <td>{{ $user->company->employee->count() ?? 0 }}</td>
                        <td>
                            @if($user->status == 'active')
                                <span class="badge badge-success">{{ $user->status }}</span>
                            @else
                                <span class="badge badge-warning">{{ $user->status }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.companies.show', $user->id) }}" class="btn btn-info btn-sm" title="View Company">
                                <i class="fas fa-eye"></i>
                            </a>
                            {{-- Optional edit/delete if needed --}}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <span style="float:right">{{ $users->links() }}</span>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css" />
<style>
    div.dataTables_wrapper div.dataTables_paginate {
        display: none;
    }
</style>
@endpush

@push('scripts')
<script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
<script src="{{ asset('backend/js/demo/datatables-demo.js') }}"></script>
<script>
    $('#user-dataTable').DataTable({
        "columnDefs": [{
            "orderable": false,
            "targets": [7]
        }]
    });
</script>
@endpush
