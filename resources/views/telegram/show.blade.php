@extends('layouts.admin')

@section('scriptcss')

@endsection

@section('content')
 
<div class="container">
    @if(isset($model))
    <br>
    <div class="card">
        @if(isset($success))   
        <div class="card-header alert alert-success">
            <h1>{{$success}}</h1>
        </div>
        @endif
        <div class="card-body">
            <table class="table">
                <tr>
                    <td>id</td>
                    <td>{{$model->id}}</td>
                </tr> 
                <tr>
                    <td>ชื่อ-สกุล</td>
                    <td>{{$model->ssn_name}}</td>
                </tr> 
                <tr>
                    <td>รหัสแชท</td>
                    <td>{{$model->telegram_chat_id}}</td>
                </tr>                
            </table>
        </div>
    </div>
    @else
    <p>No data found</p>
    @endif

</div>

@endsection

@section('scriptjs')

@endsection