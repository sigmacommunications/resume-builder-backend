@extends('backend.layouts.master')

@section('main-content')
<div class="container-fluid">

    @include('backend.layouts.notification')

    <!-- Company Basic Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Company Details: {{ $company->company->company_name ?? $company->name }}</h5>
        </div>
        <div class="card-body">
            <p><strong>Email:</strong> {{ $company->email }}</p>
            <p><strong>Tax ID:</strong> {{ $company->company->tax_identification_number ?? '-' }}</p>
            <p><strong>Total Employees:</strong> {{ $company->company->employee->count() ?? 0 }}</p>
            <p><strong>Status:</strong>
                @if($company->status == 'active')
                    <span class="badge badge-success">Active</span>
                @else
                    <span class="badge badge-warning">Inactive</span>
                @endif
            </p>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="companyTab" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="employees-tab" data-toggle="tab" href="#employees" role="tab">Employees</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="saved-templates-tab" data-toggle="tab" href="#saved-templates" role="tab">Saved Templates</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="assigned-templates-tab" data-toggle="tab" href="#assigned-templates" role="tab">Assigned Templates</a>
      </li>
    </ul>

    <div class="tab-content" id="companyTabContent">
      <!-- Employees Tab -->
      <div class="tab-pane fade show active" id="employees" role="tabpanel">
        <div class="card shadow mb-4">
          <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Employees ({{ $company->company->employee->count() }})</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="employeesTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Joining Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($company->company->employee as $i => $emp)
                    <tr>
                      <td>{{ $i+1 }}</td>
                      <td>{{ $emp->full_name }}</td>
                      <td>{{ $emp->employee_email }}</td>
                      <td>{{ $emp->department->department_name ?? '-' }}</td>
                      <td>{{ $emp->joining_date }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Saved Templates Tab -->
      <div class="tab-pane fade" id="saved-templates" role="tabpanel">
        <div class="row">
          <!-- Mail Templates -->
          <div class="col-lg-4">
            <div class="card mb-4">
              <div class="card-header bg-info text-white">
                Mail Templates ({{ optional($company->company->mails)->count() ?? 0 }})
              </div>
              <div class="card-body">
                <ul class="list-group">
					@if(!empty($company->company->mails) && count($company->company->mails) > 0)
					  @forelse($company->company->mails as $mail)
						<li class="list-group-item">
						  {{ $mail->title }}
						  <small class="text-muted d-block">Template: {{ $mail->template->heading ?? '-' }}</small>
						</li>
					  @empty
						<li class="list-group-item">No Mail Templates</li>
					  @endforelse
					@endif
                </ul>
              </div>
            </div>
          </div>
          <!-- Career Templates -->
          <div class="col-lg-4">
            <div class="card mb-4">
              <div class="card-header bg-warning text-white">
                Career Templates ({{ optional($company->careers)->count() ?? 0 }}) 
              </div>
              <div class="card-body">
                <ul class="list-group">
					@if(!empty($company->careers) && count($company->careers) > 0)
                  @forelse($company->careers as $career)
                    <li class="list-group-item">
                      {{ $career->title }}
                      <small class="text-muted d-block">Template: {{ $career->template->title ?? '-' }}</small>
                    </li>
                  @empty
                    <li class="list-group-item">No Career Templates</li>
                  @endforelse
					@endif
                </ul>
              </div>
            </div>
          </div>
          <!-- Cover Letter Templates -->
          <div class="col-lg-4">
            <div class="card mb-4">
              <div class="card-header bg-secondary text-white">
                Cover Letter Templates ({{ optional($company->coverLetters)->count() ?? 0 }}) 
              </div>
              <div class="card-body">
                <ul class="list-group">
					@if($company->coverLetters)
                  @forelse($company->coverLetters as $cl)
                    <li class="list-group-item">
                      {{ $cl->title }}
                      <small class="text-muted d-block">Template: {{ $cl->template->title ?? '-' }}</small>
                    </li>
                  @empty
                    <li class="list-group-item">No Cover Letter Templates</li>
                  @endforelse
					@endif
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Assigned Templates Tab -->
      <div class="tab-pane fade" id="assigned-templates" role="tabpanel">
        <div class="card shadow mb-4">
          <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Assigned Templates</h6>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered" id="assignedTable" width="100%" cellspacing="0">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Type</th>
                    <th>Template</th>
                    <th>Assigned To</th>
                    <th>Assigned At</th>
                  </tr>
                </thead>
                <tbody>
					@if($company->company->templateAssigns)
                  @foreach($company->company->templateAssigns as $j => $assign)
                    <tr>
                      <td>{{ $j+1 }}</td>
                      <td>{{ ucfirst($assign->type) }}</td>
                      <td>{{ $assign->assignable->title ?? '-' }}</td>
                      <td>{{ $assign->employee->name ?? '-' }}</td>
                      <td>{{ $assign->created_at->format('Y-m-d') }}</td>
                    </tr>
                  @endforeach
					@endif
                  @if(!empty($company->templateAssigns) && $company->templateAssigns->isNotEmpty())
					{{-- Show assigned templates --}}
					@else
					<tr>
						<td colspan="5" class="text-center">No Assigned Templates</td>
					</tr>
					@endif
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

</div>
@endsection

@push('styles')
  <link href="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
  <style>
    .nav-tabs .nav-link { font-weight: 500; }
  </style>
@endpush

@push('scripts')
  <script src="{{ asset('backend/vendor/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('backend/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
  <script>
    $('#employeesTable, #assignedTable').DataTable({
      "ordering": false,
      "paging":   false,
      "info":     false,
      "searching": false
    });
  </script>
@endpush
