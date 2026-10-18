<?php
require_once __DIR__ . "/../db/db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fullname = trim($_POST["fullname"] ?? "");
    $email = filter_var(trim($_POST["email"] ?? ""), FILTER_SANITIZE_EMAIL);
    $studentid = trim($_POST["studentid"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $course = trim($_POST["course"] ?? "");
    $year = trim($_POST["year"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm-password"] ?? "";
    $termsAccepted = isset($_POST["terms"]);

    if (!preg_match("/^[A-Za-z ]{2,50}$/", $fullname)) {
        $message = "Enter a valid name using letters and spaces only.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Enter a valid email address.";
    } elseif (!preg_match("/^[A-Za-z0-9-]{4,20}$/", $studentid)) {
        $message = "Enter a valid Student ID.";
    } elseif (!preg_match("/^[6-9][0-9]{9}$/", $phone)) {
        $message = "Enter a valid 10-digit mobile number.";
    } elseif (!in_array($course, ["btech", "bca", "bba", "bcom", "bsc", "ba"], true)) {
        $message = "Please select a valid course.";
    } elseif (!in_array($year, ["1", "2", "3", "4"], true)) {
        $message = "Please select a valid year.";
    } elseif (!in_array($gender, ["male", "female", "other"], true)) {
        $message = "Please select a valid gender.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/", $password)) {
        $message = "Password must contain 8+ characters, uppercase, lowercase, number and special character.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } elseif (!$termsAccepted) {
        $message = "You must accept the terms and conditions.";
    } else {
        // Basic MySQLi prepared statement. No PDO is used.
        $sql = "INSERT INTO students (student_id, full_name, email, mobile, course, year, gender)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssis",
                $studentid, $fullname, $email, $phone, $course, $year, $gender
            );

            if (mysqli_stmt_execute($stmt)) {
                $message = "Registration successful! Student record saved in MySQL.";
                $messageType = "success";
            } else {
                if (mysqli_errno($conn) === 1062) {
                    $message = "Student ID or email already exists.";
                } else {
                    $message = "Database error: " . mysqli_error($conn);
                }
            }
            mysqli_stmt_close($stmt);
        } else {
            $message = "Unable to prepare database query.";
        }
    }

    if ($messageType !== "success") {
        $messageType = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student-Hub - Registration</title>

    <link rel="stylesheet" href="../css/style.css">
</head>

<body data-theme-toggle="disabled">

    <main>
<?php if ($message !== ""): ?>
    <div class="message <?php echo htmlspecialchars($messageType); ?>"><?php echo htmlspecialchars($message); ?></div>
<?php endif; ?>

        <header class="home-header">
            <img src="../images/logo.png" height="100" class="logo" alt="Logo">
            <div>
                <span>Student-Hub</span>
                <p class="intro-text">A polished student portal for assignments, attendance, events, and profile
                    management.</p>
            </div>
        </header>
        <!-- Navigation -->
        <nav class="main-nav">
            <a href="index.html">Home</a>
            <a href="dashboard.html">Dashboard</a>
            <a href="assignment.html">Assignments</a>
            <a href="attendance.html">Attendance</a>
            <a href="events.php">Events</a>
            <a href="profile.html">Profile</a>
            <a href="faq.html">FAQ</a>
            <a href="contact.html">Contact</a>
            <a href="login.html">Login</a>
        </nav>


        <!-- Registration Form -->

        <section class="hero-card">

            <h2>Student Registration</h2>

            <p class="form-intro">
                Create your Student-Hub account by entering the required information.
            </p>

            <?php if ($message !== ""): ?>
                <p class="<?php echo $messageType === "success" ? "success-message" : "error-message"; ?>"
                   role="alert" aria-live="polite">
                    <?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?>
                </p>
            <?php endif; ?>


            <form id="registrationForm" method="POST" action="register.php" novalidate>


                <!-- Name -->

                <div class="form-group">

                    <label for="fullname">
                        Full Name *
                    </label>

                    <input type="text" id="fullname" name="fullname" placeholder="Enter your full name"
                        autocomplete="name" required aria-describedby="fullname-error">

                    <small id="fullname-error" class="error-message" aria-live="polite">
                    </small>

                </div>


                <!-- Email -->

                <div class="form-group">

                    <label for="email">
                        Email Address *
                    </label>

                    <input type="email" id="email" name="email" placeholder="example@email.com" autocomplete="email"
                        required aria-describedby="email-error">

                    <small id="email-error" class="error-message" aria-live="polite">
                    </small>

                </div>


                <!-- Student ID -->

                <div class="form-group">

                    <label for="studentid">
                        Student ID *
                    </label>

                    <input type="text" id="studentid" name="studentid" placeholder="Example: 26IT118" required
                        aria-describedby="studentid-error">

                    <small id="studentid-error" class="error-message" aria-live="polite">
                    </small>

                </div>


                <!-- Mobile -->

                <div class="form-group">

                    <label for="phone">
                        Mobile Number *
                    </label>

                    <input type="tel" id="phone" name="phone" placeholder="Enter 10-digit mobile number"
                        inputmode="numeric" maxlength="10" autocomplete="tel" required aria-describedby="phone-error">

                    <small id="phone-error" class="error-message" aria-live="polite">
                    </small>

                </div>


                <!-- Course -->

                <div class="form-group">

                    <label for="course">
                        Course *
                    </label>

                    <select id="course" name="course" required aria-describedby="course-error">

                        <option value="">
                            Select Course
                        </option>

                        <option value="btech">
                            B.Tech
                        </option>

                        <option value="bca">
                            BCA
                        </option>

                        <option value="bba">
                            BBA
                        </option>

                        <option value="bcom">
                            B.Com
                        </option>

                        <option value="bsc">
                            B.Sc
                        </option>

                        <option value="ba">
                            B.A.
                        </option>

                    </select>

                    <small id="course-error" class="error-message" aria-live="polite">
                    </small>

                </div>


                <!-- Year -->

                <div class="form-group">

                    <label for="year">
                        Year *
                    </label>

                    <select id="year" name="year" required aria-describedby="year-error">

                        <option value="">
                            Select Year
                        </option>

                        <option value="1">
                            First Year
                        </option>

                        <option value="2">
                            Second Year
                        </option>

                        <option value="3">
                            Third Year
                        </option>

                        <option value="4">
                            Fourth Year
                        </option>

                    </select>

                    <small id="year-error" class="error-message" aria-live="polite">
                    </small>

                </div>


                <!-- Gender -->

                <fieldset class="gender-group" aria-describedby="gender-error">

                    <legend>
                        Gender *
                    </legend>

                    <label>

                        <input type="radio" name="gender" value="male">

                        Male

                    </label>


                    <label>

                        <input type="radio" name="gender" value="female">

                        Female

                    </label>


                    <label>

                        <input type="radio" name="gender" value="other">

                        Other

                    </label>


                    <small id="gender-error" class="error-message" aria-live="polite">
                    </small>

                </fieldset>


                <!-- Password -->

                <div class="form-group">

                    <label for="password">
                        Password *
                    </label>

                    <input type="password" id="password" name="password" placeholder="Create a strong password"
                        autocomplete="new-password" required aria-describedby="password-help password-error">


                    <!-- Password Strength -->

                    <div class="password-meter" aria-label="Password strength">

                        <div id="password-meter-bar" class="password-meter-bar">
                        </div>

                    </div>


                    <small id="password-help" class="password-help">

                        Use at least 8 characters with
                        uppercase, lowercase, number and
                        special character.

                    </small>


                    <small id="password-error" class="error-message" aria-live="polite">
                    </small>

                </div>


                <!-- Confirm Password -->

                <div class="form-group">

                    <label for="confirm-password">
                        Confirm Password *
                    </label>

                    <input type="password" id="confirm-password" name="confirm-password"
                        placeholder="Re-enter your password" autocomplete="new-password" required
                        aria-describedby="confirm-password-error">

                    <small id="confirm-password-error" class="error-message" aria-live="polite">
                    </small>

                </div>


                <!-- Terms -->

                <div class="terms-group">

                    <label for="terms">

                        <input type="checkbox" id="terms" name="terms" required aria-describedby="terms-error">

                        I agree to the terms and conditions *

                    </label>


                    <small id="terms-error" class="error-message" aria-live="polite">
                    </small>

                </div>


                <!-- Submit -->

                <button type="submit">

                    Register

                </button>


                <!-- Success Message -->

                <p id="success-message" class="success-message" role="status" aria-live="polite">
                </p>

            </form>

        </section>


        <!-- Login Link -->

        <p>
            Already have an account?
            <a href="login.html">
                Login here
            </a>
        </p>


        <!-- Home Link -->

        <p>
            <a href="../index.html">
                Back to Home
            </a>
        </p>


        <!-- Footer -->

        <footer>

            <p>
                &copy; 2026 Student-Hub.
                Helping students stay connected.
            </p>

        </footer>

    </main>


    <!-- JavaScript -->

    <script src="../js/script.js"></script>

</body>

</html>
```