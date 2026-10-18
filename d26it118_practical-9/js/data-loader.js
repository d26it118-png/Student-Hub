export async function fetchJSON(url) {
    const response = await fetch(url);
    if (!response.ok) {
        throw new Error(`Unable to load ${url} (HTTP ${response.status})`);
    }
    return response.json();
}

export function showState(element, message, className = "loading-state") {
    element.className = className;
    element.textContent = message;
}
