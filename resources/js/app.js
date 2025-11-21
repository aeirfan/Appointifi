import "./bootstrap";

// Configure Alpine before Livewire loads it
document.addEventListener("alpine:init", () => {
    // Livewire 3 already includes persist plugin, no need to import/register it

    Alpine.store("sidebar", {
        collapsed: Alpine.$persist(false).as("sidebar-collapsed"),
        toggle() {
            this.collapsed = !this.collapsed;
        },
    });

    Alpine.store(
        "darkMode",
        Alpine.$persist(
            localStorage.getItem("darkMode") === "true" ||
                document.documentElement.classList.contains("dark")
        ).as("dark-mode")
    );
});
