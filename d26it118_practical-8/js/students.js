import { fetchJSON, showState } from "./data-loader.js";

const state = { records: [], page: 1, perPage: 6 };
const container = document.getElementById("studentContainer");
const search = document.getElementById("studentSearch");
const year = document.getElementById("studentYear");
const sort = document.getElementById("studentSort");
const pageInfo = document.getElementById("studentPageInfo");
const prev = document.getElementById("studentPrev");
const next = document.getElementById("studentNext");

function getVisibleStudents() {
    const query = search.value.trim().toLowerCase();
    let result = state.records.filter(student =>
        [student.id, student.name, student.course, student.city, student.email]
            .some(value => String(value).toLowerCase().includes(query))
    );
    if (year.value !== "all") result = result.filter(student => String(student.year) === year.value);
    if (sort.value === "az") result.sort((a, b) => a.name.localeCompare(b.name));
    if (sort.value === "za") result.sort((a, b) => b.name.localeCompare(a.name));
    if (sort.value === "year") result.sort((a, b) => a.year - b.year);
    return result;
}

function render() {
    const records = getVisibleStudents();
    const totalPages = Math.max(1, Math.ceil(records.length / state.perPage));
    state.page = Math.min(state.page, totalPages);
    const pageRecords = records.slice((state.page - 1) * state.perPage, state.page * state.perPage);
    container.innerHTML = pageRecords.length ? pageRecords.map(student => `
        <article class="student-card">
            <h3>${student.name}</h3>
            <p><strong>ID:</strong> ${student.id}</p>
            <p><strong>Course:</strong> ${student.course}</p>
            <p><strong>Year:</strong> ${student.year}</p>
            <p><strong>Email:</strong> ${student.email}</p>
            <p><strong>City:</strong> ${student.city}</p>
        </article>`).join("") : "<p class=\"empty-state\">No students found.</p>";
    pageInfo.textContent = `Page ${state.page} of ${totalPages} • ${records.length} result(s)`;
    prev.disabled = state.page === 1; next.disabled = state.page === totalPages;
}
function reset() { state.page = 1; render(); }
search.addEventListener("input", reset); year.addEventListener("change", reset); sort.addEventListener("change", reset);
prev.addEventListener("click", () => { if (state.page > 1) { state.page--; render(); } });
next.addEventListener("click", () => { state.page++; render(); });
async function init() {
    showState(container, "Loading students...");
    try { state.records = await fetchJSON("../data/students.json"); render(); }
    catch (error) { showState(container, `Error loading students: ${error.message}`, "error-state"); }
}
init();
