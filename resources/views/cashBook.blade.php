@extends('layouts.app')

@section('content')
<!-- Import Google Fonts with Swap for performance -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #4154f1 0%, #717ff5 100%);
        --success-gradient: linear-gradient(135deg, #2eca6a 0%, #28a745 100%);
        --warning-gradient: linear-gradient(135deg, #ffbb3b 0%, #f39c12 100%);
        --info-gradient: linear-gradient(135deg, #012970 0%, #0d6efd 100%);
        --primary-color: #4154f1;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: #f6f9ff;
    }

    .pagetitle h1 {
        font-size: 28px;
        font-weight: 700;
        color: #012970;
        letter-spacing: -0.5px;
    }

    /* Performance optimized CSS classes */
    .metric-card {
        border: none;
        border-radius: 20px;
        transition: transform 0.2s ease;
        background: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    }

    .metric-card:hover {
        transform: translateY(-3px);
    }

    .metric-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        margin-bottom: 12px;
    }

    .card-primary .metric-icon {
        background: rgba(65, 84, 241, 0.1);
        color: #4154f1;
    }

    .card-success .metric-icon {
        background: rgba(46, 202, 106, 0.1);
        color: #2eca6a;
    }

    .card-warning .metric-icon {
        background: rgba(255, 187, 59, 0.1);
        color: #ffbb3b;
    }

    .card-info .metric-icon {
        background: rgba(1, 41, 112, 0.1);
        color: #012970;
    }

    .metric-value {
        font-size: 20px;
        font-weight: 800;
        color: #012970;
    }

    .metric-label {
        font-size: 10px;
        color: #899bbd;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .filter-section {
        background: white;
        border-radius: 15px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
    }

    .filter-label {
        font-weight: 700;
        color: #444;
        margin-bottom: 5px;
        font-size: 11px;
        display: block;
    }

    .table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table-responsive-custom {
        width: 100%;
        overflow-x: auto;
        padding: 0 25px 25px 25px;
    }

    #myTable {
        width: 100% !important;
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    #myTable thead th {
        background: #f8faff;
        color: #012970;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 10px;
        padding: 12px;
        white-space: nowrap;
        border-bottom: 2px solid #edf1f7;
    }

    #myTable td {
        padding: 10px 12px;
        font-size: 13px;
        color: #444;
        border: none;
        vertical-align: middle;
    }

    .amount-val {
        font-family: 'JetBrains Mono', monospace;
        font-weight: 600;
        text-align: right !important;
    }

    .btn-see-more {
        background: rgba(65, 84, 241, 0.08);
        color: #4154f1;
        border: none;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 10px;
        font-weight: 700;
    }

    .btn-edit-premium {
        color: #4154f1;
        background: #f1f4f9;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Print settings */
    @media print {
        @page {
            size: landscape;
            margin: 5mm;
        }

        .no-print,
        .filter-section,
        .pagetitle,
        .metric-card,
        .sidebar,
        .header {
            display: none !important;
        }

        body {
            background: white;
        }

        .printable-area {
            position: static;
            width: 100%;
        }

        #myTable th,
        #myTable td {
            border: 1px solid #eee !important;
            font-size: 8px !important;
            padding: 4px !important;
        }
    }

    /* Loading Overlay */
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div id="loadingOverlay">
    <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
</div>

<div class="pagetitle d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1>Cash Book Insights</h1>
    </div>
    <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="window.print()">
        <i class="bi bi-printer me-2"></i>Print Report
    </button>
</div>

<section class="section">
    <!-- Fast Metrics -->
    <div class="row mb-3 g-3">
        @foreach([['tuition','primary','wallet2','Tuition'],['funds','success','bank','Funds'],['govt','warning','mortarboard','Govt'],['grand','info','currency-exchange','Grand Total']] as $m)
        <div class="col-6 col-md-3">
            <div class="card metric-card card-{{$m[1]}}">
                <div class="card-body py-3">
                    <div class="metric-icon"><i class="bi bi-{{$m[2]}}"></i></div>
                    <div class="metric-value">Rs. <span id="card-total-{{$m[0]}}">0</span></div>
                    <div class="metric-label">{{$m[3]}} Collection</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Smart Filters -->
    <div class="filter-section shadow-sm">
        <div class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="filter-label">Class</label>
                <select id="filterClass" class="form-select select2">
                    <option value="">All Classes</option>@foreach($classList as $c)<option value="{{$c->className}}">{{$c->className}}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Month</label>
                <select id="filterMonth" class="form-select select2">
                    <option value="">All Months</option>@foreach(['January','February','March','April','May','June','July','August','September','October','November','December'] as $m)<option value="{{$m}}">{{$m}}</option>@endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="filter-label">Student</label>
                <select id="filterStudent" class="form-select select2">
                    <option value="">All Students</option>@foreach($studentList as $s)<option value="{{$s->studentName}}">{{$s->studentName}} ({{$s->grno}})</option>@endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="filter-label">Status</label>
                <select id="filterStatus" class="form-select select2">
                    <option value="">All</option>
                    <option value="Paid">Paid</option>
                    <option value="Un-paid">Un-paid</option>
                </select>
            </div>
            <div class="col-md-1"><button class="btn btn-light w-100 rounded-pill border" id="resetFilters"><i class="bi bi-arrow-counterclockwise"></i></button></div>
        </div>
    </div>

    <!-- Optimized Table -->
    <div class="table-card shadow-sm printable-area">
        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark">Financial Record</h5>
            <small class="text-muted">High-speed server-side processing active</small>
        </div>
        <div class="table-responsive-custom">
            <table id="myTable" class="table">
                <thead>
                    <tr>
                        <th>Branch</th>
                        <th>Class</th>
                        <th>Month</th>
                        <th>Student Name</th>
                        <th>G.R</th>
                        <th>Adm.</th>
                        <th>Tuition</th>
                        <th>Misc.</th>
                        <th>I.T.</th>
                        <th>Exams</th>
                        <th class="no-print">Other</th>
                        <th>Total</th>
                        <th class="no-print">S.</th>
                        <th class="no-print">Act.</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</section>

