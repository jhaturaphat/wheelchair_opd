@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Message Details</h1>

    {{-- ตรวจสอบก่อนว่ามีข้อมูล --}}
    @if(isset($data))
        <div class="card">
            <div class="card-header">
                Update ID: {{ $data['update_id'] }}
            </div>
            <div class="card-body">
                {{-- ข้อมูลผู้ส่ง --}}
                <h5>From:</h5>
                <ul>
                    <li>User ID: {{ $data['message']['from']['id'] }}</li>
                    <li>Name: {{ $data['message']['from']['first_name'] }} {{ $data['message']['from']['last_name'] ?? '' }}</li>
                </ul>

                {{-- ข้อมูลข้อความ --}}
                <h5 class="mt-3">Message:</h5>
                <div class="alert alert-primary">
                    <strong>Text:</strong> {{ $data['message']['text'] ?? 'No text' }}<br>
                    <strong>Date:</strong> {{ date('Y-m-d H:i:s', $data['message']['date']) }}<br>
                    @isset($data['message']['entities'])
                        <strong>Command:</strong> {{ $data['message']['entities'][0]['type'] }}
                    @endisset
                </div>

                {{-- ข้อมูลแชท --}}
                <h5 class="mt-3">Chat Info:</h5>
                <ul>
                    <li>Chat ID: {{ $data['message']['chat']['id'] }}</li>
                    <li>Title: {{ $data['message']['chat']['title'] }}</li>
                    <li>Type: {{ $data['message']['chat']['type'] }}</li>
                </ul>
            </div>
        </div>
    @else
        <div class="alert alert-warning">No message data available</div>
    @endif
</div>
@endsection