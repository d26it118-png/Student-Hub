<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>StudentHub | Practical 13 - AJAX Event CRUD</title>
<link rel="stylesheet" href="../css/style.css">
<style>
.api-wrap{max-width:1100px;margin:28px auto;padding:20px}
.api-card{background:#fff;border:1px solid #ddd;border-radius:12px;padding:20px;margin:16px 0}
.api-form{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px}
.api-form label{display:block;font-weight:600;margin-bottom:5px}
.api-form input,.api-form select{width:100%;padding:10px;border:1px solid #bbb;border-radius:6px;box-sizing:border-box}
.api-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:14px}
.api-actions button,.table-action{padding:9px 13px;border:0;border-radius:6px;cursor:pointer}
#saveBtn{background:#1457a6;color:white}.secondary{background:#e7e7e7}
.table-scroll{overflow-x:auto}table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #ddd;text-align:left}
#notice{padding:12px;border-radius:6px;margin:12px 0;display:none}#notice.success{display:block;background:#e4f6e9;color:#155724}#notice.error{display:block;background:#fde8e8;color:#8a1c1c}#notice.info{display:block;background:#e7f1ff;color:#164a7c}
button:disabled{opacity:.55;cursor:wait}.small-btn{margin:2px}
</style>
</head>
<body>
<nav><a href="index.html">Home</a> <a href="events.php">Events (normal)</a> <a href="events_ajax.php">Practical 13 AJAX CRUD</a></nav>
<main class="api-wrap">
<h1>Practical 13: Events JSON API + Fetch CRUD</h1>
<p>Manage events without reloading the page. Requests and responses use JSON.</p>
<div id="notice" role="status" aria-live="polite"></div>

<section class="api-card">
<h2 id="formHeading">Add Event</h2>
<form id="eventForm">
<input type="hidden" id="event_id">
<div class="api-form">
<div><label for="title">Event title *</label><input id="title" maxlength="150" required></div>
<div><label for="category">Category *</label><input id="category" maxlength="50" required placeholder="Technical, Workshop..."></div>
<div><label for="event_date">Event date *</label><input type="date" id="event_date" required></div>
<div><label for="venue">Venue *</label><input id="venue" maxlength="150" required></div>
<div><label for="organizer">Organizer *</label><input id="organizer" maxlength="100" required></div>
<div><label for="status">Status *</label><select id="status" required><option>Upcoming</option><option>Completed</option><option>Cancelled</option></select></div>
</div>
<div class="api-actions">
<button id="saveBtn" type="submit">Add Event</button>
<button id="cancelBtn" class="secondary" type="button" hidden>Cancel Edit</button>
<button id="refreshBtn" class="secondary" type="button">Refresh List</button>
</div>
</form>
</section>

<section class="api-card">
<h2>Events</h2>
<p id="loading" hidden>Loading events…</p>
<div class="table-scroll"><table>
<thead><tr><th>ID</th><th>Title</th><th>Category</th><th>Date</th><th>Venue</th><th>Organizer</th><th>Status</th><th>Actions</th></tr></thead>
<tbody id="eventRows"><tr><td colspan="8">Events will appear here.</td></tr></tbody>
</table></div>
</section>
<p><strong>API endpoint:</strong> <code>../api/events.php</code></p>
</main>
<script>
const API = "../api/events.php";
const form = document.getElementById("eventForm");
const rows = document.getElementById("eventRows");
const notice = document.getElementById("notice");
const loading = document.getElementById("loading");
const saveBtn = document.getElementById("saveBtn");
let eventsCache = [];

function showNotice(message, type="info") {
  notice.textContent = message;
  notice.className = type;
}
function setBusy(busy) {
  loading.hidden = !busy;
  saveBtn.disabled = busy;
  document.getElementById("refreshBtn").disabled = busy;
}
async function requestJSON(url, options={}) {
  const response = await fetch(url, options);
  let result;
  try { result = await response.json(); }
  catch { throw new Error("Server returned invalid JSON. Check PHP errors and the API URL."); }
  if (!response.ok || result.success !== true) {
    throw new Error(result.message || `Request failed (${response.status}).`);
  }
  return result;
}
function escapeHTML(value) {
  return String(value ?? "").replace(/[&<>"']/g, ch => ({
    "&":"&amp;", "<":"&lt;", ">":"&gt;", '"':"&quot;", "'":"&#039;"
  })[ch]);
}
async function loadEvents() {
  setBusy(true);
  try {
    const result = await requestJSON(API);
    eventsCache = result.data;
    if (!eventsCache.length) {
      rows.innerHTML = '<tr><td colspan="8">No events found. Add the first event above.</td></tr>';
    } else {
      rows.innerHTML = eventsCache.map(event => `<tr>
        <td>${escapeHTML(event.event_id)}</td><td>${escapeHTML(event.title)}</td>
        <td>${escapeHTML(event.category)}</td><td>${escapeHTML(event.event_date)}</td>
        <td>${escapeHTML(event.venue)}</td><td>${escapeHTML(event.organizer)}</td>
        <td>${escapeHTML(event.status || "Upcoming")}</td>
        <td><button type="button" class="small-btn" data-edit="${escapeHTML(event.event_id)}">Edit</button>
        <button type="button" class="small-btn" data-delete="${escapeHTML(event.event_id)}">Delete</button></td>
      </tr>`).join("");
    }
    showNotice("Events loaded successfully.", "success");
  } catch (error) {
    rows.innerHTML = '<tr><td colspan="8">Unable to load events.</td></tr>';
    showNotice(error.message, "error");
  } finally { setBusy(false); }
}
function resetForm() {
  form.reset();
  document.getElementById("event_id").value = "";
  document.getElementById("formHeading").textContent = "Add Event";
  saveBtn.textContent = "Add Event";
  document.getElementById("cancelBtn").hidden = true;
}
form.addEventListener("submit", async event => {
  event.preventDefault();
  if (!form.reportValidity()) return;
  const id = document.getElementById("event_id").value;
  const payload = {
    action: id ? "update" : "add",
    ...(id ? {event_id: Number(id)} : {}),
    title: document.getElementById("title").value.trim(),
    category: document.getElementById("category").value.trim(),
    event_date: document.getElementById("event_date").value,
    venue: document.getElementById("venue").value.trim(),
    organizer: document.getElementById("organizer").value.trim(),
    status: document.getElementById("status").value
  };
  saveBtn.disabled = true;
  try {
    const result = await requestJSON(API, {
      method: "POST",
      headers: {"Content-Type":"application/json", "Accept":"application/json"},
      body: JSON.stringify(payload)
    });
    showNotice(result.message, "success");
    resetForm();
    await loadEvents();
  } catch (error) { showNotice(error.message, "error"); }
  finally { saveBtn.disabled = false; }
});
rows.addEventListener("click", async event => {
  const editButton = event.target.closest("[data-edit]");
  const deleteButton = event.target.closest("[data-delete]");
  if (editButton) {
    const item = eventsCache.find(e => String(e.event_id) === editButton.dataset.edit);
    if (!item) return showNotice("Event no longer exists. Refresh the list.", "error");
    document.getElementById("event_id").value = item.event_id;
    for (const field of ["title","category","event_date","venue","organizer","status"]) {
      document.getElementById(field).value = item[field] || (field === "status" ? "Upcoming" : "");
    }
    document.getElementById("formHeading").textContent = "Update Event #" + item.event_id;
    saveBtn.textContent = "Save Changes";
    document.getElementById("cancelBtn").hidden = false;
    document.getElementById("title").focus();
  }
  if (deleteButton) {
    const id = Number(deleteButton.dataset.delete);
    if (!confirm("Delete this event? This action cannot be undone.")) return;
    deleteButton.disabled = true;
    try {
      const result = await requestJSON(API, {
        method:"POST", headers:{"Content-Type":"application/json","Accept":"application/json"},
        body:JSON.stringify({action:"delete", event_id:id})
      });
      showNotice(result.message, "success");
      await loadEvents();
    } catch (error) {
      showNotice(error.message, "error");
      deleteButton.disabled = false;
    }
  }
});
document.getElementById("cancelBtn").addEventListener("click", resetForm);
document.getElementById("refreshBtn").addEventListener("click", loadEvents);
loadEvents();
</script>
</body>
</html>
