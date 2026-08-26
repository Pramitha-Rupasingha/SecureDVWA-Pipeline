<?php
// ==========================================
// FIX 1: SQL Injection -> Parameterized Query (PDO Prepared Statements)
// ==========================================
$id = $_GET['id'];
$stmt = $pdo->prepare('SELECT first_name, last_name FROM users WHERE user_id = :id');
$stmt->execute(['id' => $id]);
$user = $stmt->fetch();

// ==========================================
// FIX 2: Reflected XSS -> Context-Aware Output Encoding
// ==========================================
$name = $_GET['name'];
// Sanitize input to prevent HTML/Script injection in the browser context
$clean_name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
echo "Hello " . $clean_name;

// ==========================================
// FIX 3: Command Injection -> Strict Input Validation & Parameter Escaping
// ==========================================
$target = $_REQUEST['ip'];
// Validate input strictly as an IPv4 address before passing to shell
if (filter_var($target, FILTER_VALIDATE_IP)) {
    $cmd = shell_exec('ping -c 4 ' . escapeshellarg($target));
    echo "<pre>{$cmd}</pre>";
} else {
    echo "Error: Invalid IP Address format.";
}

// ==========================================
// FIX 4: Hardcoded Secret -> Environment Variables
// ==========================================
// Retrieve secrets dynamically from runtime environment variables (Not committed to Git)
$db_password = getenv('DB_PASSWORD');
$aws_access_key = getenv('AWS_ACCESS_KEY');
?>