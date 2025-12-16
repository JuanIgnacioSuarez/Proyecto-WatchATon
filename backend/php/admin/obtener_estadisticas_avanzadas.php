<?php
// Archivo: backend/php/admin/obtener_estadisticas_avanzadas.php
// Propósito: Entregar datos agregados para los gráficos del dashboard

require_once('../../classes/Conexion.php');
require_once('../verificar_admin.php');

// Verificar sesión
checkAdmin();

$conexion = new Conexion();

// Recibir filtros
$fechaInicio = $_POST['fecha_inicio'] ?? date('Y-m-d', strtotime('-30 days'));
$fechaFin    = $_POST['fecha_fin'] ?? date('Y-m-d');
// Usar filtro_id si viene, sino fallback a id_anunciante (compatibilidad)
$idAnunciante = $_POST['filtro_id'] ?? $_POST['id_anunciante'] ?? '';

// Construir condición base del WHERE
// Nota: inicio_visualizacion es DATETIME, así que usamos DATE() para comparar o rangos
$whereClause = "WHERE DATE(inicio_visualizacion) BETWEEN '$fechaInicio' AND '$fechaFin'";

if (!empty($idAnunciante)) {
    $sqlIds = "SELECT ID_anuncio FROM anuncios WHERE id_anunciante = $idAnunciante";
}


$joinAnuncios = "";
$condicionAnunciante = "";

if (!empty($idAnunciante)) {
    $joinAnuncios = "INNER JOIN anuncios a ON b.id_anuncio = a.ID_anuncio";
    $condicionAnunciante = "AND a.id_anunciante = $idAnunciante";
}

// --- 1. Tendencia de Visualizaciones (Línea de tiempo) ---
// Agrupar por fecha
$sqlTimeline = "SELECT DATE(b.inicio_visualizacion) as fecha, COUNT(*) as total 
                FROM bitacora_anuncios b 
                $joinAnuncios
                $whereClause $condicionAnunciante
                GROUP BY DATE(b.inicio_visualizacion) 
                ORDER BY fecha ASC";

$resTimeline = $conexion->consultar($sqlTimeline);

// --- 2. Dispositivos (Torta) ---
$sqlDevices = "SELECT b.dispositivo, COUNT(*) as total 
               FROM bitacora_anuncios b
               $joinAnuncios
               $whereClause $condicionAnunciante
               GROUP BY b.dispositivo";
$resDevices = $conexion->consultar($sqlDevices);

// --- 3. Retención (Completado vs Saltado) ---
$sqlRetention = "SELECT b.estado, COUNT(*) as total 
                 FROM bitacora_anuncios b
                 $joinAnuncios
                 $whereClause $condicionAnunciante
                 AND b.estado IN ('completado', 'saltado')
                 GROUP BY b.estado";
$resRetention = $conexion->consultar($sqlRetention);

// --- 4. Top Anuncios (Barras) ---
// Necesitamos el nombre del anuncio
$sqlTop = "SELECT a.nombre, COUNT(*) as vistas 
           FROM bitacora_anuncios b
           INNER JOIN anuncios a ON b.id_anuncio = a.ID_anuncio
           $whereClause
           " . (!empty($idAnunciante) ? "AND a.id_anunciante = $idAnunciante" : "") . "
           GROUP BY b.id_anuncio 
           ORDER BY vistas DESC 
           LIMIT 5";
$resTop = $conexion->consultar($sqlTop);

// --- 5. Total de Vistas en el periodo (KPI Single) ---
$sqlTotalPeriod = "SELECT COUNT(*) as total 
                   FROM bitacora_anuncios b
                   $joinAnuncios
                   $whereClause $condicionAnunciante";
$resTotal = $conexion->consultar($sqlTotalPeriod);

// --- 6. Navegadores (Procesamiento PHP para simplificar User Agent) ---
$sqlBrowsers = "SELECT navegador, COUNT(*) as total 
                FROM bitacora_anuncios b
                $joinAnuncios
                $whereClause $condicionAnunciante
                GROUP BY navegador";
$resRawBrowsers = $conexion->consultar($sqlBrowsers);

// Simplificar User Agents
$browserStats = [];
foreach ($resRawBrowsers as $row) {
    $ua = $row['navegador'];
    $count = intval($row['total']);
    
    $name = 'Otro';
    if (strpos($ua, 'Edg') !== false) $name = 'Edge';
    elseif (strpos($ua, 'Chrome') !== false) $name = 'Chrome'; // Chrome detectado despues de Edge pq Edge incluye Chrome
    elseif (strpos($ua, 'Firefox') !== false) $name = 'Firefox';
    elseif (strpos($ua, 'Safari') !== false) $name = 'Safari';
    elseif (strpos($ua, 'Opera') !== false || strpos($ua, 'OPR') !== false) $name = 'Opera';
    elseif (strpos($ua, 'Trident') !== false || strpos($ua, 'MSIE') !== false) $name = 'Internet Explorer';

    if (!isset($browserStats[$name])) $browserStats[$name] = 0;
    $browserStats[$name] += $count;
}

