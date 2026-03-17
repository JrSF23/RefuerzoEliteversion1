// Wrap initialization to run after DOM is ready. This prevents errors in test environments
// (like JSDOM) where script may execute before elements exist.
document.addEventListener("DOMContentLoaded", () => {
    let currentSlide = 0;
    let secretClicks = 0;
    let autoSlideInterval = null;

    const slides = document.querySelectorAll(".carousel-image");
    const dotsContainer = document.getElementById("carouselDots");
    const carousel = document.getElementById("carousel");

    // Acceso secreto al panel de administración (solo si existe el elemento)
    const secretLogo = document.getElementById("secretLogo");
    if (secretLogo) {
        secretLogo.addEventListener("click", () => {
            secretClicks++;
            if (secretClicks === 7) {
                // abrir en nueva pestaña
                window.open(
                    "http://www.localhost/Refuerzo_Elite-Project/admin/formularioLog.php",
                    "_blank"
                );
                secretClicks = 0;
            }
            // Reset después de 3 segundos de inactividad
            setTimeout(() => {
                secretClicks = 0;
            }, 3000);
        });
    }

    // Crear dots si hay slides y contenedor
    if (slides.length > 0 && dotsContainer) {
        slides.forEach((_, index) => {
            const dot = document.createElement("div");
            dot.className = "dot";
            if (index === 0) dot.classList.add("active");
            dot.addEventListener("click", () => {
                goToSlide(index);
                resetAutoSlide(); // Reiniciar el temporizador al hacer clic
            });
            dotsContainer.appendChild(dot);
        });
    }

    // Funciones del carrusel
    function goToSlide(index) {
        if (!slides.length) return;
        slides.forEach((s, i) => {
            s.classList.toggle("active", i === index);
        });
        if (dotsContainer) {
            const dots = dotsContainer.querySelectorAll(".dot");
            dots.forEach((d, i) => d.classList.toggle("active", i === index));
        }
        currentSlide = index;
    }

    function prevSlide() {
        if (!slides.length) return;
        const next = (currentSlide - 1 + slides.length) % slides.length;
        goToSlide(next);
        resetAutoSlide(); // Reiniciar el temporizador al navegar manualmente
    }

    function nextSlide() {
        if (!slides.length) return;
        const next = (currentSlide + 1) % slides.length;
        goToSlide(next);
    }

    // Exponer funciones globalmente para compatibilidad con atributos onclick inline
    window.goToSlide = goToSlide;
    window.prevSlide = prevSlide;
    window.nextSlide = nextSlide;

    // Función para avanzar automáticamente
    function autoSlide() {
        nextSlide();
    }

    // Función para iniciar el deslizamiento automático (hacer idempotente)
    function startAutoSlide() {
        if (autoSlideInterval) return;
        autoSlideInterval = setInterval(autoSlide, 3000); // Cambiar cada 3 segundos
    }

    // Función para reiniciar el temporizador automático
    function resetAutoSlide() {
        if (autoSlideInterval) {
            clearInterval(autoSlideInterval);
            autoSlideInterval = null;
        }
        startAutoSlide();
    }

    // Pausar el carrusel automático cuando el mouse está sobre él
    if (carousel) {
        carousel.addEventListener("mouseenter", () => {
            if (autoSlideInterval) {
                clearInterval(autoSlideInterval);
                autoSlideInterval = null;
            }
        });

        carousel.addEventListener("mouseleave", () => {
            startAutoSlide();
        });
    }

    // Botones prev/next (si existen) — preferimos listeners en lugar de atributos inline, pero
    // mantenemos compatibilidad exponiendo las funciones en window.
    const prevBtn = document.querySelector(".carousel-btn.prev");
    const nextBtn = document.querySelector(".carousel-btn.next");
    if (prevBtn)
        prevBtn.addEventListener("click", () => {
            prevSlide();
        });
    if (nextBtn)
        nextBtn.addEventListener("click", () => {
            nextSlide();
        });

    // Iniciar el carrusel automático al cargar la página
    startAutoSlide();

    // Funciones para el menú móvil (expuestas globalmente porque el HTML usa onclick)
    function toggleMenu() {
        const navLinks = document.getElementById("navLinks");
        if (navLinks) navLinks.classList.toggle("active");
    }

    function closeMenu() {
        const navLinks = document.getElementById("navLinks");
        if (navLinks) navLinks.classList.remove("active");
    }

    window.toggleMenu = toggleMenu;
    window.closeMenu = closeMenu;

    // Función para enviar el formulario
    function enviarFormulario() {
        const nombreEl = document.getElementById("nombre");
        const emailEl = document.getElementById("email");
        const telefonoEl = document.getElementById("telefono");
        const mensajeEl = document.getElementById("mensaje");

        const nombre = nombreEl ? nombreEl.value : "";
        const email = emailEl ? emailEl.value : "";
        const telefono = telefonoEl ? telefonoEl.value : "";
        const mensaje = mensajeEl ? mensajeEl.value : "";

        if (nombre && email && telefono && mensaje) {
            const successMessage = document.getElementById("successMessage");
            if (successMessage) successMessage.classList.add("show");

            // Limpiar formulario
            if (nombreEl) nombreEl.value = "";
            if (emailEl) emailEl.value = "";
            if (telefonoEl) telefonoEl.value = "";
            if (mensajeEl) mensajeEl.value = "";

            // Ocultar mensaje después de 5 segundos
            setTimeout(() => {
                if (successMessage) successMessage.classList.remove("show");
            }, 5000);
        }
    }

    window.enviarFormulario = enviarFormulario;
});
