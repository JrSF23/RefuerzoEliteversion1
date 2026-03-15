// Esperar a que el DOM esté completamente cargado
document.addEventListener("DOMContentLoaded", function () {
    // Obtener referencias a los elementos
    const menuBtn = document.getElementById("menuBtn");
    const dropdownMenu = document.getElementById("dropdownMenuHidden");

    // Verificar que los elementos existen
    if (!menuBtn || !dropdownMenu) {
        console.error("No se encontraron los elementos del menú");
        return;
    }

    // Función para alternar el dropdown
    function toggleDropdown() {
        dropdownMenu.classList.toggle("hidden");
    }

    // Función para cerrar el dropdown
    function closeDropdown() {
        dropdownMenu.classList.add("hidden");
    }

    // Evento click en el botón del menú
    menuBtn.addEventListener("click", function (e) {
        e.stopPropagation(); // Prevenir que el evento se propague
        toggleDropdown();
    });

    // Cerrar el dropdown cuando se haga click fuera de él
    document.addEventListener("click", function (e) {
        // Si el click no es en el botón del menú ni en el dropdown, cerrar
        if (!menuBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
            closeDropdown();
        }
    });

    // Opcional: Cerrar el dropdown cuando se presiona la tecla Escape
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeDropdown();
        }
    });

    // Opcional: Cerrar el dropdown cuando se hace click en un enlace del menú
    const dropdownLinks = dropdownMenu.querySelectorAll("a");
    dropdownLinks.forEach((link) => {
        link.addEventListener("click", function () {
            closeDropdown();
        });
    });
});
