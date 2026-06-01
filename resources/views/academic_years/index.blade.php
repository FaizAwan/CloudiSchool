@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<div class="container-fluid">
  <div class="row g-3">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header"><strong>Add Academic Year</strong></div>
        <div class="card-body">
          <form method="POST" action="{{ route('academic-years.store') }}">
            @csrf
            <div class="mb-2">
              <label class="form-label">From</label>
              <div class="d-flex gap-2">
                <select class="form-select" name="fromMonth">
                  @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                    <option value="{{$m}}">{{$m}}</option>
                  @endforeach
                </select>
                <select class="form-select" name="fromYear">
                  @for($y=2023;$y<=2035;$y++)
                    <option value="{{$y}}">{{$y}}</option>
                  @endfor
                </select>
              </div>
            </div>
            <div class="mb-2">
              <label class="form-label">To</label>
              <div class="d-flex gap-2">
                <select class="form-select" name="toMonth">
                  @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                    <option value="{{$m}}">{{$m}}</option>
                  @endforeach
                </select>
                <select class="form-select" name="toYear">
                  @for($y=2023;$y<=2035;$y++)
                    <option value="{{$y}}">{{$y}}</option>
                  @endfor
                </select>
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" name="is_active">
                <option value="no">No</option>
                <option value="yes">Yes</option>
                <option value="closed">Closed</option>
              </select>
            </div>
            <button class="btn btn-primary w-100" type="submit">Add</button>
          </form>
        </div>
      </div>
    </div>
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <strong>Academic Years</strong>
          @if(session('message'))<span class="text-success">{{ session('message') }}</span>@endif
          @if(session('errorMessage'))<span class="text-danger">{{ session('errorMessage') }}</span>@endif
        </div>
        <div class="card-body">
          <table id="ayTable" class="table table-striped table-hover" style="width:100%">
            <thead>
              <tr>
                <th>Label</th>
                <th>Status</th>
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
<div class="modal fade" id="ayEditModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edit Academic Year</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="ayEditForm">
        @csrf
        <div class="modal-body">
          <input type="hidden" id="ayId">
          <div class="mb-2">
            <label class="form-label">From</label>
            <div class="d-flex gap-2">
              <select class="form-select" id="ayFromMonth">
                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                  <option value="{{$m}}">{{$m}}</option>
                @endforeach
              </select>
              <select class="form-select" id="ayFromYear">
                @for($y=2023;$y<=2035;$y++)
                  <option value="{{$y}}">{{$y}}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label">To</label>
            <div class="d-flex gap-2">
              <select class="form-select" id="ayToMonth">
                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                  <option value="{{$m}}">{{$m}}</option>
                @endforeach
              </select>
              <select class="form-select" id="ayToYear">
                @for($y=2023;$y<=2035;$y++)
                  <option value="{{$y}}">{{$y}}</option>
                @endfor
              </select>
            </div>
          </div>
          <div class="mb-2">
            <label class="form-label">Status</label>
            <select class="form-select" id="ayStatus">
              <option value="no">No</option>
              <option value="yes">Yes</option>
              <option value="closed">Closed</option>
            </select>
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
  var table = $('#ayTable').DataTable({
    serverSide: true,
    processing: true,
    ajax: {
      url: '{{ route('academic-years.list') }}',
      type: 'GET'
    },
    columns: [
      { data: 'label', name: 'label' },
      { data: 'is_active', name: 'is_active', orderable:false, searchable:false },
      { data: 'action', name: 'action', orderable:false, searchable:false }
    ]
  });

  // Open edit and populate
  $(document).on('click','.ay-edit', function(){
    var btn = $(this);
    $('#ayId').val(btn.data('id'));
    $('#ayFromMonth').val(btn.data('frommonth'));
    $('#ayFromYear').val(btn.data('fromyear'));
    $('#ayToMonth').val(btn.data('tomonth'));
    $('#ayToYear').val(btn.data('toyear'));
    $('#ayStatus').val(btn.data('is_active'));
  });

  // Save edit
  $('#ayEditForm').on('submit', function(e){
    e.preventDefault();
    var id = $('#ayId').val();
    $.ajax({
      url: '{{ url('academic-years') }}/'+id,
      type: 'POST',
      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      data: {
        _token: '{{ csrf_token() }}',
        _method: 'PUT',
        fromMonth: $('#ayFromMonth').val(),
        fromYear: $('#ayFromYear').val(),
        toMonth: $('#ayToMonth').val(),
        toYear: $('#ayToYear').val(),
        is_active: $('#ayStatus').val()
      },
      success: function(){ $('#ayEditModal').modal('hide'); table.ajax.reload(null,false); },
      error: function(xhr){
        alert('Failed to update');
      }
    });
  });

  // Delete
  $(document).on('click','.ay-delete', function(){
    if(!confirm('Delete this academic year?')) return;
    var id = $(this).data('id');
    $.ajax({
      url: '{{ url('academic-years') }}/'+id,
      type: 'POST',
      data: { 
        _token: '{{ csrf_token() }}',
        _method: 'DELETE' 
      },
      success: function(res){ table.ajax.reload(null,false); },
      error: function(){ alert('Failed to delete'); }
    });
  });
});
</script>
@endsection
