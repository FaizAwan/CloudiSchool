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
color:#fff; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> R E P O R T S &nbsp;  &nbsp;  C L A S S W I S E &nbsp; &nbsp; F E E S </div>
                <div style="background-color:#f6f9ff" class="card-body">
                
                    <div style="margin-top:20px;" class="row">
                        
                        <div class="col-md-12">
                            <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-header">
                                    <h5 style="font-weight:bold; color:#fff">Classwise Fees List</h5>
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
                                                    <th>Class Fees</th>
                                                    <th>Total Students</th>
                                                    <th>Students Fees <br/> ( Expected )</th>
                                                    <th>Students Fees <br/> ( Debit )<br/> ( Un-paid )</th>
                                                    <th>Students Fees <br/> ( Credit )<br/> ( Paid )</th>
                                                    <!--<th>Balance</th>-->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $number = 1;
                                                    $totalUnpaid = 0;
                                                    $totalPaid = 0;
                                                @endphp
                                                @foreach($classTotalFees as $rowClasses)
                                                <tr>
                                                    <td>{{$number++}}</td>
                                                    <td>{{$rowClasses->schoolName}}</td>
                                                    <td>{{$rowClasses->className}}</td>
                                                    <td>{{$rowClasses->totalFees}}</td>
                                                    <td>{{$rowClasses->totalStudents}}</td>
                                                    <td>{{$rowClasses->totalFees * $rowClasses->totalStudents}}</td>
                                                    <td>{{$rowClasses->totalChallanFeesD}}</td>
                                                    <td>{{$rowClasses->totalChallanFeesC}}</td>
                                                    <!--<td></td>-->
                                                    @php
                                                        $totalUnpaid += $rowClasses->totalChallanFeesD;
                                                        $totalPaid += $rowClasses->totalChallanFeesC;
                                                    @endphp
                                                </tr>

                                                @endforeach
                                                
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th>{{$totalUnpaid}}</th>
                                                    <th>{{$totalPaid}}</th>
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
    // Initialize DataTable
    var table = $('#classesTable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": "{{ url('getClasses') }}",
        "columns": [
            { "data": "id" }, // Assuming your data has an ID field
            { "data": "schoolName" },
            { "data": "className" },
            
            { "data": "action", "orderable": false, "searchable": false } // Assuming you have an action column
        ],
       
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

    // Event delegation for edit buttons
    $('#classesTable').on('click', '.edit-button', function() {
                                  console.log("Edit button clicked");
                                  var id = $(this).data('id');
                                  var name = $(this).data('name');
                                  

                                  console.log("ID:", id);
                                  console.log("Name:", name);

                                  $('#editId').val(id);
                                  $('#editClassName').val(name);
                                  $('#editModal').modal('show'); // Open the modal
                                });
                                
                             // Hide the message after 5 seconds
                              setTimeout(function() {
                                  $('#alert-message').fadeOut('slow');
                              }, 5000); // 5000 milliseconds = 5 seconds
});
</script>



@endsection
