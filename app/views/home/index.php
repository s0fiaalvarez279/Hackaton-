<?php
/**
 * RutaX · Medellín Movilidata OS
 * 
 * Sistema de monitoreo de incidentes de tránsito en tiempo real.
 * 
 * Este archivo principal muestra el mapa, consume varias APIs externas,
 * maneja un stream de incidentes (SSE), calcula rutas seguras y muestra
 * estadísticas visuales. Incluye un heatmap de incidentes para identificar
 * zonas de riesgo vial.
 * 
 * @version 3.3 - Heatmap de incidentes para riesgos viales
 */

$APP_NAME = 'RutaX · Medellín Movilidata OS';

// -------------------------------------------------------------------
// 1. CARGAR INCIDENTES DESDE ARCHIVO GEOJSON (si existe)
// -------------------------------------------------------------------
$geojsonFile = __DIR__ . '/data/total_incidentes_transito.geojson';
$geoJsonData = null;
$totalIncidents = 0;
$criticalCount = 0;

if (file_exists($geojsonFile)) {
    $geoJsonString = file_get_contents($geojsonFile);
    $geoJsonData = json_decode($geoJsonString, true);
    if ($geoJsonData && isset($geoJsonData['features'])) {
        $totalIncidents = count($geoJsonData['features']);
        foreach ($geoJsonData['features'] as $feature) {
            $props = $feature['properties'] ?? [];
            $gravedad = strtoupper($props['gravedad'] ?? $props['tipo'] ?? '');
            if (strpos($gravedad, 'MUERTO') !== false || strpos($gravedad, 'FATAL') !== false || strpos($gravedad, 'DECESO') !== false || $gravedad === 'MORTAL') {
                $criticalCount++;
            }
        }
    } else {
        $geoJsonData = null;
    }
}

// -------------------------------------------------------------------
// 2. SI NO HAY ARCHIVO, GENERAMOS INCIDENTES DE EJEMPLO
// -------------------------------------------------------------------
if (!$geoJsonData) {
    $features = [];
    $comunas = ['Popular', 'Santa Cruz', 'Manrique', 'Aranjuez', 'Castilla', 'Doce de Octubre', 'Robledo', 'Villa Hermosa', 'Buenos Aires', 'La Candelaria', 'Laureles', 'El Poblado'];
    $barrios = ['Belén', 'La América', 'San Javier', 'El Poblado', 'Envigado', 'Itagüí', 'Sabaneta', 'La Floresta', 'Estadio', 'Carlos E. Restrepo'];
    $tipos = ['Choque', 'Atropello', 'Caída de moto', 'Daños materiales', 'Colisión múltiple', 'Obstrucción'];
    $gravedades = ['Leve', 'Grave', 'Mortal', 'Sin lesionados', 'Hospitalización'];
    
    for ($i = 0; $i < 20; $i++) {
        $lng = -75.59 + (mt_rand(-80, 80) / 1000);
        $lat = 6.24 + (mt_rand(-60, 60) / 1000);
        $tipo = $tipos[array_rand($tipos)];
        $gravedad = $gravedades[array_rand($gravedades)];
        if ($gravedad === 'Mortal') $criticalCount++;
        
        $features[] = [
            'type' => 'Feature',
            'geometry' => ['type' => 'Point', 'coordinates' => [$lng, $lat]],
            'properties' => [
                'id' => $i, 'clase' => $tipo, 'tipo' => $tipo, 'gravedad' => $gravedad,
                'direccion' => 'Cra ' . rand(1,100) . ' #' . rand(1,50) . '-' . rand(1,99),
                'barrio' => $barrios[array_rand($barrios)], 'comuna' => $comunas[array_rand($comunas)],
                'fecha' => date('Y-m-d'), 'hora' => date('H:i:s')
            ]
        ];
    }
    $totalIncidents = count($features);
    $geoJsonData = ['type' => 'FeatureCollection', 'features' => $features];
}

// -------------------------------------------------------------------
// 3. ESTADÍSTICAS INICIALES
// -------------------------------------------------------------------
$baseCongestion = min(85, 20 + floor($totalIncidents / 2.5));
$avgSpeed = max(12, 45 - floor($baseCongestion / 2.2));

