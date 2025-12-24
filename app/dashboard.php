<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$user_id = (int) $_SESSION['user_id'];

$user = null;
$stmt = $conn->prepare("SELECT firstname, lastname, email FROM users WHERE id = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
    $stmt->close();
}

$add_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $title = trim($_POST['title'] ?? '');
    
    if (empty($title)) {
        $add_error = 'Task title cannot be empty';
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (user_id, title) VALUES (?, ?)");
        if ($stmt) {
            $stmt->bind_param("is", $user_id, $title);
            if ($stmt->execute()) {
                header('Location: dashboard.php');
                exit;
            } else {
                $add_error = 'Failed to add task';
            }
            $stmt->close();
        }
    }
}

$stmt = $conn->prepare("SELECT id, title, is_completed FROM tasks WHERE user_id = ? ORDER BY is_completed ASC, id DESC");
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tasks = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
} else {
    $tasks = [];
}

$completed_count = 0;
foreach ($tasks as $task) {
    if ($task['is_completed']) {
        $completed_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MiniDo - Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            opacity: 0;
            transition: opacity 0.3s ease-in;
        }
        body.loaded {
            opacity: 1;
        }
    </style>
</head>
<body class="min-h-screen" style="background-color: #FFD1CC;" onload="document.body.classList.add('loaded')">
    <div class="flex h-screen gap-4 p-4">
        <div class="text-white rounded-lg shadow-lg p-6 w-80 flex flex-col" style="background-color: #FF6F61;">
            <div class="mb-8">
                <h1 class="text-4xl font-bold mb-2">
                    <span>Mini</span><span class="text-white">Do</span>
                </h1>
                <p class="text-sm opacity-90">Task Manager</p>
            </div>

            <div class="bg-white bg-opacity-10 rounded-lg p-4 mb-8">
                <p class="font-semibold"><?php echo htmlspecialchars($user['firstname'] ?? '') . ' ' . htmlspecialchars($user['lastname'] ?? ''); ?></p>
                <p class="text-xs opacity-75"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
            </div>

            <div class="rounded-lg p-4 mb-8" style="background-color: #FF4D6D;">
                <div class="mb-4">
                    <p class="text-sm text-white opacity-90">Total Tasks</p>
                    <p class="text-3xl font-bold text-white"><?php echo count($tasks); ?></p>
                </div>
                <div>
                    <p class="text-sm text-white opacity-90">Completed</p>
                    <p class="text-3xl font-bold text-white"><?php echo $completed_count; ?></p>
                </div>
            </div>

               <a 
                href="logout.php"
                class="mt-auto py-2 px-4 font-semibold rounded-lg text-center transition-colors"
                style="background-color: #FFFFFF; color: #FF6F61;"
                onmouseover="this.style.backgroundColor='#FFD1CC';"
                onmouseout="this.style.backgroundColor='#FFFFFF';"
            >
                Logout
            </a>
        </div>

        <div class="flex-1 flex flex-col">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-3xl font-bold mb-2" style="color: #4A1C1C;">My Tasks</h2>
                        <p style="color: #4A1C1C;">
                            <?php echo $completed_count; ?> of <?php echo count($tasks); ?> completed
                        </p>
                    </div>
                    <div class="text-right" style="color: #4A1C1C;">
                        <p class="text-sm"><?php echo date('l'); ?></p>
                        <p class="text-lg font-semibold"><?php echo date('d M Y'); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <?php if ($add_error): ?>
                    <div class="mb-4 p-3 rounded-lg" style="background-color: #FFB7B2; color: #4A1C1C;">
                        <?php echo htmlspecialchars($add_error); ?>
                    </div>
                <?php endif; ?>
                <form method="POST" class="flex gap-2">
                    <input 
                        type="text" 
                        name="title" 
                        placeholder="Add a new task..." 
                        required 
                        class="flex-1 px-4 py-2 border rounded-lg focus:outline-none focus:ring-2"
                        maxlength="255"
                        style="border-color: #FF9AA2;"
                        onfocus="this.style.borderColor='#FF6F91'; this.style.boxShadow='0 0 0 3px rgba(255, 106, 145, 0.1)';"
                        onblur="this.style.borderColor='#FF9AA2'; this.style.boxShadow='';"
                    />
                    <button 
                        type="submit" 
                        name="add_task"
                        class="px-6 py-2 text-white font-semibold rounded-lg transition-colors"
                        style="background-color: #FF6F61;"
                        onmouseover="this.style.backgroundColor='#FF4D6D';"
                        onmouseout="this.style.backgroundColor='#FF6F61';
                        type="submit" 
                        name="add_task"
                        class="px-6 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Add Task
                    </button>
                </form>
            </div>

            <div class="flex-1 overflow-y-auto bg-white rounded-lg shadow-lg p-6">
                <?php if (empty($tasks)): ?>
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <p>No tasks yet. Add one to get started!</p>
                    </div>
                <?php else: ?>
                    <ul class="space-y-2">
                        <?php foreach ($tasks as $task): ?>
                            <li class="flex items-center justify-between p-4 border rounded-lg hover:translate-x-1 hover:shadow-md transition-all duration-300" style="border-color: #FF9AA2; background-color: #FFB7B2;">
                                <div class="flex items-center flex-1 gap-3">
                                    <?php if ($task['is_completed']): ?>
                                        <svg class="w-6 h-6 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="line-through" style="color: #4A1C1C;"><?php echo htmlspecialchars($task['title']); ?></span>
                                    <?php else: ?>
                                        <div class="w-6 h-6 border-2 rounded-full" style="border-color: #FF6F91;"></div>
                                        <span style="color: #4A1C1C;"><?php echo htmlspecialchars($task['title']); ?></span>
                                    <?php endif; ?>
                                </div>
                <div class="flex gap-2">
                    <?php if (!$task['is_completed']): ?>
                        <a 
                            href="complete_task.php?id=<?php echo $task['id']; ?>" 
                            class="px-3 py-1 text-white text-sm rounded transition-colors"
                            style="background-color: #FF6F61;"
                            onmouseover="this.style.backgroundColor='#FF4D6D';"
                            onmouseout="this.style.backgroundColor='#FF6F61';"
                        >
                            Complete
                        </a>
                    <?php endif; ?>
                    <a 
                        href="delete_task.php?id=<?php echo $task['id']; ?>" 
                        class="px-3 py-1 text-white text-sm rounded transition-colors"
                        style="background-color: #FF9AA2;"
                        onmouseover="this.style.backgroundColor='#FF6F91';"
                        onmouseout="this.style.backgroundColor='#FF9AA2';"
                        onclick="return confirm('Are you sure you want to delete this task?');"
                    >
                        Delete
                    </a>
                </div>
            </li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
</div>  
</div>
</body>
</html>
