@extends('layouts.app')

@section('content')
<div class="fluid-container">
    <div class="row justify-content-center">
        
        <div class="col-md-12">
            <div class="card">
                <div style="background: #0072ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to right, #E4E5E6, #0072ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to right, #E4E5E6, #0072ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
 color:#000; font-size:27px; font-weight:bold; text-align:left;" class="card-header"> S T U D E N T S </div>

                <div class="card-body">
                    <div class="row"><hr/>
                        <div class="col-md-6">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="card">
                                <div style="background: #00c6ff;  /* fallback for old browsers */
background: -webkit-linear-gradient(to bottom, #0072ff, #00c6ff);  /* Chrome 10-25, Safari 5.1-6 */
background: linear-gradient(to bottom, #0072ff, #00c6ff); /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
" class="card-header">
                                    <h5 style="color:#fff">Edit Student: {{$studentDetail->studentName}}</h5>
                                </div>
                                <div style="background-color:#fff;" class="card-body">
                                <form id="studentAddForm" action="{{url('updateStudent')}}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{$studentDetail->id}}"/>
                                <label style="font-weight:bold;">Branch Name</label>
                                <select class="form-control select2" name="school_id">
                                    @foreach($schoolList as $rowSchoolList)
                                        <option value="{{$rowSchoolList->id}}">{{$rowSchoolList->schoolName}}</option>
                                    @endforeach
                                </select><br/>
                                
                                <label style="font-weight:bold;">Parent Name <strong><em></em></strong></label>
                                
                                <select class="form-control select2" name="parent_id">
                                <option value="{{$studentDetail->parentId}}">{{$studentDetail->parentName}}</option>
                                    @foreach($parentList as $rowParentList)
                                        <option value="{{$rowParentList->id}}">{{$rowParentList->parentName}}</option>
                                    @endforeach
                                </select><br/>

                                <label style="font-weight:bold;">Class Name</label>
                                <select class="form-control select2" name="class_id">
                                    <option value="{{$studentDetail->class_id}}">{{$studentDetail->className}}</option>
                                    @foreach($classList as $rowClassList)
                                        <option value="{{$rowClassList->id}}">{{$rowClassList->className}}</option>
                                    @endforeach
                                </select><br/>
                                <label  style="font-weight:bold;">GR No</label>
                                <input required class="form-control" name="grno" value="{{$studentDetail->grno}}" type="text"/><br/>
                                <label  style="font-weight:bold;">Student Name</label>
                                <input required class="form-control" name="studentName" value="{{$studentDetail->studentName}}" type="text"/><br/>
                                <label  style="font-weight:bold;">Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="{{$studentDetail->gender}}">{{$studentDetail->gender}}</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                                <label  style="font-weight:bold;">Session</label>
                                <select class="form-control select2" name="session">
                                    <option value="{{$studentDetail->session}}">{{$studentDetail->session}}</option>
                                    @foreach($academicYears as $year)
                                        <option value="{{$year->academicYear}}">{{$year->academicYear}}</option>
                                    @endforeach
                                </select><br/>
                                <label  style="font-weight:bold;">Status</label>
                                <select class="form-control" name="status">
                                    <option value="{{$studentDetail->status}}">{{$studentDetail->status}}</option>
                                    <option value="active">Active</option>
                                    <option value="SLC">SLC</option>
                                    <option value="Repeat">Repeat</option>
                                </select>
                                <hr/>
                                <input class="form-control btn btn-success" name="submit" value="Update Student" type="submit"/>
                            </form>
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
