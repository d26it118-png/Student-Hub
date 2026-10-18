# Practical 12 Test Cases — Event Management CRUD

Run these in the local XAMPP environment and record the actual result.

| No. | Test | Expected result |
|---|---|---|
| 1 | Open Event Management without an admin session | Access is denied or redirected to login |
| 2 | Add an event with all valid fields and no poster | Event is saved and success message appears |
| 3 | Add an event with a valid JPG under 2 MB | Event and poster are saved and displayed |
| 4 | Upload a valid PNG or WEBP image under 2 MB | Upload is accepted |
| 5 | Upload a text/PHP file renamed to `.jpg` | Server rejects the file based on MIME/image validation |
| 6 | Upload a file larger than 2 MB | Server rejects it with a size message |
| 7 | Submit a zero-byte or corrupted image | Server rejects it |
| 8 | Edit an event's details without choosing a new poster | Details update and existing poster remains |
| 9 | Edit an event and upload a new valid poster | Details and poster update; old poster is removed |
| 10 | Delete an event and confirm | Database row and associated poster are removed |
| 11 | Delete an event and cancel browser confirmation | No delete request is submitted |
| 12 | Submit an invalid/missing CSRF token | Request is rejected |
| 13 | Try blank title/category/venue/organizer | Server-side validation rejects the form |
| 14 | Change event status to Completed or Cancelled | New status is saved and displayed |
| 15 | Force a database failure or use an invalid schema | Failure message appears rather than a success message |

Record Pass/Fail and attach your own screenshots after executing each relevant test.
