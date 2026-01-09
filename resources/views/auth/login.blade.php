@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">{{ __('Inicio de Sesión') }}</div>

                <div class="card-body">
                    @php
                        // Si hay mensaje de info o errores específicos de password, mostramos el campo clave
                        $showPassword = session('info') || $errors->has('password');
                        $formAction = $showPassword ? route('login') : route('auth.check_email');
                    @endphp

                    {{-- Alerta de Errores Generales (Muy importante para ver por qué falla) --}}
                    @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Mensaje de éxito/info --}}
                    @if (session('info'))
                        <div class="alert alert-info">
                            {{ session('info') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ $formAction }}">
                        @csrf

                        {{-- PASO 1: CORREO --}}
                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Correo Institucional') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       name="email" value="{{ old('email') }}"
                                       required autofocus {{ $showPassword ? 'readonly' : '' }}>
                                @error('email')
                                    <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        </div>

                        {{-- PASO 2: CONTRASEÑA (Solo si el correo fue validado) --}}
                        @if($showPassword)
                            <div class="row mb-3">
                                <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Contraseña') }}</label>
                                <div class="col-md-6">
                                    <input id="password" type="password"
                                           class="form-control @error('password') is-invalid @enderror"
                                           name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                    @enderror
                                </div>
                            </div>
                        @endif
                           <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>


                        <div class="row mb-0">
                            <div class="col-md-8 offset-md-4">
                                @if(!$showPassword)
                                    {{-- Botón para validar en SNO_PERSONAL --}}
                                    <button type="submit" class="btn btn-primary">
                                        Verificar Correo
                                    </button>
                                @else
                                    {{-- Botón para iniciar sesión final --}}
                                    <button type="submit" class="btn btn-success">
                                        {{ __('Entrar al Sistema') }}
                                    </button>
                                    <a class="btn btn-link text-muted" href="{{ route('login') }}">
                                        Usar otro correo
                                    </a>
                                @endif
                                 @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
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
