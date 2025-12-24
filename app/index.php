<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MiniDo - Your Minimalistic Todo List</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-[#FF6F61]">
    <main class="min-h-screen flex flex-col items-center justify-center gap-3 text-white">
        <h1 class="text-7xl font-medium text-center">
            Welcome to
            <span class="font-bold">
                MiniDo
            </span>
        </h1>
        <p class="text-3xl font-medium text-center">
            Your Minimalistic & Reliable To do list - Task manager.
        </p>
        <a
          href="auth.php"
          class="mt-5 inline-block px-8 py-4 text-2xl font-semibold rounded-lg
                 bg-white text-[#FF6F61]
                 transition-all duration-300
                 hover:bg-[#FFB7B2] hover:text-[#FF6F61]">
             Get Started
        </a>
    </main>
</body>
</html>
