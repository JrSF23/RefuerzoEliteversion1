<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Refuerzo Élite - Centro de Apoyo Escolar</title>
    <link rel="stylesheet" href="css/estiloFonEnd.css" />
    <!-- Utility CSS moved to Interface/CSS/estiloFonEnd.css -->
    </head>
    <body>
        <nav class="navbar">
            <div class="nav-container">
                <div class="logo" id="secretLogo">
                    <div class="logo-circle">📚</div>
                    <span class="logo-text">REFUERZO ÉLITE</span>
                </div>
                <ul class="nav-links" id="navLinks">
                    <li><a href="#inicio" onclick="closeMenu()">Inicio</a></li>
                    <li>
                        <a href="#nosotros" onclick="closeMenu()">Nosotros</a>
                    </li>
                    <li>
                        <a href="#servicios" onclick="closeMenu()">Servicios</a>
                    </li>
                    <li>
                        <a href="#metodologia" onclick="closeMenu()"
                            >Metodología</a
                        >
                    </li>
                    <li>
                        <a href="#contacto" onclick="closeMenu()">Contacto</a>
                    </li>
                </ul>
                <div class="menu-toggle" onclick="toggleMenu()">☰</div>
            </div>
        </nav>

        <section id="inicio" class="hero">
            <div class="container">
                <div class="hero-grid">
                    <div class="hero-content">
                        <h1>
                            Alcanza tu
                            <span class="highlight">Máximo Potencial</span>
                            Académico
                        </h1>
                        
                        <p>
                            Clases de refuerzo personalizadas para estudiantes
                            de primaria, secundaria y bachillerato. Educación de
                            calidad que transforma vidas.
                        </p>
                        
                        <div class="btn-group">
                            <a href="#contacto" class="btn btn-primary"
                                >Solicita Información</a
                            >
                            <a href="#servicios" class="btn btn-secondary"
                                >Ver Servicios</a
                            >
                        </div>
                    </div>
                    <div class="hero-image">
                        
                        <img
                            src="images/Logotipo1-removebg.png"
                            alt="logo"
                        />
                    </div>
                </div>
            </div>
        </section>

        <section class="carousel-section">
            <div class="carousel-container">
                <h2 class="section-title">Nuestra Comunidad</h2>
                <div class="carousel" id="carousel">
                        <div class="carousel-image active">
                        
                        <img
                            src="images/bienvenidafoto.jpg"
                            alt="Estudiantes 1"
                        />
                    </div>
                    <div class="carousel-image">
                        <img
                            src="images/téléchargement (1).jpg"
                            alt="Estudiantes 2"
                        />
                    </div>
                    <div class="carousel-image">
                        <img
                            src="images/téléchargement45 (2).jpg"
                            alt="Estudiantes 3"
                        />
                    </div>
                    <div class="carousel-image">
                                <img
                            src="images/téléchargement56.jpg"
                            alt="Estudiantes 4"
                        />
                    </div>
                    <button class="carousel-btn prev" onclick="prevSlide()">
                        ‹
                    </button>
                    <button class="carousel-btn next" onclick="nextSlide()">
                        ›
                    </button>
                </div>
                <div class="carousel-dots" id="carouselDots"></div>
            </div>
        </section>

        <section id="nosotros" class="about-section">
            <div class="container">
                <div class="about-grid">
                    <div>
                        <div class="badge-inline">
                            <span class="badge-text">Sobre Nosotros</span>
                        </div>
                        <h2 class="large-title">
                            Comprometidos con tu
                            <span class="block-highlight">Éxito Académico</span>
                        </h2>
                        <div class="mission-box">
                            <h3>🎯 Nuestra Misión</h3>
                            <!--
                            <p>
                                Empoderar a los estudiantes a alcanzar su máximo
                                potencial académico mediante la oferta de clases
                                de refuerzo adaptadas a sus necesidades
                                individuales, brindando un ambiente de
                                aprendizaje estimulante y profesional.
                            </p>
                            -->
                        </div>
                        <div class="mission-box">
                            <h3>🏆 Nuestra Visión</h3>
                            <!--
                            <p>
                                Ser reconocidos como la agencia de referencia en
                                clases de refuerzo en la región, ofreciendo un
                                servicio de alta calidad que garantice el éxito
                                académico y personal de nuestros estudiantes.
                            </p>
                            -->
                        </div>
                    </div>
                    <div class="features-grid">
                        <div class="feature-card">
                            <div class="feature-icon">👥</div>
                            <h4>Equipo Experto</h4>
                            <p>Profesionales altamente capacitados</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">🎯</div>
                            <h4>Enfoque Personal</h4>
                            <p>Adaptado a cada estudiante</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">📚</div>
                            <h4>Metodología Activa</h4>
                            <p>Aprendizaje dinámico</p>
                        </div>
                        <div class="feature-card">
                            <div class="feature-icon">🏆</div>
                            <h4>Resultados Garantizados</h4>
                            <p>Mejora comprobada</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="servicios" class="services-section">
            <div class="container">
                <h2 class="section-title">Nuestros Servicios</h2>
                <p class="centered-light">
                    <!-- Ofrecemos una gama completa de servicios educativos
                    diseñados para impulsar el rendimiento académico de nuestros
                    estudiantes. -->
                </p>
                <div class="services-grid">
                    <div class="service-card">
                        <div class="service-icon">📚</div>
                        <h3>Clases Personalizadas</h3>
                        <p>
                            Apoyo en matemáticas, lengua, ciencias, inglés y más
                            asignaturas.
                        </p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">🎯</div>
                        <h3>Talleres de Estudio</h3>
                        <p>
                            Técnicas de estudio, gestión del tiempo y
                            preparación para exámenes.
                        </p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">👨‍🏫</div>
                        <h3>Tutorías Especializadas</h3>
                        <p>
                            Asesoramiento académico según necesidades
                            específicas.
                        </p>
                    </div>
                    <div class="service-card">
                        <div class="service-icon">📊</div>
                        <h3>Seguimiento Continuo</h3>
                        <p>
                            Evaluaciones periódicas y ajuste de planes de
                            estudio.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <section id="metodologia" class="methodology-section">
            <div class="container">
                <h2 class="section-title">Nuestra Metodología</h2>
                <p class="centered-light">
                    Un enfoque probado que combina innovación, personalización y
                    tecnología
                </p>
                <div class="methodology-grid">
                    <div class="method-card">
                        <div class="method-icon">🎮</div>
                        <h3>Aprendizaje Activo</h3>
                        <!--
                        <p>
                            Fomentamos la participación activa de los
                            estudiantes a través de actividades prácticas y
                            dinámicas que estimulan el pensamiento crítico.
                        </p>
                        -->
                    </div>
                    <div class="method-card">
                        <div class="method-icon">🎨</div>
                        <h3>Adaptación Curricular</h3>
                        <!--
                        <p>
                            Los contenidos se adaptan al curriculum escolar y a
                            las áreas donde el estudiante necesita más atención
                            personalizada.
                        </p>
                        -->
                    </div>
                    <div class="method-card">
                        <div class="method-icon">💻</div>
                        <h3>Uso de Tecnología</h3>
                        <!--
                        <p>
                            Incorporamos herramientas digitales y recursos en
                            línea modernos para complementar y enriquecer el
                            aprendizaje.
                        </p>
                        -->
                    </div>
                </div>
            </div>
        </section>

        <section id="contacto" class="contact-section">
            <div class="container">
                <h2 class="section-title">Contáctanos</h2>
                <p class="centered-light">
                    ¿Listo para mejorar tu rendimiento académico? ¡Escríbenos!
                </p>
                <div class="contact-grid">
                    <div class="contact-info">
                        <div class="contact-card">
                            <div class="contact-icon">📞</div>
                            <div>
                                <h3>Teléfono</h3>
                                <p>+240 222 862 579</p>
                            </div>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">✉️</div>
                            <div>
                                <h3>Email</h3>
                                <p>refuerzoelite@gmail.com</p>
                            </div>
                        </div>
                        <div class="contact-card">
                            <div class="contact-icon">📍</div>
                            <div>
                                <h3>Dirección</h3>
                                <p>B/ Sumco (Ela-Nguema)</p>
                            </div>
                        </div>
                        <a
                            href="https://wa.me/240222862579"
                            class="whatsapp-btn"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <span>💬</span>
                            <span>Chatea por WhatsApp</span>
                        </a>
                    </div>
                    <div>
                        <div class="contact-form">
                            <div class="form-group">
                                <input
                                    type="text"
                                    id="nombre"
                                    placeholder="Nombre completo"
                                    required
                                />
                            </div>
                            <div class="form-group">
                                <input
                                    type="email"
                                    id="email"
                                    placeholder="Correo electrónico"
                                    required
                                />
                            </div>
                            <div class="form-group">
                                <input
                                    type="tel"
                                    id="telefono"
                                    placeholder="Teléfono"
                                    required
                                />
                            </div>
                            <div class="form-group">
                                <textarea
                                    id="mensaje"
                                    placeholder="¿En qué podemos ayudarte?"
                                    required
                                ></textarea>
                            </div>
                            <button
                                class="btn btn-primary flex-center"
                                onclick="enviarFormulario()"
                            >
                                <span>✉️</span>
                                <span>Enviar Mensaje</span>
                            </button>
                            <div class="success-message" id="successMessage">
                                ¡Mensaje enviado! Te contactaremos pronto.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <footer>
            <div class="footer-logo">
                <div class="logo-circle logo-circle-small">📚</div>
                <span class="logo-text">REFUERZO ÉLITE</span>
            </div>
            <p class="muted-note">Comprometidos con tu éxito académico</p>
            <p class="copyright-small">
                © 2025 Refuerzo Élite. Todos los derechos reservados.
            </p>
        </footer>

    <script src="js/appFronEnd.js"></script>
    </body>
</html>
