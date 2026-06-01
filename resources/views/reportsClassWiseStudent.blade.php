@extends('layouts.app')

@section('content')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">


<div class="fluid-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div style="background: #1488CC;  /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #2B32B2, #1488CC);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #2B32B2, #1488CC); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
color:#fff; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> R E P O R T S &nbsp;  &nbsp;  C L A S S W I S E &nbsp; &nbsp; S T U D E N T S </div>
                <div style="background-color:#f6f9ff" class="card-body">
                
                    <div style="margin-top:20px;" class="row">
                        
                        <div class="col-md-12">
                            <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-header">
                                    <h5 style="font-weight:bold; color:#fff">Classwise Students List</h5>
                                    <span style="text-align:left">@if (session('message'))
                                      <div id="alert-message" class="alert alert-primary">
                                          {{ session('message') }}
                                      </div>
                                  @endif</span>
                                </div>
                                <div class="card-body">
                                    <div id="datatable-container">
                                    <div id="buttons-container" class="mb-3"></div>
                                        <table class="table table-striped table-hover" id="classesTable">
                                            <thead>
                                                <tr>
                                                    <th>S.NO</th>
                                                    <th>School</th>
                                                    <th>Class Name</th>
                                                    <th>Girls</th>
                                                    <th>Boys</th>
                                                    <th>Total Students</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $number = 1;
                                                    $totalMale = 0;
                                                    $totalFemale = 0;
                                                    $totalStudent = 0;
                                                @endphp
                                                @foreach($classes as $rowClasses)
                                                <tr>
                                                    <th>{{$number++}}</th>
                                                    <th>{{$rowClasses->schoolName}}</th>
                                                    <th>{{$rowClasses->className}}</th>
                                                    <th>{{$rowClasses->femaleStudents}}</th>
                                                    <th>{{$rowClasses->maleStudents}}</th>
                                                    <th>{{$rowClasses->totalStudents}}</th>
                                                    <th><a href="{{url('classStudents',$rowClasses->id)}}"> <button class="btn btn-sm btn-success">View</button></a></th>
                                                </tr>
                                                @php
                                                    $totalStudent += $rowClasses->totalStudents;
                                                    $totalMale += $rowClasses->maleStudents;
                                                    $totalFemale += $rowClasses->femaleStudents;
                                                @endphp
                                                @endforeach
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>{{$totalFemale}}</th>
                                                    <th>{{$totalMale}}</th>
                                                    <th>{{$totalStudent}}</th>
                                                    <th></th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Edit Class Details</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="editForm" method="POST" action="{{route('updateClass')}}">
        @csrf
        <div class="modal-body">
          <input type="hidden" name="id" id="editId">
          <div class="form-group">
            <label for="editClassName">Class Name</label>
            <input type="text" class="form-control" id="editClassName" name="className">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable (client-side)
    var table = $('#classesTable').DataTable({
        pageLength: 25,
        responsive: true,
        order: [[0, 'asc']],
        columnDefs: [
            { orderable: false, targets: 6 } // Action column not sortable
        ]
    });

    if ($.fn.DataTable.Buttons) {
        var buttons = new $.fn.dataTable.Buttons(table, { buttons: [] });
        $('#buttons-container').append(buttons.container().appendTo($('#datatable-container')));
    } else {
        console.error('DataTable Buttons extension initialization failed.');
    }

    // Hide the message after 5 seconds
    setTimeout(function() {
        $('#alert-message').fadeOut('slow');
    }, 5000);
});
</script>



@endsection
