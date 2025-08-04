<?php
// الاتصال بقاعدة البيانات
$host = 'localhost';
$db = 'rachel';
$user = 'root';
$pass = '';
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$usernameErr = $passwordErr = "";
$message = "";
$username = $password = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $isValid = true;

    // التحقق من الإدخال
    if (empty($username)) {
        $usernameErr = "Username is required";
        $isValid = false;
    }

    if (empty($password)) {
        $passwordErr = "Password is required";
        $isValid = false;
    }

    if ($isValid) {
        // البحث عن المستخدم في قاعدة البيانات
        $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            header("Location: home.html");
            exit();
        } else {
            $message = "Invalid username or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background: linear-gradient(135deg, #f67140, #3f3f3f);
      color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background-color: #1c1c1c;
      padding: 40px;
      border-radius: 16px;
      width: 320px;
    }
    h2 {
      text-align: center;
      margin-bottom: 24px;
      color: #f67140;
    }
    input {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      background-color: #333;
      color: white;
    }
    button {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      background-color: #f67140;
      border: none;
      border-radius: 8px;
      color: black;
      font-weight: bold;
      cursor: pointer;
    }
    .link {
      margin-top: 12px;
      text-align: center;
      font-size: 14px;
    }
    .link a {
      color: #f67140;
      text-decoration: none;
    }
    .message {
            text-align: center;
            color: rgb(233, 75, 17);
            font-size: 14px;
            margin-top: 10px;
        }
    .success {
      color: #4CAF50;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Login</h2>

    <?php if (!empty($message)): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <form method="POST" action="">
    <label>Username</label>

    <input type="text" name="username"  placeholder="Enter Your Username"  value="<?php echo htmlspecialchars($username); ?>">
    <?php if (!empty($usernameErr)) echo "<div class='error'>$usernameErr</div>"; ?>
      <label>Password</label> 
      <input type="password" name="password"  placeholder="Enter Your Password">
        <?php if (!empty($passwordErr)) echo "<div class='error'>$passwordErr</div>"; ?>

      <div style="margin: 10px 0; display: flex; align-items: center;">
        <div>
          <input type="checkbox" id="remember" name="remember" style="margin: 0 19px 0 0; transform: scale(1.2);" />
        </div>
        <div>
          <label for="remember" style="margin: 0 150px 0 0; font-size: 14px;">Remember me</label>
        </div>
      </div>

      <button type="submit">Login</button>

    </form>

    <div class="link">Don't have an account? <a href="signup.php">Sign up</a></div>
  </div>
</body>
</html>
