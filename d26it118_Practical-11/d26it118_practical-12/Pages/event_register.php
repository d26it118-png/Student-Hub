<?php

require_once __DIR__ . "/../db/db.php";

$eventId = (int) (
    $_GET["event_id"]
    ?? $_POST["event_id"]
    ?? 0
);

$message = "";


/*
 * =========================
 * Handle Registration
 * =========================
 */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $studentId = trim(
        $_POST["student_id"] ?? ""
    );


    /*
     * Validate input
     */

    if ($eventId <= 0 || $studentId === "") {

        $message =
            "Please provide a valid student and event.";

    } else {


        /*
         * Check whether student exists
         */

        $studentCheck = mysqli_prepare(
            $conn,
            "SELECT student_id
             FROM students
             WHERE student_id = ?"
        );


        if ($studentCheck) {

            mysqli_stmt_bind_param(
                $studentCheck,
                "s",
                $studentId
            );

            mysqli_stmt_execute(
                $studentCheck
            );


            $studentResult =
                mysqli_stmt_get_result(
                    $studentCheck
                );


            /*
             * Student does not exist
             */

            if (
                mysqli_num_rows(
                    $studentResult
                ) === 0
            ) {

                $message =
                    "Student ID does not exist.";

            } else {


                /*
                 * Insert registration
                 */

                $sql = "
                    INSERT INTO registrations
                    (
                        student_id,
                        event_id
                    )
                    VALUES (?, ?)
                ";


                $stmt = mysqli_prepare(
                    $conn,
                    $sql
                );


                if ($stmt) {

                    mysqli_stmt_bind_param(
                        $stmt,
                        "si",
                        $studentId,
                        $eventId
                    );


                    /*
                     * Execute registration
                     */

                    if (
                        mysqli_stmt_execute(
                            $stmt
                        )
                    ) {

                        $message =
                            "Event registration successful.";

                    } elseif (
                        mysqli_errno($conn) === 1062
                    ) {

                        $message =
                            "This student is already registered for this event.";

                    } else {

                        $message =
                            "Registration failed: "
                            . mysqli_error($conn);
                    }


                    mysqli_stmt_close(
                        $stmt
                    );

                } else {

                    $message =
                        "Unable to prepare registration query: "
                        . mysqli_error($conn);
                }
            }


            mysqli_stmt_close(
                $studentCheck
            );

        } else {

            $message =
                "Unable to check student: "
                . mysqli_error($conn);
        }
    }
}


/*
 * =========================
 * Get Event Information
 * =========================
 */

$event = null;


if ($eventId > 0) {

    $eventStmt = mysqli_prepare(
        $conn,

        "SELECT
            event_id,
            title,
            category,
            event_date,
            venue,
            organizer
         FROM events
         WHERE event_id = ?"
    );


    if ($eventStmt) {

        mysqli_stmt_bind_param(
            $eventStmt,
            "i",
            $eventId
        );


        mysqli_stmt_execute(
            $eventStmt
        );


        $eventResult =
            mysqli_stmt_get_result(
                $eventStmt
            );


        $event =
            mysqli_fetch_assoc(
                $eventResult
            );


        mysqli_stmt_close(
            $eventStmt
        );
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

    <title>Event Registration - StudentHub</title>

    <link
        rel="stylesheet"
        href="../css/style.css"
    >

</head>

<body>

    <main>

        <h1>Event Registration</h1>


        <!-- =========================
             Event Details
        ========================== -->

        <?php if ($event): ?>

            <h2>
                <?php
                echo htmlspecialchars(
                    $event["title"]
                );
                ?>
            </h2>


            <p>

                <strong>
                    Category:
                </strong>

                <?php
                echo htmlspecialchars(
                    $event["category"]
                );
                ?>

            </p>


            <p>

                <strong>
                    Date:
                </strong>

                <?php
                echo htmlspecialchars(
                    $event["event_date"]
                );
                ?>

            </p>


            <p>

                <strong>
                    Venue:
                </strong>

                <?php
                echo htmlspecialchars(
                    $event["venue"]
                );
                ?>

            </p>


            <p>

                <strong>
                    Organizer:
                </strong>

                <?php
                echo htmlspecialchars(
                    $event["organizer"]
                );
                ?>

            </p>


        <?php else: ?>

            <p>

                <strong>
                    Event not found.
                </strong>

            </p>

        <?php endif; ?>


        <!-- =========================
             Registration Message
        ========================== -->

        <?php if ($message !== ""): ?>

            <p>

                <strong>

                    <?php
                    echo htmlspecialchars(
                        $message
                    );
                    ?>

                </strong>

            </p>

        <?php endif; ?>


        <!-- =========================
             Registration Form
        ========================== -->

        <?php if ($event): ?>

            <form method="post">

                <input
                    type="hidden"
                    name="event_id"
                    value="<?php echo $eventId; ?>"
                >


                <label for="student_id">

                    Student ID:

                </label>

                <br>


                <input
                    type="text"
                    id="student_id"
                    name="student_id"
                    placeholder="e.g. ST101"
                    required
                >


                <br>
                <br>


                <button type="submit">

                    Register for Event

                </button>

            </form>

        <?php endif; ?>


        <!-- Back to Events -->

        <p>

            <a href="events.php">

                ← Back to Events

            </a>

        </p>

    </main>

</body>

</html>