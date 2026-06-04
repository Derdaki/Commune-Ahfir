@extends('layouts.app')
@section('title', __('app.auth.register_title'))
@push('head')<style>.register-wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:75px 20px 30px}.register-card{width:100%;max-width:580px;border-top:5px solid transparent;border-image:linear-gradient(90deg,#087f5b,#3268d7,#7654c8,#d68b18) 1}.auth-icon{width:52px;height:52px;display:grid;place-items:center;border-radius:16px;background:linear-gradient(135deg,#e9e3ff,#fff3df);color:#7654c8;font-size:1.3rem}</style>@endpush
@section('content')
<div class="position-absolute top-0 end-0 p-3">@include('layouts.language-switcher')</div>
<div class="register-wrap"><div class="card register-card p-4 p-md-5"><div class="auth-icon mb-4"><i class="bi bi-person-plus-fill"></i></div><span class="eyebrow">{{ __('app.brand.name') }}</span><h1 class="page-title h2 mt-1 mb-1">{{ __('app.auth.register_title') }}</h1><p class="text-secondary mb-4">{{ __('app.auth.register_subtitle') }}</p>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="post" action="{{ route('register.attempt') }}">@csrf
<div class="mb-3"><label class="form-label">{{ __('app.auth.name') }}</label><input class="form-control" name="name" value="{{ old('name') }}" required autocomplete="name"></div>
<div class="mb-3"><label class="form-label">{{ __('app.auth.email') }}</label><input class="form-control" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"></div>
<div class="row g-3"><div class="col-md-6"><label class="form-label">{{ __('app.auth.password') }}</label><input class="form-control" type="password" name="password" required autocomplete="new-password"></div><div class="col-md-6"><label class="form-label">{{ __('app.auth.password_confirmation') }}</label><input class="form-control" type="password" name="password_confirmation" required autocomplete="new-password"></div></div>
<button class="btn btn-primary w-100 mt-4">{{ __('app.auth.register') }}</button></form><div class="text-center mt-4">{{ __('app.auth.already_account') }} <a class="fw-bold" href="{{ route('login') }}">{{ __('app.auth.login') }}</a></div></div></div>
@endsection
