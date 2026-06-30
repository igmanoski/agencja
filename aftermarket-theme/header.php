<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><text y='24' font-size='24'>A</text></svg>">
  
  <svg style="position:absolute;width:0;height:0;overflow:hidden" aria-hidden="true">
    <defs>
      <linearGradient id="gPink" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#F43F5E" stop-opacity="0.5"/>
        <stop offset="100%" stop-color="#F43F5E" stop-opacity="0"/>
      </linearGradient>
      <linearGradient id="gBlue" x1="0" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#3B82F6" stop-opacity="0.4"/>
        <stop offset="100%" stop-color="#3B82F6" stop-opacity="0"/>
      </linearGradient>
    </defs>
  </svg>
  
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- GLOBAL AMBIENT BACKGROUND -->
<div class="global-ambient-bg" style="position: fixed; inset: 0; z-index: -1; pointer-events: none; overflow: hidden; background: #040408;">
  <div class="orb orb-pk" id="orb-1" style="top: -10%; left: -10%; width: 60vw; height: 60vw; opacity: 0.08; filter: blur(140px); animation: orbDrift 30s ease-in-out infinite alternate;"></div>
  <div class="orb orb-bl" id="orb-2" style="bottom: -10%; right: -10%; width: 70vw; height: 70vw; opacity: 0.07; filter: blur(140px); animation: orbDrift 35s ease-in-out infinite alternate-reverse;"></div>
  <div class="orb orb-pu" id="orb-3" style="top: 40%; right: 10%; width: 50vw; height: 50vw; opacity: 0.05; filter: blur(140px); animation: orbDrift 25s ease-in-out infinite alternate;"></div>
  <div class="orb orb-bl" id="orb-4" style="bottom: 30%; left: 10%; width: 45vw; height: 45vw; opacity: 0.05; filter: blur(140px); animation: orbDrift 33s ease-in-out infinite alternate-reverse;"></div>
  
  <div id="bg-cursor-glow" style="position: absolute; width: 900px; height: 900px; background: radial-gradient(circle, rgba(99, 102, 241, 0.07) 0%, rgba(244, 63, 94, 0.03) 45%, transparent 70%); border-radius: 50%; filter: blur(80px); pointer-events: none; z-index: 1; transform: translate(-50%, -50%); top: -1000px; left: -1000px; transition: opacity 0.5s ease;"></div>
  
  <canvas id="bg-particles" style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0.65; z-index: 2; pointer-events: none;"></canvas>
  
  <div id="bg-grid-back" style="position: absolute; inset: -150px; background-image: radial-gradient(rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 28px 28px; z-index: 3; pointer-events: none; opacity: 0.5;"></div>
  <div id="bg-grid-front" style="position: absolute; inset: -150px; background-image: radial-gradient(rgba(255,255,255,0.065) 1.5px, transparent 1.5px); background-size: 56px 56px; z-index: 4; pointer-events: none; opacity: 0.65;"></div>
</div>

<!-- HEADER -->
<header>
  <div class="wrap nav-inner">
    <a href="<?php echo esc_url(home_url('/')); ?>" class="logo" id="logo-link">Aftermarket<span class="logo-dot">.</span></a>
    <nav class="nav-group">
      <?php
      // Wykryj aktywną stronę na podstawie szablonu
      $dashboard_page = get_page_by_path('dashboard');
      $dashboard_url  = $dashboard_page ? get_permalink($dashboard_page) : home_url('/dashboard/');
      $is_dashboard   = $dashboard_page && is_page($dashboard_page->ID);
      ?>
      <a href="<?php echo esc_url(home_url('/#giveaway')); ?>" id="nav-g"<?php echo !$is_dashboard ? ' class="on"' : ''; ?>>Konkurs</a>
      <a href="<?php echo esc_url(home_url('/#sponsors')); ?>" id="nav-s">Sponsorzy</a>
      <a href="<?php echo esc_url($dashboard_url); ?>" id="nav-d"<?php echo $is_dashboard ? ' class="on"' : ''; ?>>Dashboard</a>
    </nav>
    <a href="<?php echo esc_url(home_url('/#sponsors')); ?>" class="btn btn-p btn-sm mob-hide" id="cta-nav">Dołącz jako Sponsor</a>
  </div>
</header>
