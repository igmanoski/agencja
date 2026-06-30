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
    <div class="foot-btm">
      <div class="foot-copy">&copy; <?php echo date('Y'); ?> Aftermarket Premium Instagram Growth Agency. Wszystkie prawa zastrzeżone.</div>
      <div style="display:flex;align-items:center;gap:8px;">
        <div class="pulse-dot" style="width:8px;height:8px;"></div>
        <span style="font-size:.76rem;color:var(--t3);">System operacyjny &ndash; Status: <span style="color:var(--green);font-weight:700;">online</span></span>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>