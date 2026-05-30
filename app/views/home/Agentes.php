<?php
$APP_NAME = 'RutaX · Medellín Movilidad OS';

// Rutas de imágenes (desde views/home/Agentes.php subimos dos niveles)
$logoPath = '../../images/logo.png';
$faviconPath = '../../images/favico.png';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Agentes de Tránsito - <?php echo htmlspecialchars($APP_NAME); ?></title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo $faviconPath; ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo $faviconPath; ?>">
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $faviconPath; ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo $faviconPath; ?>">
    
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
            text-decoration: none;
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
        
        /* ========== LOGO CIRCULAR ========== */
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
        nav.menu a:hover, nav.menu a.active {
            background: rgba(59, 130, 246, 0.2);
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
        
        /* ========== TARJETAS DE AGENTES ========== */
        .agent-card {
            background: #1e293b;
            border-radius: 0.75rem;
            padding: 1.2rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border);
            transition: transform 0.2s, border-color 0.2s;
        }
        .agent-card:hover {
            transform: translateX(5px);
            border-color: var(--accent);
        }
        .agent-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 0.75rem;
        }
        .agent-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fbbf24;
        }
        .agent-id {
            font-size: 0.7rem;
            color: var(--muted);
            background: rgba(0,0,0,0.3);
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
        }
        .agent-badge {
            background: #1e3a8a;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .agent-details {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin: 0.75rem 0;
            font-size: 0.85rem;
            color: #cbd5e1;
        }
        .agent-details i {
            width: 20px;
            color: var(--accent);
        }
        .agent-experience {
            color: var(--ok);
        }
        .rating-stars {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-top: 0.5rem;
        }
        .star {
            color: #64748b;
            cursor: pointer;
            font-size: 1.1rem;
            transition: color 0.1s;
        }
        .star.active {
            color: #fbbf24;
        }
        .star:hover {
            color: #fde047;
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .agent-contact-btn {
            margin-top: 0.75rem;
            background: var(--primary);
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 0.5rem;
            color: white;
            font-size: 0.75rem;
            cursor: pointer;
            transition: 0.2s;
        }
        .agent-contact-btn:hover {
            background: #1e5a9c;
        }
    </style>
</head>
<body>

<!-- BARRA SUPERIOR -->
<div class="topbar">
    <button class="pill" id="btnMenu"><i class="fa-solid fa-bars"></i><span>Menú</span></button>
    <div class="pill" style="cursor:default; gap:0.75rem;">
        <div class="circular-logo">
            <img src="<?php echo $logoPath; ?>" alt="RutaX Logo">
        </div>
        <span class="live"></span>
        <span class="app-title-text"><?php echo htmlspecialchars($APP_NAME); ?></span>
        <span class="sm:hidden">Agentes</span>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="index.php" class="pill"><i class="fa-solid fa-map"></i><span>Mapa</span></a>
    </div>
</div>

<!-- SIDEBAR -->
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
        <!-- MENÚ DE NAVEGACIÓN CON RUTAS CORRECTAS -->
        <nav class="menu">
            <div class="title">Navegación principal</div>
+            <a href="Infracciones.php"><i class="fa-solid fa-file-lines"></i> Infracciones</a>
            <a href="Agentes.php" class="active"><i class="fa-solid fa-shield-halved"></i> Agentes</a>
            <a href="Abogados.php"><i class="fa-solid fa-gavel"></i> Abogados</a>
        </nav>
    </div>
    <footer>RutaX · TransiControl · SIMIT · Cuerpo de agentes</footer>
</aside>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content" id="mainContent">
    <h1 class="section-title">Agentes de Tránsito</h1>

    <!-- Lista de agentes -->
    <div id="agentsContainer"></div>
</div>

<script>
    // Datos de agentes (ficticios pero realistas)
    const agentsData = [
        {
            id: 1,
            name: "Carlos Rodríguez Pérez",
            badge: "TP-2024-001",
            position: "Especialista en infracciones viales urbanas",
            phone: "3104567890",
            email: "carlos.rodriguez@transito.gov.co",
            experience: 12,
            rating: 4.8,
            reviews: 124
        },
        {
            id: 2,
            name: "María González López",
            badge: "TP-2024-002",
            position: "Agente de carreteras - Nivel 1",
            phone: "3117654321",
            email: "maria.gonzalez@transito.gov.co",
            experience: 8,
            rating: 4.5,
            reviews: 98
        },
        {
            id: 3,
            name: "Juan Pablo Martínez",
            badge: "TP-2024-003",
            position: "Coordinador de zona urbana central",
            phone: "3125678901",
            email: "juan.martinez@transito.gov.co",
            experience: 15,
            rating: 4.9,
            reviews: 210
        },
        {
            id: 4,
            name: "Ana Sofía Guzmán",
            badge: "TP-2024-004",
            position: "Especialista en motocicletas y seguridad vial",
            phone: "3209876543",
            email: "ana.guzman@transito.gov.co",
            experience: 6,
            rating: 4.3,
            reviews: 56
        },
        {
            id: 5,
            name: "Ricardo Sánchez Villegas",
            badge: "TP-2024-005",
            position: "Investigador de accidentes graves",
            phone: "3184567891",
            email: "ricardo.sanchez@transito.gov.co",
            experience: 10,
            rating: 4.7,
            reviews: 88
        },
        {
            id: 6,
            name: "Laura Elena Restrepo",
            badge: "TP-2024-006",
            position: "Agente de control de alcoholemia",
            phone: "3001234567",
            email: "laura.restrepo@transito.gov.co",
            experience: 5,
            rating: 4.4,
            reviews: 43
        },
        {
            id: 7,
            name: "Fernando Hoyos Duque",
            badge: "TP-2024-007",
            position: "Comandante de operaciones especiales",
            phone: "3112345678",
            email: "fernando.hoyos@transito.gov.co",
            experience: 18,
            rating: 5.0,
            reviews: 312
        }
    ];

    // Cargar calificaciones guardadas (si las hay) desde localStorage
    let ratings = JSON.parse(localStorage.getItem('agentRatings')) || {};

    function saveRatings() {
        localStorage.setItem('agentRatings', JSON.stringify(ratings));
    }

    function renderStars(agentId, currentRating) {
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            const active = (ratings[agentId] || currentRating) >= i ? 'active' : '';
            starsHtml += `<span class="star ${active}" data-agent="${agentId}" data-star="${i}">★</span>`;
        }
        return starsHtml;
    }

    function renderAgents() {
        const container = document.getElementById('agentsContainer');
        container.innerHTML = agentsData.map(agent => `
            <div class="agent-card">
                <div class="agent-header">
                    <div>
                        <div class="agent-name">${agent.name}</div>
                        <div class="agent-id">${agent.badge}</div>
                    </div>
                    <span class="agent-badge">${agent.position}</span>
                </div>
                <div class="agent-details">
                    <span><i class="fa-solid fa-briefcase"></i> ${agent.experience} años de servicio</span>
                    <span><i class="fa-solid fa-star" style="color: #fbbf24;"></i> ${agent.rating} (${agent.reviews} reseñas)</span>
                </div>
                <div class="agent-details">
                    <span><i class="fa-solid fa-phone"></i> ${agent.phone}</span>
                    <span><i class="fa-solid fa-envelope"></i> ${agent.email}</span>
                </div>
                <div class="rating-stars" data-agent="${agent.id}">
                    ${renderStars(agent.id, agent.rating)}
                </div>
                <button class="agent-contact-btn" data-phone="${agent.phone}">
                    <i class="fa-solid fa-message"></i> Contactar
                </button>
            </div>
        `).join('');

        // Añadir eventos a las estrellas
        document.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', (e) => {
                e.stopPropagation();
                const agentId = parseInt(star.dataset.agent);
                const starValue = parseInt(star.dataset.star);
                ratings[agentId] = starValue;
                saveRatings();
                renderAgents(); // refrescar para mostrar estrellas activas
            });
        });

        // Eventos de botón contactar
        document.querySelectorAll('.agent-contact-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const phone = btn.dataset.phone;
                alert(`Número de contacto: ${phone}\n(Simulación - en una app real abriría WhatsApp o llamada)`);
            });
        });
    }

    // Control del sidebar
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

    // Inicializar
    renderAgents();
</script>
</body>
</html>