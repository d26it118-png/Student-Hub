# StudentHub — D26IT118 Practical 13

## Practical Title
**PHP JSON API and AJAX/Fetch-Based CRUD without Page Reload**

## Objective
Create a PHP JSON API for StudentHub events and use JavaScript Fetch API to list, add, update and delete events asynchronously without reloading the page.

## Files added for Practical 13
- `api/events.php` — JSON API endpoint; handles GET and POST requests, validates data and uses MySQLi prepared statements.
- `Pages/events_ajax.php` — frontend using Fetch API; shows loading, success and error states, and supports add/list/update/delete without a full-page reload.
- `docs/PRACTICAL13_TEST_CASES.md` — test plan and screenshot checklist.

## Requirements
- XAMPP with Apache and MySQL running
- PHP with MySQLi enabled
- Existing `studenthub` database
- `events` table with columns: `event_id`, `title`, `category`, `event_date`, `venue`, `organizer`, and `status`
- If you are continuing from Practical 12, use the same database and its event-management schema. Do not import a second copy of the database unnecessarily.

## Run the practical
1. Extract/copy the `d26it118_Practical-13` folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in XAMPP.
3. In phpMyAdmin, check that database `studenthub` and the `events` table exist. The Practical 12 schema adds the `status` column.
4. Open the AJAX CRUD page:
   `http://localhost/d26it118_Practical-13/Pages/events_ajax.php`
5. To inspect the API response directly, open:
   `http://localhost/d26it118_Practical-13/api/events.php`
6. Open browser Developer Tools → **Network** → Fetch/XHR, then add, edit and delete an event. Select each `events.php` request to inspect its status, request payload and JSON response.

> If your extracted folder has a different name, update the URL to match that folder name. Use the local server URL (`http://localhost/...`), not a `file:///` URL.

## API contract

### GET `api/events.php`
Returns all events:
```json
{"success":true,"data":[{"event_id":1,"title":"Example event"}]}
```
The `data` array contains the selected event fields.

### GET `api/events.php?id=1`
Returns one event or a JSON 404 error.

### POST `api/events.php`
Send a JSON body with `Content-Type: application/json`.

Add:
```json
{"action":"add","title":"Web Workshop","category":"Workshop","event_date":"2026-11-18","venue":"Computer Lab 2","organizer":"Web Club","status":"Upcoming"}
```

Update:
```json
{"action":"update","event_id":1,"title":"Updated Workshop","category":"Workshop","event_date":"2026-11-18","venue":"Computer Lab 2","organizer":"Web Club","status":"Upcoming"}
```

Delete:
```json
{"action":"delete","event_id":1}
```

Responses include a boolean `success` and a `message` or `data`. Errors use appropriate HTTP status codes (for example, 400 for malformed JSON, 404 for a missing event, 422 for invalid input, and 500 for a server/database error).

## Implementation details
- `fetch()` sends requests asynchronously.
- The frontend parses JSON and checks HTTP status and the `success` field.
- Required fields, length limits, date format and event status are validated in the browser and again in PHP.
- MySQLi prepared statements are used for add, update and delete operations.
- Text is escaped before inserting event values into the table HTML.
- Loading, success and error feedback is shown to the user.
- The page is not submitted/reloaded for CRUD actions.

## Submission checklist
- API endpoint file(s)
- Screenshot of event list and CRUD operations
- Browser Network tab screenshots showing GET and POST requests and JSON responses
- Screenshot showing validation/error handling
- Updated project ZIP and practical report with answers to the viva questions
