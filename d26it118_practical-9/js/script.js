console.log("Student-Hub Java-script Loaded Successfully");
console.log("Welcome to Student-Hub");
console.log("Practical-5 Registration Form Validation Loaded");

let studentname = "Kotecha Jigar";
let course = "IT";
let semester = "3rd";

console.log("Student Name :" + studentname);
console.log("Course :" + course);
console.log("semester :" + semester);

let collage = "Charusat";
let year = 2026;
let isstudent = true;

console.log("collage :" + collage);
console.log("Year :" + year);
console.log("isstudent :" + isstudent);

function Welcomemsg() {
    console.log("Welcome to Student-hub");
}
Welcomemsg();
function Welcomemsg(name) {
    console.log("Welcome :" + name);
}

Welcomemsg("Jigar");
Welcomemsg("shivam");
Welcomemsg("jash");

const heading = document.getElementById("welcome-heading");
const headingButton = document.getElementById("change-heading-btn");
const announcementButton = document.getElementById("announcement-btn");
const announcements = document.getElementById("announcements");
const themeStorageKey = "student-hub-theme";

function saveTheme(theme) {
    try {
        localStorage.setItem(themeStorageKey, theme);
    } catch (error) {
        // The site still works if the browser blocks local storage.
    }
}

function getSavedTheme() {
    try {
        return localStorage.getItem(themeStorageKey);
    } catch (error) {
        return null;
    }
}

function updateThemeButton(button) {
    button.textContent = document.body.classList.contains("dark-mode") ? "Light Mode" : "Dark Mode";
    button.setAttribute("aria-pressed", String(document.body.classList.contains("dark-mode")));
}

if (getSavedTheme() === "dark") {
    document.body.classList.add("dark-mode");
}

let darkModeButton = document.getElementById("dark-mode-btn");

// Add the theme control to pages that do not have the Home page action buttons.
if (!darkModeButton && document.body.dataset.themeToggle !== "disabled") {
    const navigation = document.querySelector("nav");
    if (navigation) {
        darkModeButton = document.createElement("button");
        darkModeButton.type = "button";
        darkModeButton.id = "dark-mode-btn";
        navigation.append(darkModeButton);
    }
}

if (headingButton) {
    headingButton.addEventListener("click", () => {
        const isDefaultHeading = heading.textContent === "Welcome to Campus Connect";
        heading.textContent = isDefaultHeading ? "Welcome Back to Student-Hub!" : "Welcome to Campus Jigar Kotecha!";
    });
}

if (darkModeButton) {
    updateThemeButton(darkModeButton);
    darkModeButton.addEventListener("click", () => {
        document.body.classList.toggle("dark-mode");
        saveTheme(document.body.classList.contains("dark-mode") ? "dark" : "light");
        updateThemeButton(darkModeButton);
    });
}

if (announcementButton) {
    announcementButton.addEventListener("click", () => {
        const isHidden = announcements.hidden;
        announcements.hidden = !isHidden;
        announcementButton.textContent = isHidden ? "Close Announcements" : "Open Announcements";
        announcementButton.setAttribute("aria-expanded", String(isHidden));
    });
}

document.querySelectorAll(".faq-question").forEach((question) => {
    question.addEventListener("click", () => {
        const answer = question.nextElementSibling;
        const isExpanded = question.getAttribute("aria-expanded") === "true";

        // Close every other FAQ before opening the selected one.
        document.querySelectorAll(".faq-question").forEach((otherQuestion) => {
            if (otherQuestion !== question) {
                otherQuestion.setAttribute("aria-expanded", "false");
                otherQuestion.nextElementSibling.hidden = true;
            }
        });

        question.setAttribute("aria-expanded", String(!isExpanded));
        answer.hidden = isExpanded;
    });
});

/* =========================
   PRACTICAL 5 VALIDATION
   ========================= */

const registrationForm = document.getElementById("registrationForm");

