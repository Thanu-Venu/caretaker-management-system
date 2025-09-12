document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll(".btn-call").forEach(btn => {
        btn.addEventListener("click", () => {
            alert("Dialing the caretaker...");
        });
    });

    document.querySelectorAll(".btn-msg").forEach(btn => {
        btn.addEventListener("click", () => {
            alert("Opening chat window...");
        });
    });
});
