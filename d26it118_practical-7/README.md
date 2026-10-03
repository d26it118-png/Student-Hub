# Student-Hub - Practical 7

## Practical Title
**PHP Form Processing with Server-Side Validation and CSV/JSON File Storage**

## Objective
Process the Student-Hub registration form using PHP, validate and sanitize submitted data on the server side, and safely store registration records in CSV format.

## Technologies
- HTML5
- CSS3
- JavaScript
- PHP
- CSV file storage
- XAMPP

## Practical 7 Changes
1. `register.html` is converted to `register.php`.
2. The registration form uses `method="POST"`.
3. PHP validates all important form fields again on the server.
4. Input values are trimmed/sanitized before validation and storage.
5. Valid registration records are stored in `data/students.csv`.
6. CSV writing uses file locking (`flock`) to reduce concurrent-write problems.
7. Password and confirm-password values are validated but are not stored in the CSV file.
8. Clear success and error messages are displayed after submission.
9. Existing Practical 6 JavaScript validation is retained as client-side validation.

## How to Run
1. Copy the `d26it118_practical_7` folder into:
   `C:\xampp\htdocs\`
2. Start **Apache** from XAMPP.
3. Open:
   `http://localhost/d26it118_practical_7/Pages/register.php`
4. Fill in the registration form and click **Register**.
5. After a valid submission, check:
   `data/students.csv`

## Test Cases

| Test | Input | Expected Result |
|---|---|---|
| Valid registration | All fields valid | Success message and CSV record |
| Invalid name | Numbers/special characters | Name validation error |
| Invalid email | `abc@` | Email validation error |
| Invalid Student ID | Too short/invalid characters | Student ID error |
| Invalid mobile | Less than 10 digits | Mobile validation error |
| Empty course | No course selected | Course error |
| Empty year | No year selected | Year error |
| No gender | No option selected | Gender error |
| Weak password | `abc123` | Password error |
| Password mismatch | Different passwords | Password mismatch error |
| Terms unchecked | Checkbox not selected | Terms error |

## Post Laboratory Work
Submit:
- PHP source code
- JavaScript source code
- CSS/HTML files
- Test cases and screenshots
- Generated `students.csv`

## Viva Points
- Why is POST used?
- Why is server-side validation required when JavaScript validation already exists?
- What is the purpose of `filter_var()`?
- Why is `htmlspecialchars()` used when displaying messages?
- Why is `flock()` used while writing the CSV?
- Why should passwords not be stored as plain text?
