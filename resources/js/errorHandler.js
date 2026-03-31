import axios from "axios";

const reportedErrors = new Set();

window.onerror = (message, source, lineno, colno, error) => {
    const errorKey = `${message}-${source}-${lineno}-${colno}`;
    if (reportedErrors.has(errorKey)) return;
    reportedErrors.add(errorKey);

    axios.post("/report-errors", {
        error: message,
        source: source || "Unknown source",
        line: lineno || null,
        column: colno || null,
        stack: error?.stack || "No stack trace",
    });

    console.error("JS Error:", message, "at", source, ":", lineno, ":", colno);
};

window.addEventListener("unhandledrejection", (event) => {
    axios.post("/report-errors", {
        error: event.reason?.message || "Unhandled promise rejection",
        stack: event.reason?.stack || "No stack trace",
    });
});

export default {};
