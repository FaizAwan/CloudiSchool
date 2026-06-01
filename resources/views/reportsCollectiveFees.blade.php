@extends('layouts.app')

@section('content')
<div class="pagetitle">
    <h1>Collective Fee Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item">Reports</li>
            <li class="breadcrumb-item active">Collective Fees</li>
        </ol>
    </nav>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title p-0 m-0 text-white fw-bold"><i class="bi bi-cash-stack me-2"></i>Overall Collective Fees (Classwise)</h5>
                <span class="badge bg-white text-primary rounded-pill px-3 py-2 shadow-sm fs-6">
                    {{ request('fromMonth') }} {{ request('fromYear') }} - {{ request('toMonth') }} {{ request('toYear') }}
                </span>
            </div>
            
            <div class="card-body pt-4">
                <form action="{{ route('reportsCollectiveFees') }}" method="GET" class="p-3 bg-light rounded-4 mb-4 border border-white shadow-sm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">School Branch</label>
                            <select name="class_id" class="form-select border-0 shadow-sm py-2 rounded-3">
                                <option value="">All Schools</option>
                                @foreach($schoolList as $rowSchools)
                                <option value="{{$rowSchools->id}}" {{ request('class_id') == $rowSchools->id ? 'selected' : '' }}>{{$rowSchools->schoolName}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">From Month</label>
                            <select name="fromMonth" class="form-select border-0 shadow-sm py-2 rounded-3">
                                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                                <option value="{{$m}}" {{ request('fromMonth') == $m ? 'selected' : '' }}>{{$m}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Year</label>
                            <select name="fromYear" class="form-select border-0 shadow-sm py-2 rounded-3">
                                @for($y=2023; $y<=2032; $y++)
                                <option value="{{$y}}" {{ request('fromYear') == $y ? 'selected' : '' }}>{{$y}}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">To Month</label>
                            <select name="toMonth" class="form-select border-0 shadow-sm py-2 rounded-3">
                                @foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)
                                <option value="{{$m}}" {{ request('toMonth') == $m ? 'selected' : '' }}>{{$m}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-1">
                            <label class="form-label small fw-bold text-muted text-uppercase ls-1">Year</label>
                            <select name="toYear" class="form-select border-0 shadow-sm py-2 rounded-3">
                                @for($y=2023; $y<=2032; $y++)
                                <option value="{{$y}}" {{ request('toYear') == $y ? 'selected' : '' }}>{{$y}}</option>
                                @endfor
                            </select>
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100 py-2 rounded-3 fw-bold shadow-sm h-100 d-flex align-items-center justify-content-center">
                                <i class="bi bi-search me-2"></i> GENEREATE REPORT
                            </button>
                        </div>
                    </div>
                </form>

                @if (session('message'))
                <div class="alert alert-info alert-dismissible fade show rounded-3" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session('message') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="table-container rounded-4 overflow-hidden border">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="myTable">
                            <thead class="bg-light">
                                <tr class="text-uppercase small fw-bold text-muted ls-1">
                                    <th class="py-3 px-3">School / Class</th>
                                    <th>Month-Year</th>
                                    <th class="text-end">Adm</th>
                                    <th class="text-end">Tuition</th>
                                    <th class="text-end">Brk</th>
                                    <th class="text-end">Misc</th>
                                    <th class="text-end">SLC</th>
                                    <th class="text-end">IDF</th>
                                    <th class="text-end">Exm</th>
                                    <th class="text-end">IT</th>
                                    <th class="text-end">CSF</th>
                                    <th class="text-end">RDF</th>
                                    <th class="text-end">Sec</th>
                                    <th class="text-end bg-primary-light fw-bolder text-primary">Total</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($challanData as $data)
                                <tr class="collective-row">
                                    <td class="px-3">
                                        <div class="fw-bold text-dark">{{ $data->school_name }}</div>
                                        <div class="small text-muted">{{ $data->className }}</div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $data->month }} {{ $data->year }}</span></td>
                                    <td class="text-end total_admission">{{ number_format((float)$data->total_admission, 1) }}</td>
                                    <td class="text-end total_tuition_fee">{{ number_format((float)$data->total_tuition_fee, 0) }}</td>
                                    <td class="text-end total_breakage">{{ number_format((float)$data->total_breakage, 0) }}</td>
                                    <td class="text-end total_misc">{{ number_format((float)$data->total_misc, 0) }}</td>
                                    <td class="text-end total_clc">{{ number_format((float)$data->total_clc, 0) }}</td>
                                    <td class="text-end total_idf">{{ number_format((float)$data->total_idf, 0) }}</td>
                                    <td class="text-end total_exams">{{ number_format((float)$data->total_exams, 0) }}</td>
                                    <td class="text-end total_it">{{ number_format((float)$data->total_it, 0) }}</td>
                                    <td class="text-end total_csf">{{ number_format((float)$data->total_csf, 0) }}</td>
                                    <td class="text-end total_rdfcdf">{{ number_format((float)$data->total_rdfcdf, 0) }}</td>
                                    <td class="text-end total_security_fund">{{ number_format((float)$data->total_security_fund, 0) }}</td>
                                    <td class="text-end total_fee fw-bold text-primary bg-primary-light">{{ number_format((float)$data->total_fee, 0) }}</td>
                                    <td class="text-center">
                                        <a href="{{ url('viewChallan/' . urlencode($data->className)) }}?month={{ urlencode($data->month) }}&year={{ $data->year }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-dark text-white fw-bold">
                                <tr>
                                    <td colspan="2" class="text-center py-3">GRAND TOTALS</td>
                                    <td class="text-end" id="totalAdmission"></td>
                                    <td class="text-end" id="totalTutionFee"></td>
                                    <td class="text-end" id="totalBreakage"></td>
                                    <td class="text-end" id="totalMisc"></td>
                                    <td class="text-end" id="totalCLC"></td>
                                    <td class="text-end" id="totalIDF"></td>
                                    <td class="text-end" id="totalExams"></td>
                                    <td class="text-end" id="totalIT"></td>
                                    <td class="text-end" id="totalCSF"></td>
                                    <td class="text-end" id="totalRDFCDF"></td>
                                    <td class="text-end" id="totalSecurity"></td>
                                    <td class="text-end bg-white text-dark" id="totalFees"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .ls-1 { letter-spacing: 0.5px; }
    .bg-primary-light { background-color: rgba(65, 105, 225, 0.05); }
    .table-container { box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    .collective-row:hover { background-color: #f8f9ff !important; }
    th { vertical-align: middle !important; font-size: 0.7rem !important; }
    td { font-size: 0.85rem !important; padding: 0.75rem 0.5rem !important; }
    
    /* DataTables Overrides */
    .dataTables_wrapper .dataTables_filter input { border-radius: 50px; padding: 5px 15px; border: 1px solid #ddd; }
    .dt-buttons .btn { border-radius: 50px !important; font-size: 0.75rem !important; margin-right: 5px; }
</style>
@endsection

@section('scripts')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script>
    $(document).ready(function() {
        var table = $('#myTable').DataTable({
            dom: 'Bfrtip',
            buttons: [
                { extend: 'copy', className: 'btn btn-outline-secondary' },
                { extend: 'csv', className: 'btn btn-outline-secondary' },
                { extend: 'excel', className: 'btn btn-outline-success' },
                { extend: 'pdf', className: 'btn btn-outline-danger' },
                { extend: 'print', className: 'btn btn-outline-dark' }
            ],
            pageLength: 50,
            ordering: false
        });

        function calculateColumnTotal(className) {
            let total = 0;
            $(`.${className}`).each(function() {
                let text = $(this).text().replace(/,/g, '');
                const value = parseFloat(text) || 0;
                total += value;
            });
            return total.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 0});
        }

        function updateFooterTotals() {
            $('#totalAdmission').text(calculateColumnTotal('total_admission'));
            $('#totalTutionFee').text(calculateColumnTotal('total_tuition_fee'));
            $('#totalBreakage').text(calculateColumnTotal('total_breakage'));
            $('#totalMisc').text(calculateColumnTotal('total_misc'));
            $('#totalCLC').text(calculateColumnTotal('total_clc'));
            $('#totalIDF').text(calculateColumnTotal('total_idf'));
            $('#totalExams').text(calculateColumnTotal('total_exams'));
            $('#totalIT').text(calculateColumnTotal('total_it'));
            $('#totalCSF').text(calculateColumnTotal('total_csf'));
            $('#totalRDFCDF').text(calculateColumnTotal('total_rdfcdf'));
            $('#totalSecurity').text(calculateColumnTotal('total_security_fund'));
            $('#totalFees').text(calculateColumnTotal('total_fee'));
        }

        updateFooterTotals();
    });
</script>
@endsection