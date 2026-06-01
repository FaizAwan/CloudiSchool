@extends('layouts.app')
@php

function numberToWords($number) {
    $ones = array(
        0 => 'zero', 1 => 'one', 2 => 'two', 3 => 'three', 4 => 'four', 5 => 'five', 6 => 'six', 7 => 'seven', 8 => 'eight', 9 => 'nine',
        10 => 'ten', 11 => 'eleven', 12 => 'twelve', 13 => 'thirteen', 14 => 'fourteen', 15 => 'fifteen', 16 => 'sixteen', 17 => 'seventeen', 18 => 'eighteen', 19 => 'nineteen'
    );
    $tens = array(
        0 => '', 1 => '', 2 => 'twenty', 3 => 'thirty', 4 => 'forty', 5 => 'fifty', 6 => 'sixty', 7 => 'seventy', 8 => 'eighty', 9 => 'ninety'
    );
    $hundreds = array(
        '', 'one hundred', 'two hundred', 'three hundred', 'four hundred', 'five hundred', 'six hundred', 'seven hundred', 'eight hundred', 'nine hundred'
    );

    if ($number < 20) {
        return $ones[$number];
    } elseif ($number < 100) {
        return $tens[floor($number / 10)] . (($number % 10 !== 0) ? ' ' . $ones[$number % 10] : '');
    } elseif ($number < 1000) {
        return $hundreds[floor($number / 100)] . (($number % 100 !== 0) ? ' and ' . numberToWords($number % 100) : '');
    }

    return '';
}

$grno = $challanView->grno;
@endphp


@section('content')
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    tr{
        height:10px !important;
    }
    label{
        color:#000;
        font-weight:bold;
    }
    .lineBorder{
        border:1px solid #000;
    }
</style>


<div style="" class="fluid-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div style="background: #0072ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #E4E5E6, #0072ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #E4E5E6, #0072ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
color:#000; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> E D I T  &nbsp;&nbsp; C H A L L A N  </div>
                <div class="card-body">
    <style>

@media print {
        /* Define landscape orientation and legal size for printing */
        @page {
            size: legal landscape;
        }
        tr{
            height:10px !important;
        }
        body {
            margin: 0;
            padding: 0;
        }
        #printTable {
            /* Adjust table styles for printing as needed */
            width: 100%;
            border-collapse: collapse;
        }
        /* Additional print styles as needed */
    }

.horizontal-tiles {
    display: inline-block;
    width: 25%; /* Adjust the width as needed */
}

.inner-table {
    border-collapse: collapse;
    width: 100%;
}

/* Additional styling as needed */
        body {
            font-family: "sans-serif", sans-serif;
            font-size:14px !important;
        }
        td.center {
            text-align:center;
            border-bottom:1px solid #000;
            font-weight:bold;
        }
        td.copy {
            text-align:left;
            border-bottom:none;
            font-weight:bold;
        }
        td.left {
            text-align:left;
            border-bottom:1px solid #000;
            font-weight:bold;
        }
        tr.allBorders td.border{
            border:1px solid #000 !important;
            
        }

        tr.allBorders td.noBorder{
            border:1px solid #fff !important;
            
        }

        
        tr.lineBorders td{
            border:1px solid #000;
        }
        td.border{
            border:1px solid #000  !important;
        }
    </style>