if (registrationForm) {

    const fullname = document.getElementById("fullname");
    const email = document.getElementById("email");
    const studentid = document.getElementById("studentid");
    const phone = document.getElementById("phone");
    const course = document.getElementById("course");
    const year = document.getElementById("year");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm-password");
    const terms = document.getElementById("terms");

    const passwordBar =
        document.getElementById("password-meter-bar");

    const successMessage =
        document.getElementById("success-message");

    /* Regular Expressions */

    const namePattern = /^[A-Za-z ]{2,50}$/;

    const emailPattern =
        /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    const studentIdPattern =
        /^[A-Za-z0-9-]{4,20}$/;

    const mobilePattern =
        /^[6-9][0-9]{9}$/;

    const passwordPattern =
        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;


    /* Show Error */

    function setError(field, message) {

        const error =
            document.getElementById(field.id + "-error");

        if (error) {
            error.textContent = message;
        }

        field.classList.add("input-error");
        field.classList.remove("input-valid");

        field.setAttribute("aria-invalid", "true");
    }


    /* Show Valid */

    function setValid(field) {

        const error =
            document.getElementById(field.id + "-error");

        if (error) {
            error.textContent = "";
        }

        field.classList.remove("input-error");
        field.classList.add("input-valid");

        field.setAttribute("aria-invalid", "false");
    }


    /* Name Validation */

    function validateName() {

        if (!namePattern.test(fullname.value.trim())) {

            setError(
                fullname,
                "Enter a valid name using letters and spaces only."
            );

            return false;
        }

        setValid(fullname);

        return true;
    }


    /* Email Validation */

    function validateEmail() {

        if (!emailPattern.test(email.value.trim())) {

            setError(
                email,
                "Enter a valid email address."
            );

            return false;
        }

        setValid(email);

        return true;
    }


    /* Student ID Validation */

    function validateStudentId() {

        if (!studentIdPattern.test(studentid.value.trim())) {

            setError(
                studentid,
                "Enter a valid Student ID."
            );

            return false;
        }

        setValid(studentid);

        return true;
    }


    /* Mobile Validation */

    function validatePhone() {

        if (!mobilePattern.test(phone.value.trim())) {

            setError(
                phone,
                "Enter a valid 10-digit mobile number."
            );

            return false;
        }

        setValid(phone);

        return true;
    }


    /* Course Validation */

    function validateCourse() {

        if (course.value === "") {

            setError(
                course,
                "Please select your course."
            );

            return false;
        }

        setValid(course);

        return true;
    }


    /* Year Validation */

    function validateYear() {

        if (year.value === "") {

            setError(
                year,
                "Please select your year."
            );

            return false;
        }

        setValid(year);

        return true;
    }


    /* Gender Validation */

    function validateGender() {

        const selected =
            document.querySelector(
                'input[name="gender"]:checked'
            );

        const error =
            document.getElementById("gender-error");

        if (!selected) {

            error.textContent =
                "Please select your gender.";

            return false;
        }

        error.textContent = "";

        return true;
    }


    /* Password Strength */

    function updatePasswordMeter() {

        let score = 0;

        if (password.value.length >= 8)
            score++;

        if (/[a-z]/.test(password.value))
            score++;

        if (/[A-Z]/.test(password.value))
            score++;

        if (/\d/.test(password.value))
            score++;

        if (/[^A-Za-z0-9]/.test(password.value))
            score++;

        passwordBar.style.width =
            (score * 20) + "%";

        if (score === 0) {

            passwordBar.style.background =
                "transparent";

        } else if (score <= 2) {

            passwordBar.style.background =
                "#dc2626";

        } else if (score <= 4) {

            passwordBar.style.background =
                "#f59e0b";

        } else {

            passwordBar.style.background =
                "#16a34a";
        }
    }


    /* Password Validation */

    function validatePassword() {

        if (!passwordPattern.test(password.value)) {

            setError(
                password,
                "Password must contain 8+ characters, uppercase, lowercase, number and special character."
            );

            return false;
        }

        setValid(password);

        return true;
    }


    /* Confirm Password */

    function validateConfirmPassword() {

        if (
            confirmPassword.value === "" ||
            confirmPassword.value !== password.value
        ) {

            setError(
                confirmPassword,
                "Passwords do not match."
            );

            return false;
        }

        setValid(confirmPassword);

        return true;
    }


    /* Terms */

    function validateTerms() {

        const error =
            document.getElementById("terms-error");

        if (!terms.checked) {

            error.textContent =
                "You must accept the terms and conditions.";

            return false;
        }

        error.textContent = "";

        return true;
    }


    /* Live Validation */

    fullname.addEventListener("input", validateName);

    email.addEventListener("input", validateEmail);

    studentid.addEventListener(
        "input",
        validateStudentId
    );

    phone.addEventListener(
        "input",
        validatePhone
    );

    course.addEventListener(
        "change",
        validateCourse
    );

    year.addEventListener(
        "change",
        validateYear
    );

    password.addEventListener("input", function () {

        updatePasswordMeter();

        if (password.value !== "") {
            validatePassword();
        }

        if (confirmPassword.value !== "") {
            validateConfirmPassword();
        }
    });

    confirmPassword.addEventListener(
        "input",
        validateConfirmPassword
    );

    terms.addEventListener(
        "change",
        validateTerms
    );


    /* Submit */

    registrationForm.addEventListener(
        "submit",
        function (event) {

            successMessage.textContent = "";

            const valid = [

                validateName(),
                validateEmail(),
                validateStudentId(),
                validatePhone(),
                validateCourse(),
                validateYear(),
                validateGender(),
                validatePassword(),
                validateConfirmPassword(),
                validateTerms()

            ].every(Boolean);

            if (!valid) {
                // Stop the POST request when client-side validation fails.
                event.preventDefault();

                const firstError =
                    registrationForm.querySelector(".input-error");

                if (firstError) {
                    firstError.focus();
                }
            }
            // When valid, allow the normal POST request to register.php.
        }
    );
}