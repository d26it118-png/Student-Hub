<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>StudentHub | Secure Login</title>
  <link rel="stylesheet" href="../css/style.css">
</head>

<body>
  <main>
    <header class="home-header">
      <img src="../images/logo.png" height="80" class="logo" alt="StudentHub logo">
      <div>
        <h1>StudentHub Login</h1>
        <p>Sign in securely to your account.</p>
      </div>
    </header>
    <section class="hero-card">
      <form action="../php/process_login.php" method="POST">
        <div class="form-group">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" autocomplete="username" required>
        </div>
        <div class="form-group">
          <label for="password">Password</label>
         <input
              type="password"
              id="password"
              name="password"
              autocomplete="current-password"
              required
          >
        </div>
        <div class="remember-me">
          <input type="checkbox" id="remember_me" name="remember_me" value="1">
          <label for="remember_me">Remember me for 30 days</label>
        </div>
        <p><button type="submit">Login</button></p>
      </form>
    </section>
    <p>Don't have an account? <a href="register_user.html">Register here</a></p>
    <p><a href="index.html">Back to Home</a></p>
    <footer>
      <p>&copy; 2026 StudentHub</p>
    </footer>
  </main>
</body>

</html>