<!-- Breakup Modal -->
<div class="modal fade" id="feesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="border-radius:20px;">
            <div class="modal-header bg-primary text-white border-0" style="border-radius:20px 20px 0 0;">
                <h6 class="modal-title fw-bold">Fee Breakup</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="modalFeesBody"></div>
        </div>
    </div>
</div>

@section('scripts')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js" defer></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js" defer></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Utility for PK Formatting
        const fmt = (v) => parseFloat(v).toLocaleString('en-PK', {
            minimumFractionDigits: 0
        });

        // High Speed Server Side DataTable
        var table = $('#myTable').DataTable({
            serverSide: true,
            processing: true,
            ajax: {
                url: "{{ route('cashBookData') }}",
                data: function(d) {
                    d.class = $('#filterClass').val();
                    d.month = $('#filterMonth').val();
                    d.student = $('#filterStudent').val();
                    d.status = $('#filterStatus').val();
                },
                dataSrc: function(json) {
                    // Instantly update summary cards from AJAX response
                    $('#card-total-tuition').text(fmt(json.summary.tuition));
                    $('#card-total-funds').text(fmt(json.summary.funds));
                    $('#card-total-govt').text(fmt(json.summary.govt));
                    $('#card-total-grand').text(fmt(json.summary.total));
                    $('#loadingOverlay').fadeOut();
                    return json.data;
                }
            },
            pageLength: 25,
            columns: [{
                    data: 'school_name',
                    className: 'small fw-medium'
                },
                {
                    data: 'class_name',
                    render: v => `<span class="badge bg-light text-primary fw-bold">${v}</span>`
                },
                {
                    data: null,
                    render: r => `${r.month} - ${r.year}`,
                    className: 'small text-muted'
                },
                {
                    data: 'sname',
                    className: 'fw-bold text-dark'
                },
                {
                    data: 'student_grno',
                    className: 'small'
                },
                {
                    data: 'admission',
                    render: fmt,
                    className: 'amount-val'
                },
                {
                    data: 'tution_fee',
                    render: fmt,
                    className: 'amount-val text-primary'
                },
                {
                    data: 'misc',
                    render: fmt,
                    className: 'amount-val'
                },
                {
                    data: 'it',
                    render: fmt,
                    className: 'amount-val'
                },
                {
                    data: 'exams',
                    render: fmt,
                    className: 'amount-val'
                },
                {
                    data: null,
                    orderable: false,
                    className: 'no-print text-center',
                    render: r => `<button class="btn-see-more" onclick="showMore('${r.sname}', ${JSON.stringify({
                'Breakage': r.breakage, 'SLC': r.clc, 'IDF': r.idf, 'CSF': r.csf, 'RDF/CDF': r.rdfcdf, 'Security': r.security_fund
              }).replace(/"/g, '&quot;')})">More</button>`
                },
                {
                    data: 'total_fee',
                    render: v => `<span class="text-success fw-bold">${fmt(v)}</span>`,
                    className: 'amount-val'
                },
                {
                    data: 'status',
                    orderable: false,
                    className: 'no-print',
                    render: v => `<span class="badge rounded-pill bg-${v === 'Paid' ? 'success' : 'warning'}-light text-${v === 'Paid' ? 'success' : 'warning'} fw-bold px-2">${v}</span>`
                },
                {
                    data: 'id',
                    orderable: false,
                    className: 'no-print text-center',
                    render: id => `<a href="/editChallan/${id}" class="btn-edit-premium mx-auto"><i class="bi bi-pencil-fill"></i></a>`
                }
            ],
            language: {
                search: "",
                searchPlaceholder: "Search student or branch...",
                processing: "Fast Loading..."
            }
        });

        // Initialize Select2 after some delay to not block main thread
        window.setTimeout(() => {
            $('.select2').select2({
                width: '100%'
            });
            $('.select2').on('change', () => {
                $('#loadingOverlay').show();
                table.ajax.reload();
            });
        }, 100);

        $('#resetFilters').on('click', () => {
            $('.select2').val('').trigger('change');
        });

        // Global utility for "More" modal
        window.showMore = function(name, fees) {
            let html = `<label class="metric-label">Student</label><div class="fw-bold text-dark mb-4">${name}</div>`;
            Object.entries(fees).forEach(([k, v]) => {
                html += `<div class="d-flex justify-content-between py-2 border-bottom"><span class="small text-muted">${k}</span><span class="fw-bold text-primary font-monospace">Rs. ${fmt(v)}</span></div>`;
            });
            document.getElementById('modalFeesBody').innerHTML = html;
            new bootstrap.Modal(document.getElementById('feesModal')).show();
        };
    });
</script>
@endsection
@endsection