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

<body class="min-h-screen bg-[#FFD1CC]" onload="document.body.classList.add('loaded')">
<div class="flex h-screen gap-4 p-4">
    <div class="w-80 flex flex-col p-6 rounded-lg shadow-lg text-white bg-[#FF6F61]">

        <div class="mb-8">
            <h1 class="text-4xl font-bold mb-2">Mini<span class="text-white">Do</span></h1>
            <p class="text-sm opacity-90">Task Manager</p>
        </div>

        <div class="mb-8 p-4 rounded-lg bg-white/10">
            <p class="font-semibold">
                <?= htmlspecialchars($user['firstname'] ?? '') . ' ' . htmlspecialchars($user['lastname'] ?? '') ?>
            </p>
            <p class="text-xs opacity-75"><?= htmlspecialchars($user['email'] ?? '') ?></p>
        </div>

        <div class="mb-8 p-4 rounded-lg bg-[#FF4D6D]">
            <div class="mb-4">
                <p class="text-sm opacity-90">Total Tasks</p>
                <p class="text-3xl font-bold"><?= count($tasks) ?></p>
            </div>
            <div>
                <p class="text-sm opacity-90">Completed</p>
                <p class="text-3xl font-bold"><?= $completed_count ?></p>
            </div>
        </div>

        <a
            href="logout.php"
            class="mt-auto py-2 px-4 font-semibold text-center rounded-lg
                   bg-white text-[#FF6F61]
                   transition-colors hover:bg-[#FFD1CC]"
        >
            Logout
        </a>
    </div>
    <div class="flex-1 flex flex-col">
        <div class="mb-6 p-6 bg-white rounded-lg shadow-lg">
            <div class="flex justify-between items-start text-[#4A1C1C]">
                <div>
                    <h2 class="text-3xl font-bold mb-2">My Tasks</h2>
                    <p><?= $completed_count ?> of <?= count($tasks) ?> completed</p>
                </div>
                <div class="text-right">
                    <p class="text-sm"><?= date('l') ?></p>
                    <p class="text-lg font-semibold"><?= date('d M Y') ?></p>
                </div>
            </div>
        </div>
        <div class="mb-6 p-6 bg-white rounded-lg shadow-lg">
            <?php if ($add_error): ?>
                <div class="mb-4 p-3 rounded-lg bg-[#FFB7B2] text-[#4A1C1C]">
                    <?= htmlspecialchars($add_error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="flex gap-2">
                <input
                    type="text"
                    name="title"
                    required
                    maxlength="255"
                    placeholder="Add a new task..."
                    class="flex-1 px-4 py-2 rounded-lg border
                           border-[#FF9AA2]
                           focus:outline-none
                           focus:border-[#FF6F91]
                           focus:ring-2
                           focus:ring-[#FF6F91]/20"
                />

                <button
                    type="submit"
                    name="add_task"
                    class="px-6 py-2 rounded-lg font-semibold text-white
                           bg-[#FF6F61]
                           transition-colors hover:bg-[#FF4D6D]"
                >
                    Add Task
                </button>
            </form>
        </div>
        <div class="flex-1 overflow-y-auto p-6 bg-white rounded-lg shadow-lg">
            <?php if (empty($tasks)): ?>
                <div class="h-full flex items-center justify-center text-gray-400">
                    <p>No tasks yet. Add one to get started!</p>
                </div>
            <?php else: ?>
                <ul class="space-y-2">
                    <?php foreach ($tasks as $task): ?>
                        <li class="flex items-center justify-between p-4 rounded-lg
                                   border border-[#FF9AA2]
                                   bg-[#FFB7B2]
                                   transition-all duration-300
                                   hover:translate-x-1 hover:shadow-md">

                            <div class="flex items-center gap-3 flex-1">
                                <?php if ($task['is_completed']): ?>
                                    <span class="line-through text-[#4A1C1C]">
                                        <?= htmlspecialchars($task['title']) ?>
                                    </span>
                                <?php else: ?>
                                    <div class="w-6 h-6 rounded-full border-2 border-[#FF6F91]"></div>
                                    <span class="text-[#4A1C1C]">
                                        <?= htmlspecialchars($task['title']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div class="flex gap-2">
                                <?php if (!$task['is_completed']): ?>
                                    <a
                                        href="complete_task.php?id=<?= $task['id'] ?>"
                                        class="px-3 py-1 text-sm rounded text-white
                                               bg-[#FF6F61]
                                               transition-colors hover:bg-[#FF4D6D]"
                                    >
                                        Complete
                                    </a>
                                <?php endif; ?>

                                <a
                                    href="delete_task.php?id=<?= $task['id'] ?>"
                                    onclick="return confirm('Are you sure you want to delete this task?');"
                                    class="px-3 py-1 text-sm rounded text-white
                                           bg-[#FF9AA2]
                                           transition-colors hover:bg-[#FF6F91]"
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
