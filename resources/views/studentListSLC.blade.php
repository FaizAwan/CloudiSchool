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
#myTable td.actions { overflow: visible !important; text-overflow: clip !important; }
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
 color:#000; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> S T U D E N T S  &nbsp;&nbsp; L I S T  &nbsp;&nbsp; S L C   &nbsp;&nbsp; /   &nbsp;&nbsp; T C  </div>
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
                                                    <th>GR No.</th>
                                                    <th>Class Name</th>
                                                    <th>Student Name</th>
                                                    <th>Month</th>
                                                    <th>SLC Fee</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php
                                                    $sno = 1;
                                                @endphp

                                                @foreach($studentListSLC as $row)
                                                    <tr>
                                                        <td data-search="{{$row->grno}}">{{$row->grno}}</td>
                                                        <td data-search="{{$row->class_name}}">{{$row->class_name}}</td>
                                                        <td data-search="{{$row->student_id}}">{{$row->student_id}}</td>
                                                        <td data-search="{{$row->month}} - {{$row->year}}">{{$row->month ? $row->month . ' - ' . $row->year : 'N/A'}}</td>
                                                        <td data-search="{{$row->clc}}">{{$row->clc ?? '0.00'}}</td>
                                                        <td data-search="{{$row->status}}">
                                                            <span class="badge {{ $row->status == 'SLC' ? 'bg-danger' : ($row->status == 'active' ? 'bg-success' : 'bg-warning') }}">{{$row->status}}</span>
                                                        </td>
                                                        <td>
                                                        
                                                        </td>
                                                    </tr>
                                                   
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
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
    var table = $('#myTable').DataTable({
        order: [[0, 'asc']],
        columnDefs: [{
            targets: [4],
            render: function(data, type, row) {
                return type === 'display' ? data.replace(/1 - /g, '') : data;
            }
        }]
    });
    
    $('.select2').select2({ width: '100%' });

    // Date/Month range logic if needed
    $('select[name="howManyMonth"]').on('change', function() {
        const val = $(this).val();
        if (val === 'one') {
            $('.oneMonth').show(); $('.moreMonth').hide();
        } else {
            $('.moreMonth').show(); $('.oneMonth').hide();
            function getMonthNum(m) {
                const ms = ["JANUARY", "FEBRUARY", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUGUST", "SEPTEMBER", "OCTOBER", "NOVEMBER", "DECEMBER"];
                return ms.indexOf(m.toUpperCase());
            }
            function calc(){
                const fM = getMonthNum($('#fromMonth').val()); 
                const fY = parseInt($('#fromYear').val());
                const tM = getMonthNum($('#toMonth').val()); 
                const tY = parseInt($('#toYear').val());
                const fD = new Date(fY, fM, 1); 
                const tD = new Date(tY, tM, 1);
                let total = (tD.getFullYear() - fD.getFullYear()) * 12 + (tD.getMonth() - fD.getMonth());
                $('#how-many-Months').val(total);
            }
            $('#fromMonth, #fromYear, #toMonth, #toYear').change(calc);
            calc();
        }
    });
});
</script>
@endsection


