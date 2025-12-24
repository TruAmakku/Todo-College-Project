<?php
include 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$login_error = '';
$signup_error = '';
$signup_success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $login_error = 'Email and password are required';
    } else {
        $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header('Location: dashboard.php');
                exit;
            } else {
                $login_error = 'Invalid email or password';
            }
            $stmt->close();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
        $signup_error = 'All fields are required';
    } elseif ($password !== $confirm_password) {
        $signup_error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $signup_error = 'Password must be at least 6 characters';
    } else {
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        if ($check_stmt) {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $signup_error = 'Email already registered';
            } else {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $insert_stmt = $conn->prepare("INSERT INTO users (firstname, lastname, email, password) VALUES (?, ?, ?, ?)");
                
                if ($insert_stmt) {
                    $insert_stmt->bind_param("ssss", $firstname, $lastname, $email, $password_hash);
                    
                    if ($insert_stmt->execute()) {
                        $_SESSION['user_id'] = $conn->insert_id;
                        header('Location: dashboard.php');
                        exit;
                    } else {
                        $signup_error = 'Registration failed. Please try again.';
                    }
                    $insert_stmt->close();
                }
            }
            $check_stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MiniDo - Login & Register</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-lg shadow-2xl p-8 animate-in fade-in zoom-in duration-500">
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold">
                    <span class="text-[#4A1C1C]">Mini</span><span class="text-[#FF6F61]">Do</span>
                </h1>
                <p class="mt-1">Manage your tasks efficiently</p>
            </div>

            <div class="flex gap-2 mb-8">
                <button 
                    type="button" 
                    onclick="showLogin()" 
                    id="loginBtn"
                    class="flex-1 py-2 px-4 text-white font-semibold rounded-lg transition-colors bg-[#FF6F61]"
                    
                >
                    Login
                </button>
                <button 
                    type="button" 
                    onclick="showSignup()" 
                    id="signupBtn"
                    class="flex-1 py-2 px-4 text-gray-800 font-semibold rounded-lg transition-colors text-[#4A1C1C] bg-[#FFD1CC]"
                >
                    Sign Up
                </button>
            </div>

            <div id="loginSection" class="animate-in fade-in duration-300">
                <?php if ($login_error): ?>
                    <div class="mb-4 p-3 rounded-lg border text-[#4A1C1C] bg-[#FFB7B2] border-[#FF9AA2]">
                        <?php echo htmlspecialchars($login_error); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block font-semibold mb-2 text-[#4A1C1C]">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            class="w-full px-4 py-2 rounded-lg border
                                    border-[#FF9AA2]
                                    focus:outline-none
                                    focus:border-[#FF6F91]
                                    focus:ring-2
                                    focus:ring-[#FF6F91]/20 border-[#FF9AA2]"
                            placeholder="your@email.com"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-[#4A1C1C]">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            class="w-full px-4 py-2 rounded-lg border
                                    border-[#FF9AA2]
                                    focus:outline-none
                                    focus:border-[#FF6F91]
                                    focus:ring-2
                                    focus:ring-[#FF6F91]/20 border-[#FF9AA2]"
                            placeholder="Enter your password"
                        />
                    </div>
                    <button 
                        type="submit" 
                        name="login"
                        class="w-full px-4 py-2 rounded-lg border
                                border-[#FF9AA2]
                                bg-[#FF6F61]
                                text-white
                                hover:opacity-70
                                active:opacity-90"
                    >
                        Login
                    </button>
                </form>
            </div>

            <div id="signupSection" class="animate-in fade-in duration-300 hidden">
                <?php if ($signup_error): ?>
                    <div class="mb-4 p-3 rounded-lg border bg-[#FFB7B2] text-[#4A1C1C] border-[#FF9AA2]">
                        <?php echo htmlspecialchars($signup_error); ?>
                    </div>
                <?php endif; ?>
                <?php if ($signup_success): ?>
                    <div class="mb-4 p-3 rounded-lg border bg-[#FFB7B2] text-[#4A1C1C] border-[#FF9AA2]">
                        <?php echo htmlspecialchars($signup_success); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-semibold mb-2 text-[#4A1C1C]">First Name</label>
                            <input 
                                type="text" 
                                name="firstname" 
                                required 
                                class="w-full px-4 py-2 rounded-lg border
                                    border-[#FF9AA2]
                                    focus:outline-none
                                    focus:border-[#FF6F91]
                                    focus:ring-2
                                    focus:ring-[#FF6F91]/20 border-[#FF9AA2]"
                                placeholder="ex: Ahmed"
                            />
                        </div>
                        <div>
                            <label class="block font-semibold mb-2 text-[#4A1C1C]">Last Name</label>
                            <input 
                                type="text" 
                                name="lastname" 
                                required 
                                class="w-full px-4 py-2 rounded-lg border
                                    border-[#FF9AA2]
                                    focus:outline-none
                                    focus:border-[#FF6F91]
                                    focus:ring-2
                                    focus:ring-[#FF6F91]/20 border-[#FF9AA2]"
                                placeholder="ex: Eldawody"
                            />
                        </div>
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-[#4A1C1C]">Email</label>
                        <input 
                            type="email" 
                            name="email" 
                            required 
                            class="w-full px-4 py-2 rounded-lg border
                                    border-[#FF9AA2]
                                    focus:outline-none
                                    focus:border-[#FF6F91]
                                    focus:ring-2
                                    focus:ring-[#FF6F91]/20 border-[#FF9AA2]"
                            placeholder="your@email.com"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-[#4A1C1C]">Password</label>
                        <input 
                            type="password" 
                            name="password" 
                            required 
                            class="w-full px-4 py-2 rounded-lg border
                                    border-[#FF9AA2]
                                    focus:outline-none
                                    focus:border-[#FF6F91]
                                    focus:ring-2
                                    focus:ring-[#FF6F91]/20 border-[#FF9AA2]"
                            placeholder="At least 6 characters"
                        />
                    </div>
                    <div>
                        <label class="block font-semibold mb-2 text-[#4A1C1C]">Confirm Password</label>
                        <input 
                            type="password" 
                            name="confirm_password" 
                            required 
                            class="w-full px-4 py-2 rounded-lg border
                                    border-[#FF9AA2]
                                    focus:outline-none
                                    focus:border-[#FF6F91]
                                    focus:ring-2
                                    focus:ring-[#FF6F91]/20 border-[#FF9AA2]"
                            placeholder="Confirm your password"
                        />
                    </div>
                    <button 
                        type="submit" 
                        name="register"
                        class="w-full px-4 py-2 rounded-lg border
                                border-[#FF9AA2]
                                bg-[#FF6F61]
                                text-white
                                hover:opacity-70
                                active:opacity-90"
                    >
                        Sign Up
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showLogin() {
            document.getElementById('loginSection').classList.remove('hidden');
            document.getElementById('signupSection').classList.add('hidden');
            
            document.getElementById('loginBtn').style.backgroundColor = '#FF6F61';
            document.getElementById('loginBtn').style.color = '#FFFFFF';
            document.getElementById('signupBtn').style.backgroundColor = '#FFD1CC';
            document.getElementById('signupBtn').style.color = '#4A1C1C';
        }

        function showSignup() {
            document.getElementById('signupSection').classList.remove('hidden');
            document.getElementById('loginSection').classList.add('hidden');
            
            document.getElementById('signupBtn').style.backgroundColor = '#FF6F61';
            document.getElementById('signupBtn').style.color = '#FFFFFF';
            document.getElementById('loginBtn').style.backgroundColor = '#FFD1CC';
            document.getElementById('loginBtn').style.color = '#4A1C1C';
        }
    </script>
</body>
</html>
