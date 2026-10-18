console.log("Student-Hub Java-script Loaded Successfully");
console.log("Welcome to Student-Hub");
console.log("Practical-4 Javascript");

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

function Welcomemsg()
{
    console.log("Welcome to Student-hub");
}
Welcomemsg();
function Welcomemsg(name)
{
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
if (!darkModeButton) {
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

