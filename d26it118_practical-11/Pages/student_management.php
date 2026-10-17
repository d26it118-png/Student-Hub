<?php
require_once __DIR__ . "/auth_check.php";
require_role("admin");

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}
function h($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8"); }
function go_status(string $status): void {
    header("Location: student_management.php?status=" . rawurlencode($status));
    exit;
}
$statusMessages = [
    "added" => "Student added successfully.",
    "updated" => "Student updated successfully.",
    "deleted" => "Student deleted successfully."
];
$success = $statusMessages[(string)($_GET["status"] ?? "")] ?? "";
$error = "";
$editStudent = null;
$form = ["student_id"=>"", "full_name"=>"", "email"=>"", "mobile"=>"", "course"=>"", "year"=>"1", "gender"=>"Other"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["csrf_token"] ?? "";
    if (!is_string($token) || !hash_equals($_SESSION["csrf_token"], $token)) {
        http_response_code(403);
        exit("Invalid request token. Refresh the page and try again.");
    }
    $action = (string)($_POST["action"] ?? "");
    $studentId = trim((string)($_POST["student_id"] ?? ""));

    if ($action === "delete") {
        if ($studentId === "" || strlen($studentId) > 20) {
            $error = "Invalid student ID.";
        } else {
            try {
                $stmt = $conn->prepare("DELETE FROM students WHERE student_id = ?");
                $stmt->bind_param("s", $studentId);
                $stmt->execute();
                $changed = $stmt->affected_rows;
                $stmt->close();
                if ($changed > 0) go_status("deleted");
                $error = "Student was not deleted. The record may no longer exist.";
            } catch (mysqli_sql_exception $e) { $error = "Unable to delete the student. Check related records and try again."; }
        }
    } elseif ($action === "add" || $action === "update") {
        $form = [
            "student_id"=>$studentId,
            "full_name"=>trim((string)($_POST["full_name"] ?? "")),
            "email"=>trim((string)($_POST["email"] ?? "")),
            "mobile"=>trim((string)($_POST["mobile"] ?? "")),
            "course"=>trim((string)($_POST["course"] ?? "")),
            "year"=>trim((string)($_POST["year"] ?? "")),
            "gender"=>(string)($_POST["gender"] ?? "Other")
        ];
        $year = filter_var($form["year"], FILTER_VALIDATE_INT);
        if ($studentId === "" || strlen($studentId) > 20) $error = "Enter a student ID (maximum 20 characters).";
        elseif ($form["full_name"] === "" || mb_strlen($form["full_name"]) > 100) $error = "Enter a student name (maximum 100 characters).";
        elseif (!filter_var($form["email"], FILTER_VALIDATE_EMAIL) || strlen($form["email"]) > 100) $error = "Enter a valid email address (maximum 100 characters).";
        elseif (strlen($form["mobile"]) > 15) $error = "Mobile number must be 15 characters or fewer.";
        elseif ($form["course"] === "" || strlen($form["course"]) > 50) $error = "Enter a course (maximum 50 characters).";
        elseif ($year === false || $year < 1 || $year > 10) $error = "Enter a valid academic year.";
        elseif (!in_array($form["gender"], ["male", "female", "Other"], true)) $error = "Select a valid gender.";
        else {
            try {
                if ($action === "add") {
                    $stmt = $conn->prepare("INSERT INTO students (student_id, full_name, email, mobile, course, year, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->bind_param("sssssis", $studentId, $form["full_name"], $form["email"], $form["mobile"], $form["course"], $year, $form["gender"]);
                    $stmt->execute(); $stmt->close(); go_status("added");
                } else {
                    $originalId = trim((string)($_POST["original_student_id"] ?? ""));
                    if ($originalId === "" || strlen($originalId) > 20) $error = "Invalid original student ID.";
                    else {
                        $stmt = $conn->prepare("UPDATE students SET student_id = ?, full_name = ?, email = ?, mobile = ?, course = ?, year = ?, gender = ? WHERE student_id = ?");
                        $stmt->bind_param("sssssiss", $studentId, $form["full_name"], $form["email"], $form["mobile"], $form["course"], $year, $form["gender"], $originalId);
                        $stmt->execute(); $stmt->close(); go_status("updated");
                    }
                }
            } catch (mysqli_sql_exception $e) {
                $error = ($e->getCode() === 1062) ? "That student ID or email already exists. Use unique values." : "The database operation failed. Please check the details and try again.";
            }
        }
    } else $error = "Unknown action.";
}

$editId = trim((string)($_GET["edit"] ?? ""));
if ($_SERVER["REQUEST_METHOD"] !== "POST" && $editId !== "") {
    if (strlen($editId) <= 20) {
        $stmt = $conn->prepare("SELECT student_id, full_name, email, mobile, course, year, gender FROM students WHERE student_id = ?");
        $stmt->bind_param("s", $editId); $stmt->execute();
        $editStudent = $stmt->get_result()->fetch_assoc() ?: null; $stmt->close();
        if ($editStudent) $form = $editStudent;
        else $error = "Student record not found.";
    } else $error = "Invalid student ID.";
}

$q = trim((string)($_GET["q"] ?? ""));
$courseFilter = trim((string)($_GET["course"] ?? ""));
$yearFilter = trim((string)($_GET["year"] ?? ""));
$pageNum = filter_var($_GET["page"] ?? 1, FILTER_VALIDATE_INT);
$pageNum = ($pageNum && $pageNum > 0) ? $pageNum : 1;
$perPage = 10;
$where = []; $params = []; $types = "";
if ($q !== "") { $where[] = "(full_name LIKE ? OR email LIKE ? OR course LIKE ?)"; $like = "%".$q."%"; array_push($params, $like, $like, $like); $types .= "sss"; }
if ($courseFilter !== "") { $where[] = "course = ?"; $params[] = $courseFilter; $types .= "s"; }
if ($yearFilter !== "") { $validYear = filter_var($yearFilter, FILTER_VALIDATE_INT); if ($validYear !== false && $validYear >= 1 && $validYear <= 10) { $where[] = "year = ?"; $params[] = $validYear; $types .= "i"; } else { $yearFilter = ""; } }
$whereSql = $where ? " WHERE ".implode(" AND ", $where) : "";
$countSql = "SELECT COUNT(*) AS total FROM students".$whereSql;
$countStmt = $conn->prepare($countSql);
if ($types !== "") $countStmt->bind_param($types, ...$params);
$countStmt->execute(); $totalRecords = (int)$countStmt->get_result()->fetch_assoc()["total"]; $countStmt->close();
$totalPages = max(1, (int)ceil($totalRecords / $perPage)); $pageNum = min($pageNum, $totalPages); $offset = ($pageNum - 1) * $perPage;
$listSql = "SELECT student_id, full_name, email, mobile, course, year, gender FROM students".$whereSql." ORDER BY student_id ASC LIMIT ? OFFSET ?";
$listStmt = $conn->prepare($listSql); $listTypes = $types."ii"; $listParams = $params; $listParams[] = $perPage; $listParams[] = $offset;
$listStmt->bind_param($listTypes, ...$listParams); $listStmt->execute(); $students = $listStmt->get_result();
$courseResult = $conn->query("SELECT DISTINCT course FROM students ORDER BY course"); $courses = []; while ($c = $courseResult->fetch_assoc()) $courses[] = $c["course"];
function page_url(int $page, string $q, string $course, string $year): string { return "student_management.php?".http_build_query(["page"=>$page,"q"=>$q,"course"=>$course,"year"=>$year]); }
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Student Management | StudentHub</title><link rel="stylesheet" href="../css/style.css"><style>
:root{--pr11-blue:#2447a8;--pr11-border:#dbe2ef;--pr11-bg:#f5f7fb}body{background:var(--pr11-bg)}.pr11-wrap{max-width:1250px;margin:28px auto;padding:0 18px}.pr11-head,.pr11-panel{background:#fff;border:1px solid var(--pr11-border);border-radius:14px;padding:22px;margin-bottom:18px;box-shadow:0 4px 16px #13294b0b}.pr11-head{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}.pr11-head h1{margin:0;color:#172554}.pr11-sub{color:#64748b;margin:6px 0 0}.pr11-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}.pr11-field label{display:block;font-weight:600;margin-bottom:6px}.pr11-field input,.pr11-field select,.pr11-filter input,.pr11-filter select{width:100%;box-sizing:border-box;padding:10px 11px;border:1px solid #cbd5e1;border-radius:8px;background:white}.pr11-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:16px}.pr11-btn{display:inline-block;border:0;border-radius:8px;padding:10px 15px;background:var(--pr11-blue);color:white;text-decoration:none;cursor:pointer;font-weight:600}.pr11-btn.secondary{background:#e8edf7;color:#24324b}.pr11-btn.danger{background:#b42318}.pr11-notice{padding:12px 15px;border-radius:8px;margin-bottom:14px}.pr11-success{background:#e8f8ee;color:#166534}.pr11-error{background:#fff0f0;color:#991b1b}.pr11-filter{display:grid;grid-template-columns:2fr 1fr 1fr auto auto;gap:10px;align-items:end}.pr11-table-wrap{overflow-x:auto}table.pr11-table{width:100%;border-collapse:collapse;min-width:850px}table.pr11-table th,table.pr11-table td{padding:12px 10px;text-align:left;border-bottom:1px solid #e5eaf2}table.pr11-table th{background:#f0f4fb;color:#253454}table.pr11-table tr:hover td{background:#fafcff}.pr11-inline{display:inline}.pr11-link{color:#2447a8;font-weight:600}.pr11-pager{display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-top:18px}.pr11-pager a{padding:8px 12px;border-radius:7px;background:#e8edf7;text-decoration:none;color:#24324b}.pr11-pager a.disabled{opacity:.45;pointer-events:none}.pr11-count{color:#64748b;font-size:.95rem}@media(max-width:750px){.pr11-grid,.pr11-filter{grid-template-columns:1fr}}
</style></head><body><main class="pr11-wrap">
<header class="pr11-head"><div><h1>Student Management</h1><p class="pr11-sub">Practical 11 · Create, view, edit, delete, search and filter students</p></div><div class="pr11-actions"><a class="pr11-btn secondary" href="admin_dashboard.php">Admin Dashboard</a><a class="pr11-btn secondary" href="../php/logout.php">Logout</a></div></header>
<?php if ($success !== ""): ?><div class="pr11-notice pr11-success" role="status"><?=h($success)?></div><?php endif; ?>
<?php if ($error !== ""): ?><div class="pr11-notice pr11-error" role="alert"><?=h($error)?></div><?php endif; ?>
<section class="pr11-panel"><h2><?= $editStudent ? "Edit Student" : "Add New Student" ?></h2><form method="post" action="student_management.php<?= $editStudent ? '?edit='.rawurlencode($editStudent['student_id']) : '' ?>"><input type="hidden" name="csrf_token" value="<?=h($_SESSION['csrf_token'])?>"><input type="hidden" name="action" value="<?=$editStudent?'update':'add'?>"><?php if($editStudent): ?><input type="hidden" name="original_student_id" value="<?=h($editStudent['student_id'])?>"><?php endif; ?>
<div class="pr11-grid"><div class="pr11-field"><label for="student_id">Student ID *</label><input id="student_id" name="student_id" maxlength="20" required value="<?=h($form['student_id'])?>" placeholder="e.g. ST121"></div><div class="pr11-field"><label for="full_name">Full Name *</label><input id="full_name" name="full_name" maxlength="100" required value="<?=h($form['full_name'])?>"></div><div class="pr11-field"><label for="email">Email *</label><input id="email" name="email" type="email" maxlength="100" required value="<?=h($form['email'])?>"></div><div class="pr11-field"><label for="mobile">Mobile</label><input id="mobile" name="mobile" maxlength="15" value="<?=h($form['mobile'])?>"></div><div class="pr11-field"><label for="course">Course *</label><input id="course" name="course" maxlength="50" required value="<?=h($form['course'])?>" placeholder="e.g. B.Tech IT"></div><div class="pr11-field"><label for="year">Academic Year *</label><select id="year" name="year" required><?php for($y=1;$y<=4;$y++): ?><option value="<?=$y?>" <?=((string)$form['year']===(string)$y)?'selected':''?>>Year <?=$y?></option><?php endfor; ?></select></div><div class="pr11-field"><label for="gender">Gender</label><select id="gender" name="gender"><option value="male" <?=$form['gender']==='male'?'selected':''?>>Male</option><option value="female" <?=$form['gender']==='female'?'selected':''?>>Female</option><option value="Other" <?=$form['gender']==='Other'?'selected':''?>>Other</option></select></div></div><div class="pr11-actions"><button class="pr11-btn" type="submit"><?=$editStudent?'Save Changes':'Add Student'?></button><?php if($editStudent): ?><a class="pr11-btn secondary" href="student_management.php">Cancel Edit</a><?php endif; ?></div></form></section>
<section class="pr11-panel"><h2>Search and Filter</h2><form class="pr11-filter" method="get" action="student_management.php"><div><label for="q">Search name, email or course</label><input id="q" name="q" value="<?=h($q)?>" placeholder="Search students..."></div><div><label for="course-filter">Course</label><select id="course-filter" name="course"><option value="">All courses</option><?php foreach($courses as $course): ?><option value="<?=h($course)?>" <?=$courseFilter===$course?'selected':''?>><?=h($course)?></option><?php endforeach; ?></select></div><div><label for="year-filter">Year</label><select id="year-filter" name="year"><option value="">All years</option><?php for($y=1;$y<=4;$y++): ?><option value="<?=$y?>" <?=$yearFilter===(string)$y?'selected':''?>>Year <?=$y?></option><?php endfor; ?></select></div><button class="pr11-btn" type="submit">Search / Filter</button><a class="pr11-btn secondary" href="student_management.php">Reset</a></form></section>
<section class="pr11-panel"><div class="pr11-head" style="padding:0;border:0;box-shadow:none;margin-bottom:10px"><div><h2>Student Records</h2><p class="pr11-sub">Showing <?= $totalRecords ? $offset+1 : 0 ?>–<?=min($offset+$perPage,$totalRecords)?> of <?=$totalRecords?> matching records</p></div><span class="pr11-count">Page <?=$pageNum?> of <?=$totalPages?></span></div><div class="pr11-table-wrap"><table class="pr11-table"><thead><tr><th>Student ID</th><th>Name</th><th>Email</th><th>Mobile</th><th>Course</th><th>Year</th><th>Gender</th><th>Actions</th></tr></thead><tbody><?php if($students->num_rows===0): ?><tr><td colspan="8">No student records found.</td></tr><?php else: while($row=$students->fetch_assoc()): ?><tr><td><?=h($row['student_id'])?></td><td><?=h($row['full_name'])?></td><td><?=h($row['email'])?></td><td><?=h($row['mobile'])?></td><td><?=h($row['course'])?></td><td><?=h($row['year'])?></td><td><?=h($row['gender'])?></td><td><a class="pr11-link" href="student_management.php?edit=<?=rawurlencode($row['student_id'])?>">Edit</a> <form class="pr11-inline" method="post" action="student_management.php" onsubmit="return confirm('Are you sure you want to delete this student record?');"><input type="hidden" name="csrf_token" value="<?=h($_SESSION['csrf_token'])?>"><input type="hidden" name="action" value="delete"><input type="hidden" name="student_id" value="<?=h($row['student_id'])?>"><button class="pr11-btn danger" style="padding:6px 9px" type="submit">Delete</button></form></td></tr><?php endwhile; endif; ?></tbody></table></div><div class="pr11-pager"><span class="pr11-count">10 records per page · Search and filters stay active while paging</span><div class="pr11-actions" style="margin:0"><a class="<?=$pageNum<=1?'disabled':''?>" href="<?=h(page_url(max(1,$pageNum-1),$q,$courseFilter,$yearFilter))?>">← Previous</a><a class="<?=$pageNum>=$totalPages?'disabled':''?>" href="<?=h(page_url(min($totalPages,$pageNum+1),$q,$courseFilter,$yearFilter))?>">Next →</a></div></div></section>
<p><a href="admin_dashboard.php">← Back to Admin Dashboard</a></p></main></body></html>
