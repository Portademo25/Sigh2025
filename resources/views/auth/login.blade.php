@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">{{ __('Inicio de Sesión') }}</div>

                <div class="card-body">
                    @php
                        // Sincronizamos con la variable 'user_verified' enviada desde el OnboardingController
                        // También permitimos que se mantenga visible si hay errores de validación de contraseña
                        $showPassword = session('user_verified') || $errors->has('password');
                        $formAction = $showPassword ? route('login') : route('auth.check_email');
                    @endphp

                    {{-- Alerta de errores de bloqueo o nómina --}}
                    @error('email')
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror

                    {{-- Mensaje Informativo --}}
                    @if (session('info'))
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> {{ session('info') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ $formAction }}">
                        @csrf

                        {{-- PASO 1: CORREO --}}
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Correo Institucional') }}</label>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-secondary">
                                        <i class="bi bi-envelope-fill"></i>
                                    </span>
                                    <input id="email" type="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           name="email" value="{{ old('email') }}"
                                           required autofocus {{ $showPassword ? 'readonly' : '' }}>

                                    @if($showPassword)
                                        {{-- Botón para resetear el formulario si el correo es incorrecto --}}
                                        <a href="{{ route('login') }}" class="btn btn-outline-secondary" title="Cambiar correo">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- PASO 2: CONTRASEÑA (Solo si el correo fue validado) --}}
                        @if($showPassword)
                            <div class="row mb-3 animate__animated animate__fadeInDown">
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Contraseña') }}</label>
                                <div class="col-md-6">
        <div class="input-group">
            <span class="input-group-text bg-light text-secondary">
                <i class="bi bi-lock-fill"></i>
            </span>

            <input id="password" type="password"
                   class="form-control @error('password') is-invalid @enderror"
                   name="password" required autocomplete="current-password">



            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="remember">
                                            {{ __('Recordarme') }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                @if(!$showPassword)
                                    <button type="submit" class="btn btn-primary px-4">
                                        {{ __('Continuar') }} <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-success px-4">
                                        {{ __('Entrar al Sistema') }} <i class="fas fa-sign-in-alt ms-2"></i>
                                    </button>
                                @endif

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('¿Olvidó su contraseña?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
