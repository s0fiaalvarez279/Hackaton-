<?php
/**
 * RutaX · Medellín Movilidata OS
 * Sistema de monitoreo de incidentes de tránsito en tiempo real con APIs
 * @version 3.0 - Clima en tiempo real desde Open-Meteo + SSE
 */

$APP_NAME = 'RutaX · Medellín Movilidata OS';

// Cargar incidentes iniciales desde GeoJSON (si existe)
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

// Si no hay archivo, generar 20 incidentes de ejemplo
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

// Estadísticas iniciales
$baseCongestion = min(85, 20 + floor($totalIncidents / 2.5));
$avgSpeed = max(12, 45 - floor($baseCongestion / 2.2));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title><?php echo htmlspecialchars($APP_NAME); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>
    <script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/05/27/17/20260527172542-3Z3UA6G3.js" defer></script>
    <style>
        /* ========== VARIABLES Y ESTILOS GLOBALES ========== */
        :root{
            --bg:#0b1220; --card:#111a2e; --border:#1f2a44;
            --fg:#e6edf7; --muted:#8a97b2;
            --primary:#0F4C81; --accent:#F9A825;
            --ok:#10b981; --warn:#f59e0b; --danger:#ef4444;
        }
        *{box-sizing:border-box}
        html,body{margin:0;height:100%;background:var(--bg);color:var(--fg);font-family:'Inter',sans-serif;overflow:hidden}
        #map{position:absolute;inset:0;z-index:0;background:#0b1220}
        
        /* ========== BARRA SUPERIOR ========== */
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
        
        /* ========== PANEL LATERAL ========== */
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
        
        /* ========== ESTADÍSTICAS ========== */
        .stats{display:grid;grid-template-columns:1fr 1fr;gap:.65rem;padding:0 1rem}
        .stat{background:rgba(11,18,32,.65);border:1px solid var(--border);border-radius:.85rem;padding:.75rem}
        .stat .label{display:flex;align-items:center;gap:.4rem;font-size:.72rem;color:var(--muted);font-weight:500}
        .stat .value{font-size:1.35rem;font-weight:800;margin-top:.2rem}
        .stat .hint{font-size:.68rem;color:var(--muted);margin-top:.1rem}
        .ok{color:var(--ok)} .warn{color:var(--warn)} .danger{color:var(--danger)}
        
        /* ========== MENÚ ========== */
        nav.menu{padding:.5rem .75rem 1.25rem}
        nav.menu .title{padding:.5rem .5rem;font-size:.68rem;font-weight:600;color:var(--muted);text-transform:uppercase}
        nav.menu a{display:flex;align-items:center;gap:.7rem;padding:.65rem .75rem;border-radius:.55rem;color:#cbd5e1;text-decoration:none;font-size:.88rem;font-weight:500;transition:.15s}
        nav.menu a i{color:var(--accent);width:18px;text-align:center}
        nav.menu a:hover{background:rgba(255,255,255,.06);color:#fff}
        .panel footer{padding:.85rem 1rem;border-top:1px solid var(--border);font-size:.7rem;color:var(--muted)}
        
        /* ========== TARJETA DEL CLIMA ========== */
        .weather-card{background:linear-gradient(135deg,rgba(15,76,129,0.2),rgba(0,0,0,0.2));border:1px solid var(--border);border-radius:1rem;margin:1rem 1rem 0 1rem;padding:.75rem;backdrop-filter:blur(4px)}
        .weather-temp{font-size:2rem;font-weight:800;line-height:1}
        .weather-desc{font-size:.75rem;text-transform:capitalize}
        .weather-update{font-size:.6rem;color:var(--muted);text-align:right;margin-top:.5rem}
        
        /* ========== BOTONES DE FILTRO ========== */
        .alert-btn{position:relative;overflow:hidden;transition:all .3s ease}
        .alert-btn:hover{transform:translateY(-4px);box-shadow:0 0 20px rgba(239,68,68,0.4)}
        .death-alert{animation:pulse-red 2s infinite}
        @keyframes pulse-red{0%,100%{box-shadow:0 0 0 0 rgba(239,68,68,0.6)}70%{box-shadow:0 0 0 15px rgba(239,68,68,0)}}
        
        /* ========== POPUPS ========== */
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
        
        /* ========== MODAL ========== */
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
        
        .logo-icon{font-size:1.4rem;margin-right:.5rem;color:var(--accent)}
        .sidebar-logo{display:flex;align-items:center;gap:.5rem}
    </style>
</head>
<body>

<div id="map"></div>

<!-- BARRA SUPERIOR -->
<div class="topbar">
    <button class="pill" id="btnMenu"><i class="fa-solid fa-bars"></i><span>Menú</span></button>
    <div class="pill" style="cursor:default">
        <i class="fa-solid fa-car-side logo-icon"></i>
        <span class="live"></span>
        <span class="app-title-text"><?php echo htmlspecialchars($APP_NAME); ?></span>
        <span class="sm:hidden">RutaX</span>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <button class="pill" id="zoomInBtn"><i class="fa-solid fa-plus"></i><span>Acercar</span></button>
        <button class="pill" id="zoomOutBtn"><i class="fa-solid fa-minus"></i><span>Alejar</span></button>
        <button class="pill" id="btnListIncidents"><i class="fa-solid fa-list"></i><span>Incidentes</span></button>
    </div>
</div>

<!-- MODAL LISTA DE INCIDENTES -->
<div id="incidentModal" class="modal-overlay">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="flex items-center gap-2"><i class="fa-solid fa-car-crash text-amber-500"></i> Lista de Incidentes Activos</h3>
            <button id="closeModalBtn" class="text-xl text-slate-400 hover:text-white"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div id="incidentListContainer" class="incident-list"></div>
    </div>
</div>

<!-- SIDEBAR -->
<aside class="panel sidebar hidden" id="sidebar">
    <header>
        <div class="sidebar-logo">
            <i class="fa-solid fa-traffic-light fa-2x text-amber-500"></i>
            <div><h3>MIMS</h3><small>Medellín Movilidata OS</small></div>
        </div>
        <button class="icon-btn" id="closeSidebar"><i class="fa-solid fa-xmark"></i></button>
    </header>
    <div class="scroll">
        <!-- TARJETA DEL CLIMA (API en vivo) -->
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

        <!-- FILTROS DE INCIDENTES -->
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

        <!-- ESTADÍSTICAS EN VIVO -->
        <div class="stats">
            <div class="stat"><div class="label warn"><i class="fa-solid fa-chart-line"></i>Congestión</div><div class="value" id="sCong"><?php echo $baseCongestion; ?>%</div><div class="hint" id="sCongHint">Tiempo real</div></div>
            <div class="stat"><div class="label danger"><i class="fa-solid fa-triangle-exclamation"></i>Puntos críticos</div><div class="value" id="sCrit"><?php echo $criticalCount; ?></div><div class="hint">Fatalidades/Graves</div></div>
            <div class="stat"><div class="label ok"><i class="fa-solid fa-droplet"></i>Inundación</div><div class="value">Bajo</div><div class="hint">Quebradas</div></div>
            <div class="stat"><div class="label ok"><i class="fa-solid fa-gauge-high"></i>Velocidad</div><div class="value" id="sSpeed"><?php echo $avgSpeed; ?> km/h</div><div class="hint">Estimación dinámica</div></div>
        </div>

        <!-- MENÚ DE NAVEGACIÓN -->
        <nav class="menu">
            <div class="title">Navegación</div>
            <a href="app/views/home/index.php"><i class="fa-solid fa-map-location-dot"></i> Bienvenida</a>
            <a href="app/views/home/Infracciones.php"><i class="fa-solid fa-file-lines"></i> Infracciones</a>
            <a href="app/views/home/Reglamento.php"><i class="fa-solid fa-scale-balanced"></i> Reglamento</a>
            <a href="app/views/home/Agentes.php"><i class="fa-solid fa-shield-halved"></i> Agentes</a>
            <a href="app/views/home/Veedores.php"><i class="fa-solid fa-users"></i> Veedores</a>
            <a href="app/views/home/Abogados.php"><i class="fa-solid fa-gavel"></i> Abogados</a>
            <a href="app/views/home/Audiencias.php"><i class="fa-solid fa-gavel"></i> Audiencias</a>
            <a href="app/views/home/Reportes.php"><i class="fa-solid fa-chart-pie"></i> Reportes</a>
        </nav>
    </div>
    <footer>RutaX · TransiControl · SIMIT · <span id="footerTotalIncidents"><?php echo $totalIncidents; ?></span> incidentes en malla</footer>
</aside>

<script>
    // =================================================================
    // SISTEMA EN TIEMPO REAL CON SSE + API DE CLIMA
    // =================================================================
    
    let map = null;
    let mainClusterGroup = null;
    let eventSource = null;
    let allIncidents = [];
    
    let totalIncidentsCount = <?php echo $totalIncidents; ?>;
    let criticalCount = <?php echo $criticalCount; ?>;
    let congestionPercent = <?php echo $baseCongestion; ?>;
    let avgSpeedKmh = <?php echo $avgSpeed; ?>;
    let isModalOpen = false;
    
    const MEDELLIN_LAT = 6.2476;
    const MEDELLIN_LON = -75.5658;
    
    // -------------------------------------------------------------
    // 1. MAPA (sin control de zoom nativo)
    // -------------------------------------------------------------
    function initMap() {
        map = L.map('map', { zoomControl: false }).setView([MEDELLIN_LAT, MEDELLIN_LON], 13);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap · CARTO',
            maxZoom: 19
        }).addTo(map);
        
        mainClusterGroup = L.markerClusterGroup({ maxClusterRadius: 50, spiderfyOnMaxZoom: true });
        map.addLayer(mainClusterGroup);
        
        connectToEventStream();
    }
    
    // -------------------------------------------------------------
    // 2. ZOOM PERSONALIZADO (botones + y -)
    // -------------------------------------------------------------
    function zoomIn() { if (map) map.zoomIn(); }
    function zoomOut() { if (map) map.zoomOut(); }
    
    // -------------------------------------------------------------
    // 3. CLIMA EN TIEMPO REAL DESDE API (Open‑Meteo)
    // -------------------------------------------------------------
    async function fetchWeather() {
        try {
            const url = `https://api.open-meteo.com/v1/forecast?latitude=${MEDELLIN_LAT}&longitude=${MEDELLIN_LON}&current=temperature_2m,relative_humidity_2m,apparent_temperature,weather_code,wind_speed_10m&timezone=auto`;
            const response = await fetch(url);
            const data = await response.json();
            if (data && data.current) {
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
            } else throw new Error('Datos no disponibles');
        } catch (error) {
            console.error('Error al obtener el clima:', error);
            document.getElementById('weatherDesc').innerHTML = 'Error al cargar clima';
            document.getElementById('weatherIcon').innerHTML = '<i class="fa-solid fa-exclamation-triangle"></i>';
        }
    }
    
    // -------------------------------------------------------------
    // 4. STREAM DE INCIDENTES (SSE)
    // -------------------------------------------------------------
    function connectToEventStream() {
        if (eventSource) eventSource.close();
        eventSource = new EventSource('stream_incidents.php');
        console.log("✅ Conectado al stream de incidentes en tiempo real");
        
        eventSource.onmessage = function(event) {
            try {
                const incident = JSON.parse(event.data);
                console.log("🆕 Nuevo incidente:", incident);
                handleNewIncident(incident);
            } catch(e) {
                console.error("Error al parsear incidente:", e);
            }
        };
        
        eventSource.onerror = function(error) {
            console.error("❌ Error en EventSource, reintentando en 5 segundos...", error);
            eventSource.close();
            setTimeout(connectToEventStream, 5000);
        };
    }
    
    // -------------------------------------------------------------
    // 5. PROCESAR NUEVO INCIDENTE
    // -------------------------------------------------------------
    function handleNewIncident(incident) {
        allIncidents.push(incident);
        updateStatsAfterNewIncident(incident);
        
        document.getElementById('totalIncidentsLabel').innerHTML = totalIncidentsCount + ' registros';
        document.getElementById('criticalIncidentsLabel').innerHTML = criticalCount + ' alertas activas';
        document.getElementById('footerTotalIncidents').innerHTML = totalIncidentsCount;
        document.getElementById('sCrit').innerHTML = criticalCount;
        document.getElementById('sCong').innerHTML = congestionPercent + '%';
        document.getElementById('sSpeed').innerHTML = avgSpeedKmh + ' km/h';
        
        addIncidentToMap(incident);
        if (isModalOpen) refreshIncidentList();
    }
    
    function updateStatsAfterNewIncident(incident) {
        totalIncidentsCount++;
        const gravedad = (incident.properties.gravedad || incident.properties.tipo || '').toUpperCase();
        const isCritical = gravedad.includes('MUERTO') || gravedad.includes('FATAL') || gravedad.includes('DECESO') || gravedad === 'MORTAL';
        if (isCritical) criticalCount++;
        
        congestionPercent = Math.min(85, 20 + Math.floor(totalIncidentsCount / 2.5));
        avgSpeedKmh = Math.max(12, 45 - Math.floor(congestionPercent / 2.2));
    }
    
    // -------------------------------------------------------------
    // 6. DIBUJAR MARCADOR EN EL MAPA
    // -------------------------------------------------------------
    function addIncidentToMap(incident) {
        const coords = incident.geometry.coordinates;
        const lat = coords[1];
        const lng = coords[0];
        const props = incident.properties;
        
        let color = '#3b82f6';
        const gravedad = (props.gravedad || props.tipo || '').toUpperCase();
        if (gravedad.includes('MUERTO') || gravedad.includes('FATAL') || gravedad.includes('DECESO') || gravedad === 'MORTAL') color = '#ef4444';
        else if (gravedad.includes('GRAVE') || gravedad.includes('HOSPITAL')) color = '#f97316';
        else if (gravedad.includes('LEVE')) color = '#eab308';
        
        const marker = L.circleMarker([lat, lng], {
            radius: 8,
            fillColor: color,
            color: '#0b1220',
            weight: 1.5,
            fillOpacity: 0.9
        });
        
        let nivel = 'popup-leve', titulo = '📌 INCIDENTE REGULAR', icono = '🚗';
        if (gravedad.includes('MUERTO') || gravedad.includes('FATAL') || gravedad.includes('DECESO') || gravedad === 'MORTAL') {
            nivel = 'popup-mortal';
            titulo = '⚠️ CASO FATAL';
            icono = '💀';
        } else if (gravedad.includes('GRAVE') || gravedad.includes('HOSPITAL')) {
            nivel = 'popup-grave';
            titulo = '🚨 ALERTA CRÍTICA';
            icono = '⚠️';
        }
        
        let emoji = '🚨';
        const tipoIncidente = props.clase || props.tipo || 'Incidente';
        if (tipoIncidente.toLowerCase().includes('choque')) emoji = '💥';
        else if (tipoIncidente.toLowerCase().includes('moto')) emoji = '🏍️';
        else if (tipoIncidente.toLowerCase().includes('atropello')) emoji = '🚶‍♂️';
        
        const popupHtml = `
            <div class="custom-popup ${nivel}">
                <div class="popup-header"><span>${icono}</span><span>${titulo}</span></div>
                <div class="popup-content">
                    <div class="popup-row"><span class="popup-label">Evento:</span><span class="popup-value">${emoji} ${tipoIncidente}</span></div>
                    <div class="popup-row"><span class="popup-label">Dirección:</span><span class="popup-value">${props.direccion || 'No registrada'}</span></div>
                    <div class="popup-row"><span class="popup-label">Ubicación:</span><span class="popup-value">${props.barrio || ''} • Comuna ${props.comuna || 'N/A'}</span></div>
                    <div class="popup-row"><span class="popup-label">Hora:</span><span class="popup-value">📅 ${props.fecha} ⏰ ${props.hora}</span></div>
                    <div class="popup-row"><span class="popup-label">Gravedad:</span><span class="popup-value gravedad-destacada">${props.gravedad}</span></div>
                </div>
            </div>`;
        
        marker.bindPopup(popupHtml, { className: 'enhanced-popup' });
        mainClusterGroup.addLayer(marker);
    }
    
    // -------------------------------------------------------------
    // 7. CARGAR INCIDENTES INICIALES
    // -------------------------------------------------------------
    function loadInitialIncidents() {
        const incidentsGeoJSON = <?php echo json_encode($geoJsonData); ?>;
        if (incidentsGeoJSON && incidentsGeoJSON.features) {
            allIncidents = [...incidentsGeoJSON.features];
            totalIncidentsCount = allIncidents.length;
            let initialCritical = 0;
            allIncidents.forEach(incident => {
                const gravedad = (incident.properties.gravedad || '').toUpperCase();
                if (gravedad.includes('MUERTO') || gravedad.includes('FATAL') || gravedad.includes('DECESO') || gravedad === 'MORTAL') initialCritical++;
                addIncidentToMap(incident);
            });
            criticalCount = initialCritical;
            document.getElementById('totalIncidentsLabel').innerHTML = totalIncidentsCount + ' registros';
            document.getElementById('criticalIncidentsLabel').innerHTML = criticalCount + ' alertas activas';
            document.getElementById('footerTotalIncidents').innerHTML = totalIncidentsCount;
            document.getElementById('sCrit').innerHTML = criticalCount;
        }
    }
    
    // -------------------------------------------------------------
    // 8. MODAL DE INCIDENTES
    // -------------------------------------------------------------
    function refreshIncidentList() {
        const container = document.getElementById('incidentListContainer');
        if (!allIncidents.length) {
            container.innerHTML = '<p class="text-center text-slate-400 py-6">No hay registros.</p>';
            return;
        }
        container.innerHTML = allIncidents.slice().reverse().map(i => {
            const p = i.properties;
            const gClass = p.gravedad.toLowerCase().includes('mortal') || p.gravedad.toLowerCase().includes('muerto') ? 'gravedad-mortal' : (p.gravedad.toLowerCase().includes('grave') ? 'gravedad-grave' : 'gravedad-leve');
            return `
                <div class="incident-card">
                    <div class="flex justify-between items-center mb-2">
                        <span class="font-bold text-sm">${p.clase}</span>
                        <span class="gravedad-badge ${gClass}">${p.gravedad}</span>
                    </div>
                    <div class="text-xs text-slate-400 space-y-1">
                        <div><i class="fa-solid fa-location-dot"></i> ${p.direccion} (${p.barrio})</div>
                        <div><i class="fa-solid fa-clock"></i> ${p.fecha} a las ${p.hora}</div>
                    </div>
                    <button class="btn-view-map" onclick="flyToIncident(${i.geometry.coordinates[1]}, ${i.geometry.coordinates[0]})"><i class="fa-solid fa-eye"></i> Enfocar mapa</button>
                </div>`;
        }).join('');
    }
    
    function flyToIncident(lat, lng) {
        document.getElementById('incidentModal').classList.remove('active');
        isModalOpen = false;
        map.flyTo([lat, lng], 17, { duration: 1.5 });
    }
    
    // -------------------------------------------------------------
    // 9. CONFIGURACIÓN DE UI
    // -------------------------------------------------------------
    function setupUI() {
        document.getElementById('zoomInBtn').onclick = zoomIn;
        document.getElementById('zoomOutBtn').onclick = zoomOut;
        
        document.getElementById('alertAllBtn').onclick = () => {
            mainClusterGroup.clearLayers();
            allIncidents.forEach(incident => addIncidentToMap(incident));
        };
        document.getElementById('alertDeathsBtn').onclick = () => {
            mainClusterGroup.clearLayers();
            allIncidents.forEach(incident => {
                const gravedad = (incident.properties.gravedad || '').toUpperCase();
                const isCritical = gravedad.includes('MUERTO') || gravedad.includes('FATAL') || gravedad.includes('DECESO') || gravedad === 'MORTAL';
                if (isCritical) addIncidentToMap(incident);
            });
        };
        
        const sb = document.getElementById('sidebar');
        document.getElementById('btnMenu').onclick = () => sb.classList.toggle('hidden');
        document.getElementById('closeSidebar').onclick = () => sb.classList.add('hidden');
        
        const modal = document.getElementById('incidentModal');
        document.getElementById('btnListIncidents').onclick = () => {
            modal.classList.add('active');
            isModalOpen = true;
            refreshIncidentList();
        };
        document.getElementById('closeModalBtn').onclick = () => {
            modal.classList.remove('active');
            isModalOpen = false;
        };
    }
    
    // -------------------------------------------------------------
    // 10. ARRANQUE
    // -------------------------------------------------------------
    window.onload = () => {
        initMap();
        loadInitialIncidents();
        fetchWeather();
        setInterval(fetchWeather, 900000); // Cada 15 minutos
        setupUI();
    };
</script>
</body>
</html>
