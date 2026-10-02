import { fetchJSON, showState } from "./data-loader.js";

const state = { records: [], page: 1, perPage: 6 };
const container = document.getElementById("eventContainer");
const search = document.getElementById("eventSearch");
const category = document.getElementById("eventCategory");
const sort = document.getElementById("eventSort");
const pageInfo = document.getElementById("eventPageInfo");
const prev = document.getElementById("eventPrev");
const next = document.getElementById("eventNext");

function getVisibleEvents() {
    const query = search.value.trim().toLowerCase();
    let result = state.records.filter(event =>
        [event.title, event.category, event.venue, event.organizer]
            .some(value => value.toLowerCase().includes(query))
    );

    if (category.value !== "all") {
        result = result.filter(event => event.category === category.value);
    }

    if (sort.value === "az") result.sort((a, b) => a.title.localeCompare(b.title));
    if (sort.value === "za") result.sort((a, b) => b.title.localeCompare(a.title));
    if (sort.value === "date") result.sort((a, b) => new Date(a.date) - new Date(b.date));
    return result;
}

function render() {
    const records = getVisibleEvents();
    const totalPages = Math.max(1, Math.ceil(records.length / state.perPage));
    state.page = Math.min(state.page, totalPages);
    const start = (state.page - 1) * state.perPage;
    const pageRecords = records.slice(start, start + state.perPage);

    if (!pageRecords.length) {
        container.innerHTML = "<p class=\"empty-state\">No events found.</p>";
    } else {
        container.innerHTML = pageRecords.map(event => `
            <article class="event-card">
                <h3>${event.title}</h3>
                <p><strong>Category:</strong> ${event.category}</p>
                <p><strong>Date:</strong> ${event.date}</p>
                <p><strong>Venue:</strong> ${event.venue}</p>
                <p><strong>Organizer:</strong> ${event.organizer}</p>
            </article>`).join("");
    }

    pageInfo.textContent = `Page ${state.page} of ${totalPages} • ${records.length} result(s)`;
    prev.disabled = state.page === 1;
    next.disabled = state.page === totalPages;
}

function resetAndRender() { state.page = 1; render(); }
search.addEventListener("input", resetAndRender);
category.addEventListener("change", resetAndRender);
sort.addEventListener("change", resetAndRender);
prev.addEventListener("click", () => { if (state.page > 1) { state.page--; render(); } });
next.addEventListener("click", () => { state.page++; render(); });

async function init() {
    showState(container, "Loading events...");
    try {
        state.records = await fetchJSON("../data/events.json");
        render();
    } catch (error) {
        showState(container, `Error loading events: ${error.message}`, "error-state");
    }
}
init();
