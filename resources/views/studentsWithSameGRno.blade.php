@extends('layouts.app')

@section('content')

<h1>Students List with same GRNO (If Any)</h1>


<table class="table table-stripped">
    <tr>
        <th>G.R. No</th> <th>Count</th> <th>Student Info</th>
    </tr>
    @foreach($duplicateStudents as $row)
        <tr>
            <td>{{$row->grno}}</td>
            <td>{{$row->grno_count}}</td>
            <td>
                @php
                    $studentList = DB::table('students')->where('grno','=',$row->grno)->get();
                @endphp
                @foreach($studentList as $rowStudentList)
                    {{$rowStudentList->studentName}} - {{$rowStudentList->className}} <br/>
                    Parent Name: 
                    @php
                    $parentName = DB::table('parents')->where('id','=',$rowStudentList->parent_id)->first();
                    if($parentName){
                    echo $parentName->parentName;
                    echo "<br/>";
                    }else{
                    echo "<br/>";
                    }
                    
                @endphp
                
                @endforeach
                
            </td>
        </tr>
    @endforeach
    
</table>
@endsection
