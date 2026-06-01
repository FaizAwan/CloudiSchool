@extends('layouts.app')

@section('content')

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

@endphp
<style>
    tr{
        height:10px !important;
    }
    label{
        color:#000;
        font-weight:bold;
    }
    
    @media print {
        /* Define landscape orientation and legal size for printing */
        @page {
            size: legal landscape;
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
</style>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />


<div class="fluid-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div style="background: #1488CC;  /* fallback for old browsers */
background: -webkit-linear-gradient(to left, #2B32B2, #1488CC);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to left, #2B32B2, #1488CC); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
color:#fff; box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19) !important; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> P R I N T &nbsp;&nbsp;  C H A L L A N    </div>
                <div class="card-body">
                    <div class="row"> <hr/>
                        <div class="col-md-12">
                            <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-header">
                                    <h5  style="color:#fff">Print Challan Classwise</h5>
                                </div>
                                <div style="background-color:#fff;" class="card-body">
                                    <form action="{{url('printClassWiseChallans')}}" method="POST">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-2">
                                                
                                                 <label>Schools</label>
                                                    <select name="school_id" class="form-control select2">
                                                        @foreach($schoolList as $rowClasses)
                                                            <option value="{{$rowClasses->id}}">{{$rowClasses->schoolName}}</option>
                                                        @endforeach
                                                    </select>
                                            </div>
                                            <div class="col-md-2">

                                            <label>Class</label>
                                            <select name="class_id" class="form-control select2">
                                                @foreach($classList as $rowClasses)
                                                    <option value="{{$rowClasses->className}}">{{$rowClasses->className}}</option>
                                                @endforeach
                                            </select>

                                            </div>

                                        <div class="col-md-2">

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
                                        <div class="col-md-2">
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
                                        
                                        
                                        <div class="col-md-2">
                                        <label> &nbsp; </label>
                                        <input class="form-control btn btn-success" name="submit" value="Print Challan" type="submit"/>
                                        
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <hr/>
                        </div>
                        
                        <div class="col-md-12">
                        <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-header">
                                    <h5 style="color:#fff">Challans Classwise</h5>
                                </div>
                                <div class="card-body">
                                    <div id="datatable-container">
                                        
                                        

<div class="fluid-container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
    <style>
        body {
            font-family: "Roboto", sans-serif;
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
        tr.allBorders td{
            border:1px solid #000;
            text-align:center;
        }
        td.border{
            border:1px solid #000;
        }
    </style>

<br/><br/>
<button style="float:right;" class="btn btn-success btn-lg" id="printButton">Print</button>    
    <div id="printTable" class="fluid-container">
        
        
        @foreach($challanList as $rowChallanList)
        
        @php 
        
            $studentId = DB::table('students')->where('className','=',$rowChallanList->class_name)->where('grno','=',$rowChallanList->grno)->first();
            $parentName = DB::table('parents')->where('id','=',$studentId->parent_id)->first();
        @endphp
        
                <table style="border-bottom:5px solid #000; margin-bottom:20px; width:100%">
            <tr>
                <td class="copy" style="width:24%; margin-left:10px; padding-left: 10px; padding-right: 10px;">
                    <table style="border:1px solid #000;">
                        <tr>
                            <td colspan="6" class="center"><strong><center>FEE CHALLAN (NON-REFUNDABLE)</center></strong></td>
                        </tr>
                        <tr>
                            <td colspan="6"   class="center"><center>B A N K &nbsp;&nbsp;&nbsp;  C O P Y </center></td>
                        </tr>
                        <tr>
                            <td colspan="6"  class="center"><center>FG FPS (2nd Shift) PAF BASE FAISAL KARACHI</center></td>
                        </tr>
                        <tr>
                            <td colspan="6"  class="center"><center>Bank Makramah Ltd</center></td>
                        </tr>
                        <tr>
                            <td colspan="6"  class="center"><center>STUDENT FUND</center></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6" ><center>A/C No # 1-99-15-26201-714-114164</center></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Issued on <strong>01/03/2024</strong> &nbsp;&nbsp;&nbsp; Due Date: <strong>30/03/2024</strong></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Period = From <strong> {{$rowChallanList->month}}-{{$rowChallanList->year}} </strong> &nbsp;&nbsp; To </td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Student Name: <strong>{{$rowChallanList->student_id}}</strong> 
                            @if($studentId->gender == 'Female') D/O @else S/O @endif <strong> {{strtoupper($parentName->parentName)}}</strong></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">G.R No : <strong>{{$rowChallanList->grno}}-{{$rowChallanList->class_name}}</strong> &nbsp;&nbsp; Class / Sec : <strong>{{$rowChallanList->class_name}}</strong></td>
                        </tr>
                        <tr class="allBorders">
                        <td style="border:1px solid #000"  class="">Sr</td>
                        <td style="border:1px solid #000"  class="">Govt Fee</td>
                        <td style="border:1px solid #000"  class="">Rs</td>
                        <td style="border:1px solid #000"  class="">Sr</td>
                        <td style="border:1px solid #000"  class="">Fund</td>
                        <td style="border:1px solid #000"  class="">Rs</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">1</td>
                            <td style="border:1px solid #000"  class="">Admission</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->admission}}</strong></td>
                            <td style="border:1px solid #000"  class="">1</td>
                            <td style="border:1px solid #000"  class="">IDF</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->idf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">2</td>
                            <td style="border:1px solid #000"  class="">Tution</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->tution_fee}}</strong></td>
                            <td style="border:1px solid #000"  class="">2</td>
                            <td style="border:1px solid #000"  class="">Exams</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->exams}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">3</td>
                            <td style="border:1px solid #000"  class="">Breakage</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->breakage}}</strong></td>
                            <td style="border:1px solid #000"  class="">3</td>
                            <td style="border:1px solid #000"  class="">IT</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->it}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">4</td>
                            <td style="border:1px solid #000"  class="">Misc</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->misc}}</strong></td>
                            <td style="border:1px solid #000"  class="">4</td>
                            <td style="border:1px solid #000"  class="">CSF</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->csf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">5</td>
                            <td style="border:1px solid #000"  class="">SLC</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->clc}}</strong></td>
                            <td style="border:1px solid #000"  class="">5</td>
                            <td style="border:1px solid #000"  class="">RDF / CDF </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->rdfcdf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class=""></td>
                            <td style="border:1px solid #000"  class="">Total</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total}}</strong></td>
                            <td style="border:1px solid #000"  class="">6</td>
                            <td style="border:1px solid #000"  class="">Security </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->security_fund}}</strong></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"></td>
                            <td style="border:1px solid #000"  class="">Total </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total_fee - $rowChallanList->total}}</strong></td>
                        </tr>
                        <tr style="border-bottom:1px solid #000;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"></td>
                            <td style="border:1px solid #000"  class="">G.Total </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total_fee}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                Rupees (In words) : (<strong><span style="font-weight:bold;">@php echo strtoupper(numberToWords($rowChallanList->total_fee)); @endphp ONLY </span></strong>)
                                <br/><br /><br /><br/>
                                
                                Depositor's Sign   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bank Officer's Sign
                            </td>
                            
                        </tr>
                    </table>

                </td>
                <td class="copy" style="width:24%; margin-left:10px; padding-left: 10px; padding-right: 10px;">
                    <table style="border:1px solid #000;">
                        <tr>
                            <td colspan="6" class="center"><strong><center>FEE CHALLAN (NON-REFUNDABLE)</center></strong></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="copy center"><center>S C H O O L 's &nbsp;&nbsp;&nbsp; C O P Y </center></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="center"><center>FG FPS (2nd Shift) PAF BASE FAISAL KARACHI</center></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="center"><center>Bank Makramah Ltd</center></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="center"><center>STUDENT FUND</center></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6"><center>A/C No # 1-99-15-26201-714-114164</center></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Issued on <strong>01/03/2024</strong> &nbsp;&nbsp; &nbsp; Due Date: <strong>30/03/2024</strong></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Period = From <strong> {{$rowChallanList->month}}-{{$rowChallanList->year}} </strong> &nbsp;&nbsp; To </td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Student Name: <strong>{{$rowChallanList->student_id}}</strong> @if($studentId->gender == 'Female') D/O @else S/O @endif <strong> 
                            {{strtoupper($parentName->parentName)}}</strong></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">G.R No : <strong>{{$rowChallanList->grno}}-{{$rowChallanList->class_name}}</strong> &nbsp;&nbsp; Class / Sec : <strong>{{$rowChallanList->class_name}}</strong></td>
                        </tr>
                        <tr class="allBorders">
                        <td style="border:1px solid #000"  class="">Sr</td>
                        <td style="border:1px solid #000"  class="">Govt Fee</td>
                        <td style="border:1px solid #000"  class="">Rs</td>
                        <td style="border:1px solid #000"  class="">Sr</td>
                        <td style="border:1px solid #000"  class="">Fund</td>
                        <td style="border:1px solid #000"  class="">Rs</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">1</td>
                            <td style="border:1px solid #000"  class="">Admission</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->admission}}</strong></td>
                            <td style="border:1px solid #000"  class="">1</td>
                            <td style="border:1px solid #000"  class="">IDF</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->idf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">2</td>
                            <td style="border:1px solid #000"  class="">Tution</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->tution_fee}}</strong></td>
                            <td style="border:1px solid #000"  class="">2</td>
                            <td style="border:1px solid #000"  class="">Exams</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->exams}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">3</td>
                            <td style="border:1px solid #000"  class="">Breakage</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->breakage}}</strong></td>
                            <td style="border:1px solid #000"  class="">3</td>
                            <td style="border:1px solid #000"  class="">IT</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->it}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">4</td>
                            <td style="border:1px solid #000"  class="">Misc</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->misc}}</strong></td>
                            <td style="border:1px solid #000"  class="">4</td>
                            <td style="border:1px solid #000"  class="">CSF</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->csf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">5</td>
                            <td style="border:1px solid #000"  class="">SLC</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->clc}}</strong></td>
                            <td style="border:1px solid #000"  class="">5</td>
                            <td style="border:1px solid #000"  class="">RDF / CDF </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->rdfcdf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class=""></td>
                            <td style="border:1px solid #000"  class="">Total</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total}}</strong></td>
                            <td style="border:1px solid #000"  class="">6</td>
                            <td style="border:1px solid #000"  class="">Security </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->security_fund}}</strong></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"></td>
                            <td style="border:1px solid #000"  class="">Total </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total_fee - $rowChallanList->total}}</strong></td>
                        </tr>
                        <tr style="border-bottom:1px solid #000;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"></td>
                            <td style="border:1px solid #000"  class="">G.Total </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total_fee}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                Rupees (In words) : (<strong><span style="font-weight:bold;">@php echo strtoupper(numberToWords($rowChallanList->total_fee)); @endphp ONLY </span></strong>)
                                <br/><br /><br /><br/>
                                
                                Depositor's Sign   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bank Officer's Sign
                            </td>
                            
                        </tr>
                    </table>

                </td>
                <td class="copy" style="width:24%; margin-left:10px; padding-left: 10px; padding-right: 10px;">
                <table style="border:1px solid #000;">
                    <tr>
                        <td colspan="6" class="center"><strong><center>FEE CHALLAN (NON-REFUNDABLE)</center></strong></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="center"><center> S T U D E N T 's  &nbsp;&nbsp;&nbsp; C O P Y </center></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="center"><center>FG FPS (2nd Shift) PAF BASE FAISAL KARACHI</center></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="center"><center>Bank Makramah Ltd<center></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="center"><center>STUDENT FUND<center></td>
                    </tr>
                    <tr>
                        <td class="left" colspan="6"><center>A/C No # 1-99-15-26201-714-114164</center></td>
                    </tr>
                    <tr>
                            <td class="left" colspan="6">Issued on <strong>01/03/2024</strong> &nbsp;&nbsp;&nbsp; Due Date: <strong>30/03/2024</strong></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Period = From <strong> {{$rowChallanList->month}}-{{$rowChallanList->year}} </strong> &nbsp;&nbsp; To </td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Student Name: <strong>{{$rowChallanList->student_id}}</strong> @if($studentId->gender == 'Female') D/O @else S/O @endif <strong> 
                            {{strtoupper($parentName->parentName)}}</strong></td>
                        </tr>
                       <tr>
                            <td class="left" colspan="6">G.R No : <strong>{{$rowChallanList->grno}}-{{$rowChallanList->class_name}}</strong> &nbsp;&nbsp; Class / Sec : <strong>{{$rowChallanList->class_name}}</strong></td>
                        </tr>
                        <tr class="allBorders">
                        <td style="border:1px solid #000"  class="">Sr</td>
                        <td style="border:1px solid #000"  class="">Govt Fee</td>
                        <td style="border:1px solid #000"  class="">Rs</td>
                        <td style="border:1px solid #000"  class="">Sr</td>
                        <td style="border:1px solid #000"  class="">Fund</td>
                        <td style="border:1px solid #000"  class="">Rs</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">1</td>
                            <td style="border:1px solid #000"  class="">Admission</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->admission}}</strong></td>
                            <td style="border:1px solid #000"  class="">1</td>
                            <td style="border:1px solid #000"  class="">IDF</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->idf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">2</td>
                            <td style="border:1px solid #000"  class="">Tution</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->tution_fee}}</strong></td>
                            <td style="border:1px solid #000"  class="">2</td>
                            <td style="border:1px solid #000"  class="">Exams</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->exams}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">3</td>
                            <td style="border:1px solid #000"  class="">Breakage</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->breakage}}</strong></td>
                            <td style="border:1px solid #000"  class="">3</td>
                            <td style="border:1px solid #000"  class="">IT</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->it}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">4</td>
                            <td style="border:1px solid #000"  class="">Misc</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->misc}}</strong></td>
                            <td style="border:1px solid #000"  class="">4</td>
                            <td style="border:1px solid #000"  class="">CSF</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->csf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">5</td>
                            <td style="border:1px solid #000"  class="">SLC</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->clc}}</strong></td>
                            <td style="border:1px solid #000"  class="">5</td>
                            <td style="border:1px solid #000"  class="">RDF / CDF </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->rdfcdf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class=""></td>
                            <td style="border:1px solid #000"  class="">Total</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total}}</strong></td>
                            <td style="border:1px solid #000"  class="">6</td>
                            <td style="border:1px solid #000"  class="">Security </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->security_fund}}</strong></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"></td>
                            <td style="border:1px solid #000"  class="">Total </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total_fee - $rowChallanList->total}}</strong></td>
                        </tr>
                        <tr style="border-bottom:1px solid #000;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"></td>
                            <td style="border:1px solid #000"  class="">G.Total </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total_fee}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                Rupees (In words) : (<strong><span style="font-weight:bold;">@php echo strtoupper(numberToWords($rowChallanList->total_fee)); @endphp ONLY </span></strong>)
                                <br/><br /><br /><br/>
                                
                                Depositor's Sign   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bank Officer's Sign
                            </td>
                            
                        </tr>
                </table>

                </td>
                <td class="copy" style="width:24%; margin-left:10px; padding-left: 10px; padding-right: 10px;">
                <table style="border:1px solid #000;">
                    <tr>
                        <td colspan="6" class="center"><strong><center>FEE CHALLAN (NON-REFUNDABLE)</center></strong></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="center"> <center>T E A C H  E R 's &nbsp;&nbsp;&nbsp; C O P Y</center> </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="center"><center>FG FPS (2nd Shift) PAF BASE FAISAL KARACHI</center></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="center"><center>Bank Makramah Ltd</center></td>
                    </tr>
                    <tr>
                        <td colspan="6" class="center"><center>STUDENT FUND</center></td>
                    </tr>
                    <tr>
                        <td class="left" colspan="6"><center>A/C No # 1-99-15-26201-714-114164</center></td>
                    </tr>
                    <tr>
                            <td class="left" colspan="6">Issued on <strong>01/03/2024</strong> &nbsp;&nbsp;&nbsp; Due Date: <strong>30/03/2024</strong></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Period = From <strong> {{$rowChallanList->month}}-{{$rowChallanList->year}} </strong> &nbsp;&nbsp; To </td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">Student Name: <strong>{{$rowChallanList->student_id}}</strong> @if($studentId->gender == 'Female') D/O @else S/O @endif <strong> 
                           
                            {{strtoupper($parentName->parentName)}}</strong></td>
                        </tr>
                        <tr>
                            <td class="left" colspan="6">G.R No : <strong>{{$rowChallanList->grno}}-{{$rowChallanList->class_name}}</strong> &nbsp;&nbsp; Class / Sec : <strong>{{$rowChallanList->class_name}}</strong></td>
                        </tr>
                        <tr class="allBorders">
                        <td style="border:1px solid #000"  class="">Sr</td>
                        <td style="border:1px solid #000"  class="">Govt Fee</td>
                        <td style="border:1px solid #000"  class="">Rs</td>
                        <td style="border:1px solid #000"  class="">Sr</td>
                        <td style="border:1px solid #000"  class="">Fund</td>
                        <td style="border:1px solid #000"  class="">Rs</td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">1</td>
                            <td style="border:1px solid #000"  class="">Admission</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->admission}}</strong></td>
                            <td style="border:1px solid #000"  class="">1</td>
                            <td style="border:1px solid #000"  class="">IDF</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->idf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">2</td>
                            <td style="border:1px solid #000"  class="">Tution</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->tution_fee}}</strong></td>
                            <td style="border:1px solid #000"  class="">2</td>
                            <td style="border:1px solid #000"  class="">Exams</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->exams}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">3</td>
                            <td style="border:1px solid #000"  class="">Breakage</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->breakage}}</strong></td>
                            <td style="border:1px solid #000"  class="">3</td>
                            <td style="border:1px solid #000"  class="">IT</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->it}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">4</td>
                            <td style="border:1px solid #000"  class="">Misc</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->misc}}</strong></td>
                            <td style="border:1px solid #000"  class="">4</td>
                            <td style="border:1px solid #000"  class="">CSF</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->csf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class="">5</td>
                            <td style="border:1px solid #000"  class="">SLC</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->clc}}</strong></td>
                            <td style="border:1px solid #000"  class="">5</td>
                            <td style="border:1px solid #000"  class="">RDF / CDF </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->rdfcdf}}</strong></td>
                        </tr>
                        <tr>
                            <td style="border:1px solid #000"  class=""></td>
                            <td style="border:1px solid #000"  class="">Total</td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total}}</strong></td>
                            <td style="border:1px solid #000"  class="">6</td>
                            <td style="border:1px solid #000"  class="">Security </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->security_fund}}</strong></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"></td>
                            <td style="border:1px solid #000"  class="">Total </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total_fee - $rowChallanList->total}}</strong></td>
                        </tr>
                        <tr style="border-bottom:1px solid #000;">
                            <td></td>
                            <td></td>
                            <td></td>
                            <td style="border:1px solid #000"></td>
                            <td style="border:1px solid #000"  class="">G.Total </td>
                            <td style="border:1px solid #000"  class=""><strong>{{$rowChallanList->total_fee}}</strong></td>
                        </tr>
                        <tr>
                            <td colspan="6">
                                Rupees (In words) : (<strong><span style="font-weight:bold;">@php echo strtoupper(numberToWords($rowChallanList->total_fee)); @endphp ONLY </span></strong>)
                                <br/><br /><br /><br/>
                                
                                Depositor's Sign   &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Bank Officer's Sign
                            </td>
                            
                        </tr>
                </table>
                

                </td>
            </tr>
        </table>
    
            
        @endforeach
        
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


        document.getElementById("printButton").addEventListener("click", function() {
            var printContent = document.getElementById("printTable").outerHTML;

            // Create a new window
            var printWindow = window.open('', '_blank');
            printWindow.document.open();
            printWindow.document.write('<html><head><title>Print Content</title></head><body>');

            // Write the content to the new window
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();

            // Print the window
            printWindow.print();
        });
    
</script>


@endsection
