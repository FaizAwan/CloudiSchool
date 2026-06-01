@extends('layouts.app')

@section('content')
<!-- Premium Challan Styles -->
<style>
    :root {
        --challan-border: #444;
        --challan-bg-alt: #f1f5f9;
        --challan-text: #000;
        --accent-color: #000;
    }

    /* Print settings for legal landscape */
    @media print {
        @page {
            size: legal landscape;
            margin: 5mm;
        }
        body { background: white !important; margin: 0; padding: 0; }
        .no-print { display: none !important; }
        .challan-page { box-shadow: none !important; border: none !important; padding: 0 !important; margin: 0 !important; width: 100% !important; }
        .challan-copy { break-inside: avoid; }
        /* Professional crisp fonts for print */
        * { -webkit-print-color-adjust: exact !important; font-family: 'Inter', 'Segoe UI', Arial, sans-serif !important; }
    }

    .challan-page {
        background: #fff;
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }

    .challan-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        grid-template-rows: repeat(2, 1fr);
        gap: 15px;
    }

    .challan-copy {
        border: 1.5px solid var(--challan-border);
        padding: 12px;
        position: relative;
        overflow: hidden;
        background: #fff;
    }

    .copy-tag {
        background: #000;
        color: #fff;
        font-size: 0.7rem;
        padding: 2px 10px;
        position: absolute;
        top: 0;
        right: 0;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .school-header {
        text-align: center;
        border-bottom: 2px solid #000;
        margin-bottom: 8px;
        padding-bottom: 5px;
    }

    .school-name {
        font-size: 1.4rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 2px;
    }

    .bank-header {
        background: #eee;
        text-align: center;
        border-bottom: 1.5px solid #000;
        font-weight: 800;
        padding: 3px;
        text-transform: uppercase;
        font-size: 0.85rem;
    }

    .bank-info {
        font-size: 0.75rem;
        padding: 4px;
        border-bottom: 1px solid #000;
        line-height: 1.3;
    }

    .student-meta {
        font-size: 0.8rem;
        padding: 5px 0;
        border-bottom: 1px solid #000;
    }

    .student-meta .row { margin: 0; }
    .meta-val { font-weight: 800; text-transform: uppercase; border-bottom: 1px dotted #555; }

    .fee-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.8rem;
        margin-top: 5px;
    }

    .fee-table th {
        background: #e2e8f0;
        border: 1px solid #000;
        padding: 4px;
        text-transform: uppercase;
        font-weight: 800;
    }

    .fee-table td {
        border: 1px solid #000;
        padding: 3px 6px;
    }

    .total-field {
        background: #e2e8f0;
        font-weight: 900;
        text-align: right;
    }

    .grand-total-box {
        border: 2px solid #000;
        margin-top: 8px;
        padding: 6px;
        background: #f8fafc;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .grand-total-label { font-weight: 900; font-size: 0.9rem; text-transform: uppercase; }
    .grand-total-val { font-weight: 900; font-size: 1.1rem; }

    .in-words {
        font-size: 0.7rem;
        margin-top: 4px;
        font-style: italic;
        border-bottom: 1px solid #ddd;
        padding-bottom: 2px;
    }

    .signature-row {
        display: flex;
        justify-content: space-between;
        margin-top: 25px;
        font-size: 0.7rem;
        font-weight: 700;
    }

    .sig-line { border-top: 1px solid #000; width: 45%; text-align: center; padding-top: 4px; }

    .paid-stamp {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-15deg);
        border: 4px double #dc2626;
        color: #dc2626;
        padding: 10px 40px;
        font-size: 3rem;
        font-weight: 900;
        opacity: 0.2;
        pointer-events: none;
        z-index: 10;
        text-transform: uppercase;
    }

    .no-print-btn-float {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 1000;
    }
</style>

<div class="no-print p-4 bg-light border-bottom mb-4 d-flex justify-content-between align-items-center">
    <div>
        <h3 class="mb-0 fw-bold text-dark">Challan Preview - <span class="text-primary">#{{ $challanView->id }}</span></h3>
        <p class="text-muted small mb-0">Generated for Class: <strong>{{ $challanView->class_name }}</strong> | Period: <strong>{{ $challanView->month }}-{{ $challanView->year }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('challan') }}" class="btn btn-outline-dark px-4 rounded-pill">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
        <button onclick="window.print()" class="btn btn-primary px-4 rounded-pill shadow">
            <i class="bi bi-printer-fill me-2"></i>Print Legal Landscape
        </button>
    </div>
</div>

<div class="challan-page">
    <div class="challan-grid">
        @php
            $copies = ['Bank Copy', 'School Copy', 'Accounts Copy', 'Student Copy'];
            $genderWord = ($student && strtolower((string)$student->gender) === 'female') ? 'D/O' : 'S/O';
            $parentName = $parent->parentName ?? 'N/A';
            $studentName = $student->studentName ?? ($challanView->student_name ?? 'ALL STUDENTS');
            $issuedDate = date('d/m/Y', strtotime($challanView->created_at ?? 'now'));
            $dueDate = date('d/m/Y', strtotime(($challanView->created_at ?? 'now') . ' +10 days'));
        @endphp

        @foreach($copies as $copyName)
        <div class="challan-copy">
            @if($challanView->status == 'paid' || $challanView->paid == 'YES')
                <div class="paid-stamp">PAID</div>
            @endif
            <div class="copy-tag">{{ $copyName }}</div>
            
            <div class="school-header">
                <div class="school-name">{{ $school->schoolName ?? 'CloudiSchool' }}</div>
                <div style="font-size: 0.65rem; font-weight: 600; text-transform: uppercase;">{{ $school->address ?? 'Islamabad, Pakistan' }}</div>
            </div>

            <div class="bank-header">Meezan Bank Limited</div>
            <div class="bank-info text-center">
                <strong>{{ $school->bank_account_title ?? 'School Revenue Account' }}</strong><br>
                A/C: <span class="fw-bold">{{ $school->bank_account_number ?? '0000-0000-0000' }}</span>
                @if(!empty($school->bank_iban)) | IBAN: <span class="fw-bold">{{ $school->bank_iban }}</span> @endif
            </div>

            <div class="student-meta">
                <div class="d-flex justify-content-between mb-1">
                    <span>Issued: <span class="meta-val">{{ $issuedDate }}</span></span>
                    <span>No: <span class="meta-val">{{ $challanView->id }}</span></span>
                    <span>Due: <span class="meta-val text-danger">{{ $dueDate }}</span></span>
                </div>
                <div class="mb-1">Name: <span class="meta-val d-inline-block" style="min-width: 150px;">{{ $studentName }}</span> {{ $genderWord }} <span class="meta-val">{{ $parentName }}</span></div>
                <div class="d-flex justify-content-between">
                    <span>G.R. No: <span class="meta-val">{{ $challanView->grno }}</span></span>
                    <span>Class: <span class="meta-val">{{ $challanView->class_name }}</span></span>
                    <span>Period: <span class="meta-val">{{ $challanView->month }}-{{ $challanView->year }}</span></span>
                </div>
            </div>

            <div class="row gx-1">
                <div class="col-6">
                    <table class="fee-table">
                        <thead>
                            <tr><th style="width: 20px;">#</th><th>Govt. Fee</th><th class="text-end">Rs.</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>Admission</td><td class="text-end fw-bold">{{ number_format($challanView->admission ?? 0, 2) }}</td></tr>
                            <tr><td>2</td><td>Tuition</td><td class="text-end fw-bold">{{ number_format($challanView->tution_fee ?? 0, 2) }}</td></tr>
                            <tr><td>3</td><td>Breakage</td><td class="text-end fw-bold">{{ number_format($challanView->breakage ?? 0, 2) }}</td></tr>
                            <tr><td>4</td><td>Misc.</td><td class="text-end fw-bold">{{ number_format($challanView->misc ?? 0, 2) }}</td></tr>
                            <tr><td>5</td><td>SLC/CLC</td><td class="text-end fw-bold">{{ number_format($challanView->clc ?? 0, 2) }}</td></tr>
                            <tr class="total-field">
                                <td colspan="2">SUB TOTAL (G)</td>
                                <td>{{ number_format($challanView->total ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-6">
                    <table class="fee-table">
                        <thead>
                            <tr><th style="width: 20px;">#</th><th>Funds</th><th class="text-end">Rs.</th></tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>IDF</td><td class="text-end fw-bold">{{ number_format($challanView->idf ?? 0, 2) }}</td></tr>
                            <tr><td>2</td><td>Exams</td><td class="text-end fw-bold">{{ number_format($challanView->exams ?? 0, 2) }}</td></tr>
                            <tr><td>3</td><td>IT/Computer</td><td class="text-end fw-bold">{{ number_format($challanView->it ?? 0, 2) }}</td></tr>
                            <tr><td>4</td><td>{{ $fund4_label }}</td><td class="text-end fw-bold">{{ number_format($challanView->csf ?? 0, 2) }}</td></tr>
                            <tr><td>5</td><td>RDF / CDF</td><td class="text-end fw-bold">{{ number_format($challanView->rdfcdf ?? 0, 2) }}</td></tr>
                            <tr><td>6</td><td>Security</td><td class="text-end fw-bold">{{ number_format($challanView->security_fund ?? 0, 2) }}</td></tr>
                            <tr class="total-field">
                                <td colspan="2">SUB TOTAL (F)</td>
                                <td>{{ number_format($totalFee ?? 0, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grand-total-box">
                <div class="grand-total-label">Grand Total Payable</div>
                <div class="grand-total-val">PKR {{ number_format($GTotal ?? 0, 2) }}</div>
            </div>
            
            <div class="in-words text-uppercase">
                <strong>In Words:</strong> {{ $total_fee_in_words }} ONLY
            </div>

            <div class="signature-row">
                <div class="sig-line">Depositor's Signature</div>
                <div class="sig-line">Bank Officer/Stamp</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Floating Action Button for Mobile Interaction -->
<div class="no-print no-print-btn-float d-lg-none">
    <button onclick="window.print()" class="btn btn-primary btn-lg rounded-circle shadow-lg" style="width: 60px; height: 60px;">
        <i class="bi bi-printer"></i>
    </button>
</div>
@endsection
