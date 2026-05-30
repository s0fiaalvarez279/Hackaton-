<?php
$abogados = [
    ["id" => 1, "name" => "Dr. Carlos Mendoza", "rating" => 5, "successRate" => "92%", "phone" => "+57 300 123 4567"],
    ["id" => 2, "name" => "Dra. Laura Restrepo", "rating" => 4, "successRate" => "87%", "phone" => "+57 315 987 6543"]
];
?>

<div class="profile-grid">
    <?php foreach ($abogados as $lawyer): ?>
        <div class="profile-card" style="cursor: pointer;">
            <div style="display: flex; align-items: center; gap: 14px;">
                <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #0F4C81, #1E88E5); color: #fff; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div>
                    <h3 style="font-size: 1rem; font-weight: 700; color: #1E293B; margin: 0;"><?php echo htmlspecialchars($lawyer['name']); ?></h3>
                    <span class="infrac-penalty" style="background: #EFF6FF; color: #0F4C81;">Esp. Tránsito</span>
                </div>
            </div>

            <div class="rating-stars">
                <?php for ($star = 1; $star <= 5; $star++): ?>
                    <span class="star-btn <?php echo $star <= $lawyer['rating'] ? 'active' : ''; ?>" style="font-size: 14px; padding: 2px 4px;">★</span>
                <?php endfor; ?>
            </div>

            <div class="contact-row">
                <i class="fas fa-briefcase" style="color: #8BA0BC;"></i>
                <span>Casos Éxito: <?php echo htmlspecialchars($lawyer['successRate']); ?></span>
            </div>
            
            <div class="contact-row">
                <i class="fas fa-phone" style="color: #8BA0BC;"></i>
                <span><?php echo htmlspecialchars($lawyer['phone']); ?></span>
            </div>

            <button class="btn-acceder login-btn" style="padding: 8px 16px; font-size: 12px; margin-top: 12px; justify-content: center; width: 100%;">
                <i class="fas fa-envelope"></i> Contactar Consultoría
            </button>
        </div>
    <?php endforeach; ?>
</div>