@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">

                {{-- {{dd($depart) }} --}}
                <div class="card-header">
                    {{ __('เลือกที่ท่านเข้าใช้งาน') }}
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('depart_') }}">
                        @csrf
                        <input id="username" type="hidden" class="form-control @error('section') is-invalid @enderror"
                            name="username" value="{{ Auth::user()->username }}" required autocomplete="section"
                            autofocus>
                        <div class="form-group row">
                            <label for="section"
                                class="col-md-4 col-form-label text-md-right">{{ __('เลือกบริการ') }}</label>
                            <div class="col-md-6">
                                {{-- <input id="section" type="text" class="form-control @error('section') is-invalid @enderror" name="section" value="{{ old('section') }}"
                                required autocomplete="section" autofocus> --}}
                                <select id="service" type="text" class="form-control" name="service"
                                    value="{{ old('service') }}" required autocomplete="service"
                                    data-live-search="true" data-dropup-auto="true" title="--กรุณาเลือกบริการ--"
                                    onchange="show(this)" autofocus>
                                    <option selected hidden value=''>--เลือกบริการ--</option>
                                    <option value="เคลื่อนย้ายผู้ป่วย">เคลื่อนย้ายผู้ป่วย</option>
                                    <option value="ตามรับยา">ตามรับยา</option>
                                </select>
                                @error('service')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row" id="depart_1" style="display: none">
                            <label for="section"
                                class="col-md-4 col-form-label text-md-right">{{ __('แผนก') }}</label>
                            <div class="col-md-6">
                                {{-- <input id="section" type="text" class="form-control @error('section') is-invalid @enderror" name="section" value="{{ old('section') }}"
                                required autocomplete="section" autofocus> --}}
                                <select id="section" type="text" class="selectpicker form-control" name="section"
                                    value="{{ old('section') }}" required autocomplete="section"
                                    data-live-search="true" data-dropup-auto="true" title="--กรุณาเลือกแผนก--"
                                    autofocus>
                                    <option selected hidden value=''>--กรุณาเลือกแผนก--</option>
                                    @foreach($depart as $row)
                                        <option value="{{ $row->department }}">{{ $row->department }}</option>
                                    @endforeach
                                </select>
                                @error('section')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('ยืนยัน') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    function show(t) {
        if (t.value == 'เคลื่อนย้ายผู้ป่วย' || t.value == 'ตามรับยา') {
            document.getElementById('depart_1').style.display = '';
            $('#section').prop('required', true);
        } else {
            document.getElementById('depart_1').style.display = 'none';
            $('#section').prop('required', false);
            $('#section').prop('value', null);
        }
    }

</script>
@endsection