// Convertir a formato de array para frontend
$finalBrowsers = [];
foreach ($browserStats as $name => $count) {
    $finalBrowsers[] = ['navegador' => $name, 'total' => $count];
}


// --- 7. Top Anunciantes (Barras) ---
$sqlTopAdvertisers = "SELECT an.nombre, COUNT(*) as vistas 
                      FROM bitacora_anuncios b
                      INNER JOIN anuncios a ON b.id_anuncio = a.ID_anuncio
                      INNER JOIN anunciantes an ON a.id_anunciante = an.id
                      $whereClause
                      " . (!empty($idAnunciante) ? "AND a.id_anunciante = $idAnunciante" : "") . "
                      GROUP BY an.nombre 
                      ORDER BY vistas DESC 
                      LIMIT 5";
$resTopAdvertisers = $conexion->consultar($sqlTopAdvertisers);

// --- 8. Tiempo Total Visto (Segundos) ---
// Por Anunciante
$sqlTimeAdvertiser = "SELECT an.nombre, SUM(TIMESTAMPDIFF(SECOND, b.inicio_visualizacion, b.fin_visualizacion)) as segundos
                      FROM bitacora_anuncios b
                      INNER JOIN anuncios a ON b.id_anuncio = a.ID_anuncio
                      INNER JOIN anunciantes an ON a.id_anunciante = an.id
                      $whereClause
                      " . (!empty($idAnunciante) ? "AND a.id_anunciante = $idAnunciante" : "") . "
                      GROUP BY an.nombre
                      ORDER BY segundos DESC";
$resTimeAdvertiser = $conexion->consultar($sqlTimeAdvertiser);

// Por Anuncio
$sqlTimeAd = "SELECT a.nombre, SUM(TIMESTAMPDIFF(SECOND, b.inicio_visualizacion, b.fin_visualizacion)) as segundos
              FROM bitacora_anuncios b
              INNER JOIN anuncios a ON b.id_anuncio = a.ID_anuncio
              $whereClause
              " . (!empty($idAnunciante) ? "AND a.id_anunciante = $idAnunciante" : "") . "
              GROUP BY a.nombre
              ORDER BY segundos DESC";
$resTimeAd = $conexion->consultar($sqlTimeAd);

// --- 9. Top Recompensas Canjeadas ---
$whereCanjes = "WHERE DATE(c.Fecha) BETWEEN '$fechaInicio' AND '$fechaFin'";
$sqlTopRedemptions = "SELECT b.Descripcion, COUNT(*) as total
                      FROM canjeos c
                      INNER JOIN beneficios b ON c.ID_beneficio = b.ID_beneficio
                      $whereCanjes
                      GROUP BY c.ID_beneficio
                      ORDER BY total DESC
                      LIMIT 5";
$resTopRedemptions = $conexion->consultar($sqlTopRedemptions);

// --- 10. Estadísticas de Premium (Pagos vs Puntos) ---
// Usuarios únicos activos hoy con beneficio 0 (Premium)
$sqlPremiumStats = "
    SELECT 
        COUNT(DISTINCT c.ID_usuario) as total,
        COUNT(DISTINCT CASE WHEN p.id_pago IS NOT NULL THEN c.ID_usuario END) as pagados
    FROM canjeos c
    LEFT JOIN pagos p ON c.ID_canjeo = p.id_canje
    WHERE c.ID_beneficio = 0 
    AND (c.fecha_vencimiento IS NULL OR c.fecha_vencimiento > NOW())
";
$premiumStats = $conexion->consultar($sqlPremiumStats);
$totalPremium = $premiumStats[0]['total'] ?? 0;
$pagadosPremium = $premiumStats[0]['pagados'] ?? 0;
$puntosPremium = $totalPremium - $pagadosPremium;

// Formatear respuesta JSON
echo json_encode([
    'success' => true,
    'timeline' => $resTimeline,
    'devices' => $resDevices,
    'retention' => $resRetention,
    'top_ads' => $resTop,
    'browsers' => $finalBrowsers,
    'top_advertisers' => $resTopAdvertisers,
    'time_advertiser' => $resTimeAdvertiser,
    'time_ad' => $resTimeAd,
    'top_redemptions' => $resTopRedemptions,
    'total_period' => $resTotal[0]['total'] ?? 0,
    'premium_stats' => [ // New Field
        'pagados' => $pagadosPremium,
        'puntos' => $puntosPremium
    ]
]);
?>
