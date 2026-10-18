<?php

require_once __DIR__ . "/../db/db.php";

$eventRows = [];

/*
 * Fetch events from MySQL database
 */
$sql = "
    SELECT
        event_id,
        title,
        category,
        event_date,
        venue,
        organizer
    FROM events
    ORDER BY event_date
";

$result = mysqli_query($conn, $sql);

if ($result) {

    while ($row = mysqli_fetch_assoc($result)) {

        $eventRows[] = [
            "id"        => (int) $row["event_id"],
            "title"     => $row["title"],
            "category"  => $row["category"],
            "date"      => $row["event_date"],
            "venue"     => $row["venue"],
            "organizer" => $row["organizer"]
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Student Hub - Events</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >
</head>

<body>

    <!-- =========================
         Navigation Bar
    ========================== -->

    <nav>

        <a href="index.html">Home</a>

        <a href="dashboard.html">Dashboard</a>

        <a href="events.php">Events</a>

        <a href="students.php">Students</a>

        <a href="profile.html">Profile</a>

        <a href="faq.html">FAQ</a>

        <a href="contact.html">Contact</a>

    </nav>


    <!-- =========================
         Main Content
    ========================== -->

    <main>

       <div class="events-heading">

    <h1 style="text-align: center;">Upcoming Events</h1>

        <p class="intro-text" style="text-align: center;">
            Events are fetched from the MySQL database.
        </p>

        <!-- =========================
             Search / Filter / Sort
        ========================== -->

        <div class="data-controls">

            <!-- Search -->
            <input
                id="eventSearch"
                type="search"
                placeholder="Search events..."
            >


            <!-- Category -->
            <select id="eventCategory">

                <option value="all">
                    All Categories
                </option>

                <option value="Technical">
                    Technical
                </option>

                <option value="Workshop">
                    Workshop
                </option>

                <option value="Competition">
                    Competition
                </option>

                <option value="Seminar">
                    Seminar
                </option>

                <option value="Sports">
                    Sports
                </option>

            </select>


            <!-- Sorting -->
            <select id="eventSort">

                <option value="default">
                    Sort By
                </option>

                <option value="az">
                    Name A-Z
                </option>

                <option value="za">
                    Name Z-A
                </option>

                <option value="date">
                    Date
                </option>

            </select>

        </div>


        <!-- =========================
             Events Container
        ========================== -->

        <div
            id="eventContainer"
            class="data-grid"
            aria-live="polite"
        >
            Loading events...
        </div>


        <!-- =========================
             Pagination
        ========================== -->

        <div class="pagination">

            <button
                id="eventPrev"
                type="button"
            >
                ← Previous
            </button>


            <span id="eventPageInfo">
                Page 1
            </span>


            <button
                id="eventNext"
                type="button"
            >
                Next →
            </button>

        </div>


        <!-- =========================
             Footer
        ========================== -->

        <footer>

            <p>
                &copy; 2026 Student-Hub.
                Helping students stay connected.
            </p>

        </footer>

    </main>


    <!-- =========================
         Send MySQL Data to JavaScript
    ========================== -->

    <script>

        window.STUDENTHUB_EVENTS =
            <?php

            echo json_encode(
                $eventRows,
                JSON_HEX_TAG |
                JSON_HEX_APOS |
                JSON_HEX_AMP |
                JSON_HEX_QUOT
            );

            ?>;

    </script>


    <!-- Events JavaScript -->

    <script
        type="module"
        src="../js/events.js"
    ></script>

</body>

</html>