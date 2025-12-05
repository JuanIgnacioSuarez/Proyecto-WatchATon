<?php
require_once('../../backend/php/verificar_sesion_admin.php');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Panel de Administración - WatchATon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="../css/misestilos.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  
  <!-- Firebase -->
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-app.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-analytics.js"></script>
  <script src="https://www.gstatic.com/firebasejs/8.2.4/firebase-auth.js"></script>
  <script src="../js/firebase-config.js"></script>

</head>
<body class="admin-body">
  
  <div class="admin-wrapper">
      <!-- Sidebar -->
      <aside class="admin-sidebar">
          <div class="d-flex align-items-center mb-5">
              <img src="../assets/images/logo.jpg" alt="Logo" width="40" height="40" class="rounded-circle me-3">
              <div>
                  <h5 class="text-white fw-bold mb-0">Admin Panel</h5>
                  <small class="text-white-50">WatchATon</small>
              </div>
          </div>
          
          <nav class="nav flex-column flex-grow-1">
              <a href="#" class="nav-link-admin active" data-section="dashboard">
                  <i class="bi bi-speedometer2 me-3 fs-5"></i>Dashboard
              </a>
              <a href="#" class="nav-link-admin" data-section="anunciantes">
                  <i class="bi bi-briefcase me-3 fs-5"></i>Anunciantes
              </a>
              <a href="#" class="nav-link-admin" data-section="anuncios">
                  <i class="bi bi-megaphone me-3 fs-5"></i>Anuncios
              </a>
              <a href="#" class="nav-link-admin" data-section="admins">
                  <i class="bi bi-shield-lock me-3 fs-5"></i>Administradores
              </a>
          </nav>

          <div class="mt-auto border-top border-secondary border-opacity-25 pt-3">
              <a href="index.php" class="nav-link-admin text-white-50">
                  <i class="bi bi-box-arrow-left me-3 fs-5"></i>Volver al Sitio
              </a>
          </div>
      </aside>

      <!-- Contenido Principal -->
      <main class="admin-content">
          
          <!-- Sección Dashboard -->
          <div id="section-dashboard" class="section-content">
              <h2 class="text-white fw-bold mb-4">Dashboard</h2>
              <div class="row g-4">
                  <div class="col-md-3">
                      <div class="stat-card">
                          <h6 class="text-white-50 mb-2">Usuarios Totales</h6>
                          <h3 class="text-white fw-bold mb-0" id="stat-users">-</h3>
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="stat-card">
                          <h6 class="text-white-50 mb-2">Videos Subidos</h6>
                          <h3 class="text-white fw-bold mb-0" id="stat-videos">-</h3>
                      </div>
                  </div>
                  <div class="col-md-3">
                      <div class="stat-card">
                          <h6 class="text-white-50 mb-2">Anuncios Activos</h6>
                          <h3 class="text-white fw-bold mb-0" id="stat-ads">-</h3>
                      </div>
                  </div>
              </div>
          </div>

          <!-- Sección Anunciantes -->
          <div id="section-anunciantes" class="section-content d-none">
              <div class="d-flex justify-content-between align-items-center mb-4">
                  <h2 class="text-white fw-bold mb-0">Gestión de Anunciantes</h2>
                  <button class="btn btn-gradient rounded-pill" data-bs-toggle="modal" data-bs-target="#addAnuncianteModal">
                      <i class="bi bi-plus-lg me-2"></i>Nuevo Anunciante
                  </button>
              </div>
              
              <div class="glass-panel p-0 rounded-4 overflow-hidden">
                  <div class="table-responsive">
                      <table class="table table-dark table-hover mb-0 bg-transparent">
                          <thead>
                              <tr>
                                  <th class="p-3 bg-transparent border-bottom border-secondary border-opacity-25">ID</th>
                                  <th class="p-3 bg-transparent border-bottom border-secondary border-opacity-25">Nombre</th>
                                  <th class="p-3 bg-transparent border-bottom border-secondary border-opacity-25 text-end">Acciones</th>
                              </tr>
                          </thead>
                          <tbody id="tabla-anunciantes">
                              <!-- Carga dinámica -->
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>

          <!-- Sección Anuncios -->
          <div id="section-anuncios" class="section-content d-none">
              <div class="d-flex justify-content-between align-items-center mb-4">
                  <h2 class="text-white fw-bold mb-0">Gestión de Anuncios</h2>
                  <div class="d-flex gap-3">
                      <select id="filter-anunciante" class="form-select form-select-sm rounded-pill bg-dark text-white border-secondary" style="max-width: 200px;">
                          <option value="">Todos los Anunciantes</option>
                      </select>
                      <button class="btn btn-gradient rounded-pill" data-bs-toggle="modal" data-bs-target="#addAnuncioModal">
                          <i class="bi bi-plus-lg me-2"></i>Nuevo Anuncio
                      </button>
                  </div>
              </div>
              
              <div class="row g-4" id="grid-anuncios">
                  <!-- Carga dinámica de tarjetas de anuncios -->
              </div>
          </div>

          <!-- Sección Administradores -->
          <div id="section-admins" class="section-content d-none">
              <div class="d-flex justify-content-between align-items-center mb-4">
                  <h2 class="text-white fw-bold mb-0">Administradores</h2>
                  <button class="btn btn-gradient rounded-pill" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                      <i class="bi bi-person-plus me-2"></i>Nuevo Admin
                  </button>
              </div>
              
              <div class="glass-panel p-4 rounded-4">
                  <p class="text-white-50">Lista de usuarios con permisos de administrador.</p>
                  <ul class="list-group list-group-flush bg-transparent" id="lista-admins">
                      <!-- Carga dinámica -->
                  </ul>
              </div>
          </div>

      </main>
  </div>

  <!-- Modales -->
  
  <!-- Modal Agregar Anunciante -->
  <div class="modal fade" id="addAnuncianteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-panel border-0 text-white">
        <div class="modal-header border-bottom border-secondary border-opacity-25">
          <h5 class="modal-title fw-bold">Agregar Anunciante</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="form-add-anunciante" novalidate>
              <div class="mb-3">
                  <label class="form-label text-white-50">Nombre de la Empresa/Marca</label>
                  <input type="text" class="form-control form-control-glass" id="nombre-anunciante" required>
              </div>
              <button type="submit" class="btn btn-gradient w-100 rounded-pill">Guardar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Anuncio Modal -->
  <div class="modal fade" id="addAnuncioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-panel border-0 text-white">
        <div class="modal-header border-bottom border-secondary border-opacity-25">
          <h5 class="modal-title fw-bold">Nuevo Anuncio</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="form-add-anuncio" novalidate>
              <div class="mb-3">
                  <label class="form-label text-white-50">Anunciante</label>
                  <select class="form-select form-control-glass" id="select-anunciante" required>
                      <option value="">Seleccionar...</option>
                      <!-- Carga dinámica -->
                  </select>
              </div>
              <div class="mb-3">
                  <label class="form-label text-white-50">Nombre del Anuncio</label>
                  <input type="text" class="form-control form-control-glass" id="nombre-anuncio" required placeholder="Ej: Promo Verano 2025">
              </div>
              <div class="mb-3">
                  <label class="form-label text-white-50">Video del Anuncio</label>
                  <button type="button" class="btn btn-outline-light w-100 border-white border-opacity-25" id="btn-upload-ad-video">
                      <i class="bi bi-cloud-upload me-2"></i>Seleccionar Video (MP4)
                  </button>
                  <div id="ad-video-preview-container" class="mt-3 d-none">
                      <video id="ad-video-preview" controls class="w-100 rounded"></video>
                  </div>
                  <div class="form-text text-white-50">Solo se permiten archivos MP4.</div>
              </div>
              <button type="submit" class="btn btn-gradient w-100 rounded-pill">Subir Anuncio</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Admin Modal -->
  <div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-panel border-0 text-white">
        <div class="modal-header border-bottom border-secondary border-opacity-25">
          <h5 class="modal-title fw-bold">Registrar Nuevo Admin</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="form-add-admin" novalidate>
              <div class="mb-3">
                  <label class="form-label text-white-50">Nombre de Usuario</label>
                  <input type="text" class="form-control form-control-glass" id="admin-username" required>
              </div>
              <div class="mb-3">
                  <label class="form-label text-white-50">Correo Electrónico</label>
                  <input type="email" class="form-control form-control-glass" id="admin-email" required>
              </div>
              <div class="mb-3">
                  <label class="form-label text-white-50">Contraseña</label>
                  <input type="password" class="form-control form-control-glass" id="admin-password" required>
              </div>
              <button type="submit" class="btn btn-gradient w-100 rounded-pill">Crear Admin</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Anuncio Confirmation Modal -->
  <div class="modal fade" id="deleteAnuncioModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-panel border-0 text-white">
        <div class="modal-header border-bottom border-secondary border-opacity-25">
          <h5 class="modal-title fw-bold">Eliminar Anuncio</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-4 text-center">
            <div class="mb-3">
                <i class="bi bi-exclamation-triangle-fill text-warning display-1"></i>
            </div>
            <p class="fs-5">¿Estás seguro de que quieres eliminar este anuncio?</p>
            <p class="text-white-50 small">Esta acción no se puede deshacer.</p>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-25 justify-content-center">
          <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger rounded-pill px-4" id="btn-confirm-delete-anuncio">Eliminar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Anunciante Confirmation Modal -->
  <div class="modal fade" id="deleteAnuncianteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content glass-panel border-0 text-white">
        <div class="modal-header border-bottom border-secondary border-opacity-25">
          <h5 class="modal-title fw-bold">Eliminar Anunciante</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body py-4 text-center">
            <div class="mb-3">
                <i class="bi bi-exclamation-triangle-fill text-warning display-1"></i>
            </div>
            <p class="fs-5">¿Estás seguro de que quieres eliminar este anunciante?</p>
            <p class="text-white-50 small">Esta acción también podría borrar sus anuncios asociados.</p>
        </div>
        <div class="modal-footer border-top border-secondary border-opacity-25 justify-content-center">
          <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
          <button type="button" class="btn btn-danger rounded-pill px-4" id="btn-confirm-delete-anunciante">Eliminar</button>
        </div>
      </div>
    </div>
  </div>

  <div class="toast-container position-fixed bottom-0 end-0 p-3" id="toast-container"></div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://widget.cloudinary.com/v2.0/global/all.js" type="text/javascript"></script>
  <script src="../js/panel_admin.js"></script>
</body>
</html>
