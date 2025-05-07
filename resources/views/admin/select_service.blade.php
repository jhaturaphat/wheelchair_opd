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
                    <form method="POST" action="{{ route('depart_admin') }}">
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
                                    data-live-search="true" data-dropup-auto="true" title="--กรุณาเลือกบริการ--" autofocus>
                                    <option selected hidden value=''>--เลือกบริการ--</option>
                                    <option value="เคลื่อนย้ายผู้ป่วย">เคลื่อนย้ายผู้ป่วย</option>
                                    <option value="พนักงานขับรถ">พนักงานขับรถ</option>
                                    <option value="พยาบาลรีเฟอร์">พยาบาลรีเฟอร์</option>
                                </select>
                                @error('service')
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
@endsection
