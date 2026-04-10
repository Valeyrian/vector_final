/**
 * VECTOR — Theme Toggle (Dark/Light)
 */
(function () {
    "use strict";

    const STORAGE_KEY = "vector-theme";
    const DARK = "dark";
    const LIGHT = "light";

    // Applique le thème immédiatement (avant rendu pour éviter le flash)
    const saved = localStorage.getItem(STORAGE_KEY) || LIGHT;
    document.documentElement.setAttribute("data-theme", saved);

    document.addEventListener("DOMContentLoaded", function () {
        const toggle = document.getElementById("themeToggle");
        const icon = document.querySelector(".theme-icon");

        function applyTheme(theme) {
            document.documentElement.setAttribute("data-theme", theme);
            localStorage.setItem(STORAGE_KEY, theme);
            if (icon) {
                icon.textContent = theme === DARK ? "☀️" : "🌙";
            }
        }

        // Init
        applyTheme(saved);

        if (toggle) {
            toggle.addEventListener("click", function () {
                const current =
                    document.documentElement.getAttribute("data-theme");
                applyTheme(current === DARK ? LIGHT : DARK);
            });
        }
    });
})();
