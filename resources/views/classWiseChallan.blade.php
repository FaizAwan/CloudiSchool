@extends('layouts.app')

@section('content')
<style>
    label{
        color:#000;
        font-weight:bold;
    }
</style>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />


<div class="fluid-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div style="background: #0072ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #E4E5E6, #0072ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #E4E5E6, #0072ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
 color:#000; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> P R I N T &nbsp;&nbsp;  C H A L L A N    </div>
                <div class="card-body">
                    <div class="row"> <hr/>
                        <div class="col-md-3">
                            <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
;" class="card-header">
                                    <h5  style="color:#fff">Print Challan Classwise</h5>
                                </div>
                                <div style="background-color:#fff;" class="card-body">
                                    <form action="{{url('printClassWiseChallans')}}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12">

                                            <label>Schools</label>
                                            <select name="school_id" class="form-control select2">
                                                @foreach($schoolList as $rowClasses)
                                                    <option value="{{$rowClasses->id}}">{{$rowClasses->schoolName}}</option>
                                                @endforeach
                                            </select>

                                            </div>
                                            <div class="col-md-12">

                                            <label>Class</label>
                                            <select name="class_id" class="form-control select2">
                                                @foreach($classList as $rowClasses)
                                                    <option value="{{$rowClasses->className}}">{{$rowClasses->className}}</option>
                                                @endforeach
                                            </select>

                                            </div>

                                        <div class="col-md-12">

                                        <label>Month</label>
                                        <select name="month" class="form-control">
                                                <option value="January">January</option>
                                                <option value="February">February</option>
                                                <option value="March">March</option>
                                                <option value="April">April</option>
                                                <option value="May">May</option>
                                                <option value="June">June</option>
                                                <option value="July">July</option>
                                                <option value="August">August</option>
                                                <option value="September">September</option>
                                                <option value="October">October</option>
                                                <option value="November">November</option>
                                                <option value="December">December</option>
                                        </select>

                                        </div>
                                        <div class="col-md-12">
                                        <label>Year</label>
                                        <select name="year" class="form-control">
                                                <option value="2024">2024</option>
                                                <option value="2025">2025</option>
                                                <option value="2025">2026</option>
                                                <option value="2025">2027</option>
                                                <option value="2025">2028</option>
                                                <option value="2025">2029</option>
                                                <option value="2025">2030</option>
                                                <option value="2025">2031</option>
                                                <option value="2025">2032</option>
                                        </select>
                                        </div>
                                        
                                        <label> &nbsp; </label>
                                        <input class="form-control btn btn-success" name="submit" value="Print Challan" type="submit"/>
                                        
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <hr/>
                        </div>
                        
                        <div class="col-md-9">
                        <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-header">
                                    <h5 style="color:#fff">Challans Classwise</h5>
                                </div>
                                <div class="card-body">
                                    <div id="datatable-container">
                                        
                                        
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
 $(document).ready(function() {
    var table = $('#myTable').DataTable({
        columnDefs: [{
            targets: [4], // Assuming the fees column is the 5th column (index 4)
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

    $('.select2').select2();
});


</script>


@endsection
