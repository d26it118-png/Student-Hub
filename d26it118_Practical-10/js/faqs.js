import { fetchJSON, showState } from "./data-loader.js";

const container = document.getElementById("faqContainer");
const search = document.getElementById("faqSearch");
let records = [];

function render() {
    const query = search.value.trim().toLowerCase();
    const filtered = records.filter(faq =>
        faq.question.toLowerCase().includes(query) || faq.answer.toLowerCase().includes(query)
    );
    container.innerHTML = filtered.length ? filtered.map(faq => `
        <article class="faq-item">
            <button class="faq-question" type="button" aria-expanded="false">${faq.question}</button>
            <div class="faq-answer" hidden><p>${faq.answer}</p></div>
        </article>`).join("") : "<p class=\"empty-state\">No FAQs found.</p>";
    container.querySelectorAll(".faq-question").forEach(button => {
        button.addEventListener("click", () => {
            const answer = button.nextElementSibling;
            const open = button.getAttribute("aria-expanded") === "true";
            button.setAttribute("aria-expanded", String(!open));
            answer.hidden = open;
        });
    });
}
search.addEventListener("input", render);
async function init() {
    showState(container, "Loading FAQs...");
    try { records = await fetchJSON("../data/faqs.json"); render(); }
    catch (error) { showState(container, `Error loading FAQs: ${error.message}`, "error-state"); }
}
init();
