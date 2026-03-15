/**
 * Sistema de Modal JavaScript
 * Autor: Tu nombre
 * Fecha: 2025
 */

class ModalSystem {
    constructor() {
        this.modal = null;
        this.modalTitle = null;
        this.modalIcon = null;
        this.modalMessage = null;
        this.modalClose = null;
        this.modalOkBtn = null;
        this.callback = null;

        this.init();
    }

    init() {
        // Crear el modal si no existe
        this.createModal();

        // Obtener referencias a los elementos
        this.modal = document.getElementById("modal");
        this.modalTitle = document.getElementById("modalTitle");
        this.modalIcon = document.getElementById("modalIcon");
        this.modalMessage = document.getElementById("modalMessage");
        this.modalClose = document.getElementById("modalClose");
        this.modalOkBtn = document.getElementById("modalOkBtn");

        // Configurar event listeners
        this.setupEventListeners();
    }

    createModal() {
        // Verificar si el modal ya existe
        if (document.getElementById("modal")) {
            return;
        }

        // Crear el HTML del modal
        const modalHTML = `
            <div id="modal" class="modal-overlay">
                <div class="modal-box">
                    <div class="modal-header">
                        <h3 id="modalTitle" class="modal-title">Notificación</h3>
                        <button id="modalClose" class="modal-close">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div id="modalIcon" class="modal-icon"></div>
                        <div id="modalMessage" class="modal-message"></div>
                    </div>
                    <div class="modal-footer">
                        <button id="modalOkBtn" class="modal-btn">Aceptar</button>
                    </div>
                </div>
            </div>
        `;

        // Insertar el modal en el body
        document.body.insertAdjacentHTML("beforeend", modalHTML);

        // Crear los estilos CSS si no existen
        this.createStyles();
    }

    createStyles() {
        // Verificar si los estilos ya existen
        if (document.getElementById("modal-styles")) {
            return;
        }

        const styles = `
            <style id="modal-styles">
                .modal-overlay {
                    display: none;
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.7);
                    z-index: 1000;
                    animation: fadeIn 0.3s ease-out;
                }

                .modal-overlay.show {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                }

                .modal-box {
                    background-color: #1a1a1a;
                    border: 2px solid #1dd505;
                    border-radius: 15px;
                    padding: 30px;
                    max-width: 500px;
                    width: 90%;
                    text-align: center;
                    position: relative;
                    animation: slideIn 0.3s ease-out;
                    box-shadow: 0 10px 30px rgba(29, 213, 5, 0.3);
                }

                .modal-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 20px;
                }

                .modal-title {
                    color: #1dd505;
                    font-size: 24px;
                    font-weight: bold;
                    margin: 0;
                    font-family: 'Courier New', Courier, monospace;
                }

                .modal-close {
                    background: none;
                    border: none;
                    font-size: 30px;
                    color: #1dd505;
                    cursor: pointer;
                    line-height: 1;
                    transition: color 0.3s;
                }

                .modal-close:hover {
                    color: white;
                }

                .modal-body {
                    margin-bottom: 25px;
                }

                .modal-message {
                    color: white;
                    font-size: 18px;
                    line-height: 1.5;
                    font-family: Georgia, 'Times New Roman', Times, serif;
                }

                .modal-icon {
                    font-size: 48px;
                    margin-bottom: 15px;
                }

                .modal-icon.success {
                    color: #1dd505;
                }

                .modal-icon.error {
                    color: #ff4444;
                }

                .modal-icon.warning {
                    color: #ffaa00;
                }

                .modal-icon.info {
                    color: #00aaff;
                }

                .modal-footer {
                    display: flex;
                    justify-content: center;
                    gap: 15px;
                }

                .modal-btn {
                    padding: 12px 25px;
                    border: 2px solid #1dd505;
                    background: transparent;
                    color: #1dd505;
                    border-radius: 8px;
                    cursor: pointer;
                    font-size: 16px;
                    font-weight: bold;
                    transition: all 0.3s;
                    font-family: 'Courier New', Courier, monospace;
                }

                .modal-btn:hover {
                    background-color: #1dd505;
                    color: black;
                }

                .modal-btn.secondary {
                    border-color: #666;
                    color: #666;
                }

                .modal-btn.secondary:hover {
                    background-color: #666;
                    color: white;
                }

                @keyframes fadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }

                @keyframes slideIn {
                    from { 
                        transform: translateY(-50px);
                        opacity: 0;
                    }
                    to { 
                        transform: translateY(0);
                        opacity: 1;
                    }
                }

                body.modal-open {
                    overflow: hidden;
                }
            </style>
        `;

        // Insertar los estilos en el head
        document.head.insertAdjacentHTML("beforeend", styles);
    }