// -------------------------------------------------------------------
// 4. RUTAS DE IMÁGENES
// -------------------------------------------------------------------
$logoPath = '../../images/logo.png';
$faviconPath = '../../images/favico.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($APP_NAME); ?></title>
    
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $faviconPath; ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $faviconPath; ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $faviconPath; ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo $faviconPath; ?>">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.css">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
    
    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
    <script src="https://files.bpcontent.cloud/2026/05/27/17/20260527172542-3Z3UA6G3.js" defer></script>
    
    <style>
        :root{
            --bg:#0b1220; --card:#111a2e; --border:#1f2a44;
            --fg:#e6edf7; --muted:#8a97b2;
            --primary:#0F4C81; --accent:#F9A825;
            --ok:#10b981; --warn:#f59e0b; --danger:#ef4444;
        }
        *{box-sizing:border-box}
        html,body{margin:0;height:100%;background:var(--bg);color:var(--fg);font-family:'Inter',sans-serif;overflow:hidden}
        #map{position:absolute;inset:0;z-index:0;background:#0b1220}
        
        .topbar{position:absolute;inset:1rem 1rem auto 1rem;z-index:20;display:flex;justify-content:space-between;align-items:flex-start;pointer-events:none}
        .pill{pointer-events:auto;display:inline-flex;align-items:center;gap:.5rem;padding:.65rem 1rem;border-radius:.85rem;
          background:rgba(17,26,46,.88);border:1px solid var(--border);backdrop-filter:blur(10px);
          color:var(--fg);font-weight:600;font-size:.9rem;cursor:pointer;box-shadow:0 8px 24px rgba(0,0,0,.35);transition:.2s}
        .pill:hover{background:rgba(17,26,46,1)}
        @media (max-width: 640px){
            .pill span{display:none}
            .app-title-text{display:none}
        }
        .live{display:inline-block;width:8px;height:8px;border-radius:50%;background:var(--ok);box-shadow:0 0 0 0 rgba(16,185,129,.6);animation:pulse 1.8s infinite}
        @keyframes pulse{0%{box-shadow:0 0 0 0 rgba(16,185,129,.6)}70%{box-shadow:0 0 0 10px rgba(16,185,129,0)}100%{box-shadow:0 0 0 0 rgba(16,185,129,0)}}
        
        .circular-logo {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--primary);
            border: 2px solid var(--accent);
            box-shadow: 0 2px 8px rgba(0,0,0,0.3);
            overflow: hidden;
        }
        .circular-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .sidebar-logo .circular-logo {
            width: 44px;
            height: 44px;
        }
        
        .panel{position:absolute;top:0;height:100%;background:rgba(17,26,46,.96);backdrop-filter:blur(14px);
          border-color:var(--border);box-shadow:0 20px 60px rgba(0,0,0,.5);z-index:30;
          display:flex;flex-direction:column;transition:transform .45s cubic-bezier(.22,.61,.36,1);width:100%;}
        @media (min-width:640px){.sidebar{width:340px}}
        .sidebar{left:0;border-right:1px solid var(--border);transform:translateX(0)}
        .sidebar.hidden{transform:translateX(-100%)}
        .panel header{display:flex;justify-content:space-between;align-items:center;padding:1.1rem 1.2rem;border-bottom:1px solid var(--border)}
        .panel header h3{margin:0;font-size:1.05rem;font-weight:700}
        .panel header small{color:var(--muted);font-size:.72rem}
        .icon-btn{background:transparent;border:none;color:var(--fg);cursor:pointer;padding:.4rem;border-radius:.5rem}
        .icon-btn:hover{background:rgba(255,255,255,.08)}
        .scroll{overflow-y:auto;flex:1}
        .scroll::-webkit-scrollbar{width:6px}
        .scroll::-webkit-scrollbar-thumb{background:var(--border);border-radius:3px}
        
        .stats{display:grid;grid-template-columns:1fr 1fr;gap:.65rem;padding:0 1rem}
        .stat{background:rgba(11,18,32,.65);border:1px solid var(--border);border-radius:.85rem;padding:.75rem}
        .stat .label{display:flex;align-items:center;gap:.4rem;font-size:.72rem;color:var(--muted);font-weight:500}
        .stat .value{font-size:1.35rem;font-weight:800;margin-top:.2rem}
        .stat .hint{font-size:.68rem;color:var(--muted);margin-top:.1rem}
        .ok{color:var(--ok)} .warn{color:var(--warn)} .danger{color:var(--danger)}
        
        nav.menu{padding:.5rem .75rem 1.25rem}
        nav.menu .title{padding:.5rem .5rem;font-size:.68rem;font-weight:600;color:var(--muted);text-transform:uppercase}
        nav.menu a{display:flex;align-items:center;gap:.7rem;padding:.65rem .75rem;border-radius:.55rem;color:#cbd5e1;text-decoration:none;font-size:.88rem;font-weight:500;transition:.15s}
        nav.menu a i{color:var(--accent);width:18px;text-align:center}
        nav.menu a:hover{background:rgba(255,255,255,.06);color:#fff}
        .panel footer{padding:.85rem 1rem;border-top:1px solid var(--border);font-size:.7rem;color:var(--muted)}
        
        .weather-card, .air-card, .sun-card, .route-card{background:linear-gradient(135deg,rgba(15,76,129,0.2),rgba(0,0,0,0.2));border:1px solid var(--border);border-radius:1rem;margin:1rem 1rem 0 1rem;padding:.75rem;backdrop-filter:blur(4px)}
        .weather-temp{font-size:2rem;font-weight:800;line-height:1}
        .weather-desc{font-size:.75rem;text-transform:capitalize}
        .weather-update{font-size:.6rem;color:var(--muted);text-align:right;margin-top:.5rem}
        .air-quality-index{font-size:1.8rem;font-weight:800;line-height:1}
        .sun-icon{font-size:1.8rem}
        
        .alert-btn{position:relative;overflow:hidden;transition:all .3s ease}
        .alert-btn:hover{transform:translateY(-4px);box-shadow:0 0 20px rgba(239,68,68,0.4)}
        .death-alert{animation:pulse-red 2s infinite}
        @keyframes pulse-red{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,0.6)}70%{box-shadow:0 0 0 15px rgba(239,68,68,0)}}
        
        .enhanced-popup .leaflet-popup-content-wrapper{background:rgba(11,18,32,0.95);backdrop-filter:blur(8px);border-radius:16px;box-shadow:0 10px 25px rgba(0,0,0,0.5);padding:0}
        .enhanced-popup .leaflet-popup-content{margin:0;width:280px!important}
        .custom-popup{color:#fff}
        .popup-header{display:flex;align-items:center;gap:8px;padding:12px 16px;font-weight:800;font-size:.85rem;border-bottom:1px solid rgba(255,255,255,0.1)}
        .popup-content{padding:12px 16px;display:flex;flex-direction:column;gap:6px}
        .popup-row{display:flex;justify-content:space-between;font-size:.78rem;border-bottom:1px dashed rgba(255,255,255,0.08);padding-bottom:4px}
        .popup-label{font-weight:600;color:var(--muted)}
        .popup-value{text-align:right;font-weight:500}
        .gravedad-destacada{font-weight:800;text-transform:uppercase;padding:2px 6px;border-radius:10px;font-size:.7rem}
        .popup-mortal .popup-header{background:#b91c1c}
        .popup-grave .popup-header{background:#ea580c}
        .popup-leve .popup-header{background:#1e3a8a}
        
        .modal-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);z-index:1000;display:flex;align-items:center;justify-content:center;visibility:hidden;opacity:0;transition:.2s}
        .modal-overlay.active{visibility:visible;opacity:1}
        .modal-container{background:rgba(17,26,46,0.98);backdrop-filter:blur(12px);border-radius:1.5rem;border:1px solid var(--border);width:90%;max-width:650px;max-height:85vh;display:flex;flex-direction:column}
        .modal-header{display:flex;justify-content:space-between;padding:1rem 1.5rem;border-bottom:1px solid var(--border)}
        .incident-list{flex:1;overflow-y:auto;padding:1rem}
        .incident-card{background:rgba(11,18,32,0.5);border:1px solid var(--border);border-radius:1rem;padding:.8rem 1rem;margin-bottom:.6rem}
        .gravedad-badge{font-size:.7rem;padding:.2rem .6rem;border-radius:20px;font-weight:700}
        .gravedad-mortal{background:rgba(239,68,68,0.15);color:#ff6b6b}
        .gravedad-grave{background:rgba(249,115,22,0.15);color:#ffb347}
        .gravedad-leve{background:rgba(234,179,8,0.15);color:#fde047}
        .btn-view-map{background:var(--primary);color:white;padding:.35rem .85rem;border-radius:.5rem;font-size:.75rem;cursor:pointer;margin-top:.5rem;display:inline-block}
        .btn-view-map:hover{filter:brightness(1.2)}
        
        .sidebar-logo{display:flex;align-items:center;gap:.5rem}
        
        .bar-stats-section {
            margin: 1rem;
            background: rgba(11,18,32,0.4);
            border-radius: 1rem;
            border: 1px solid var(--border);
            padding: 0.8rem;
        }
        .bar-stats-title {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--accent);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .bar-item {
            margin-bottom: 0.7rem;
        }
        .bar-label {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            font-weight: 500;
            margin-bottom: 0.2rem;
        }
        .bar-bg {
            background: rgba(255,255,255,0.08);
            border-radius: 20px;
            overflow: hidden;
            height: 8px;
            width: 100%;
        }
        .bar-fill {
            height: 100%;
            width: 0%;
            border-radius: 20px;
            transition: width 0.6s cubic-bezier(0.22, 0.97, 0.36, 1.02);
        }
        .fill-leve { background: linear-gradient(90deg, #10b981, #34d399); }
        .fill-grave { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
        .fill-mortal { background: linear-gradient(90deg, #ef4444, #f97316); }
        .fill-comuna { background: linear-gradient(90deg, #0F4C81, #3b82f6); }
        .comuna-rank {
            font-size: 0.7rem;
            font-family: monospace;
            color: var(--accent);
        }
        .bar-hint {
            font-size: 0.6rem;
            color: var(--muted);
            text-align: right;
            margin-top: 0.2rem;
        }
        
        .btn-route-active {
            background: #f97316 !important;
            border-color: #f97316 !important;
            color: white !important;
        }
        
        /* Control de visibilidad del heatmap */
        .heatmap-toggle {
            margin-left: 0.5rem;
            background: rgba(17,26,46,0.9);
            border: 1px solid var(--accent);
        }
        .heatmap-toggle i {
            color: var(--accent);
        }
    </style>
</head>
<body>

<div id="map"></div>

<div class="topbar">
    <button class="pill" id="btnMenu"><i class="fa-solid fa-bars"></i><span>Menú</span></button>
    <div class="pill" style="cursor:default; gap:0.75rem;">
        <div class="circular-logo">
            <img src="<?php echo $logoPath; ?>" alt="RutaX Logo">
        </div>
        <span class="live"></span>
        <span class="app-title-text"><?php echo htmlspecialchars($APP_NAME); ?></span>
        <span class="sm:hidden">RutaX</span>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button class="pill" id="zoomInBtn"><i class="fa-solid fa-plus"></i><span>Acercar</span></button>
        <button class="pill" id="zoomOutBtn"><i class="fa-solid fa-minus"></i><span>Alejar</span></button>
        <button class="pill" id="btnListIncidents"><i class="fa-solid fa-list"></i><span>Incidentes</span></button>
        <button class="pill" id="btnGeolocate"><i class="fa-solid fa-location-dot"></i><span>Mi ubicación</span></button>
        <button class="pill" id="btnSafeRoute"><i class="fa-solid fa-route"></i><span>Ruta segura</span></button>
        <button class="pill heatmap-toggle" id="toggleHeatmapBtn"><i class="fa-solid fa-fire"></i><span>Heatmap</span></button>
    </div>
</div>

<div id="incidentModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="flex items-center gap-2"><i class="fa-solid fa-car-crash text-amber-500"></i> Lista de Incidentes Activos</h3>
            <button id="closeModalBtn" class="text-xl text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="incidentListContainer" class="incident-list"></div>
    </div>
</div>

<aside class="panel sidebar hidden" id="sidebar">
    <header>
        <div class="sidebar-logo">
            <div class="circular-logo">
                <img src="<?php echo $logoPath; ?>" alt="RutaX Logo">
            </div>
            <div><h3>RutaX</h3><small>Medellín Movilidata OS</small></div>
        </div>
        <button class="icon-btn" id="closeSidebar"><i class="fa-solid fa-xmark"></i></button>
    </header>
    <div class="scroll">
        <div class="weather-card" id="weatherCard">
            <div class="flex justify-between items-center">
                <div>
                    <div class="weather-temp" id="weatherTemp">--°C</div>
                    <div class="weather-desc" id="weatherDesc">Cargando clima...</div>
                </div>
                <div class="text-4xl" id="weatherIcon"><i class="fa-solid fa-cloud-sun"></i></div>
            </div>
            <div class="flex justify-between mt-2 text-xs text-slate-400">
                <span><i class="fa-solid fa-droplet"></i> <span id="weatherHumidity">--</span>%</span>
                <span><i class="fa-solid fa-wind"></i> <span id="weatherWind">--</span> km/h</span>
                <span><i class="fa-solid fa-temperature-low"></i> <span id="weatherFeels">--</span>°C</span>
            </div>
            <div class="weather-update" id="weatherUpdate"></div>
        </div>

        <div class="air-card" id="airCard">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400"><i class="fa-solid fa-wind"></i> Calidad del Aire</div>
                    <div class="air-quality-index" id="aqiValue">--</div>
                    <div class="text-xs" id="aqiCategory">Cargando...</div>
                </div>
                <div class="text-3xl" id="aqiIcon"><i class="fa-solid fa-leaf"></i></div>
            </div>
            <div class="grid grid-cols-3 gap-1 mt-2 text-[10px] text-slate-400 text-center">
                <div><span id="pm25">--</span> PM2.5</div>
                <div><span id="pm10">--</span> PM10</div>
                <div><span id="co2">--</span> CO₂</div>
            </div>
            <div class="weather-update" id="airUpdate"></div>
        </div>

        <div class="sun-card" id="sunCard">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400"><i class="fa-regular fa-sun"></i> Iluminación solar</div>
                    <div class="text-base font-bold" id="dayPeriod">--</div>
                    <div class="text-[11px]" id="sunTimes">Salida: --:-- / Puesta: --:--</div>
                </div>
                <div class="sun-icon" id="sunIcon"><i class="fa-regular fa-sun"></i></div>
            </div>
            <div class="weather-update" id="sunUpdate"></div>
        </div>

        <div class="route-card" id="routeCard">
            <div class="flex justify-between items-center">
                <div>
                    <div class="text-xs uppercase tracking-wide text-slate-400"><i class="fa-solid fa-map"></i> Ruta sugerida</div>
                    <div class="text-sm font-bold" id="routeDistance">-- km</div>
                    <div class="text-xs" id="routeDuration">-- min</div>
                </div>
                <div class="text-2xl" id="routeIcon"><i class="fa-solid fa-route"></i></div>
            </div>
            <div class="mt-2 text-xs text-slate-400" id="routeWarning"></div>
            <div class="weather-update" id="routeUpdate"></div>
        </div>

        <div class="p-4 space-y-3">
            <div class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mb-1 flex items-center gap-2">
                <i class="fa-solid fa-bell"></i> Filtros de Consola
            </div>
            <button id="alertAllBtn" class="alert-btn w-full bg-slate-800 border border-slate-700 text-white p-3 rounded-xl font-semibold flex items-center gap-3 text-left">
                <i class="fa-solid fa-car-crash text-amber-500 text-xl"></i>
                <div><div class="text-sm">Todos los Incidentes</div><div class="text-xs opacity-60" id="totalIncidentsLabel"><?php echo $totalIncidents; ?> registros</div></div>
            </button>
            <button id="alertDeathsBtn" class="alert-btn death-alert w-full bg-gradient-to-r from-red-950 to-red-900 border border-red-700 text-white p-3 rounded-xl font-semibold flex items-center gap-3 text-left">
                <i class="fa-solid fa-skull-crossbones text-red-500 text-xl"></i>
                <div><div class="text-sm">Casos Críticos / Mortales</div><div class="text-xs text-red-300 opacity-80" id="criticalIncidentsLabel"><?php echo $criticalCount; ?> alertas activas</div></div>
            </button>
        </div>

        <div class="stats">
            <div class="stat"><div class="label warn"><i class="fa-solid fa-chart-line"></i>Congestión</div><div class="value" id="sCong"><?php echo $baseCongestion; ?>%</div><div class="hint" id="sCongHint">Tiempo real</div></div>
            <div class="stat"><div class="label danger"><i class="fa-solid fa-triangle-exclamation"></i>Puntos críticos</div><div class="value" id="sCrit"><?php echo $criticalCount; ?></div><div class="hint">Fatalidades/Graves</div></div>
            <div class="stat"><div class="label ok"><i class="fa-solid fa-droplet"></i>Inundación</div><div class="value">Bajo</div><div class="hint">Quebradas</div></div>
            <div class="stat"><div class="label ok"><i class="fa-solid fa-gauge-high"></i>Velocidad</div><div class="value" id="sSpeed"><?php echo $avgSpeed; ?> km/h</div><div class="hint">Estimación dinámica</div></div>
        </div>

        <div class="bar-stats-section">
            <div class="bar-stats-title">
                <i class="fa-solid fa-chart-simple"></i> Incidentes por gravedad
            </div>
            <div id="severityBarsContainer"></div>
        </div>
        <div class="bar-stats-section">
            <div class="bar-stats-title">
                <i class="fa-solid fa-ranking-star"></i> Top comunas con incidentes
            </div>
            <div id="comunaBarsContainer"></div>
        </div>

        <nav class="menu">
            <div class="title">Navegación principal</div>
            <a href="Infracciones.php"><i class="fa-solid fa-file-lines"></i> Infracciones</a>
            <a href="Agentes.php"><i class="fa-solid fa-shield-halved"></i> Agentes</a>
            <a href="Abogados.php"><i class="fa-solid fa-gavel"></i> Abogados</a>
            <div class="title mt-2">Autenticación</div>
            <a href="../auth/login.php"><i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión</a>
            <a href="../auth/google.php"><i class="fa-brands fa-google"></i> Google Auth</a>
        </nav>
    </div>
    <footer>RutaX · TransiControl · SIMIT · <span id="footerTotalIncidents"><?php echo $totalIncidents; ?></span> incidentes en malla</footer>
</aside>

<script>
    // Variables globales
    let map = null;
    let mainClusterGroup = null;
    let eventSource = null;
    let allIncidents = [];
    let userMarker = null;
    let userCircle = null;
    let currentRouteLayer = null;
    let destinationMarker = null;
    let selectingDestination = false;
    let heatmapLayer = null;
    let heatmapEnabled = true;
    
    let totalIncidentsCount = <?php echo $totalIncidents; ?>;
    let criticalCount = <?php echo $criticalCount; ?>;
    let congestionPercent = <?php echo $baseCongestion; ?>;
    let avgSpeedKmh = <?php echo $avgSpeed; ?>;
    let isModalOpen = false;
    
    const MEDELLIN_LAT = 6.2476;
    const MEDELLIN_LON = -75.5658;
    
    const OR_API_KEY = '5b3ce3597851110001cf6248c299c86a284a4e28bb1e4f3efb6cee29';
    const ORS_BASE_URL = 'https://api.openrouteservice.org/v2/directions/driving-car';
    
    // Inicialización del mapa
    function initMap() {
        map = L.map('map', { zoomControl: false }).setView([MEDELLIN_LAT, MEDELLIN_LON], 13);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap · CARTO',
            maxZoom: 19
        }).addTo(map);
        mainClusterGroup = L.markerClusterGroup({ maxClusterRadius: 50, spiderfyOnMaxZoom: true });
        map.addLayer(mainClusterGroup);
        
        connectToEventStream();
        map.on('click', onMapClickForDestination);
    }
    
    // Heatmap: generar puntos desde incidentes (lat, lng, intensidad)
    function updateHeatmap() {
        if (heatmapLayer) {
            map.removeLayer(heatmapLayer);
        }
        if (!heatmapEnabled) return;
        
        // Preparar puntos para el heatmap: [lat, lng, intensidad]
        // La intensidad es mayor para incidentes graves/mortales
        const heatPoints = [];
        allIncidents.forEach(incident => {
            const [lng, lat] = incident.geometry.coordinates;
            const grav = (incident.properties.gravedad || '').toUpperCase();
            let intensity = 0.5;
            if (grav.includes('MUERTO') || grav.includes('FATAL') || grav.includes('DECESO') || grav === 'MORTAL') {
                intensity = 1.0;
            } else if (grav.includes('GRAVE') || grav.includes('HOSPITAL')) {
                intensity = 0.8;
            } else if (grav.includes('LEVE')) {
                intensity = 0.4;
            } else {
                intensity = 0.3;
            }
            heatPoints.push([lat, lng, intensity]);
        });
        
        if (heatPoints.length > 0) {
            heatmapLayer = L.heatLayer(heatPoints, {
                radius: 25,
                blur: 15,
                maxZoom: 17,
                minOpacity: 0.3,
                gradient: {
                    0.2: '#10b981',  // verde para baja densidad
                    0.4: '#f59e0b',  // amarillo/naranja
                    0.7: '#f97316',  // naranja intenso
                    1.0: '#ef4444'   // rojo para alta concentración
                }
            }).addTo(map);
        }
    }
    
    // Alternar visibilidad del heatmap
    function toggleHeatmap() {
        heatmapEnabled = !heatmapEnabled;
        const btn = document.getElementById('toggleHeatmapBtn');
        if (heatmapEnabled) {
            btn.style.background = 'rgba(17,26,46,0.9)';
            btn.style.borderColor = 'var(--accent)';
            updateHeatmap();
        } else {
            if (heatmapLayer) {
                map.removeLayer(heatmapLayer);
                heatmapLayer = null;
            }
            btn.style.background = 'rgba(239,68,68,0.5)';
            btn.style.borderColor = '#ef4444';
        }
    }
    
    // Manejo de selección de destino
    function onMapClickForDestination(e) {
        if (selectingDestination) {
            const { lat, lng } = e.latlng;
            suggestSafeRoute(lat, lng);
            deactivateRouteSelection();
        }
    }
    
    function activateRouteSelection() {
        selectingDestination = true;
        const btn = document.getElementById('btnSafeRoute');
        btn.classList.add('btn-route-active');
        document.getElementById('routeUpdate').innerHTML = 'Haz clic en el mapa para seleccionar el destino';
        if (currentRouteLayer) map.removeLayer(currentRouteLayer);
        if (destinationMarker) map.removeLayer(destinationMarker);
        currentRouteLayer = null;
        destinationMarker = null;
        document.getElementById('routeDistance').innerHTML = '-- km';
        document.getElementById('routeDuration').innerHTML = '-- min';
        document.getElementById('routeWarning').innerHTML = '';
    }
    
    function deactivateRouteSelection() {
        selectingDestination = false;
        const btn = document.getElementById('btnSafeRoute');
        btn.classList.remove('btn-route-active');
    }
    
    async function suggestSafeRoute(destLat, destLng) {
        let originLat, originLng;
        if (userMarker) {
            const latlng = userMarker.getLatLng();
            originLat = latlng.lat;
            originLng = latlng.lng;
        } else {
            originLat = MEDELLIN_LAT;
            originLng = MEDELLIN_LON;
        }
        
        const url = `${ORS_BASE_URL}?api_key=${OR_API_KEY}&start=${originLng},${originLat}&end=${destLng},${destLat}`;
        
        try {
            const response = await fetch(url);
            const data = await response.json();
            
            if (data.features && data.features.length > 0) {
                const route = data.features[0];
                const geometry = route.geometry.coordinates;
                const distance = route.properties.summary.distance / 1000;
                const duration = route.properties.summary.duration / 60;
                
                const latlngs = geometry.map(coord => [coord[1], coord[0]]);
                if (currentRouteLayer) map.removeLayer(currentRouteLayer);
                currentRouteLayer = L.polyline(latlngs, { color: '#f97316', weight: 6, opacity: 0.9 }).addTo(map);
                map.fitBounds(currentRouteLayer.getBounds());
                
                if (destinationMarker) map.removeLayer(destinationMarker);
                const destIcon = L.divIcon({
                    html: '<i class="fa-solid fa-flag-checkered" style="color:#f97316; font-size:28px;"></i>',
                    iconSize: [28, 28],
                    className: 'dest-marker'
                });
                destinationMarker = L.marker([destLat, destLng], { icon: destIcon }).addTo(map)
                    .bindPopup('Destino seleccionado').openPopup();
                
                document.getElementById('routeDistance').innerHTML = distance.toFixed(2) + ' km';
                document.getElementById('routeDuration').innerHTML = Math.round(duration) + ' min';
                
                const warnings = analyzeIncidentsNearRoute(latlngs, 200);
                if (warnings.length > 0) {
                    document.getElementById('routeWarning').innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> ' + warnings.slice(0, 3).join('; ');
                } else {
                    document.getElementById('routeWarning').innerHTML = '<i class="fa-solid fa-check-circle"></i> Ruta sin incidentes cercanos (radio 200m)';
                }
                document.getElementById('routeUpdate').innerHTML = `Actualizado: ${new Date().toLocaleTimeString()}`;
            } else {
                throw new Error('No se encontró ruta');
            }
        } catch (error) {
            console.error('Error al calcular ruta:', error);
            document.getElementById('routeWarning').innerHTML = '<i class="fa-solid fa-exclamation-triangle"></i> Error al calcular la ruta.';
            document.getElementById('routeUpdate').innerHTML = 'Error';
        }
    }
    
    function getDistance(lat1, lon1, lat2, lon2) {
        const R = 6371000;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLon = (lon2 - lon1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLon/2) * Math.sin(dLon/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        return R * c;
    }
    
    function analyzeIncidentsNearRoute(routeLatLngs, thresholdMeters = 200) {
        const warnings = [];
        for (const incident of allIncidents) {
            const [lng, lat] = incident.geometry.coordinates;
            let minDist = Infinity;
            for (let i = 0; i < routeLatLngs.length; i++) {
                const d = getDistance(lat, lng, routeLatLngs[i][0], routeLatLngs[i][1]);
                if (d < minDist) minDist = d;
            }
            if (minDist <= thresholdMeters) {
                const grav = (incident.properties.gravedad || '').toUpperCase();
                let level = '';
                if (grav.includes('MUERTO') || grav.includes('FATAL') || grav.includes('DECESO')) level = 'Mortal';
                else if (grav.includes('GRAVE') || grav.includes('HOSPITAL')) level = 'Grave';
                else level = 'Leve';
                warnings.push(`${level} a ${Math.round(minDist)}m (${incident.properties.clase || 'Incidente'})`);
            }
        }
        return warnings;
    }
    
    function zoomIn() { if (map) map.zoomIn(); }
    function zoomOut() { if (map) map.zoomOut(); }
    
    function locateUser() {
        if (!navigator.geolocation) {
            alert("Geolocalización no soportada.");
            return;
        }
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                const accuracy = position.coords.accuracy;
                map.setView([lat, lng], 15);
                if (userMarker) map.removeLayer(userMarker);
                if (userCircle) map.removeLayer(userCircle);
                const userIcon = L.divIcon({
                    html: '<div style="background-color:#3b82f6; width:16px; height:16px; border-radius:50%; border:3px solid white; box-shadow:0 0 8px rgba(0,0,0,0.5);"></div>',
                    iconSize: [16, 16]
                });
                userMarker = L.marker([lat, lng], { icon: userIcon }).addTo(map)
                    .bindPopup('<strong>Tu ubicación actual</strong><br>Precisión: ±' + Math.round(accuracy) + ' m')
                    .openPopup();
                userCircle = L.circle([lat, lng], {
                    radius: accuracy,
                    color: '#3b82f6',
                    fillColor: '#3b82f6',
                    fillOpacity: 0.15,
                    weight: 1.5
                }).addTo(map);
            },
            (error) => {
                let msg = "";
                switch(error.code) {
                    case error.PERMISSION_DENIED: msg = "Permiso denegado."; break;
                    case error.POSITION_UNAVAILABLE: msg = "Ubicación no disponible."; break;
                    case error.TIMEOUT: msg = "Tiempo agotado."; break;
                    default: msg = "Error desconocido.";
                }
                alert("No se pudo obtener tu ubicación: " + msg);
            },
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }
    
    async function fetchWeather() {
        try {
            const url = `https://api.open-meteo.com/v1/forecast?latitude=${MEDELLIN_LAT}&longitude=${MEDELLIN_LON}&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m&timezone=auto`;
            const resp = await fetch(url);
            const data = await resp.json();
            if (data?.current) {
                const code = data.current.weather_code;
                let icon = 'fa-cloud-sun', desc = '';
                if (code === 0) { icon = 'fa-sun'; desc = 'Despejado'; }
                else if (code === 1 || code === 2) { icon = 'fa-cloud-sun'; desc = 'Parcialmente nublado'; }
                else if (code === 3) { icon = 'fa-cloud'; desc = 'Nublado'; }
                else if (code >= 45 && code <= 48) { icon = 'fa-smog'; desc = 'Niebla'; }
                else if (code >= 51 && code <= 55) { icon = 'fa-cloud-rain'; desc = 'Llovizna'; }
                else if (code >= 61 && code <= 65) { icon = 'fa-cloud-showers-heavy'; desc = 'Lluvia'; }
                else if (code >= 71 && code <= 77) { icon = 'fa-snowflake'; desc = 'Nieve'; }
                else if (code >= 80 && code <= 82) { icon = 'fa-cloud-rain'; desc = 'Chubascos'; }
                else if (code >= 95 && code <= 99) { icon = 'fa-cloud-bolt'; desc = 'Tormenta'; }
                else { icon = 'fa-cloud'; desc = 'Variable'; }
                document.getElementById('weatherTemp').innerHTML = `${Math.round(data.current.temperature_2m)}°C`;
                document.getElementById('weatherDesc').innerHTML = desc;
                document.getElementById('weatherHumidity').innerHTML = data.current.relative_humidity_2m;
                document.getElementById('weatherWind').innerHTML = Math.round(data.current.wind_speed_10m);
                document.getElementById('weatherFeels').innerHTML = Math.round(data.current.apparent_temperature);
                document.getElementById('weatherIcon').innerHTML = `<i class="fa-solid ${icon}"></i>`;
                document.getElementById('weatherUpdate').innerHTML = `Actualizado: ${new Date().toLocaleTimeString()}`;
            }
        } catch(e) { console.error(e); }
    }
    
    async function fetchAirQuality() {
        try {
            const url = `https://air-quality-api.open-meteo.com/v1/air-quality?latitude=${MEDELLIN_LAT}&longitude=${MEDELLIN_LON}&current=us_aqi,pm10,pm2_5,carbon_monoxide&timezone=auto`;
            const resp = await fetch(url);
            const data = await resp.json();
            if (data?.current) {
                const aqi = data.current.us_aqi;
                let cat = '', ico = '', col = '';
                if (aqi <= 50) { cat = 'Bueno'; ico = 'fa-smile'; col = '#10b981'; }
                else if (aqi <= 100) { cat = 'Moderado'; ico = 'fa-meh'; col = '#f59e0b'; }
                else if (aqi <= 150) { cat = 'Insalubre (sensible)'; ico = 'fa-mask'; col = '#f97316'; }
                else if (aqi <= 200) { cat = 'Insalubre'; ico = 'fa-skull-crossbones'; col = '#ef4444'; }
                else { cat = 'Muy insalubre'; ico = 'fa-biohazard'; col = '#b91c1c'; }
                document.getElementById('aqiValue').innerHTML = aqi;
                document.getElementById('aqiCategory').innerHTML = cat;
                document.getElementById('aqiIcon').innerHTML = `<i class="fa-solid ${ico}" style="color:${col}"></i>`;
                document.getElementById('pm25').innerHTML = Math.round(data.current.pm2_5);
                document.getElementById('pm10').innerHTML = Math.round(data.current.pm10);
                document.getElementById('co2').innerHTML = Math.round(data.current.carbon_monoxide);
                document.getElementById('airUpdate').innerHTML = `Actualizado: ${new Date().toLocaleTimeString()}`;
            }
        } catch(e) { console.error(e); }
    }
    
    async function fetchSunriseSunset() {
        try {
            const today = new Date().toISOString().split('T')[0];
            const url = `https://api.sunrise-sunset.org/json?lat=${MEDELLIN_LAT}&lng=${MEDELLIN_LON}&date=${today}&formatted=0`;
            const resp = await fetch(url);
            const data = await resp.json();
            if (data.status === 'OK') {
                const sunriseUTC = new Date(data.results.sunrise);
                const sunsetUTC = new Date(data.results.sunset);
                const now = new Date();
                const sunriseLocal = new Date(sunriseUTC.getTime());
                const sunsetLocal = new Date(sunsetUTC.getTime());
                const sunriseStr = sunriseLocal.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                const sunsetStr = sunsetLocal.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                const isDay = now >= sunriseLocal && now <= sunsetLocal;
                const period = isDay ? 'Día (alta visibilidad)' : 'Noche (conducción con precaución)';
                const icon = isDay ? 'fa-sun' : 'fa-moon';
                document.getElementById('dayPeriod').innerHTML = period;
                document.getElementById('sunTimes').innerHTML = `Salida: ${sunriseStr} / Puesta: ${sunsetStr}`;
                document.getElementById('sunIcon').innerHTML = `<i class="fa-regular ${icon}"></i>`;
                document.getElementById('sunUpdate').innerHTML = `Actualizado: ${now.toLocaleTimeString()}`;
            }
        } catch(e) { console.error(e); }
    }
    
    function connectToEventStream() {
        if (eventSource) eventSource.close();
        eventSource = new EventSource('stream_incidents.php');
        eventSource.onmessage = (e) => {
            try {
                const inc = JSON.parse(e.data);
                handleNewIncident(inc);
            } catch(ex) {}
        };
        eventSource.onerror = () => {
            eventSource.close();
            setTimeout(connectToEventStream, 5000);
        };
    }
    
    function handleNewIncident(incident) {
        allIncidents.push(incident);
        totalIncidentsCount++;
        const grav = (incident.properties.gravedad || '').toUpperCase();
        if (grav.includes('MUERTO') || grav.includes('FATAL') || grav.includes('DECESO') || grav === 'MORTAL') criticalCount++;
        congestionPercent = Math.min(85, 20 + Math.floor(totalIncidentsCount / 2.5));
        avgSpeedKmh = Math.max(12, 45 - Math.floor(congestionPercent / 2.2));
        
        document.getElementById('totalIncidentsLabel').innerHTML = totalIncidentsCount + ' registros';
        document.getElementById('criticalIncidentsLabel').innerHTML = criticalCount + ' alertas activas';
        document.getElementById('footerTotalIncidents').innerHTML = totalIncidentsCount;
        document.getElementById('sCrit').innerHTML = criticalCount;
        document.getElementById('sCong').innerHTML = congestionPercent + '%';
        document.getElementById('sSpeed').innerHTML = avgSpeedKmh + ' km/h';
        addIncidentToMap(incident);
        if (isModalOpen) refreshIncidentList();
        updateBarStats();
        updateHeatmap(); // Actualizar heatmap al agregar nuevo incidente
    }
    
    function addIncidentToMap(incident) {
        const [lng, lat] = incident.geometry.coordinates;
        const props = incident.properties;
        let color = '#3b82f6';
        const grav = (props.gravedad || '').toUpperCase();
        if (grav.includes('MUERTO') || grav.includes('FATAL') || grav.includes('DECESO') || grav === 'MORTAL') color = '#ef4444';
        else if (grav.includes('GRAVE') || grav.includes('HOSPITAL')) color = '#f97316';
        else if (grav.includes('LEVE')) color = '#eab308';
        const marker = L.circleMarker([lat, lng], { radius: 8, fillColor: color, color: '#0b1220', weight: 1.5, fillOpacity: 0.9 });
        let nivel = 'popup-leve', titulo = 'INCIDENTE REGULAR', icono = '<i class="fas fa-car"></i>';
        if (grav.includes('MUERTO') || grav.includes('FATAL') || grav.includes('DECESO') || grav === 'MORTAL') { nivel = 'popup-mortal'; titulo = 'CASO FATAL'; icono = '<i class="fas fa-skull"></i>'; }
        else if (grav.includes('GRAVE') || grav.includes('HOSPITAL')) { nivel = 'popup-grave'; titulo = 'ALERTA CRÍTICA'; icono = '<i class="fas fa-exclamation-triangle"></i>'; }
        let tipoIcono = '<i class="fas fa-car-crash"></i>';
        const tipo = props.clase || props.tipo || 'Incidente';
        if (tipo.toLowerCase().includes('choque')) tipoIcono = '<i class="fas fa-car-crash"></i>';
        else if (tipo.toLowerCase().includes('moto')) tipoIcono = '<i class="fas fa-motorcycle"></i>';
        else if (tipo.toLowerCase().includes('atropello')) tipoIcono = '<i class="fas fa-person-walking"></i>';
        const popupHtml = `<div class="custom-popup ${nivel}"><div class="popup-header"><span>${icono}</span><span>${titulo}</span></div><div class="popup-content"><div class="popup-row"><span class="popup-label">Evento:</span><span class="popup-value">${tipoIcono} ${tipo}</span></div><div class="popup-row"><span class="popup-label">Dirección:</span><span class="popup-value">${props.direccion || 'No registrada'}</span></div><div class="popup-row"><span class="popup-label">Ubicación:</span><span class="popup-value">${props.barrio || ''} • Comuna ${props.comuna || 'N/A'}</span></div><div class="popup-row"><span class="popup-label">Hora:</span><span class="popup-value">${props.fecha} ${props.hora}</span></div><div class="popup-row"><span class="popup-label">Gravedad:</span><span class="popup-value gravedad-destacada">${props.gravedad}</span></div></div></div>`;
        marker.bindPopup(popupHtml, { className: 'enhanced-popup' });
        mainClusterGroup.addLayer(marker);
    }
    
    function computeSeverityStats() {
        let leves=0, graves=0, mortales=0;
        allIncidents.forEach(i => {
            const g = (i.properties.gravedad || '').toUpperCase();
            if (g.includes('MUERTO') || g.includes('FATAL') || g.includes('DECESO') || g === 'MORTAL') mortales++;
            else if (g.includes('GRAVE') || g.includes('HOSPITAL')) graves++;
            else leves++;
        });
        return { leves, graves, mortales, total: allIncidents.length };
    }
    
    function computeComunaStats() {
        const mapC = new Map();
        allIncidents.forEach(i => {
            const c = i.properties.comuna || 'Desconocida';
            mapC.set(c, (mapC.get(c)||0)+1);
        });
        return Array.from(mapC.entries()).sort((a,b)=>b[1]-a[1]).slice(0,3).map(([n,c])=>({name:n,count:c}));
    }
    
    function updateBarStats() {
        const s = computeSeverityStats();
        const total = s.total;
        document.getElementById('severityBarsContainer').innerHTML = `
            <div class="bar-item"><div class="bar-label"><span><i class="fa-regular fa-circle-check text-emerald-400"></i> Leves</span><span>${s.leves} (${total?((s.leves/total)*100).toFixed(0):0}%)</span></div><div class="bar-bg"><div class="bar-fill fill-leve" style="width: ${total?(s.leves/total)*100:0}%;"></div></div></div>
            <div class="bar-item"><div class="bar-label"><span><i class="fa-solid fa-truck-medical text-amber-400"></i> Graves</span><span>${s.graves} (${total?((s.graves/total)*100).toFixed(0):0}%)</span></div><div class="bar-bg"><div class="bar-fill fill-grave" style="width: ${total?(s.graves/total)*100:0}%;"></div></div></div>
            <div class="bar-item"><div class="bar-label"><span><i class="fa-solid fa-skull text-red-400"></i> Mortales</span><span>${s.mortales} (${total?((s.mortales/total)*100).toFixed(0):0}%)</span></div><div class="bar-bg"><div class="bar-fill fill-mortal" style="width: ${total?(s.mortales/total)*100:0}%;"></div></div></div>
            <div class="bar-hint"><i class="fa-regular fa-chart-scatter"></i> Total incidentes: ${total}</div>`;
        const top = computeComunaStats();
        let html = '';
        if (top.length) {
            top.forEach((c,idx) => {
                const percent = (c.count / top[0].count) * 100;
                html += `<div class="bar-item"><div class="bar-label"><span><span class="comuna-rank">#${idx+1}</span> ${c.name}</span><span>${c.count} incidentes</span></div><div class="bar-bg"><div class="bar-fill fill-comuna" style="width: ${percent}%;"></div></div></div>`;
            });
        } else html = '<div class="text-xs text-slate-400 text-center py-2">Sin datos aún</div>';
        document.getElementById('comunaBarsContainer').innerHTML = html;
    }
    
    function loadInitialIncidents() {
        const geo = <?php echo json_encode($geoJsonData); ?>;
        if (geo?.features) {
            allIncidents = [...geo.features];
            totalIncidentsCount = allIncidents.length;
            let crit = 0;
            allIncidents.forEach(inc => {
                const g = (inc.properties.gravedad || '').toUpperCase();
                if (g.includes('MUERTO') || g.includes('FATAL') || g.includes('DECESO') || g === 'MORTAL') crit++;
                addIncidentToMap(inc);
            });
            criticalCount = crit;
            document.getElementById('totalIncidentsLabel').innerHTML = totalIncidentsCount + ' registros';
            document.getElementById('criticalIncidentsLabel').innerHTML = criticalCount + ' alertas activas';
            document.getElementById('footerTotalIncidents').innerHTML = totalIncidentsCount;
            document.getElementById('sCrit').innerHTML = criticalCount;
            updateBarStats();
            updateHeatmap(); // Heatmap inicial
        }
    }
    
    function refreshIncidentList() {
        const container = document.getElementById('incidentListContainer');
        if (!allIncidents.length) { container.innerHTML = '<p class="text-center text-slate-400 py-6">No hay registros.</p>'; return; }
        container.innerHTML = allIncidents.slice().reverse().map(i => {
            const p = i.properties;
            const gClass = p.gravedad.toLowerCase().includes('mortal') || p.gravedad.toLowerCase().includes('muerto') ? 'gravedad-mortal' : (p.gravedad.toLowerCase().includes('grave') ? 'gravedad-grave' : 'gravedad-leve');
            return `<div class="incident-card"><div class="flex justify-between items-center mb-2"><span class="font-bold text-sm">${p.clase}</span><span class="gravedad-badge ${gClass}">${p.gravedad}</span></div><div class="text-xs text-slate-400 space-y-1"><div><i class="fa-solid fa-location-dot"></i> ${p.direccion} (${p.barrio})</div><div><i class="fa-solid fa-clock"></i> ${p.fecha} a las ${p.hora}</div></div><button class="btn-view-map" onclick="flyToIncident(${i.geometry.coordinates[1]}, ${i.geometry.coordinates[0]})"><i class="fa-solid fa-eye"></i> Enfocar mapa</button></div>`;
        }).join('');
    }
    
    function flyToIncident(lat, lng) {
        document.getElementById('incidentModal').classList.remove('active');
        isModalOpen = false;
        map.flyTo([lat, lng], 17, { duration: 1.5 });
    }
    
    function setupUI() {
        document.getElementById('zoomInBtn').onclick = zoomIn;
        document.getElementById('zoomOutBtn').onclick = zoomOut;
        document.getElementById('btnGeolocate').onclick = locateUser;
        document.getElementById('toggleHeatmapBtn').onclick = toggleHeatmap;
        document.getElementById('btnSafeRoute').onclick = () => {
            if (selectingDestination) {
                deactivateRouteSelection();
                document.getElementById('routeUpdate').innerHTML = 'Selección cancelada';
            } else {
                activateRouteSelection();
            }
        };
        
        document.getElementById('alertAllBtn').onclick = () => { 
            mainClusterGroup.clearLayers(); 
            allIncidents.forEach(i => addIncidentToMap(i));
            updateHeatmap();
        };
        document.getElementById('alertDeathsBtn').onclick = () => {
            mainClusterGroup.clearLayers();
            allIncidents.forEach(i => {
                const g = (i.properties.gravedad || '').toUpperCase();
                if (g.includes('MUERTO') || g.includes('FATAL') || g.includes('DECESO') || g === 'MORTAL') addIncidentToMap(i);
            });
            // No actualizamos heatmap aquí porque sigue mostrando todos los incidentes
            // Podría mejorarse para filtrar también el heatmap, pero por ahora se mantiene completo
        };
        
        const sb = document.getElementById('sidebar');
        document.getElementById('btnMenu').onclick = () => sb.classList.toggle('hidden');
        document.getElementById('closeSidebar').onclick = () => sb.classList.add('hidden');
        const modal = document.getElementById('incidentModal');
        document.getElementById('btnListIncidents').onclick = () => { modal.classList.add('active'); isModalOpen = true; refreshIncidentList(); };
        document.getElementById('closeModalBtn').onclick = () => { modal.classList.remove('active'); isModalOpen = false; };
    }
    
    window.onload = () => {
        initMap();
        loadInitialIncidents();
        fetchWeather();
        fetchAirQuality();
        fetchSunriseSunset();
        setInterval(fetchWeather, 900000);
        setInterval(fetchAirQuality, 1800000);
        setInterval(fetchSunriseSunset, 3600000);
        setupUI();
    };
</script>
</body>
</html>