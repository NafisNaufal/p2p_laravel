@extends('layouts.app')

@section('title', 'Setup PIN - P2P Communication')

@section('content')
    <div class="max-w-md mx-auto mt-20">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">P2P Communication</h1>
                <p class="text-gray-600">Setup PIN 4 digit untuk mengakses aplikasi</p>
            </div>

            <form action="{{ route('pin.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username (Opsional)
                    </label>
                    <input type="text" id="username" name="username" value="{{ old('username') }}"
                        placeholder="Masukkan username Anda"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700 mb-2">
                        PIN (4 Digit) *
                    </label>
                    <input type="password" id="pin" name="pin" maxlength="4" placeholder="••••"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-center text-2xl tracking-widest @error('pin') border-red-500 @enderror">
                    @error('pin')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200">
                    Setup PIN
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-500">
                    Sudah punya PIN?
                    <a href="{{ route('pin.login') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        Login di sini
                    </a>
                </p>
            </div>

            <div class="mt-8 p-4 bg-gray-50 rounded-lg">
                <h3 class="font-semibold text-gray-700 mb-2">Info Jaringan:</h3>
                <p class="text-sm text-gray-600">IP Address: <span class="font-mono">{{ request()->ip() }}</span></p>
                <p class="text-sm text-gray-600">User Agent: <span
                        class="font-mono text-xs">{{ request()->userAgent() }}</span></p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-focus PIN input and format
            document.getElementById('pin').addEventListener('input', function(e) {
                this.value = this.value.replace(/\D/g, '');
            });
        </script>
    @endpush
@endsection