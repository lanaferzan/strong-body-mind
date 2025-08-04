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

$username = $email = $password = "";
$usernameErr = $emailErr = $passwordErr = "";
$successMsg = "";
$isValid = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["username"])) {
        $usernameErr = "Username is required";
        $isValid = false;
    } else {
        $username = trim($_POST["username"]);
    }

    if (empty($_POST["email"])) {
        $emailErr = "Email is required";
        $isValid = false;
      } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL) || substr($_POST["email"], -9) !== "@mail.com") {
        $emailErr = "Email must be valid and end with @mail";
        $isValid = false;
    } else {
        $email = trim($_POST["email"]);
    }

    if (empty($_POST["password"])) {
        $passwordErr = "Password is required";
        $isValid = false;
    } elseif (strlen($_POST["password"]) <= 6) {
        $passwordErr = "Password must be more than 6 characters";
        $isValid = false;
    } else {
        $password = trim($_POST["password"]);
    }

    if ($isValid) {
        $stmt = $pdo->prepare("SELECT * FROM user WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            $usernameErr = "Username already exists";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO user (username, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword]);
            $successMsg = "Account created successfully!";
            $username = $email = $password = ""; // clear fields
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up - FitClub</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Montserrat', sans-serif;
      background:linear-gradient(135deg, #f67140, #3f3f3f);
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
      color:rgb(233, 75, 17);
  font-size: 13px;
  margin-bottom: 8px;
}
.success {
  color: #4CAF50;
  text-align: center;
  margin-top: 10px;
}

  </style>
</head>
<body>
  <div class="container">
    <h2>Sign Up</h2>


    <form method="POST" action="">
    <label>Username</label> 
    <input type="text" name="username" value="<?php echo htmlspecialchars($username); ?>" placeholder="Username" />
    <div class="message"><?php echo $usernameErr; ?></div>

      <label>Email</label>
<input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="@mail.com" />
  <div class="message"><?php echo $emailErr; ?></div>


      <label>Password</label>
      <input type="password" name="password" placeholder="Password" />
      <div class="message"><?php echo $passwordErr; ?></div>
      <button type="submit">Create Account</button>
    </form>
    <?php if (!empty($successMsg)): ?>
  <div class="message success"><?php echo $successMsg; ?></div>
<?php endif; ?>

     <div class="link">Already have an account? <a href="login.php">Login</a></div>
  </div> 
</body>
</html>
