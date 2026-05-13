<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('user_id')) {
            return redirect()->route('pos.index');
        }

        return view('auth.login');
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('user_id')) {
            return redirect()->route('pos.index');
        }

        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Escribe tu nombre completo.',
            'email.required' => 'Escribe tu correo.',
            'email.email' => 'El correo no tiene un formato válido.',
            'email.unique' => 'Ese correo ya está registrado.',
            'password.required' => 'Escribe una contraseña.',
            'password.min' => 'La contraseña debe tener mínimo 6 caracteres.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'cliente',
        ]);

        $this->startSession($request, $user);

        return redirect()->route('pos.index')->with('success', 'Cuenta creada correctamente. Ya puedes comprar productos.');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Escribe tu correo.',
            'email.email' => 'El correo no tiene un formato válido.',
            'password.required' => 'Escribe tu contraseña.',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()->withInput($request->only('email'))->with('error', 'Usuario o contraseña incorrectos.');
        }

        $this->startSession($request, $user);

        return redirect()->route('pos.index')->with('success', 'Bienvenido al sistema.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->flush();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }

    private function startSession(Request $request, User $user): void
    {
        $request->session()->regenerate();
        $request->session()->put('user_id', $user->id);
        $request->session()->put('user_name', $user->name);
        $request->session()->put('user_role', $user->role);
    }
}
