@extends('layouts.app')

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

<style>
/* Squeeze table to fit on screen */
#myTable { table-layout: fixed; width: 100% !important; border-collapse: collapse !important; }
#myTable th, #myTable td {
  padding: 4px 6px !important;
  font-size: 11px !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
  vertical-align: middle;
}
.table-container-squeeze { overflow: hidden; width: 100%; }
.container-fluid, .fluid-container { max-width: 100% !important; padding: 10px !important; }
</style>


<div class="fluid-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div style="background: #0072ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #E4E5E6, #0072ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #E4E5E6, #0072ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
 color:#000; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> S T U D E N T S  &nbsp;&nbsp; L I S T  &nbsp;&nbsp; A C C O R D I N G  T O   &nbsp;&nbsp; G R N O  </div>
                <div class="card-body">
                    <div class="row"> <hr/>
                        <div class="col-md-12">
                        <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-header">
                                    <h5 style="font-weight:bold; color:#fff">STUDENT LIST</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-container-squeeze">
                                        <table class="table table-striped table-hover" id="myTable">
                                            <thead>
                                                <tr>
                                                    <!--<th>S.NO</th>-->
                                                    <th>Branch Name</th>
                                                    <th>GR No.</th>
                                                    <th>Class Name</th>
                                                    <th>Student Name</th>
                                                    <th>Gender</th>
                                                    <th>Parent Name</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $sno = 1;
                                                @endphp

                                                @foreach($students as $rowStudentList)
                                                    <tr>
                                                        
                                                        <td data-search="{{$rowStudentList->schoolName}}">{{$rowStudentList->schoolName}}</td>
                                                        <td data-search="{{$rowStudentList->grno}}">{{$rowStudentList->grno}}</td>
                                                        <td data-search="{{$rowStudentList->className}}">{{$rowStudentList->className}}</td>
                                                        <td data-search="{{$rowStudentList->studentName}}">{{$rowStudentList->studentName}}</td>
                                                        <td data-search="{{$rowStudentList->gender}}">{{$rowStudentList->gender}}</td>
                                                        <td data-search="{{$rowStudentList->parentName}}">{{$rowStudentList->parentName}}</td>
                                                        <td data-search="{{$rowStudentList->status}}">
                                                            <span class="badge bg-success">{{$rowStudentList->status}}</span>
                                                            </td>
                                                        <td>
                                                        
                                                        </td>
                                                    </tr>
                                                   
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <!--<th>S.NO</th>-->
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
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

@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<script>
 $(document).ready(function() {
     
     
     $('select[name="howManyMonth"]').on('change', function() {
        const selectedValues = $(this).val(); // Get selected values (array)
        if (selectedValues === 'one') {
            $('.oneMonth').show(); // Show if "One Month" is selected
            $('.moreMonth').hide(); // Show if "One Month" is selected
        } else {
            $('.moreMonth').show(); // Show if "One Month" is selected
            $('.oneMonth').hide(); // Hide otherwise
            
            function getMonthNumber(monthName) {
                const months = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
                return months.indexOf(monthName.toUpperCase());
            }
    
      function calculateMonths() {
            const fromMonth = getMonthNumber($('#fromMonth').val()); 
            const fromYear = parseInt($('#fromYear').val());
            const toMonth = getMonthNumber($('#toMonth').val()); 
            const toYear = parseInt($('#toYear').val());
            
            const fromDate = new Date(fromYear, parseInt(fromMonth), 1); 
            const toDate = new Date(toYear, parseInt(toMonth), 1);
        
            let yearDiff = toDate.getFullYear() - fromDate.getFullYear(); 
            let monthDiff = toDate.getMonth() - fromDate.getMonth(); // Change const to let
            let totalMonths = yearDiff * 12 + monthDiff; 
        
            if (monthDiff < 0) {
                yearDiff--;
                monthDiff += 11; // Add 11 instead of 12
            }
            
            $('#how-many-Months').val(totalMonths);
            console.log("totalMonths:", totalMonths); 
        }

          $('#fromMonth, #fromYear, #toMonth, #toYear').change(calculateMonths);
      
      calculateMonths();
        }
    });
    
    $('select[name="howManyStudents"]').on('change', function() {
        const selectedValuesStudent = $(this).val(); // Get selected values (array)
        if (selectedValuesStudent === 'oneStudent') {
            $('.oneStudent').show(); // Show if "One Month" is selected
            $('.moreStudent').hide(); // Show if "One Month" is selected
        } else {
            $('.moreStudent').show(); // Show if "One Month" is selected
            $('.oneStudent').hide(); // Hide otherwise
        }
    });
    
    
    var table = $('#myTable').DataTable({
        order: [[1, 'asc']],
        columnDefs: [{
            targets: [4],
            render: function(data, type, row) {
                return type === 'display' ? data.replace(/1 - /g, '') : data;
            }
        }]
    });
    
    // Recalculate total fees on each draw event (including after search/filter)
    table.on('draw', function () {
        var totalFees = 0;
        table.rows({ search: 'applied' }).every(function () {
            var rowData = this.data();
            var feeValue = parseFloat(rowData[4].display.toString().replace(/[^0-9.]/g, '').trim());
            if (!isNaN(feeValue)) {
                totalFees += feeValue;
            }
        });
        $('#totalFees').text(totalFees.toFixed(2)); // Display total fees in the footer
    });

    $('.select2').select2({ width: '100%' });
});
</script>
@endsection