    setupEventListeners() {
        // Event listeners para cerrar el modal
        this.modalClose.addEventListener("click", () => this.close());
        this.modalOkBtn.addEventListener("click", () => this.close());

        // Cerrar modal al hacer click fuera de él
        this.modal.addEventListener("click", (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });

        // Cerrar modal con la tecla Escape
        document.addEventListener("keydown", (e) => {
            if (e.key === "Escape" && this.modal.classList.contains("show")) {
                this.close();
            }
        });
    }

    show(message, type = "info", title = "Notificación", callback = null) {
        this.modalTitle.textContent = title;
        this.modalMessage.textContent = message;

        // Configurar icono según el tipo
        this.modalIcon.className = `modal-icon ${type}`;
        switch (type) {
            case "success":
                this.modalIcon.innerHTML = "✅";
                this.modalTitle.textContent = title || "Éxito";
                break;
            case "error":
                this.modalIcon.innerHTML = "❌";
                this.modalTitle.textContent = title || "Error";
                break;
            case "warning":
                this.modalIcon.innerHTML = "⚠️";
                this.modalTitle.textContent = title || "Advertencia";
                break;
            case "info":
                this.modalIcon.innerHTML = "ℹ️";
                this.modalTitle.textContent = title || "Información";
                break;
            default:
                this.modalIcon.innerHTML = "ℹ️";
                break;
        }

        // Mostrar modal
        this.modal.classList.add("show");
        document.body.classList.add("modal-open");

        // Guardar callback si existe
        this.callback = callback;

        // Enfocar el botón OK para accesibilidad
        this.modalOkBtn.focus();
    }

    close() {
        this.modal.classList.remove("show");
        document.body.classList.remove("modal-open");

        // Ejecutar callback si existe
        if (this.callback) {
            this.callback();
            this.callback = null;
        }
    }

    // Métodos de conveniencia
    success(message, title = "Éxito", callback = null) {
        this.show(message, "success", title, callback);
    }

    error(message, title = "Error", callback = null) {
        this.show(message, "error", title, callback);
    }

    warning(message, title = "Advertencia", callback = null) {
        this.show(message, "warning", title, callback);
    }

    info(message, title = "Información", callback = null) {
        this.show(message, "info", title, callback);
    }

    // Método para confirmación (con botones Sí/No)
    confirm(
        message,
        title = "Confirmación",
        onConfirm = null,
        onCancel = null
    ) {
        this.modalTitle.textContent = title;
        this.modalMessage.textContent = message;

        // Configurar icono de confirmación
        this.modalIcon.className = "modal-icon warning";
        this.modalIcon.innerHTML = "❓";

        // Cambiar los botones del footer
        const footer = this.modal.querySelector(".modal-footer");
        footer.innerHTML = `
            <button id="modalConfirmBtn" class="modal-btn">Sí</button>
            <button id="modalCancelBtn" class="modal-btn secondary">No</button>
        `;

        // Obtener referencias a los nuevos botones
        const confirmBtn = document.getElementById("modalConfirmBtn");
        const cancelBtn = document.getElementById("modalCancelBtn");

        // Event listeners para los botones
        confirmBtn.addEventListener("click", () => {
            this.close();
            if (onConfirm) onConfirm();
        });

        cancelBtn.addEventListener("click", () => {
            this.close();
            if (onCancel) onCancel();
        });

        // Mostrar modal
        this.modal.classList.add("show");
        document.body.classList.add("modal-open");

        // Enfocar el botón de cancelar por defecto
        cancelBtn.focus();
    }
}

// Inicializar el sistema de modal automáticamente
let modalSystem = null;

// Función para inicializar el modal
function initModal() {
    if (!modalSystem) {
        modalSystem = new ModalSystem();
    }
    return modalSystem;
}

// Auto-inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
    window.modalSystem = initModal();
});

// Exportar para uso en otros scripts
if (typeof module !== "undefined" && module.exports) {
    module.exports = ModalSystem;
}
