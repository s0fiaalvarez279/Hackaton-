<?php
$APP_NAME = 'RutaX · Medellín Movilidad OS';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Abogados Especializados - <?php echo htmlspecialchars($APP_NAME); ?></title>
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
        
        /* ========== TARJETAS DE ABOGADOS ========== */
        .lawyer-card {
            background: #1e293b;
            border-radius: 0.75rem;
            padding: 1.2rem;
            margin-bottom: 1rem;
            border: 1px solid var(--border);
            transition: transform 0.2s, border-color 0.2s;
        }
        .lawyer-card:hover {
            transform: translateX(5px);
            border-color: var(--accent);
        }
        .lawyer-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 0.5rem;
        }
        .lawyer-name {
            font-size: 1.2rem;
            font-weight: 700;
            color: #fbbf24;
        }
        .lawyer-badge {
            background: #1e3a8a;
            padding: 0.2rem 0.6rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .type-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 1rem;
            font-weight: 600;
        }
        .type-private {
            background: #fb923c;
            color: #0f172a;
        }
        .type-public {
            background: #4ade80;
            color: #0f172a;
        }
        .lawyer-desc {
            color: #94a3b8;
            font-size: 0.85rem;
            margin: 0.5rem 0;
        }
        .rating-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-top: 0.5rem;
        }
        .rating-stars {
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }
        .star {
            color: #64748b;
            cursor: pointer;
            font-size: 1rem;
            transition: color 0.1s;
        }
        .star.active {
            color: #fbbf24;
        }
        .star:hover {
            color: #fde047;
        }
        .rating-average {
            font-weight: 700;
            color: #fbbf24;
        }
        .rating-count {
            font-size: 0.7rem;
            color: var(--muted);
        }
        .contact-btn {
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
        .contact-btn:hover {
            background: #1e5a9c;
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
    </style>
</head>
<body>

<!-- BARRA SUPERIOR -->
<div class="topbar">
    <button class="pill" id="btnMenu"><i class="fa-solid fa-bars"></i><span>Menú</span></button>
    <div class="pill" style="cursor:default">
        <i class="fa-solid fa-car-side logo-icon"></i>
        <span class="live"></span>
        <span class="app-title-text"><?php echo htmlspecialchars($APP_NAME); ?></span>
        <span class="sm:hidden">Abogados</span>
    </div>
    <div style="display: flex; gap: 0.5rem;">
        <a href="index.php" class="pill"><i class="fa-solid fa-map"></i><span>Mapa</span></a>
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
            <a href="infracciones.php"><i class="fa-solid fa-file-lines"></i> Infracciones</a>
            <a href="agentes.php"><i class="fa-solid fa-shield-halved"></i> Agentes</a>
            <a href="abogados.php" class="active"><i class="fa-solid fa-gavel"></i> Abogados</a>
            <a href="#"><i class="fa-solid fa-users"></i> Veedores</a>
            <a href="#"><i class="fa-solid fa-gavel"></i> Audiencias</a>
            <a href="#"><i class="fa-solid fa-chart-pie"></i> Reportes</a>
        </nav>
    </div>
    <footer>RutaX · TransiControl · SIMIT · Asesoría legal</footer>
</aside>

<!-- CONTENIDO PRINCIPAL -->
<div class="main-content" id="mainContent">
    <h1 class="section-title">⚖️ Abogados Especializados</h1>
    <div id="lawyersContainer"></div>
</div>

<script>
    // Datos de abogados
    const lawyersData = [
        {
            id: 1,
            name: "Dr. Felipe Castro García",
            type: "Privado",
            description: "Defensa especializada en infracciones graves",
            phone: "3104521234",
            email: "felipe.castro@abogados.co",
            experience: 18,
            rating: 4.9,
            reviews: 45,
            price: "$200,000/consulta"
        },
        {
            id: 2,
            name: "Dra. Laura Estrada Moreno",
            type: "Oficio",
            description: "Abogada de oficio con 500+ casos",
            phone: "3116543210",
            email: "laura.estrada@abogados.co",
            experience: 12,
            rating: 4.6,
            reviews: 38,
            price: "Gratis (Oficio)"
        },
        {
            id: 3,
            name: "Dr. Andrés Vélez López",
            type: "Privado",
            description: "Especialista en licencias de conducción",
            phone: "3125432109",
            email: "andres.velez@abogados.co",
            experience: 20,
            rating: 5.0,
            reviews: 52,
            price: "$250,000/consulta"
        },
        {
            id: 4,
            name: "Dra. Catalina Rojas",
            type: "Oficio",
            description: "Abogada de oficio joven y dinámica",
            phone: "3209123456",
            email: "catalina.rojas@abogados.co",
            experience: 8,
            rating: 4.4,
            reviews: 25,
            price: "Gratis (Oficio)"
        }
    ];

    // Cargar calificaciones guardadas (localStorage)
    let ratings = JSON.parse(localStorage.getItem('lawyerRatings')) || {};

    function saveRatings() {
        localStorage.setItem('lawyerRatings', JSON.stringify(ratings));
    }

    function renderStars(lawyerId, baseRating) {
        const currentRating = ratings[lawyerId] || baseRating;
        let starsHtml = '';
        for (let i = 1; i <= 5; i++) {
            const active = currentRating >= i ? 'active' : '';
            starsHtml += `<span class="star ${active}" data-lawyer="${lawyerId}" data-star="${i}">★</span>`;
        }
        return starsHtml;
    }

    function renderLawyers() {
        const container = document.getElementById('lawyersContainer');
        container.innerHTML = lawyersData.map(lawyer => `
            <div class="lawyer-card">
                <div class="lawyer-header">
                    <div class="lawyer-name">${lawyer.name}</div>
                    <span class="type-badge ${lawyer.type === 'Privado' ? 'type-private' : 'type-public'}">${lawyer.type}</span>
                </div>
                <div class="lawyer-desc">${lawyer.description}</div>
                <div class="rating-row">
                    <div class="rating-stars" data-lawyer="${lawyer.id}">
                        ${renderStars(lawyer.id, lawyer.rating)}
                    </div>
                    <span class="rating-average">${(ratings[lawyer.id] || lawyer.rating).toFixed(1)}</span>
                    <span class="rating-count">${lawyer.reviews} reseñas</span>
                </div>
                <div class="lawyer-desc" style="margin-top: 0.25rem;">
                    <i class="fa-solid fa-briefcase"></i> ${lawyer.experience} años de experiencia<br>
                    <i class="fa-solid fa-phone"></i> ${lawyer.phone}<br>
                    <i class="fa-solid fa-envelope"></i> ${lawyer.email}<br>
                    <i class="fa-solid fa-tag"></i> ${lawyer.price}
                </div>
                <button class="contact-btn" data-phone="${lawyer.phone}" data-name="${lawyer.name}">
                    <i class="fa-solid fa-calendar-check"></i> Solicitar consulta
                </button>
            </div>
        `).join('');

        // Eventos de las estrellas
        document.querySelectorAll('.star').forEach(star => {
            star.addEventListener('click', (e) => {
                e.stopPropagation();
                const lawyerId = parseInt(star.dataset.lawyer);
                const starValue = parseInt(star.dataset.star);
                ratings[lawyerId] = starValue;
                saveRatings();
                renderLawyers();
            });
        });

        // Eventos de botón consulta
        document.querySelectorAll('.contact-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const name = btn.dataset.name;
                const phone = btn.dataset.phone;
                alert(`📞 Solicitud enviada a ${name}\nTeléfono: ${phone}\n(Un asesor se comunicará pronto)`);
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
    renderLawyers();
</script>
</body>
</html>