# Practical 13 — Test Cases

| No. | Test | Steps | Expected result |
|---|---|---|---|
| 1 | GET events | Open `api/events.php` | HTTP 200 and valid JSON with `success: true` and `data` array |
| 2 | Add event | Complete all fields and click Add Event | New row appears without page reload; JSON success response |
| 3 | Required-field validation | Leave a required field blank | Browser blocks submission; no API write occurs |
| 4 | Server-side validation | Send POST JSON with empty title or invalid date | HTTP 422 and JSON error message |
| 5 | Update event | Click Edit, change a field, save | Updated row appears without page reload |
| 6 | Delete event | Click Delete and confirm | Event is removed without page reload |
| 7 | Cancel delete | Click Delete and cancel confirmation | Event remains unchanged |
| 8 | Unknown ID | GET `api/events.php?id=999999` | HTTP 404 and valid JSON error |
| 9 | Malformed JSON | Send invalid JSON to POST endpoint | HTTP 400 and valid JSON error |
| 10 | Loading state | Refresh list with Network throttling enabled | Loading indicator appears while request is pending |
| 11 | Network inspection | Open DevTools → Network → Fetch/XHR and perform CRUD | Requests, status codes, payloads and JSON responses are visible |
| 12 | Database verification | Check the `events` table in phpMyAdmin | Add/update/delete changes match the frontend |

## Screenshots to attach
1. Event list loaded from the API.
2. Add event success.
3. Update event success.
4. Delete event success.
5. Validation error or server JSON error.
6. DevTools Network tab showing GET and POST requests and a JSON response.
