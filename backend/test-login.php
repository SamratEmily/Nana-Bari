<?php
require_once 'config.php';

echo "<h1>Login Test Page</h1>";

// Test credentials
$test_email = 'shahnoormaymuna@gmail.com';
$test_password = 'shahnoormaymuna@gmail.com';

echo "<h2>Configuration Test:</h2>";
echo "Admin Email (from config): " . ADMIN_EMAIL . "<br>";
echo "Admin Password (from config): " . ADMIN_PASSWORD . "<br>";
echo "<br>";

echo "<h2>Comparison Test:</h2>";
echo "Test Email: " . $test_email . "<br>";
echo "Email Match: " . ($test_email === ADMIN_EMAIL ? '✅ YES' : '❌ NO') . "<br>";
echo "<br>";
echo "Test Password: " . $test_password . "<br>";
echo "Password Match: " . ($test_password === ADMIN_PASSWORD ? '✅ YES' : '❌ NO') . "<br>";
echo "<br>";

echo "<h2>Database Connection Test:</h2>";
try {
    $conn = getDBConnection();
    echo "✅ Database connected successfully!<br>";
    echo "Database: " . DB_NAME . "<br>";
    
    // Check tables
    $result = $conn->query("SHOW TABLES");
    echo "<br>Tables in database:<br>";
    while ($row = $result->fetch_array()) {
        echo "- " . $row[0] . "<br>";
    }
    
    // Check users table
    echo "<br><h3>Users Table Check:</h3>";
    $result = $conn->query("SELECT id, email, name, created_at FROM users");
    if ($result->num_rows > 0) {
        echo "✅ Users found in database:<br>";
        echo "<table border='1' style='border-collapse: collapse; margin-top: 10px;'>";
        echo "<tr><th style='padding: 5px;'>ID</th><th style='padding: 5px;'>Email</th><th style='padding: 5px;'>Name</th><th style='padding: 5px;'>Created</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td style='padding: 5px;'>" . $row['id'] . "</td>";
            echo "<td style='padding: 5px;'>" . $row['email'] . "</td>";
            echo "<td style='padding: 5px;'>" . ($row['name'] ?? 'N/A') . "</td>";
            echo "<td style='padding: 5px;'>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "❌ No users found in database!<br>";
    }
    
    $conn->close();
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

echo "<br><h2>Session Test:</h2>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? '✅ Active' : '❌ Inactive') . "<br>";

echo "<br><h2>Login Form Test:</h2>";
?>
<form method="POST" action="login.php" style="font-family: Arial; max-width: 400px;">
    <div style="margin-bottom: 10px;">
        <label>Email:</label><br>
        <input type="email" name="email" value="shahnoormaymuna@gmail.com" style="width: 100%; padding: 8px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label>Password:</label><br>
        <input type="password" name="password" value="shahnoormaymuna@gmail.com" style="width: 100%; padding: 8px;">
    </div>
    <button type="submit" style="padding: 10px 20px; background: #0d9488; color: white; border: none; cursor: pointer;">
        Test Login (Form POST)
    </button>
</form>

<script>
// Test AJAX Login
async function testAjaxLogin() {
    try {
        const response = await fetch('login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                email: 'shahnoormaymuna@gmail.com',
                password: 'shahnoormaymuna@gmail.com'
            })
        });
        
        const data = await response.json();
        alert(JSON.stringify(data, null, 2));
        
        if (data.success) {
            window.location.href = '../index.html';
        }
    } catch (error) {
        alert('Error: ' + error.message);
    }
}
</script>

<br>
<button onclick="testAjaxLogin()" style="padding: 10px 20px; background: #f59e0b; color: white; border: none; cursor: pointer;">
    Test Login (AJAX/JSON)
</button>

<br><br>
<a href="../login.html">← Go to Login Page</a>
