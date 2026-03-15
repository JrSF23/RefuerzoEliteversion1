// Scroll Progress Bar
function updateScrollProgress() {
    const scrollProgress = document.getElementById("scrollProgress");
    const scrollTop = window.pageYOffset;
    const docHeight =
        document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = (scrollTop / docHeight) * 100;
    scrollProgress.style.width = scrollPercent + "%";
}

// Smooth Scrolling for Navigation
function initSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener("click", function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute("href"));
            if (target) {
                target.scrollIntoView({
                    behavior: "smooth",
                    block: "start",
                });
            }
        });
    });
}

// Intersection Observer for Animations
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("visible");
            }
        });
    }, observerOptions);

    document.querySelectorAll(".fade-in").forEach((el) => {
        observer.observe(el);
    });
}

// Mobile Menu Toggle
function initMobileMenu() {
    const mobileToggle = document.getElementById("mobileToggle");
    const navLinks = document.getElementById("navLinks");
    mobileToggle.addEventListener("click", () => {
        navLinks.classList.toggle("active");
    });

    // Close menu when clicking a link
    document.querySelectorAll(".nav-link").forEach((link) => {
        link.addEventListener("click", () => {
            navLinks.classList.remove("active");
        });
    });
}

// Floating Action Button (Scroll to Top)
function initScrollToTop() {
    const scrollToTop = document.getElementById("scrollToTop");
    scrollToTop.addEventListener("click", () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    });
}

// Toast Notification
function showToast(message) {
    const toast = document.getElementById("toast");
    const toastMessage = document.getElementById("toastMessage");
    toastMessage.textContent = message;
    toast.classList.add("show");
    setTimeout(() => {
        toast.classList.remove("show");
    }, 3000);
}

// Contact Form Submission (Demo)
function initContactForm() {
    const contactForm = document.getElementById("contactForm");
    if (contactForm) {
        contactForm.addEventListener("submit", function (e) {
            e.preventDefault();
            showToast("¡Mensaje enviado correctamente!");
            contactForm.reset();
        });
    }
}

// Stats Counter Animation
function initStatsCounter() {
    const counters = document.querySelectorAll(".stat-number");
    counters.forEach((counter) => {
        const updateCount = () => {
            const target = +counter.getAttribute("data-target");
            const count = +counter.innerText;
            const increment = Math.ceil(target / 100);

            if (count < target) {
                counter.innerText = count + increment;
                setTimeout(updateCount, 30);
            } else {
                counter.innerText = target;
            }
        };
        updateCount();
    });
}

// Init all
window.addEventListener("scroll", updateScrollProgress);
document.addEventListener("DOMContentLoaded", () => {
    updateScrollProgress();
    initSmoothScrolling();
    initScrollAnimations();
    initMobileMenu();
    initScrollToTop();
    initContactForm();
    initStatsCounter();
});
