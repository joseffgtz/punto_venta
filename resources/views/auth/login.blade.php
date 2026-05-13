@extends('layouts.app')

@section('title', 'Iniciar sesión | Punto de Venta UBM')

@section('content')
@php
    $fondoLogin = asset('images/fondo-login.jpg');
@endphp

<main class="login-screen login-screen-bg" style="background-image: linear-gradient(rgba(15, 23, 42, 0.65), rgba(15, 23, 42, 0.65)), url('{{ $fondoLogin }}');">
    <section class="login-card">
        <div class="brand-badge">PV</div>

        <span class="eyebrow">Acceso seguro</span>

        <h1>Iniciar sesión</h1>

        <p class="login-description">
            Ingresa con tu correo y contraseña para acceder al punto de venta.
        </p>

        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="login-form">
            @csrf

            <label>
                <span>Correo electrónico</span>
                <input 
                    type="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    placeholder="correo@ejemplo.com" 
                    autocomplete="email" 
                    required
                >
            </label>

            <label>
                <span>Contraseña</span>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="••••••••" 
                    autocomplete="current-password" 
                    required
                >
            </label>

            <button type="submit">Entrar al sistema</button>
        </form>

        <div class="auth-switch">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}">Crear usuario cliente</a>
        </div>
    </section>

    <section class="login-info">
        <div>
            <span class="eyebrow light">Proyecto Con Laravel</span>

            <h2>
                Punto de venta con registro de clientes, compras y administración de productos.
            </h2>


        </div>
    </section>
</main>
@endsection