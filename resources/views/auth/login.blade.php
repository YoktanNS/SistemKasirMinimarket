<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | SmartMart Campus</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="icon" href="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" type="image/png">
</head>

<body class="bg-gradient-to-br from-blue-100 via-blue-200 to-blue-300 flex items-center justify-center min-h-screen font-sans">

  <div class="bg-white/90 backdrop-blur-md shadow-2xl rounded-3xl p-10 w-full max-w-md transition duration-300 hover:shadow-blue-300">
    <!-- Header -->
    <div class="text-center mb-10">
      <div class="flex items-center justify-center mb-3">
        <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png"
             alt="SmartMart Logo"
             class="w-14 h-14 mr-2 animate-bounce">
        <h1 class="text-3xl font-extrabold text-blue-700 tracking-tight">SmartMart Campus</h1>
      </div>
      <p class="text-gray-600 text-sm italic">Sistem Informasi Minimarket Kampus<br>Universitas Negeri Yogyakarta</p>
    </div>

    <!-- Error messages -->
    @if ($errors->any())
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
        <ul class="list-disc pl-5 space-y-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <!-- Login Form -->
    <form method="POST" action="{{ route('login.submit') }}" class="space-y-6">
      @csrf

      <!-- Email -->
      <div>
        <label for="email" class="block text-gray-700 font-semibold mb-2">Email Kampus</label>
        <div class="relative">
          <input type="email" name="email" id="email" required
                 class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                 placeholder="contoh: admin@kampus.ac.id">
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-3.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12H8m8 4H8m2-8h8m-4-4h.01M4 6h16M4 6a2 2 0 00-2 2v12a2 2 0 002 2h16a2 2 0 002-2V8a2 2 0 00-2-2H4z" />
          </svg>
        </div>
      </div>

      <!-- Password -->
      <div>
        <label for="password" class="block text-gray-700 font-semibold mb-2">Password</label>
        <div class="relative">
          <input type="password" name="password" id="password" required
                 class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                 placeholder="Masukkan password Anda">
          <svg xmlns="http://www.w3.org/2000/svg" class="absolute right-3 top-3.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.656 0 3-1.343 3-3S13.656 5 12 5s-3 1.343-3 3 1.344 3 3 3zM4.293 17.293A8 8 0 0112 15a8 8 0 017.707 2.293M15 19a3 3 0 11-6 0" />
          </svg>
        </div>
      </div>

      <!-- Button -->
      <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl shadow-md transition transform hover:scale-[1.02] duration-200">
        Masuk Sekarang
      </button>
    </form>

    <!-- Footer -->
    <div class="text-center mt-10">
      <p class="text-xs text-gray-500 leading-5">
        © {{ date('Y') }} <span class="font-semibold text-blue-700">SmartMart Campus</span><br>
        Universitas Negeri Yogyakarta
      </p>
    </div>
  </div>

</body>
</html>
