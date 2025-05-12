@extends('layouts.admin')

@section('scriptcss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.9/dist/css/autoComplete.min.css">
@endsection

@section('content')
<div class="container">    
    <h1>ลงทะเบียน Telegram</h1>

    {{-- ตรวจสอบก่อนว่ามีข้อมูล --}}
    @if(isset($data))
        <div class="card">
            <div class="card-header">
                ลงทะเบียนแจ้งเตือน Telegram
                <img src="{{ asset('img/telegram.svg') }}" alt="ไอคอน" width="32" height="32">                
            </div>
            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
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
                        <strong>ComYmand:</strong> {{ $data['message']['entities'][0]['type'] }}
                    @endisset
                </div>
                
                <form action="{{route('telegram')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="chatid">Telegram ID</label>
                        <input type="text" class="form-control" id="chatid" name="chatid" readonly value="{{ $data['message']['from']['id'] }}">                        
                    </div>
                    <div class="form-group">
                        <label for="chatid">รหัส</label>
                        <input type="text" class="form-control" id="id" name="id" readonly>
                    </div>
                    <div class="form-group">
                        <label for="fullname">ชื่อ-นามสุกล</label>
                        <input type="text" class="form-control" id="fullname" name="fullname">
                        <small id="fullname" class="form-text text-muted">กรอกชื่อ-นามสกุล</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                    
                </form>
            </div> 
        </div>
    @else
        <div class="alert alert-warning">No message data available</div>
    @endif
    
</div>
@endsection

@section('scriptjs')
<script src="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.9/dist/autoComplete.min.js"></script>
<script>
    // API Advanced Configuration Object
const autoCompleteJS = new autoComplete({
    selector: "#fullname",
    placeHolder: "Search for Food...",
    data: {
        src: async (query) => {
        try {
            // Fetch Data from external Source
            const source = await fetch(`{{URL('telegram')}}/${query}`);
            // Data should be an array of `Objects` or `Strings`
            const data = await source.json();

            return data;
        } catch (error) {
            return error;
        }
        },
        // Data source 'Object' key to be searched
        keys: ["ssn_name"]
    },

    resultsList: {
        element: (list, data) => {
            if (!data.results.length) {
                // Create "No Results" message element
                const message = document.createElement("div");
                // Add class to the created element
                message.setAttribute("class", "no_result");
                // Add message text content
                message.innerHTML = `<span>Found No Results for "${data.query}"</span>`;
                // Append message element to the results list
                list.prepend(message);
            }
        },
        noResults: true,
    },
    resultItem: {
        highlight: true,
    }
});

document.querySelector("#fullname").addEventListener("selection", function (event) {
    // "event.detail" carries the autoComplete.js "feedback" object
    document.getElementById("id").value = event.detail.selection.value.id;
    document.getElementById("fullname").value = event.detail.selection.value.ssn_name;
    console.log(event.detail);
});


</script>

@endsection