@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style>
#pTable { table-layout: fixed; width: 100% !important; }
#pTable th:first-child, #pTable td:first-child { width: 40px; text-align: center; }
#pTable th:last-child, #pTable td:last-child { width: 110px; }
#pTable td:last-child { overflow: visible !important; white-space: nowrap !important; }
.actions .btn { padding: 2px 6px !important; font-size: 12px !important; line-height: 1 !important; }
.actions .btn i { font-size: 14px !important; }
</style>
<div class="container-fluid">
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><strong>Add Parent</strong></div>
        <div class="card-body">
          <form id="pAddForm" method="POST" action="{{ route('parents.store') }}">
            @csrf
            <div class="mb-2">
              <label class="form-label">School *</label>
              @php $sid = auth()->check() ? (auth()->user()->tenant_id ?? auth()->user()->school_id) : null; $s = $sid ? DB::table('schools')->select('id','schoolName')->where('id',$sid)->first() : null; @endphp
              <input class="form-control" value="{{ $s ? $s->schoolName : ($sid ?: 'No school available') }}" readonly />
              <input type="hidden" name="school_id" value="{{$sid}}" />
            </div>
            <div class="mb-2">
              <label class="form-label">Parent Name *</label>
              <input class="form-control" name="parentName" required />
            </div>
            <div class="mb-2">
<label class="form-label">School Employee?</label>
              <select class="form-select" name="is_commandercityschool_employee">
                <option value="No">No</option>
                <option value="Yes">Yes</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Phone</label>
              <input class="form-control" name="phone" />
            </div>
            <div class="mb-3">
              <label class="form-label">Address</label>
              <input class="form-control" name="address" />
            </div>
            <button class="btn btn-primary w-100" type="submit">Add</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Parents</strong>
          @if(session('message'))<span class="text-success">{{ session('message') }}</span>@endif
          @if(session('errorMessage'))<span class="text-danger">{{ session('errorMessage') }}</span>@endif
        </div>
        <div class="card-body">
          <table id="pTable" class="table table-striped table-hover" style="width:100%">
            <thead>
              <tr>
                <th>#</th>
                <th>Parent</th>
                <th>Children</th>
                <th>Employee</th>
                <th>Phone</th>
                <th>School</th>
                <th>Address</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="pEditModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Parent</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="pEditForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="pId">
          <div class="mb-2">
            <label class="form-label">Parent Name *</label>
            <input class="form-control" id="pName" required />
          </div>
          <div class="mb-2">
<label class="form-label">School Employee?</label>
            <select class="form-select" id="pEmp">
              <option value="No">No</option>
              <option value="Yes">Yes</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Phone</label>
            <input class="form-control" id="pPhone" />
          </div>
          <div class="mb-2">
            <label class="form-label">Address</label>
            <input class="form-control" id="pAddress" />
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(function(){
  var table = $('#pTable').DataTable({
    serverSide: true,
    processing: true,
    ajax: { url: '{{ route('parents.list') }}', type: 'GET' },
    columnDefs: [
      { targets: 0, className: 'text-center', width: '40px', orderable:false, searchable:false },
      { targets: -1, className: 'text-nowrap', width: '110px' }
    ],
    columns: [
      { data: 'sn', name: 'sn', orderable:false, searchable:false },
      { data: 'parent', name: 'parent' },
      { data: 'children', name: 'children' },
      { data: 'employee', name: 'employee' },
      { data: 'phone', name: 'phone' },
      { data: 'school', name: 'school' },
      { data: 'address', name: 'address' },
      { data: 'action', name: 'action', orderable:false, searchable:false }
    ]
  });

  // Populate edit modal on click
  $(document).on('click','.p-edit', function(){
    var b = $(this);
    $('#pId').val(b.data('id'));
    $('#pName').val(b.data('name'));
    $('#pEmp').val(b.data('emp'));
    $('#pPhone').val(b.data('phone'));
    $('#pAddress').val(b.data('address'));
  });

  // Save edit via Ajax (POST + _method PUT)
  $('#pEditForm').on('submit', function(e){
    e.preventDefault();
    var id = $('#pId').val();
    $.ajax({
      url: '{{ url('parents') }}/'+id,
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      data: {
        _method: 'PUT',
        parentName: $('#pName').val(),
        is_commandercityschool_employee: $('#pEmp').val(),
        phone: $('#pPhone').val(),
        address: $('#pAddress').val()
      },
      success: function(){ $('#pEditModal').modal('hide'); table.ajax.reload(null,false); },
      error: function(){ alert('Failed to update'); }
    });
  });

  // Delete (POST + _method DELETE)
  $(document).on('click','.p-delete', function(e){
    e.preventDefault();
    if(!confirm('Delete this parent?')) return;
    var href = $(this).attr('href');
    $.ajax({
      url: href,
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      data: { _method: 'DELETE' },
      success: function(){ table.ajax.reload(null,false); },
      error: function(){ alert('Failed to delete'); }
    });
  });
});
</script>
@endsection
