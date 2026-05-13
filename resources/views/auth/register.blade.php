@extends('layouts.app')

@section('title', 'Registro de usuario | Punto de Venta UBM')

@section('content')
<main class="login-screen">
    <section class="login-card">
        <div class="brand-badge">UBM</div>
        <span class="eyebrow">Nuevo cliente</span>
        <h1>Crear cuenta</h1>
        <p>Regístrate para iniciar sesión y comprar productos directamente desde el punto de venta.</p>

        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="login-form">
            @csrf
            <label>
                <span>Nombre completo</span>
                <input type="text" name="name" value="{{ old('name') }}" placeholder="Ej. Juan Pérez" autocomplete="name" required>
            </label>

            <label>
                <span>Correo electrónico</span>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="cliente@ejemplo.com" autocomplete="email" required>
            </label>

            <label>
                <span>Contraseña</span>
                <input type="password" name="password" placeholder="Mínimo 6 caracteres" autocomplete="new-password" required>
            </label>

            <label>
                <span>Confirmar contraseña</span>
                <input type="password" name="password_confirmation" placeholder="Repite tu contraseña" autocomplete="new-password" required>
            </label>

            <button type="submit">Registrarme y comprar</button>
        </form>

        <div class="auth-switch">
            ¿Ya tienes cuenta?
            <a href="{{ route('login') }}">Iniciar sesión</a>
        </div>
    </section>

    <section class="login-info register-info">
        <div>
            <span class="eyebrow">Clientes</span>
            <h2>Los usuarios registrados pueden comprar sin entrar como administrador.</h2>
            <p>El administrador conserva permisos especiales para agregar, editar y eliminar productos.</p>
        </div>
    </section>
</main>
@endsection
