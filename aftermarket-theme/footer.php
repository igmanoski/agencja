<!-- ████ FOOTER ████ -->
<footer>
  <div class="wrap">
    <div class="foot-grid">
      <div class="foot-brand">
        <div class="logo">Aftermarket<span class="logo-dot">.</span></div>
        <p>Wiodąca agencja wzrostu na polskim Instagramie. Dostarczamy markom wymierne rezultaty &ndash; a uczestnikom wyjątkowe nagrody.</p>
      </div>
      <div class="foot-col">
        <h5>Platforma</h5>
        <div class="foot-links">
          <a href="<?php echo esc_url(home_url('/#sponsors')); ?>">Dla Sponsorów</a>
          <a href="<?php echo esc_url(home_url('/#giveaway')); ?>">Dla Uczestników</a>
          <a href="<?php
            $dash = get_page_by_path('dashboard');
            echo esc_url($dash ? get_permalink($dash) : home_url('/dashboard/'));
          ?>">Panel Sponsora</a>
        </div>
      </div>
      <div class="foot-col">
        <h5>Kontakt</h5>
        <div class="foot-links">
          <a href="mailto:kontakt.aftermarket@onet.pl">kontakt.aftermarket@onet.pl</a>
          <a href="https://www.instagram.com/aftermarket.ag/" target="_blank" rel="noopener noreferrer">@aftermarket.ag</a>
        </div>
      </div>
    </div>
    <div class="foot-btm" style="flex-wrap: wrap; gap: 15px;">
      <div class="foot-copy">
        &copy; <?php echo date('Y'); ?> Aftermarket. All rights reserved. &bull; 
        <a href="<?php echo esc_url(home_url('/regulamin/')); ?>" style="color: var(--t3); text-decoration: none; margin-left: 5px;">Regulamin</a> &bull; 
        <a href="<?php echo esc_url(home_url('/polityka-prywatnosci/')); ?>" style="color: var(--t3); text-decoration: none; margin-left: 5px;">Polityka prywatności</a>
      </div>
      <div style="display:flex;align-items:center;gap:8px;">
        <div class="pulse-dot" style="width:8px;height:8px;"></div>
        <span style="font-size:.76rem;color:var(--t3);">Status systemu &ndash; <span style="color:var(--green);font-weight:700;">Online</span></span>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>