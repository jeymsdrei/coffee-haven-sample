<?php

namespace App\Http\Controllers;

use App\Models\User;

class AuthController
{
    public function showLogin()
    {
        return view('auth/login');
    }

    public function login()
    {
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            return redirect('/login')->withErrors(['Invalid credentials']);
        }

        $users = User::where('email', $email);
        $user = $users[0] ?? null;

        if (!$user) {
            return redirect('/login')->withErrors(['User not found']);
        }

        if (!password_verify($password, $user->password)) {
            return redirect('/login')->withErrors(['Invalid password']);
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->username;
        $_SESSION['is_admin'] = $user->is_admin;

        return redirect('/');
    }

    public function showRegister()
    {
        return view('auth/register');
    }

    public function register()
    {
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (!$username || !$email || !$password || $password !== $confirmPassword) {
            return redirect('/register')->withErrors(['Invalid input']);
        }

        $existingUsers = User::where('email', $email);
        if (!empty($existingUsers)) {
            return redirect('/register')->withErrors(['Email already exists']);
        }

        $user = User::create([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'is_admin' => 0
        ]);

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_name'] = $user->username;

        return redirect('/');
    }

    public function logout()
    {
        session_destroy();
        return redirect('/');
    }

    public function profile()
    {
        if (!isset($_SESSION['user_id'])) {
            return redirect('/login');
        }

        $user = User::find($_SESSION['user_id']);
        return view('profile', ['user' => $user]);
    }
}
