<?php
$APP_NAME = 'RutaX · Medellín Movilidad OS';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Infracciones - <?php echo htmlspecialchars($APP_NAME); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* ========== VARIABLES Y ESTILOS GLOBALES ========== */
        :root {
            --bg: #0b1220;
            --card: #111a2e;
            --border: #1f2a44;
            --fg: #e6edf7;
            --muted: #8a97b2;
            --primary: #0F4C81;
            --accent: #F9A825;
            --ok: #10b981;
            --warn: #f59e0b;
            --danger: #ef4444;
        }
        * {
            box-sizing: border-box;
        }
        body {
            margin: 0;
            background: var(--bg);
            color: var(--fg);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        
        /* ========== BARRA SUPERIOR ========== */
        .topbar {
            position: fixed;
            top: 1rem;
            left: 1rem;
            right: 1rem;
            z-index: 20;
            display: flex;
            justify-content: space-between;
            align-items: center;
            pointer-events: none;
        }
        .pill {
            pointer-events: auto;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1rem;
            border-radius: 0.85rem;
            background: rgba(17, 26, 46, 0.88);
            border: 1px solid var(--border);
            backdrop-filter: blur(10px);
            color: var(--fg);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
            transition: 0.2s;
        }
        .pill:hover {
            background: rgba(17, 26, 46, 1);
        }
        @media (max-width: 640px) {
            .pill span { display: none; }
            .app-title-text { display: none; }
        }
        .live {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--ok);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6);
            animation: pulse 1.8s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        
        /* ========== PANEL LATERAL ========== */
        .panel {
            position: fixed;
            top: 0;
            height: 100%;
            background: rgba(17, 26, 46, 0.96);
            backdrop-filter: blur(14px);
            border-color: var(--border);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            z-index: 30;
            display: flex;
            flex-direction: column;
            transition: transform 0.45s cubic-bezier(0.22, 0.61, 0.36, 1);
            width: 100%;
        }
        @media (min-width: 640px) {
            .sidebar { width: 340px; }
        }
        .sidebar {
            left: 0;
            border-right: 1px solid var(--border);
            transform: translateX(0);
        }
        .sidebar.hidden {
            transform: translateX(-100%);
        }
        .panel header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.1rem 1.2rem;
            border-bottom: 1px solid var(--border);
        }
        .panel header h3 {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 700;
        }
        .panel header small {
            color: var(--muted);
            font-size: 0.72rem;
        }
        .icon-btn {
            background: transparent;
            border: none;
            color: var(--fg);
            cursor: pointer;
            padding: 0.4rem;
            border-radius: 0.5rem;
        }
        .icon-btn:hover {
            background: rgba(255, 255, 255, 0.08);
        }
        .scroll {
            overflow-y: auto;
            flex: 1;
        }
        .scroll::-webkit-scrollbar {
            width: 6px;
        }
        .scroll::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 3px;
        }
        
        /* ========== MENÚ ========== */
        nav.menu {
            padding: 0.5rem 0.75rem 1.25rem;
        }
        nav.menu .title {
            padding: 0.5rem 0.5rem;
            font-size: 0.68rem;
            font-weight: 600;
            color: var(--muted);
            text-transform: uppercase;
        }
        nav.menu a {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.65rem 0.75rem;
            border-radius: 0.55rem;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: 0.15s;
        }
        nav.menu a i {
            color: var(--accent);
            width: 18px;
            text-align: center;
        }
        nav.menu a:hover {
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
        }
        .panel footer {
            padding: 0.85rem 1rem;
            border-top: 1px solid var(--border);
            font-size: 0.7rem;
            color: var(--muted);
        }
        
        /* ========== CONTENIDO PRINCIPAL ========== */
        .main-content {
            margin-left: 0;
            padding: 90px 1.5rem 2rem 1.5rem;
            transition: margin-left 0.45s;
            max-width: 1200px;
        }
        @media (min-width: 640px) {
            .main-content {
                margin-left: 0;
            }
            .main-content.sidebar-open {
                margin-left: 340px;
            }
        }
        .section-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            color: #f1f5f9;
            border-left: 4px solid #fbbf24;
            padding-left: 1rem;
        }
        .infraction-item {
            background: #1e293b;
            border-radius: 0.75rem;
            padding: 1.2rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border);
            transition: transform 0.2s;
        }
        .infraction-item:hover {
            transform: translateX(5px);
            border-color: var(--accent);
        }
        .infraction-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.75rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .infraction-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fbbf24;
        }
        .infraction-penalty {
            color: #f87171;
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        .infraction-risk {
            color: #94a3b8;
            font-size: 0.85rem;
            border-top: 1px dashed var(--border);
            padding-top: 0.5rem;
            margin-top: 0.5rem;
        }
        .logo-icon {
            font-size: 1.4rem;
            margin-right: 0.5rem;
            color: var(--accent);
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .badge-sml {
            background: #1e3a8a;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- BARRA SUPERIOR (igual que en index.php) -->
<div class="topbar">
    <button class="pill" id="btnMenu"><i class="fa-solid fa-bars"></i><span>Menú</span></button>
    <div class="pill" style="cursor:default">
        <i class="fa-solid fa-car-side logo-icon"></i>
        <span class="live"></span>
        <span class="app-title-text"><?php echo htmlspecialchars($APP_NAME); ?></span>
        <span class="sm:hidden">Infracciones</span>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <!-- Botones de navegación rápida (opcional) -->
        <a href="index.php" class="pill" style="text-decoration: none;"><i class="fa-solid fa-map"></i><span>Mapa</span></a>
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
        <nav class="menu">
            <div class="title">Navegación</div>
            <a href="index.php"><i class="fa-solid fa-map-location-dot"></i> Mapa en vivo</a>
            <a href="infracciones.php" class="active" style="background: rgba(59,130,246,0.2);"><i class="fa-solid fa-file-lines"></i> Infracciones</a>
            <a href="#"><i class="fa-solid fa-scale-balanced"></i> Reglamento</a>
            <a href="#"><i class="fa-solid fa-shield-halved"></i> Agentes</a>
            <a href="#"><i class="fa-solid fa-users"></i> Veedores</a>
            <a href="#"><i class="fa-solid fa-gavel"></i> Abogados</a>
            <a href="#"><i class="fa-solid fa-gavel"></i> Audiencias</a>
            <a href="#"><i class="fa-solid fa-chart-pie"></i> Reportes</a>
        </nav>
    </div>
    <footer>RutaX · TransiControl · SIMIT · Catálogo de infracciones</footer>
</aside>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content" id="mainContent">
    <h1 class="section-title">⚠️ Infracciones Comunes y sus Consecuencias</h1>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">🚗 Exceso de Velocidad</h2>
            <span class="badge-sml">Alta incidencia</span>
        </div>
        <div class="infraction-penalty">💰 Multa 15-30 SMLDV + Inmovilización del vehículo</div>
        <div class="infraction-risk">⚠️ Aumenta 80% el riesgo de accidentes mortales. La velocidad es la principal causa de siniestros fatales.</div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">🍺 Conducir en Estado de Embriaguez</h2>
            <span class="badge-sml">Grave</span>
        </div>
        <div class="infraction-penalty">💰 Multa 30 SMLDV + Suspensión de licencia 6-10 años</div>
        <div class="infraction-risk">⚠️ Causa el 40% de las muertes en accidentes de tránsito. Pérdida total de reflejos.</div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">🔢 Placas Adulteradas o Falsas</h2>
            <span class="badge-sml">Delito</span>
        </div>
        <div class="infraction-penalty">💰 Multa 8 SMLDV + Inmovilización + Posible comiso del vehículo</div>
        <div class="infraction-risk">⚠️ Constituye un delito penal. La falsedad documental puede llevar a prisión.</div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">🪑 No Usar Cinturón de Seguridad</h2>
            <span class="badge-sml">Frecuente</span>
        </div>
        <div class="infraction-penalty">💰 Multa 15 SMLDV</div>
        <div class="infraction-risk">⚠️ Reduce un 75% la probabilidad de lesiones graves en caso de choque.</div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">📄 Conducir sin Licencia Válida</h2>
            <span class="badge-sml">Grave</span>
        </div>
        <div class="infraction-penalty">💰 Multa 30 SMLDV + Inmovilización del vehículo</div>
        <div class="infraction-risk">⚠️ Inhabilita la posibilidad de reclamar seguros. Delito penal reincidente.</div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">🔄 Transitar en Contravía</h2>
            <span class="badge-sml">Peligroso</span>
        </div>
        <div class="infraction-penalty">💰 Multa 30 SMLDV + Inmovilización</div>
        <div class="infraction-risk">⚠️ Genera choques frontales letales. Alta probabilidad de muerte inmediata.</div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">🏍️ Moto Modificada / Ilegal</h2>
            <span class="badge-sml">Técnica</span>
        </div>
        <div class="infraction-penalty">💰 Multa variable (hasta 30 SMLDV) + Inmovilización</div>
        <div class="infraction-risk">⚠️ Afecta la estabilidad del vehículo y contamina el medio ambiente.</div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">🚦 Pasar la Luz Roja</h2>
            <span class="badge-sml">Urbano</span>
        </div>
        <div class="infraction-penalty">💰 Multa 30 SMLDV</div>
        <div class="infraction-risk">⚠️ Causa el 25% de los accidentes en intersecciones urbanas.</div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">📱 Usar Celular al Conducir</h2>
            <span class="badge-sml">Distracción</span>
        </div>
        <div class="infraction-penalty">💰 Multa 15 SMLDV</div>
        <div class="infraction-risk">⚠️ Multiplica por 4 el riesgo de accidente. La reacción se retrasa 2 segundos.</div>
    </div>
</div>

<script>
    // Control del sidebar (igual que en index.php)
    const sb = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    document.getElementById('btnMenu').onclick = () => {
        sb.classList.toggle('hidden');
        if (window.innerWidth >= 640) {
            mainContent.classList.toggle('sidebar-open');
        }
    };
    document.getElementById('closeSidebar').onclick = () => {
        sb.classList.add('hidden');
        mainContent.classList.remove('sidebar-open');
    };
    
    // Ajuste por tamaño de pantalla
    if (window.innerWidth >= 640) {
        mainContent.classList.remove('sidebar-open');
    }
</script>
</body>
</html>