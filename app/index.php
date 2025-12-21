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
<body class="min-h-screen" style="background-color: #FF6F61;">
    <main class="min-h-screen flex flex-col items-center justify-center gap-3 text-white animate-in fade-in zoom-in duration-500">
        <h1 class="text-7xl font-medium text-center">
            Welcome to
            <span class="font-bold">
                Mini<span class="text-white" style="opacity: 0.9;">Do</span>
            </span>
        </h1>
        <p class="text-3xl font-medium text-center">
            Your Minimalistic & Reliable To do list - Task manager.
        </p>
        <a 
            href="auth.php" 
            class="text-2xl px-8 py-4 rounded-lg mt-5 transition-all cursor-pointer font-semibold" 
            style="background-color: #FFFFFF; color: #FF6F61;"
            onmouseover="this.style.backgroundColor='#FFB7B2'; this.style.color='#FF6F61';"
            onmouseout="this.style.backgroundColor='#FFFFFF';"
        >
            Get Started
        </a>
    </main>
</body>
</html>