<!--<button style="float:right;" class="btn btn-success btn-lg" id="printButton">Print</button>-->
    <div class="fluid-container">
        <table id="printTable">
            <tr>
                <td class="copy" style="horizontal-tiles ">
                    <form action="{{route('editChallanByChallanID')}}" method="POST">
                        @csrf
                        
                        <input type="hidden" name="challanID" value="{{$challanView->id}}">
                        
                    
                    <table class="inner-table" style="border:1px solid #000;">
                        <tr>
                            <td colspan="6" style="border-bottom:1px solid #000" class="center">FEE CHALLAN ( NON - REFUNDABLE )</td>
                        </tr>
                        <tr>
                            <td colspan="6"   style="border-bottom:1px solid #000" class="center">B A N K &nbsp;&nbsp;&nbsp;  C O P Y </td>
                        </tr>
                        <tr>
                            <td colspan="6"  style="border-bottom:1px solid #000" class="center">{{ $school->schoolName ?? '' }}</td>
                        </tr>
                        <tr>
                            <td colspan="6"  style="border-bottom:1px solid #000" class="center">{{ $school->bank_name ?? '' }}</td>
                        </tr>
                        <tr>
                            <td colspan="6"  style="border-bottom:1px solid #000" class="center">{{ $school->bank_account_title ?? 'STUDENT FUND' }}</td>
                        </tr>
                        <tr>
                            <td style="border-bottom:1px solid #000" class="left" colspan="6" >A/C No # {{ $school->bank_account_number ?? '' }} @if(!empty($school->bank_iban)) &nbsp;&nbsp; IBAN: {{ $school->bank_iban }} @endif @if(!empty($school->bank_branch)) &nbsp;&nbsp; Branch: {{ $school->bank_branch }} @endif</td>
                        </tr>
                        <tr>
                            <td style="font-weight:bold;" style="border-bottom:1px solid #000" class="left" colspan="6">
                            @php
                                $issuedDate = strtotime($challanView->year . '-' . $challanView->month . '-01');
                                $dueDate = strtotime('+29 days', $issuedDate);
                                
                                $studentId = DB::table('students')->where('studentName', '=', $challanView->student_id)->first();
                                $parentName = null;
                                if ($studentId) {
                                    $parentName = DB::table('parents')->where('id', '=', $studentId->parent_id)->first();
                                }
                            @endphp
                            Issued on:<strong> {{ date('d/m/Y', $issuedDate) }} </strong>

                            Due Date: <strong>{{ date('d/m/Y', $dueDate) }}</strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="border-bottom:1px solid #000" class="left" colspan="6">Period = From:
                                @php
                                    $currentMonthUpper = strtoupper(date('F', $issuedDate));
                                    $currentYear = (int) $challanView->year;
                                    $months = ["JANUARY","FEBRUARY","MARCH","APRIL","MAY","JUNE","JULY","AUGUST","SEPTEMBER","OCTOBER","NOVEMBER","DECEMBER"];
                                @endphp
                                <select id="fromMonth">
                                    @foreach($months as $m)
                                        <option value="{{ $m }}" {{ $m == $currentMonthUpper ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>-
                                <select id="fromYear">
                                    @for($y = 2023; $y <= 2030; $y++)
                                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; To
                                <select id="toMonth">
                                    @foreach($months as $m)
                                        <option value="{{ $m }}" {{ $m == $currentMonthUpper ? 'selected' : '' }}>{{ $m }}</option>
                                    @endforeach
                                </select>-
                                <select id="toYear">
                                    @for($y = 2023; $y <= 2030; $y++)
                                        <option value="{{ $y }}" {{ $y == $currentYear ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                                <input style="width:50px;" id="how-many-Months" readonly name="how-many-Months" type="text"> Months</td>
                        </tr>
                        <tr>
                            <td style="border-bottom:1px solid #000" class="left" colspan="6">Student Name: <strong> {{ strtoupper($challanView->student_id) }}</strong> @if($studentId && $studentId->gender == 'Female') D/O @else S/O @endif <strong> {{ strtoupper(optional($parentName)->parentName ?? '') }}</strong></td>
                        </tr>
                        <tr>
                            <td style="border-bottom:1px solid #000" class="left" colspan="6">G.R No : <strong>@php
                                
                                $parts = explode('-', $grno);
                                $modifiedGrno = $parts[0];
                                
                                // If the string contains a space, remove all characters after the space
                                if (strpos($modifiedGrno, ' ') !== false) {
                                    $modifiedGrno = preg_replace('/\s.*/', '', $modifiedGrno);
                                }
                                @endphp {{$modifiedGrno}}-{{$challanView->class_name}} </strong>&nbsp; &nbsp;&nbsp; &nbsp;&nbsp; &nbsp; Class / Sec : <strong>{{$challanView->class_name}}</strong></td>
                        </tr>
                        <tr class="allBorders">
                        <td style="border:1px solid #000"  class="border">Sr</td>
                        <td style="border:1px solid #000"  class="border">Govt Fee</td>
                        <td style="border:1px solid #000"  class="border">Rs</td>
                        <td style="border:1px solid #000"  class="border">Sr</td>
                        <td style="border:1px solid #000"  class="border">Fund</td>
                        <td style="border:1px solid #000"  class="border">Rs</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="border">1</td>
                            <td style="border:1px solid #000"  class="border">Admission</td>
                            <td style="border:1px solid #000"  class="border"><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('admission', $challanView->admission) }}" name="admission"/></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000" class="border">2</td>
                            <td style="border:1px solid #000" class="border">Tution</td>
                            <td style="border:1px solid #000" class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('tution_fee', $challanView->tution_fee) }}" name="tution_fee"/></strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="border">3</td>
                            <td style="border:1px solid #000"  class="border">Breakage</td>
                            <td style="border:1px solid #000"  class="border"><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('breakage', $challanView->breakage) }}" name="breakage"/></td>
                            <td style="border:1px solid #000"  class="border">1</td>
                            <td style="border:1px solid #000"  class="border">IDF</td>
                            <td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('idf', $challanView->idf) }}" name="idf"/></strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="border">4</td>
                            <td style="border:1px solid #000"  class="border">Misc</td>
                            <td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('misc', $challanView->misc) }}" name="misc"/></strong></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="border">5</td>
                            <td style="border:1px solid #000"  class="border">SLC</td>
                            <td style="border:1px solid #000"  class="border"><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('slc', $challanView->clc) }}" name="slc"/></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <!--<td style="border:1px solid #000"  class="border"></td>-->
                            <!--<td style="border:1px solid #000"  class="border">Total</td>-->
                            <!--<td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{$challanView->tution_fee}}" name="breakage"/></strong></td>-->
                            <!--<td></td>-->
                            <!--<td></td>-->
                            <!--<td></td>-->
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"  class="border">2</td>
                            <td style="border:1px solid #000"  class="border">Exams</td>
                            <td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('exams', $challanView->exams) }}" name="exams"/></strong></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"  class="border">3</td>
                            <td style=\"border:1px solid #000\"  class=\"border\">{{ $fund4_label ?? 'CSF' }}</td>
                            <td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('it', $challanView->it) }}" name="it"/></strong></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"  class="border">4</td>
                            <td style="border:1px solid #000"  class="border">CSF</td>
                            <td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('csf', $challanView->csf) }}" name="csf"/></strong></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"  class="border">5</td>
                            <td style="border:1px solid #000"  class="border">RDF / CDF </td>
                            <td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('rdfcdf', $challanView->rdfcdf) }}" name="rdfcdf"/></strong></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"  class="border">6</td>
                            <td style="border:1px solid #000"  class="border">Security </td>
                            <td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{ old('security_fund', $challanView->security_fund) }}" name="security_fund"/></strong></td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td class="border"></td>-->
                        <!--    <td style="border:1px solid #000"  class="border">Total </td>-->
                        <!--    <td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="@php echo $challanView->total_fee - $challanView->tution_fee @endphp" name="total"/></strong></td>-->
                        <!--</tr>-->
                        <!--<tr style="border-bottom:1px solid #000;">-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td></td>-->
                        <!--    <td class="border"></td>-->
                        <!--    <td style="border:1px solid #000"  class="border">G.Total </td>-->
                        <!--    <td style="border:1px solid #000"  class="border"><strong><input style="border:2px solid #000;" type="text" class="form-control" value="{{$challanView->total_fee}}" name="total_fee"/></strong></td>-->
                        <!--</tr>-->
                        <tr>
                            <td colspan="6">
                                    <input type="submit" class="btn btn-primary form-control" value="Edit Student Record"/>    
                            </td>
                            
                        </tr>
                            
                        </form>
                    </table>

                </td>
                
                </td>
                <td class="copy" style="horizontal-tiles ">

                </td>
            </tr>
        </table>
    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.3.min.js"></script>

<script>
    
    
    $(document).ready(function() {
        
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
    });

    </script>
@endsection
