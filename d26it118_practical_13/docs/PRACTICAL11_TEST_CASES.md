# Practical 11 — Student CRUD test cases

| ID | Test | Action | Expected result |
|---|---|---|---|
| TC-01 | View records | Open Student Management as admin | Students appear in a table, 10 per page |
| TC-02 | Add student | Add a valid new unique student | Success message and new row |
| TC-03 | Edit student | Edit an existing record and save | Updated values appear |
| TC-04 | Delete student | Confirm deletion of a test record | Record is removed and success message appears |
| TC-05 | Search by name | Search a name | Matching record(s) appear |
| TC-06 | Search by email | Search an email | Matching record(s) appear |
| TC-07 | Search by course | Search a course name | Matching record(s) appear |
| TC-08 | Pagination Next | Click Next on page 1 | Next set of up to 10 records appears |
| TC-09 | Pagination Previous | Click Previous on page 2 | Previous set appears |
| TC-10 | No results | Search for `zzzz-not-found` | “No student records found.” appears |
| TC-11 | Required fields | Submit with a required field empty | Browser/server validation prevents save |
| TC-12 | Invalid email | Enter invalid email | Validation message; no save |
| TC-13 | Duplicate email/ID | Use an existing email or student ID | Duplicate values rejected with clear message |
| TC-14 | Cancel deletion | Click Delete then Cancel | Record remains unchanged |
| TC-15 | Record count | Run `SELECT COUNT(*) FROM students;` | At least 20 seeded records |
| TC-16 | Course filter | Select a course | Only students from that course appear |
| TC-17 | Year filter | Select an academic year | Only students in that year appear |
| TC-18 | Access control | Open page without admin login | Redirected to login or appropriate dashboard |

**Note:** The supplied test-case PDF marks some tests as pending; execute them in XAMPP and update the result/status with your actual observation.
