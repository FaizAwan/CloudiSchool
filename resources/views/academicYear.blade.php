@extends('layouts.app')

@section('content')

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">


<style>
    label{
        font-weight:bold;
        color:#000;
        
    }
</style>
<div class="fluid-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div style="background: #1488CC;  /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #2B32B2, #1488CC);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #2B32B2, #1488CC); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
color:#fff; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> A C A D E M I C &nbsp;&nbsp; Y E A R </div>
                <div style="background: #ECE9E6;  /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #FFFFFF, #ECE9E6);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #FFFFFF, #ECE9E6); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-body">
                
                    <div style="margin-top:20px;" class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-header">
                                    <h5  style="font-weight:bold; color:#fff">Add New Academic Year</h5>
                                </div>
                                <div style="font-weight:bold; background-color:#fff;" class="card-body">
                                    <form action="{{url('addAcademicYear')}}" method="POST">
                                        @csrf
                                        <label>Academic Year</label>
                                        <input readonly class="form-control" id="academicYear" name="academicYear" type="text"/>
                                        <hr/>
                                        
                                        <label>From Month</label>
                                        <select id="fromMonth" name="fromMonth" class="form-control">
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

                                        
                                        
                                        <label>From Year</label>
                                        <select id="fromYear" name="fromYear" class="form-control">
                                                <option value="2023">2023</option>
                                                <option value="2024">2024</option>
                                                <option value="2025">2025</option>
                                                <option value="2026">2026</option>
                                                <option value="2027">2027</option>
                                                <option value="2028">2028</option>
                                                <option value="2029">2029</option>
                                                <option value="2030">2030</option>
                                                <option value="2031">2031</option>
                                                <option value="2032">2032</option>
                                        </select>
                                        
                                        <label>To Month</label>
                                        <select id="toMonth" name="toMonth" class="form-control">
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

                                        
                                        
                                        <label>To Year</label>
                                        <select id="toYear" name="toYear" class="form-control">
                                                <option value="2023">2023</option>
                                                <option value="2024">2024</option>
                                                <option value="2025">2025</option>
                                                <option value="2026">2026</option>
                                                <option value="2027">2027</option>
                                                <option value="2028">2028</option>
                                                <option value="2029">2029</option>
                                                <option value="2030">2030</option>
                                                <option value="2031">2031</option>
                                                <option value="2032">2032</option>
                                        </select>
                                        
                                        
                                        <input class="form-control btn btn-primary" name="submit" value="Add Academic Year" type="submit"/>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
                                        background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
                                        background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
                                        " class="card-header">
                                    <h5 style="font-weight:bold; color:#fff">Academic Years List</h5>
                                    <span style="text-align:left">@if (session('message'))
                                          <div id="alert-message" class="alert alert-primary">
                                              {{ session('message') }}
                                          </div>
                                      @endif</span>
                                      
                                      <span style="text-align:left">@if (session('errorMessage'))
                                          <div id="alert-message" class="alert alert-danger">
                                              {{ session('errorMessage') }}
                                          </div>
                                      @endif</span>
                                      
                                      <span style="text-align:left">@if ($errors->any())
                                          <div id="alert-message" class="alert alert-warning">
                                              <strong>Validation Errors:</strong>
                                              <ul>
                                                  @foreach ($errors->all() as $error)
                                                      <li>{{ $error }}</li>
                                                  @endforeach
                                              </ul>
                                          </div>
                                      @endif</span>
                                      
                                </div>
                                <div class="card-body">
                                    <div id="datatable-container">
                                    <div id="buttons-container" class="mb-3"></div>
                                        <table class="table table-striped table-hover" id="classesTable">
                                            <thead>
                                                <tr>
                                                    <th>Academic Year</th>
                                                    <th>is Active</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($academicYearList as $year)
                                                <tr>
                                                    <td>{{ $year->academicYear }}</td>
                                                    <td>{{ $year->is_active }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $year->is_active == 'yes' ? 'success' : ($year->is_active == 'closed' ? 'danger' : 'secondary') }}">
                                                            {{ ucfirst($year->is_active) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $fromMonth = $year->start_date ? \Carbon\Carbon::parse($year->start_date)->format('F') : '';
                                                            $fromYear = $year->start_date ? \Carbon\Carbon::parse($year->start_date)->format('Y') : '';
                                                            $toMonth = $year->end_date ? \Carbon\Carbon::parse($year->end_date)->format('F') : '';
                                                            $toYear = $year->end_date ? \Carbon\Carbon::parse($year->end_date)->format('Y') : '';
                                                        @endphp
                                                        <button type="button" class="btn btn-primary edit-button" 
                                                            data-bs-toggle="modal" data-bs-target="#editModal" 
                                                            data-id="{{ $year->id }}" 
                                                            data-name="{{ trim($fromMonth.' '.$fromYear.' - '.$toMonth.' '.$toYear) }}" 
                                                            data-frommonth="{{ $fromMonth }}" 
                                                            data-fromyear="{{ $fromYear }}" 
                                                            data-tomonth="{{ $toMonth }}" 
                                                            data-toyear="{{ $toYear }}" 
                                                            data-is_active="{{ $year->is_active }}">
                                                            Edit
                                                        </button>
                                                    </td>
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



<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true" data-backdrop="static">
                <div class="modal-dialog modal-dialog-centered">
                  <div class="modal-content">
                    <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="modal-header">
                      <h5  style="font-weight:bold; color:#fff" class="modal-title">Edit Academic Year Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <form id="editForm" method="POST" action="{{route('updateAcademicYear')}}">
                    @csrf
                      <input type="hidden" name="id" id="editId">
                      <div class="form-group">
                        <label for="editAcademicYear">Academic Year</label>
                        <input readonly type="text" class="form-control" id="editAcademicYear" name="academicYear">
                      </div>
                                         <div class="form-group">
                                            <label>From Month</label>
                                            <select id="fromMonthEdit" name="fromMonth" class="form-control">
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
                                        <div class="form-group">
                                            <label>From Year</label>
                                            <select id="fromYearEdit" name="fromYear" class="form-control">
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                    <option value="2026">2026</option>
                                                    <option value="2027">2027</option>
                                                    <option value="2028">2028</option>
                                                    <option value="2029">2029</option>
                                                    <option value="2030">2030</option>
                                                    <option value="2031">2031</option>
                                                    <option value="2032">2032</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>To Month</label>
                                            <select id="toMonthEdit" name="toMonth" class="form-control">
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
                                        <div class="form-group">
                                            <label>To Year</label>
                                            <select id="toYearEdit" name="toYear" class="form-control">
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                    <option value="2026">2026</option>
                                                    <option value="2027">2027</option>
                                                    <option value="2028">2028</option>
                                                    <option value="2029">2029</option>
                                                    <option value="2030">2030</option>
                                                    <option value="2031">2031</option>
                                                    <option value="2032">2032</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Is Active Year</label>
                                            <select id="editIsActive" name="is_active" class="form-control">
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
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>

<script>
$(function(){
    function updateAcademicYear(){
        var fromMonth=$('#fromMonth').val();
        var fromYear=$('#fromYear').val();
        var toMonth=$('#toMonth').val();
        var toYear=$('#toYear').val();
        $('#academicYear').val(fromMonth+' '+fromYear+' - '+toMonth+' '+toYear);
    }
    function updateAcademicYearEdit(){
        var fm=$('#fromMonthEdit').val();
        var fy=$('#fromYearEdit').val();
        var tm=$('#toMonthEdit').val();
        var ty=$('#toYearEdit').val();
        $('#editAcademicYear').val(fm+' '+fy+' - '+tm+' '+ty);
    }
    $(document).on('change','#fromMonthEdit,#fromYearEdit,#toMonthEdit,#toYearEdit',updateAcademicYearEdit);
    $('#fromMonth,#fromYear,#toMonth,#toYear').change(updateAcademicYear);
    updateAcademicYear();
    updateAcademicYearEdit();

    // Initialize DataTable for client-side processing
    var table = $('#classesTable').DataTable({
        "pageLength": 25,
        "responsive": true,
        "order": [[0, "desc"]], // Sort by academic year descending
        "language": {
            "search": "Search Academic Years:",
            "lengthMenu": "Show _MENU_ academic years per page",
            "info": "Showing _START_ to _END_ of _TOTAL_ academic years",
            "emptyTable": "No academic years found"
        },
        "columnDefs": [
            { "orderable": false, "targets": 3 } // Action column not sortable
        ]
    });

    // Handle edit button clicks
    $(document).on('click', '.edit-button', function(){
        // Helpers
        function normMonth(m){
            if(!m) return '';
            var t = m.toString().trim().toLowerCase();
            var map={january:'January',february:'February',march:'March',april:'April',may:'May',june:'June',july:'July',august:'August',september:'September',october:'October',november:'November',december:'December'};
            return map[t]||'';
        }
        function setSelect($sel, val){ if(val){ $sel.val(val); } }
        function parseFromName(n){
            if(!n) return null;
            var s=n.toString().trim();
            // Pattern: Month YYYY - Month YYYY
            var re=/^(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{4})\s*-\s*(January|February|March|April|May|June|July|August|September|October|November|December)\s+(\d{4})$/i;
            var m=s.match(re);
            if(m){ return {fromMonth:normMonth(m[1]), fromYear:m[2], toMonth:normMonth(m[3]), toYear:m[4]}; }
            // Pattern: YYYY-YY or YYYY-YYYY (assume Apr-Mar session)
            var re2=/(\d{4})\s*-\s*(\d{2}|\d{4})/;
            var m2=s.match(re2);
            if(m2){
                var y1=parseInt(m2[1],10);
                var y2=(m2[2].length===2)? (y1 - (y1%100)) + parseInt(m2[2],10) : parseInt(m2[2],10);
                return {fromMonth:'April', fromYear:String(y1), toMonth:'March', toYear:String(y2)};
            }
            return null;
        }

        var id = $(this).attr('data-id');
        var name = $(this).attr('data-name');
        var fromMonth = normMonth($(this).attr('data-frommonth'));
        var fromYear = ($(this).attr('data-fromyear')||'').toString().trim();
        var toMonth = normMonth($(this).attr('data-tomonth'));
        var toYear = ($(this).attr('data-toyear')||'').toString().trim();
        var isActive = ($(this).attr('data-is_active')||'').toString().trim();

        if((!fromMonth || !fromYear || !toMonth || !toYear) && name){
            var parsed = parseFromName(name);
            if(parsed){
                fromMonth = fromMonth||parsed.fromMonth;
                fromYear = fromYear||parsed.fromYear;
                toMonth = toMonth||parsed.toMonth;
                toYear = toYear||parsed.toYear;
            }
        }

        if (!id || id === 'undefined' || id === 'null') {
            alert('Error: Academic year ID is missing. Please refresh the page and try again.');
            return false;
        }

        // Populate exact values
        $('#editId').val(id);
        setSelect($('#fromMonthEdit'), fromMonth);
        setSelect($('#fromYearEdit'), fromYear);
        setSelect($('#toMonthEdit'), toMonth);
        setSelect($('#toYearEdit'), toYear);
        if (isActive) { $('#editIsActive').val(isActive); }
        // Set first field to the label from the list (already month/year string)
        if (name) {
            $('#editAcademicYear').val(name);
        } else {
            var display = (fromMonth?fromMonth:'') + (fromYear?(' '+fromYear):'') + ' - ' + (toMonth?toMonth:'') + (toYear?(' '+toYear):'');
            $('#editAcademicYear').val(display.trim());
        }
        // Show the modal
        $('#editModal').modal('show');
    });

    // Also enforce values right when modal starts showing
    $('#editModal').on('show.bs.modal', function (e) {
        var btn = $(e.relatedTarget);
        if (!btn || !btn.length) return;
        function norm(m){ return (m||'').toString().trim(); }
        var id = norm(btn.attr('data-id'));
        var name = norm(btn.attr('data-name'));
        var fm = norm(btn.attr('data-frommonth'));
        var fy = norm(btn.attr('data-fromyear'));
        var tm = norm(btn.attr('data-tomonth'));
        var ty = norm(btn.attr('data-toyear'));
        var ia = norm(btn.attr('data-is_active'));
        if (id) $('#editId').val(id);
        if (fm) $('#fromMonthEdit').val(fm);
        if (fy) $('#fromYearEdit').val(fy);
        if (tm) $('#toMonthEdit').val(tm);
        if (ty) $('#toYearEdit').val(ty);
        if (ia) $('#editIsActive').val(ia);
        if (name) {
            $('#editAcademicYear').val(name);
        }
    });

    // Debug form submission
    $('#editForm').on('submit', function(e) {
        console.log('Form submitting...');
        console.log('Form data:', {
            id: $('#editId').val(),
            academicYear: $('#editAcademicYear').val(),
            fromMonth: $('#fromMonthEdit').val(),
            fromYear: $('#fromYearEdit').val(),
            toMonth: $('#toMonthEdit').val(),
            toYear: $('#toYearEdit').val(),
            is_active: $('#editIsActive').val()
        });
        // Let the form submit normally
    });
    
    // Check for success/error messages and reload if needed
    if ($('#alert-message').length > 0) {
        console.log('Alert message found, page was reloaded after form submission');
        // Reinitialize DataTable after the page reload
        setTimeout(function() {
            if (table) {
                table.destroy();
                table = $('#classesTable').DataTable({
                    "pageLength": 25,
                    "responsive": true,
                    "order": [[0, "desc"]],
                    "language": {
                        "search": "Search Academic Years:",
                        "lengthMenu": "Show _MENU_ academic years per page",
                        "info": "Showing _START_ to _END_ of _TOTAL_ academic years",
                        "emptyTable": "No academic years found"
                    },
                    "columnDefs": [
                        { "orderable": false, "targets": 3 }
                    ]
                });
            }
        }, 100);
    }
    
    setTimeout(function(){ $('#alert-message').fadeOut('slow'); },5000);
});
</script>

@endsection
