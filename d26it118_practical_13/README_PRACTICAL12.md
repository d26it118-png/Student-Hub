# StudentHub — Practical 12: Event Management CRUD

## Features
- Admin-only add, view, update and delete event functionality.
- Uses MySQLi prepared statements for insert, update, delete and edit lookup.
- Uploads event poster images and validates upload errors, actual MIME type, image data and size.
- Accepted poster types: JPG/JPEG, PNG and WEBP. Maximum file size: 2 MB.
- Randomized server-generated filenames; poster uploads are stored in `uploads/event_posters/`.
- Event status management: Upcoming, Completed and Cancelled.
- CSRF token, HTML escaping, delete confirmation and success/failure messages.
- Replacing a poster removes the old poster file; deleting an event removes its associated poster.

## Setup in XAMPP
1. Copy `d26it118_Practical-12` into `C:\xampp\htdocs\`.
2. Start Apache and MySQL.
3. Open phpMyAdmin and select the `studenthub` database used by Practical 11.
4. Import `db/practical12_event_management.sql` once. It adds the `poster` and `status` columns to the existing `events` table and inserts sample events. If you have already added either column manually, remove that column's `ADD COLUMN` clause before importing.
5. Check `db/db.php` for local MySQL settings (default XAMPP often uses `root` with an empty password).
6. Sign in with an existing admin account: `http://localhost/d26it118_Practical-12/Pages/login_user.php`.
7. From Admin Dashboard choose **Open Event Management (Practical 12)**, or open `http://localhost/d26it118_Practical-12/Pages/event_management.php` directly.

## Poster validation
- Client-side `accept` is only a convenience; server-side validation is authoritative.
- Server checks upload status, non-empty file size up to 2 MB, MIME type using `finfo`, and real image data using `getimagesize`.
- File extensions are assigned by the server from the verified MIME type, not trusted from the submitted filename.
- Ensure `uploads/event_posters/` is writable by Apache/PHP on your machine.
- For additional hardening on a production server, configure the upload directory to disallow PHP/script execution.

## Suggested screenshots for submission
1. Event list showing sample events and posters.
2. Add-event form and success message.
3. Edit event and updated values.
4. Delete confirmation and success message.
5. Invalid file type rejection (e.g. a `.txt` file renamed as `.jpg`).
6. Oversized file rejection (over 2 MB).
7. Invalid/no poster upload still allows a normal event to be saved when no file is selected.

## Test cases
See `docs/PRACTICAL12_TEST_CASES.md`. Run the tests locally and mark actual outcomes before submission; screenshots are not fabricated or included.
