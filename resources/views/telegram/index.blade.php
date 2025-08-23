@extends('layouts.admin')

@section('scriptcss')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tarekraafat/autocomplete.js@10.2.9/dist/css/autoComplete.min.css">
@endsection

@section('content')
<div class="container"> 
<h1>หมอพร้อม แจ้งเตือน</h1>
    <div class="card">
        <div class="card-header">
            ทดสอบแจ้งเตือนผ่านหมอพร้อม
            <img src="{{ asset('img/หมอพร้อม-icon.png') }}" alt="ไอคอน" width="64" height="64">   
        </div>
        <div class="card-body">
        <div class="form-group">
            <label for="fullname">ชื่อ-นามสุกล</label>
            <input type="text" class="form-control" id="fullname" name="fullname" autocomplete="off">
            <small id="fullname" class="form-text text-muted">กรอกชื่อ-นามสกุล</small>
        </div> 

        <div class="form-group">
            <input type="hidden" id="ssn_id">
            <button class="btn btn-success" id="testMorpromt">ทดสอบ หมอพร้อม</button>
            <label class="text-muted" id="testfullname">ทดสอบ หมอพร้อม</label>
        </div>
        </div>
    </div>

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
                <h5>ข้อมูลผู้ส่ง:</h5>
                <ul>
                    <li>User ID: {{ $data['message']['from']['id'] }}</li>
                    <li>Name: {{ $data['message']['from']['first_name'] }} {{ $data['message']['from']['last_name'] ?? '' }}</li>
                </ul>

                {{-- ข้อมูลข้อความ --}}
                <h5 class="mt-3">ข้อมูลข้อความ:</h5>
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
                        <label for="chatid"></label>
                        <input type="hidden" class="form-control" id="chatid" name="chatid" readonly value="{{ $data['message']['from']['id'] }}">                        
                        <button id="testNotify" type="button" class="btn btn-info" value="{{ $data['message']['from']['id'] }}" >ส่งทดสอบ</button>    
                    </div>
                    <div class="form-group">
                        <label for="chatid">รหัส</label>
                        <input type="text" class="form-control" id="id" name="id" readonly required>
                    </div>
                    <!-- <div class="form-group">
                        <label for="fullname">ชื่อ-นามสุกล</label>
                        <input type="text" class="form-control" id="fullname" name="fullname" autocomplete="off">
                        <small id="fullname" class="form-text text-muted">กรอกชื่อ-นามสกุล</small>
                    </div>                     -->
                    
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                    
                </form>
            </div> 
        </div>
    @else
        <div class="alert alert-warning">ส่งข้อความในกลุ่มก่อน 1 ครั้ง</div>
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
    document.getElementById("ssn_id").value = event.detail.selection.value.ssn_id;
    document.getElementById("fullname").value = event.detail.selection.value.ssn_name;
    document.getElementById("testfullname").innerHTML = event.detail.selection.value.ssn_name;
    // console.log(event.detail);
});


document.querySelector("#testNotify").addEventListener("click", async function (event) {
    event.preventDefault();
    const respone = await  fetch(`{{URL('telegram.test')}}/`+this.value);
    if(!respone.ok) throw new Error(`Response status: ${response.status}`);
    alert('สำเร็จ');
});

document.querySelector("#testMorpromt").addEventListener("click", async function (event) {
    event.preventDefault();
    let ssn = document.getElementById("ssn_id").value;
    const respone = await  fetch(`{{URL('telegram.morpromt')}}/`+ssn);
    if(!respone.ok) throw new Error(`Response status: ${response.status}`);
    alert('สำเร็จ');
});

</script>

@endsection