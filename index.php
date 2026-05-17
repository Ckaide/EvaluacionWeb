<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Landing Tienda Celulares</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUa6mY1hW1y+I1iCHU3O3+f6Y6F5i3MsZB1dUj69Wb6e0DKeXy2Z6VYg4m1c" crossorigin="anonymous">
  <link rel="stylesheet" href="css/landing.css">
</head>
<body>
  <header class="landing-header py-3">
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-2">
        <div class="brand-icon">📱</div>
        <div>
          <h1 class="h4 mb-0 brand-title">Tienda Celulares</h1>
          <p class="mb-0 text-muted">Los mejores equipos al mejor precio.</p>
        </div>
      </div>
      <nav>
        <a href="auth/registro.php" class="btn btn-outline-light btn-sm me-2">Crear cuenta</a>
        <a href="auth/login.php" class="btn btn-light btn-sm">Entrar</a>
      </nav>
    </div>
  </header>

  <main>
    <section class="hero-section py-5 text-white text-center">
      <div class="container">
        <span class="eyebrow">Lanzamiento 2026</span>
        <h2 class="display-5 fw-bold mt-3">Tu próxima experiencia móvil comienza aquí</h2>
        <p class="lead mb-4 text-white-75">Explora los últimos modelos de celulares con envío rápido, soporte seguro y pagos fáciles.</p>
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
          <a href="auth/login.php" class="btn btn-cta-primary btn-lg px-4">Iniciar sesión</a>
          <a href="auth/registro.php" class="btn btn-cta-secondary btn-lg px-4">Crear cuenta</a>
        </div>
      </div>
    </section>

    <section class="features py-5">
      <div class="container">
        <div class="row g-4 text-center">
          <div class="col-md-4">
            <div class="feature-card p-4 h-100">
              <h3 class="h5">Envío rápido</h3>
              <p>Entrega segura en tiempo récord para que tu dispositivo llegue cuanto antes.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-card p-4 h-100">
              <h3 class="h5">Garantía confiable</h3>
              <p>Todos nuestros celulares cuentan con garantía y servicio postventa.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="feature-card p-4 h-100">
              <h3 class="h5">Soporte 24/7</h3>
              <p>Resolvemos tus dudas rápidamente con asistencia disponible siempre.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="highlight py-5 text-white">
      <div class="container">
        <div class="row align-items-center g-4">
          <div class="col-lg-6">
            <h3>Diseño moderno, rendimiento superior</h3>
            <p class="mb-4">Nuestra selección incluye todos los modelos populares, desde gama alta hasta opciones económicas, con las mejores prestaciones y batería duradera.</p>
            <a href="auth/login.php" class="btn btn-cta-secondary btn-lg">Iniciar sesión</a>
          </div>
          <div class="col-lg-6">
            <div class="info-box p-4">
              <h4>Oferta exclusiva</h4>
              <p>Regístrate hoy y recibe un descuento especial en tu primera compra.</p>
              <ul class="list-unstyled mb-0">
                <li>✔ Envío gratis</li>
                <li>✔ Financiamiento disponible</li>
                <li>✔ Productos certificados</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="testimonials py-5">
      <div class="container text-center">
        <div class="mb-4">
          <span class="eyebrow">Clientes felices</span>
          <h3 class="mt-2">Confían en nosotros</h3>
        </div>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="testimonial-card p-4 h-100">
              <p>La mejor atención y entrega muy rápida. Compré un celular y llegó en 24 horas.</p>
              <strong>— Ana R.</strong>
            </div>
          </div>
          <div class="col-md-4">
            <div class="testimonial-card p-4 h-100">
              <p>Excelente precio y calidad. La tienda me ayudó a elegir el modelo perfecto.</p>
              <strong>— Carlos M.</strong>
            </div>
          </div>
          <div class="col-md-4">
            <div class="testimonial-card p-4 h-100">
              <p>Compré en línea y todo fue sencillo. Muy recomendado para comprar celulares.</p>
              <strong>— Laura G.</strong>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="footer-section py-4 text-center text-white">
    <div class="container">
      <p class="mb-1">© 2026 Tienda Celulares</p>
      <small>Confianza, calidad y mejor servicio en cada compra.</small>
    </div>
  </footer>
</body>
</html>
