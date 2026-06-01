@extends('layouts.app')

@section('content')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">


<div class="fluid-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div style="background: #0072ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #E4E5E6, #0072ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #E4E5E6, #0072ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
color:#000; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> R E P O R T S &nbsp;  &nbsp;  C L A S S W I S E &nbsp; &nbsp; S T U D E N T S </div>
                <div style="background-color:#f6f9ff" class="card-body">
                
                    <div style="margin-top:20px;" class="row">
                        
                        <div class="col-md-9">
                            <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-header">
                                    <h5 style="font-weight:bold; color:#fff">Class List</h5>
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
                                                    <th>Branch Name</th>
                                                    <th>Session</th>
                                                    <th>Class Name</th>
                                                    <th>Section</th>
                                                    <th>Student Name</th>
                                                    <th>Gender</th>
                                                    <th>G.R.No</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $number = 1;
                                                @endphp
                                                @foreach($classWiseStudent as $rowClasses)
                                                <tr>
                                                    <th>{{$number++}}</th>
                                                    <th>{{$rowClasses->schoolName}}</th>
                                                    <th>{{$rowClasses->studentSession}}</th>
                                                    <th>{{$rowClasses->className}}</th>
                                                    <th>{{ (empty($rowClasses->section) || $rowClasses->section == '0') ? '' : $rowClasses->section }}</th>
                                                    <th>{{$rowClasses->studentName}}</th>
                                                    <th>{{$rowClasses->gender}}</th>
                                                    <th>{{$rowClasses->grno}}</th>
                                                    <th>
                                                        @if(Auth::user()->role == 'admin')
                                                            <a href="{{ url('deleteStudent', $rowClasses->studentID) }}" onclick="return confirm('Are you sure you want to delete this student?');">
                                                                <button class="btn btn-sm btn-danger">Delete</button>
                                                            </a></th>
                                                        @else
                                                           <button class="btn btn-sm btn-warning" onclick="return confirm('Sorry Teacher, You have not permission to delete this record please update your Admin');">Delete</button></th>
                                                        @endif
                                                </tr>

                                                @endforeach
    
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

<!-- No edit modal needed for this view -->

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable for client-side processing
    var table = $('#classesTable').DataTable({
        "pageLength": 25,
        "responsive": true,
        "order": [[0, "asc"]], // Sort by S.NO ascending
        "language": {
            "search": "Search Students:",
            "lengthMenu": "Show _MENU_ students per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ students",
            "emptyTable": "No students found for this class"
        },
        "columnDefs": [
            { "orderable": false, "targets": 8 } // Action column not sortable (index 8 after adding Section)
        ]
    });

    // Check if DataTable initialization is successful
    if ($.fn.DataTable.Buttons) {
        // Initialize DataTable buttons
        var buttons = new $.fn.dataTable.Buttons(table, {
            buttons: [
                //'csv', 'pdf', 'print'
            ]
        });
        
        // Append buttons to the container
        
        $('#buttons-container').append(buttons.container().appendTo($('#datatable-container')));
    } else {
        console.error('DataTable Buttons extension initialization failed.');
    }

    // No edit functionality needed for this view
                                
                             // Hide the message after 5 seconds
                              setTimeout(function() {
                                  $('#alert-message').fadeOut('slow');
                              }, 5000); // 5000 milliseconds = 5 seconds
});
</script>



@endsection
