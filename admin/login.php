<?php
session_start();

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit;
}

require_once '../includes/config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("
        SELECT * FROM admins
        WHERE username = ?
    ");

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $admin = $result->fetch_assoc();

        if (password_verify($password, $admin['password'])) {

            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin['username'];

            header("Location: index.php");
            exit;

        } else {
            $error = "Wrong password";
        }

    } else {
        $error = "Admin not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>

    <title>Admin Login</title>

    <!-- 📱 Mobile responsiveness -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        :root{
  --bg:#f8f3ee;
  --surface:#fffaf6;
  --surface-2:#f1e4da;
  --primary:#6b422d;
  --primary-dark:#4e2d1f;
  --accent:#c58f7b;
  --text:#2e211b;
  --text-light:#6b5b55;
  --border:#eadfd7;
  --shadow:0 12px 40px rgba(0,0,0,.08);

  --radius-sm:14px;
  --radius-md:22px;
  --radius-lg:32px;
}

body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: var(--bg);
    padding: 20px;
    color: var(--text);
}

.login-container {
    width: 100%;
    max-width: 400px;
    padding: 40px;
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
}

h2 {
    text-align: center;
    margin-bottom: 25px;
    color: var(--primary-dark);
    font-weight: 700;
}

.input-box {
    margin-bottom: 18px;
    position: relative;
}

.input-box input {
    width: 100%;
    padding: 14px;
    border-radius: var(--radius-md);
    border: 1px solid var(--border);
    background: var(--surface-2);
    color: var(--text);
    outline: none;
    transition: 0.2s ease;
}

.input-box input:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(107,66,45,0.15);
}

.input-box input::placeholder {
    color: var(--text-light);
}

.btn {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: var(--radius-md);
    background: var(--primary);
    color: white;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
}

.btn:hover {
    background: var(--primary-dark);
    transform: translateY(-2px);
}

.error {
    background: #ffe9e3;
    border: 1px solid #f3c7bc;
    color: var(--primary-dark);
    padding: 10px;
    border-radius: var(--radius-sm);
    margin-bottom: 15px;
    text-align: center;
}

/* 👁️ toggle */
.toggle-password {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    display: flex;
    align-items: center;
}

.toggle-password svg {
    stroke: var(--text-light);
    transition: 0.2s ease;
}

.toggle-password:hover svg {
    stroke: var(--primary);
}

/* 📱 mobile */
@media (max-width: 480px) {
    .login-container {
        padding: 25px;
        border-radius: var(--radius-md);
    }

    h2 {
        font-size: 20px;
    }

    .input-box input {
        font-size: 16px;
        padding: 14px;
    }

    .btn {
        font-size: 16px;
    }
}
    </style>

</head>

<body>

<div class="login-container">

    <h2>Admin Login</h2>

    <?php if ($error): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">

        <!-- prevent autofill -->
        <input type="text" style="display:none">
        <input type="password" style="display:none">

        <div class="input-box">
            <input type="text" name="username" placeholder="Username" required autocomplete="off">
        </div>

        <div class="input-box">
            <input type="password" id="password" name="password" placeholder="Password" required autocomplete="new-password">

            <span class="toggle-password" onclick="togglePassword()">

                <!-- Eye Open -->
                <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>

                <!-- Eye Off -->
                <svg id="eyeOff" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" style="display:none;">
                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.77 21.77 0 0 1 5.06-5.94"></path>
                    <path d="M1 1l22 22"></path>
                    <path d="M9.9 4.24A10.88 10.88 0 0 1 12 4c7 0 11 8 11 8a21.77 21.77 0 0 1-3.22 4.5"></path>
                </svg>

            </span>
        </div>

        <button class="btn" type="submit">Login</button>

    </form>

</div>

<script>
function togglePassword() {
    const password = document.getElementById("password");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeOff = document.getElementById("eyeOff");

    if (password.type === "password") {
        password.type = "text";
        eyeOpen.style.display = "none";
        eyeOff.style.display = "block";
    } else {
        password.type = "password";
        eyeOpen.style.display = "block";
        eyeOff.style.display = "none";
    }
}
</script>

</body>
</html>