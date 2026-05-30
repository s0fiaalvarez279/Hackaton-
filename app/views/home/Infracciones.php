<?php
/**
 * RutaX · Medellín Movilidata OS
 * Página de Infracciones de Tránsito
 * @version 3.2 - Sin emojis, con favicon y logo circular
 */

$APP_NAME = 'RutaX · Medellín Movilidad OS';

// Rutas de assets (desde views/home/Infracciones.php)
$logoPath = '../../images/logo.png';
$faviconPath = '../../images/favico.png';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Infracciones - <?= htmlspecialchars($APP_NAME) ?></title>
    
    <!-- Favicons multiplataforma -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?= $faviconPath ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?= $faviconPath ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $faviconPath ?>">
    <link rel="shortcut icon" type="image/png" href="<?= $faviconPath ?>">
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
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
        
        * { box-sizing: border-box; }
        
        body {
            margin: 0;
            background: var(--bg);
            color: var(--fg);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
        
        /* Barra Superior */
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
            text-decoration: none;
        }
        
        .pill:hover {
            background: rgba(17, 26, 46, 1);
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
            0%   { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.6); }
            70%  { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        
        /* Logo circular */
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
        
        /* Sidebar */
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
            padding: 1rem;
        }
        
        .menu a {
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
        
        .menu a i {
            color: var(--accent);
            width: 18px;
            text-align: center;
        }
        
        .menu a:hover, .menu a.active {
            background: rgba(59, 130, 246, 0.2);
            color: #fff;
        }
        
        /* Contenido Principal */
        .main-content {
            margin-left: 0;
            padding: 90px 1.5rem 2rem 1.5rem;
            transition: margin-left 0.45s;
            max-width: 1200px;
        }
        
        @media (min-width: 640px) {
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
            transition: transform 0.2s, border-color 0.2s;
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
        
        .badge-sml {
            background: #1e3a8a;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        footer {
            padding: 0.85rem 1rem;
            border-top: 1px solid var(--border);
            font-size: 0.7rem;
            color: var(--muted);
        }
        
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
    </style>
</head>
<body>

<!-- BARRA SUPERIOR -->
<div class="topbar">
    <button class="pill" id="btnMenu">
        <i class="fa-solid fa-bars"></i>
        <span>Menú</span>
    </button>
    
    <div class="pill" style="cursor:default; gap:0.75rem;">
        <div class="circular-logo">
            <img src="<?= $logoPath ?>" alt="RutaX Logo">
        </div>
        <span class="live"></span>
        <span class="app-title-text"><?= htmlspecialchars($APP_NAME) ?></span>
        <span class="sm:hidden">Infracciones</span>
    </div>
    
    <div style="display: flex; gap: 0.5rem;">
        <a href="index.php" class="pill">
            <i class="fa-solid fa-map"></i>
            <span>Mapa</span>
        </a>
    </div>
</div>

<!-- SIDEBAR -->
<aside class="panel sidebar hidden" id="sidebar">
    <header>
        <div class="sidebar-logo">
            <div class="circular-logo">
                <img src="<?= $logoPath ?>" alt="RutaX Logo">
            </div>
            <div>
                <h3>RutaX</h3>
                <small>Medellín Movilidata OS</small>
            </div>
        </div>
        <button class="icon-btn" id="closeSidebar">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </header>
    
    <div class="scroll">
        <nav class="menu">
            <div class="text-xs uppercase tracking-widest text-slate-400 font-bold mb-3">Navegación principal</div>
            <a href="Infracciones.php" class="active"><i class="fa-solid fa-file-lines"></i> Infracciones</a>
            <a href="Agentes.php"><i class="fa-solid fa-shield-halved"></i> Agentes</a>
            <a href="Abogados.php"><i class="fa-solid fa-gavel"></i> Abogados</a>
        </nav>
    </div>
    
    <footer>
        RutaX · TransiControl · SIMIT · Catálogo de Infracciones
    </footer>
</aside>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content" id="mainContent">
    <h1 class="section-title">Infracciones Comunes y Sus Consecuencias</h1>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">Exceso de Velocidad</h2>
            <span class="badge-sml">Alta incidencia</span>
        </div>
        <div class="infraction-penalty">Multa de 15 a 30 SMLDV + Inmovilización del vehículo</div>
        <div class="text-slate-400 text-sm mt-2">
            Aumenta significativamente el riesgo de accidentes mortales. 
            Es una de las principales causas de siniestros viales.
        </div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">Conducir en Estado de Embriaguez</h2>
            <span class="badge-sml">Grave</span>
        </div>
        <div class="infraction-penalty">Multa de 30 SMLDV + Suspensión de licencia de 6 a 10 años</div>
        <div class="text-slate-400 text-sm mt-2">
            Representa una de las mayores causas de muertes en accidentes de tránsito.
        </div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">Placas Adulteradas o Falsas</h2>
            <span class="badge-sml">Delito</span>
        </div>
        <div class="infraction-penalty">Multa de 8 SMLDV + Inmovilización + Posible comiso</div>
        <div class="text-slate-400 text-sm mt-2">
            Constituye un delito penal que puede derivar en sanciones judiciales.
        </div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">No Usar Cinturón de Seguridad</h2>
            <span class="badge-sml">Frecuente</span>
        </div>
        <div class="infraction-penalty">Multa de 15 SMLDV</div>
        <div class="text-slate-400 text-sm mt-2">
            Reduce drásticamente la protección en caso de colisión.
        </div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">Conducir sin Licencia Válida</h2>
            <span class="badge-sml">Grave</span>
        </div>
        <div class="infraction-penalty">Multa de 30 SMLDV + Inmovilización del vehículo</div>
        <div class="text-slate-400 text-sm mt-2">
            Inhabilita reclamaciones de seguro y puede generar sanciones adicionales.
        </div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">Transitar en Contravía</h2>
            <span class="badge-sml">Peligroso</span>
        </div>
        <div class="infraction-penalty">Multa de 30 SMLDV + Inmovilización</div>
        <div class="text-slate-400 text-sm mt-2">
            Genera alto riesgo de choques frontales con consecuencias fatales.
        </div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">Pasar Luz Roja</h2>
            <span class="badge-sml">Urbano</span>
        </div>
        <div class="infraction-penalty">Multa de 30 SMLDV</div>
        <div class="text-slate-400 text-sm mt-2">
            Causa una gran cantidad de accidentes en intersecciones.
        </div>
    </div>
    
    <div class="infraction-item">
        <div class="infraction-header">
            <h2 class="infraction-title">Usar Celular al Conducir</h2>
            <span class="badge-sml">Distracción</span>
        </div>
        <div class="infraction-penalty">Multa de 15 SMLDV</div>
        <div class="text-slate-400 text-sm mt-2">
            Aumenta considerablemente el tiempo de reacción y el riesgo de accidente.
        </div>
    </div>
</div>

<script>
    // Control del Sidebar
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    
    document.getElementById('btnMenu').addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
        if (window.innerWidth >= 640) {
            mainContent.classList.toggle('sidebar-open');
        }
    });
    
    document.getElementById('closeSidebar').addEventListener('click', () => {
        sidebar.classList.add('hidden');
        mainContent.classList.remove('sidebar-open');
    });
    
    // Ajuste inicial según tamaño de pantalla
    if (window.innerWidth >= 640) {
        mainContent.classList.remove('sidebar-open');
    }
</script>

</body>
</html>