<?php
/**
 * Template: front-page.php
 * Automatycznie używany przez WordPress jako szablon strony głównej.
 * NIE dodawaj tu "Template Name:" bo WP przestanie go traktować jako front-page!
 */

get_header();
?>

<!-- ████████████████████████████████████████
     PAGE: SPONSOR B2B  (#sponsors)
████████████████████████████████████████ -->
<main id="view-s" class="pv">

  <!-- Hero -->
  <div class="scene" id="s-hero">
    <style>
      @keyframes shimmerLink {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
      }
      @media (max-width: 991px) {
        #s-hero section.wrap.hero {
          flex-direction: column !important;
          text-align: center !important;
          padding-top: 195px !important;
          gap: 30px !important;
        }
        .s-hero-left { text-align: center !important; max-width: 100% !important; }
        .s-hero-left .hero-sub { text-align: center !important; margin: 0 auto 36px !important; }
        .s-hero-left .hero-ctas { justify-content: center !important; }
      }
      @media (max-width: 767px) {
        .s-hero-left h1 { font-size: 1.55rem !important; line-height: 1.2 !important; padding: 0 8px; }
        .s-hero-left .hero-sub { font-size: 0.84rem !important; line-height: 1.55 !important; padding: 0 16px; margin-bottom: 42px !important; }
      }
    </style>
    <section class="wrap hero rev" style="position: relative; padding-top: 190px; display: flex; align-items: center; justify-content: space-between; gap: 40px; flex-wrap: wrap;">
      <div style="position: absolute; inset: 0; background-image: radial-gradient(rgba(255,255,255,0.015) 1px, transparent 1px); background-size: 24px 24px; opacity: 0.8; pointer-events: none; z-index: 0;"></div>
      <div style="position: absolute; top: 12%; left: 5%; width: 350px; height: 350px; background: radial-gradient(circle, rgba(59, 130, 246, 0.08) 0%, rgba(168, 85, 247, 0.03) 65%, transparent 100%); filter: blur(50px); pointer-events: none; z-index: 0;"></div>

      <div style="position: absolute; top: 12%; left: 0%; width: 100%; max-width: 550px; height: 320px; opacity: 0.08; pointer-events: none; z-index: 0;">
        <svg viewBox="0 0 200 120" style="width: 100%; height: 100%;" preserveAspectRatio="none">
          <defs>
            <linearGradient id="growthGrad" x1="0%" y1="100%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="var(--blue)" stop-opacity="0.2"/>
              <stop offset="50%" stop-color="var(--pink)" stop-opacity="0.8"/>
              <stop offset="100%" stop-color="var(--pink)" stop-opacity="1"/>
            </linearGradient>
            <linearGradient id="growthFill" x1="0%" y1="100%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="var(--blue)" stop-opacity="0.0"/>
              <stop offset="100%" stop-color="var(--pink)" stop-opacity="0.08"/>
            </linearGradient>
          </defs>
          <path d="M 10 110 Q 50 100 90 70 T 170 30 L 170 110 Z" fill="url(#growthFill)" />
          <path d="M 10 110 Q 50 100 90 70 T 170 30" fill="none" stroke="url(#growthGrad)" stroke-width="3" stroke-linecap="round" />
          <path d="M 158 32 L 170 30 L 168 42" fill="none" stroke="url(#growthGrad)" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
      </div>

      <!-- Lewa kolumna: Tekst i CTA -->
      <div style="flex: 1 1 500px; max-width: 650px; text-align: left;" class="s-hero-left">
        <h1 style="font-size: clamp(2.0rem, 4.5vw, 3.4rem); line-height: 1.15; letter-spacing: -0.01em; word-spacing: 0.03em; text-transform: uppercase; font-weight: 900; filter: drop-shadow(0 0 2px #000) drop-shadow(0 4px 16px rgba(0,0,0,0.8)); margin-bottom: 24px;">
          <span class="grad" style="background: linear-gradient(90deg, #FFFFFF 20%, var(--blue) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo esc_html(am_field('s_hero_title_line_1', 'Skaluj swój biznes i buduj')); ?>
          </span><br>
          <span class="grad" style="background: linear-gradient(90deg, #FFFFFF 20%, var(--pink) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
            <?php echo esc_html(am_field('s_hero_title_line_2', 'wizerunek na Instagramie')); ?>
          </span>
        </h1>
        <p class="hero-sub" style="margin: 0 0 36px; text-align: left; max-width: 560px;">
          <?php echo esc_html(am_field('s_hero_subtitle', 'Połącz swoją markę z największymi konkursami na polskim Instagramie. Gwarantowany, organiczny wzrost i natychmiastowy skok rozpoznawalności.')); ?>
        </p>
        <div class="hero-ctas" style="justify-content: flex-start;">
          <a href="#s-pricing" class="btn btn-p btn-lg" id="hero-cta" style="font-size: 1.15rem; padding: 18px 46px;">Zabezpiecz Swój Slot</a>
        </div>
      </div>

      <!-- Prawa kolumna: Smartfon -->
      <div style="flex: 1 1 350px; display: flex; justify-content: center; align-items: center; position: relative;" class="s-hero-right">
        <div style="position: absolute; width: 380px; height: 380px; background: radial-gradient(circle, rgba(168, 85, 247, 0.22) 0%, rgba(244, 63, 94, 0.08) 50%, transparent 70%); filter: blur(50px); pointer-events: none; z-index: 0;"></div>

        <div class="iphone-17-pro" style="position: relative; z-index: 1; width: 310px; height: 630px; border-radius: 46px; background: #000; padding: 7px; box-shadow: 0 25px 60px -10px rgba(0,0,0,0.9), 0 0 0 1px rgba(255,255,255,0.06), 0 0 25px rgba(168,85,247,0.12); display: flex; flex-direction: column;">
          <div style="position: absolute; inset: 0; border: 3px solid #2d2d38; border-radius: 46px; pointer-events: none; z-index: 20; box-shadow: inset 0 0 4px rgba(255,255,255,0.15);"></div>
          <div style="position: absolute; top: 7px; left: 7px; right: 7px; bottom: 7px; border-radius: 39px; background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 40%); pointer-events: none; z-index: 15;"></div>

          <div style="width: 100%; height: 100%; border-radius: 39px; background: #08080c; overflow: hidden; display: flex; flex-direction: column; position: relative; border: 1px solid #14141d;">
            <!-- iOS Status Bar -->
            <div style="height: 40px; display: flex; justify-content: space-between; align-items: center; padding: 0 22px; position: absolute; top: 0; left: 0; width: 100%; z-index: 10; font-size: 0.72rem; font-weight: 700; color: #ffffff;">
              <span style="font-family: var(--fb);">15:24</span>
              <div style="width: 55px; height: 15px; background: #000; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); display: flex; align-items: center; justify-content: center; position: absolute; left: 50%; transform: translateX(-50%); gap: 4px; padding: 0 6px;">
                <div style="width: 4px; height: 4px; background: #0c0c24; border-radius: 50%; border: 1.5px solid #1a1a3a;"></div>
                <div style="width: 14px; height: 2px; background: #111; border-radius: 1px;"></div>
              </div>
              <div style="display: flex; align-items: center; gap: 5px;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor"><path d="M12 3c-4.97 0-9 4.03-9 9 0 2.12.74 4.07 1.97 5.61L4.35 19.4c-.39.39-.39 1.02 0 1.41.39.39 1.02.39 1.41 0l1.9-1.9C9.17 19.58 10.53 20 12 20c4.97 0 9-4.03 9-9s-4.03-9-9-9zm0 15c-3.31 0-6-2.69-6-6s2.69-6 6-6 6 2.69 6 6-2.69 6-6 6z"></path></svg>
                <span style="font-size: 0.65rem; font-family: var(--fb);">5G</span>
                <div style="width: 17px; height: 9px; border: 1px solid #ffffff; border-radius: 3px; padding: 1px; display: flex;">
                  <div style="width: 100%; height: 100%; background: #ffffff; border-radius: 1px;"></div>
                </div>
              </div>
            </div>

            <!-- Instagram Top Bar -->
            <div style="padding: 42px 14px 10px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(12,12,20,0.8); backdrop-filter: blur(15px); z-index: 9;">
              <div style="display: flex; align-items: center; gap: 8px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="color: #ffffff;"><polyline points="15 18 9 12 15 6"></polyline></svg>
                <span style="font-weight: 800; font-size: 0.95rem; letter-spacing: -0.01em; display: flex; align-items: center; gap: 4px; font-family: var(--fh);">
                  <?php echo esc_html(am_field('ig_username', 'aftermarket.ag')); ?>
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor" style="color: #0095f6; flex-shrink: 0;"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"></path></svg>
                </span>
              </div>
              <div style="display: flex; gap: 14px; color: #ffffff; align-items: center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"></circle><circle cx="12" cy="5" r="1"></circle><circle cx="12" cy="19" r="1"></circle></svg>
              </div>
            </div>

            <!-- Feed Content -->
            <div style="flex: 1; overflow-y: hidden; overflow-x: hidden; padding-bottom: 50px; scrollbar-width: none;">
              <div style="padding: 14px 14px 8px; display: flex; flex-direction: column; gap: 12px; background: linear-gradient(180deg, rgba(168,85,247,0.06) 0%, transparent 100%);">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 10px;">
                  <div style="position: relative; width: 66px; height: 66px; border-radius: 50%; padding: 2.5px; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); flex-shrink: 0;">
                    <div style="width: 100%; height: 100%; border-radius: 50%; background: #ffffff; padding: 1.5px; overflow: hidden; display: flex; align-items: center; justify-content: center;">
                      <?php
                      $avatar = am_field('ig_avatar', false);
                      if ($avatar && is_array($avatar) && isset($avatar['url'])):
                      ?>
                        <img src="<?php echo esc_url($avatar['url']); ?>" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Logo">
                      <?php else: ?>
                        <img src="<?php echo get_template_directory_uri(); ?>/logo_ig.jpg" style="width:100%;height:100%;object-fit:cover;border-radius:50%;" alt="Logo">
                      <?php endif; ?>
                    </div>
                    <div style="position: absolute; bottom: 1px; right: 1px; width: 13px; height: 13px; background: var(--green); border-radius: 50%; border: 2px solid #08080c; box-shadow: 0 0 10px var(--green);"></div>
                  </div>

                  <div style="display: flex; justify-content: space-between; flex-grow: 1; text-align: center; font-family: var(--fb); margin-left: 2px; gap: 6px;">
                    <div style="flex: 1; min-width: 0;">
                      <div style="font-weight: 800; font-size: 0.9rem; color: #ffffff;">142</div>
                      <div style="font-size: 0.5rem; color: var(--t2); margin-top: 2px; white-space: nowrap; letter-spacing: -0.01em;">posty</div>
                    </div>
                    <div style="flex: 1; min-width: 0; position: relative; left: -5px;">
                      <div id="phone-followers-count" style="font-weight: 800; font-size: 0.9rem; color: #ffffff; letter-spacing: -0.02em; transition: transform 0.12s ease, color 0.12s ease; text-shadow: 0 0 12px rgba(255,255,255,0.15); white-space: nowrap;">
                        <?php echo esc_html(am_field('ig_followers_text', '55,3 tys.')); ?>
                      </div>
                      <div style="font-size: 0.5rem; color: var(--t2); margin-top: 2px; white-space: nowrap; letter-spacing: -0.01em;">obserwujących</div>
                      <div id="phone-followers-badge" style="position: absolute; top: -16px; left: 50%; transform: translateX(-50%) scale(0.7); background: var(--green); color: #000; font-weight: 900; font-size: 0.62rem; padding: 1px 6px; border-radius: var(--r-pill); opacity: 0; transition: all 0.3s var(--spring); pointer-events: none; white-space: nowrap; box-shadow: 0 4px 10px rgba(16,185,129,0.3);">+1,420</div>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                      <div style="font-weight: 800; font-size: 0.9rem; color: #ffffff;">81</div>
                      <div style="font-size: 0.5rem; color: var(--t2); margin-top: 2px; white-space: nowrap; letter-spacing: -0.01em;">obserwowanych</div>
                    </div>
                  </div>
                </div>

                <div style="font-family: var(--fb);">
                  <h4 style="font-weight: 700; font-size: 0.82rem; color: #ffffff; margin: 0 0 1px;">
                    <?php echo esc_html(am_field('ig_name_desc', 'Aftermarket. | Agencja Marketingowa')); ?>
                  </h4>
                  <span style="font-size: 0.65rem; color: var(--t2); font-weight: 500;">Agencja reklamowa / social media</span>
                  <p style="font-size: 0.72rem; color: #dddde5; line-height: 1.4; margin: 5px 0 0; font-weight: 400;">
                    <?php echo esc_html(am_field('ig_bio', '🚀 Skalujemy przychody i budujemy silne marki osobiste.')); ?>
                  </p>
                  <a href="#" style="font-size: 0.72rem; text-decoration: none; font-weight: 700; display: inline-flex; align-items: center; gap: 4px; margin-top: 5px; background: linear-gradient(90deg, #F43F5E, #3B82F6, #A855F7, #F43F5E); background-size: 200% auto; -webkit-background-clip: text; -webkit-text-fill-color: transparent; animation: shimmerLink 3s linear infinite;">
                    <?php echo esc_html(am_field('ig_website', 'aftermarket.ag')); ?>
                  </a>
                </div>

                <div style="display: flex; gap: 6px; margin-top: 4px;">
                  <div style="flex: 1; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.06); color: #ffffff; font-weight: 700; font-size: 0.72rem; text-align: center; padding: 7px; border-radius: var(--r-xs);">Edytuj profil</div>
                  <div style="flex: 1; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.06); color: #ffffff; font-weight: 700; font-size: 0.72rem; text-align: center; padding: 7px; border-radius: var(--r-xs);">Udostępnij</div>
                  <div style="width: 30px; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; border-radius: var(--r-xs);">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><line x1="20" y1="8" x2="20" y2="14"></line><line x1="23" y1="11" x2="17" y2="11"></line></svg>
                  </div>
                </div>
              </div>

              <!-- Stories Highlights -->
              <div style="display: flex; gap: 12px; padding: 8px 14px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); overflow-x: auto; background: rgba(8,8,12,0.3);">
                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; flex-shrink: 0;">
                  <div style="width: 48px; height: 48px; border-radius: 50%; padding: 2px; border: 1px solid rgba(255,255,255,0.15); background: #08080c; display: flex; align-items: center; justify-content: center;">
                    <div style="width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg, rgba(244,63,94,0.15), rgba(168,85,247,0.15)); display: flex; align-items: center; justify-content: center; color: var(--pink);">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                    </div>
                  </div>
                  <span style="font-size: 0.58rem; color: var(--t2); font-weight: 500;">Konkursy</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; flex-shrink: 0;">
                  <div style="width: 48px; height: 48px; border-radius: 50%; padding: 2px; border: 1px solid rgba(255,255,255,0.15); background: #08080c; display: flex; align-items: center; justify-content: center;">
                    <div style="width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg, rgba(59,130,246,0.15), rgba(168,85,247,0.15)); display: flex; align-items: center; justify-content: center; color: var(--blue);">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                    </div>
                  </div>
                  <span style="font-size: 0.58rem; color: var(--t2); font-weight: 500;">Wyniki B2B</span>
                </div>
                <div style="display: flex; flex-direction: column; align-items: center; gap: 4px; flex-shrink: 0;">
                  <div style="width: 48px; height: 48px; border-radius: 50%; padding: 2px; border: 1px solid rgba(255,255,255,0.15); background: #08080c; display: flex; align-items: center; justify-content: center;">
                    <div style="width: 100%; height: 100%; border-radius: 50%; background: rgba(255,255,255,0.04); display: flex; align-items: center; justify-content: center; color: #fff;">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                    </div>
                  </div>
                  <span style="font-size: 0.58rem; color: var(--t2); font-weight: 500;">Opinie</span>
                </div>
              </div>

              <!-- Grid Tab Headers -->
              <div style="display: flex; justify-content: space-around; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(8,8,12,0.2);">
                <div style="flex: 1; text-align: center; padding: 10px 0; border-bottom: 1.5px solid #ffffff; color: #ffffff;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
                </div>
                <div style="flex: 1; text-align: center; padding: 10px 0; color: var(--t2);">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                </div>
                <div style="flex: 1; text-align: center; padding: 10px 0; color: var(--t2);">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
              </div>

              <!-- Feed Grid -->
              <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 2.5px; padding: 2.5px 14px 14px;">
                <div style="aspect-ratio: 1; background: linear-gradient(135deg, #1e1b4b, #311042); border-radius: 4px; display: flex; flex-direction: column; justify-content: space-between; padding: 6px; border: 1px solid rgba(255,255,255,0.03);">
                  <span style="font-size: 0.45rem; font-weight: 800; text-transform: uppercase; color: var(--pink); background: rgba(0,0,0,0.5); padding: 1px 3px; border-radius: 2px; align-self: flex-start;">ANALIZA</span>
                  <div style="display: flex; flex-direction: column; gap: 3px;">
                    <div style="width: 100%; height: 2px; background: rgba(255,255,255,0.3); border-radius: 1px;"></div>
                    <div style="width: 80%; height: 2px; background: rgba(255,255,255,0.3); border-radius: 1px;"></div>
                  </div>
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.5rem; font-weight: 800; color: #fff;">+420%</span>
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor" style="color: var(--pink);"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
                  </div>
                </div>
                <div style="aspect-ratio: 1; background: linear-gradient(135deg, #093333, #061c30); border-radius: 4px; display: flex; flex-direction: column; justify-content: space-between; padding: 6px; border: 1px solid rgba(255,255,255,0.03);">
                  <span style="font-size: 0.45rem; font-weight: 800; text-transform: uppercase; color: var(--green); background: rgba(0,0,0,0.5); padding: 1px 3px; border-radius: 2px; align-self: flex-start;">KAMPANIA</span>
                  <div style="width: 26px; height: 26px; background: radial-gradient(circle, var(--blue) 0%, transparent 80%); border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #fff;"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect></svg>
                  </div>
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.5rem; font-weight: 800; color: #fff;">LIVE</span>
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor" style="color: var(--green);"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
                  </div>
                </div>
                <div style="aspect-ratio: 1; background: linear-gradient(135deg, #3c091d, #140b28); border-radius: 4px; display: flex; flex-direction: column; justify-content: space-between; padding: 6px; border: 1px solid rgba(255,255,255,0.03);">
                  <span style="font-size: 0.45rem; font-weight: 800; text-transform: uppercase; color: var(--purple); background: rgba(0,0,0,0.5); padding: 1px 3px; border-radius: 2px; align-self: flex-start;">BRANDING</span>
                  <svg width="100%" height="15" viewBox="0 0 100 30" preserveAspectRatio="none" style="opacity: 0.6;"><path d="M0,15 C20,30 40,0 60,15 C80,30 100,5 100,5" fill="none" stroke="var(--pink)" stroke-width="3"></path></svg>
                  <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.5rem; font-weight: 800; color: #fff;">IG PRO</span>
                    <svg width="8" height="8" viewBox="0 0 24 24" fill="currentColor" style="color: var(--purple);"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"></path></svg>
                  </div>
                </div>
              </div>
            </div>

            <!-- Instagram Bottom Nav -->
            <div style="position: absolute; bottom: 0; left: 0; width: 100%; height: 50px; background: rgba(12,12,20,0.92); backdrop-filter: blur(15px); border-top: 1px solid rgba(255,255,255,0.05); display: flex; justify-content: space-around; align-items: center; z-index: 10; padding: 0 10px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: #ffffff;"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--t2);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--t2);"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--t2);"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
              <div style="width: 20px; height: 20px; border-radius: 50%; border: 1.5px solid #ffffff; padding: 1px;">
                <div style="width: 100%; height: 100%; border-radius: 50%; background: linear-gradient(135deg, var(--blue), var(--pink));"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- FOMO bar -->
  <section class="wrap sec-sm" style="padding-top: 35px; position: relative;">
    <div class="widget-glow" style="position: absolute; inset: 20px; background: radial-gradient(circle, rgba(168, 85, 247, 0.18) 0%, rgba(244, 63, 94, 0.08) 50%, transparent 70%); filter: blur(55px); z-index: 0; pointer-events: none;"></div>
    <div class="fomo rev" style="position: relative; z-index: 1; border: 1px solid rgba(168,85,247,0.25); background: rgba(15,15,26,0.65); box-shadow: 0 0 50px rgba(168,85,247,0.06); backdrop-filter: blur(20px);">
      <div class="fomo-lft">
        <div class="pulse-dot"></div>
        <div class="fomo-txt">
          <h3 style="letter-spacing: -0.01em;">Zostało tylko <span class="shimmer-text" style="font-weight: 900;"><?php echo esc_html(am_field('fomo_free_slots', '8')); ?> wolnych slotów</span>!</h3>
          <p>Poprzednia edycja wyprzedała się w niespełna 48 godzin.</p>
        </div>
      </div>
      <div class="fomo-track" style="margin-left: auto;">
        <div class="fomo-labels">
          <span style="color: var(--t2); font-weight: 500;">Zarezerwowane miejsca</span>
          <span class="shimmer-text" style="font-weight: 800; font-size: 0.9rem;">
            <?php
            $reserved = am_field('fomo_reserved_slots', '72');
            $total    = am_field('fomo_total_slots', '80');
            echo esc_html($reserved . ' / ' . $total);
            ?>
          </span>
        </div>
        <div class="fomo-bg" style="height: 10px; background: rgba(255,255,255,0.05);">
          <?php $pct = (int)$total > 0 ? round(((int)$reserved / (int)$total) * 100) : 90; ?>
          <div class="fomo-fill" id="slots-bar" style="width: <?php echo esc_attr($pct); ?>%;"></div>
        </div>
        <div class="fomo-dots-container" style="display: flex; gap: 6px; margin-top: 10px; justify-content: flex-end; align-items: center;">
          <span style="font-size: 0.65rem; color: var(--t3); text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; margin-right: 4px;">Dostępne sloty (<?php echo esc_html(am_field('fomo_free_slots', '8')); ?>):</span>
          <style>@keyframes pulseFreeSlot { 0% { opacity: 0.35; transform: scale(0.9); } 100% { opacity: 1; transform: scale(1.15); box-shadow: 0 0 10px var(--green), 0 0 20px var(--green); } }</style>
          <?php for ($i = 0; $i < (int)am_field('fomo_free_slots', 8); $i++): ?>
            <div style="width: 7px; height: 7px; border-radius: 50%; background: var(--green); box-shadow: 0 0 6px var(--green); animation: pulseFreeSlot 1.2s infinite alternate; animation-delay: <?php echo esc_attr($i * 0.15); ?>s;"></div>
          <?php endfor; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- PRICING SECTION -->
  <section class="wrap sec" id="s-pricing" style="padding-bottom:80px;">
    <div class="sh center rev" style="position: relative;">
      <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 280px; height: 120px; background: radial-gradient(circle, rgba(59, 130, 246, 0.15) 0%, transparent 70%); filter: blur(35px); pointer-events: none; z-index: 0;"></div>
      <h2 style="position: relative; z-index: 1; font-size: clamp(2.0rem, 5.5vw, 3.2rem); line-height: 1.15; text-transform: uppercase; font-weight: 900; filter: drop-shadow(0 0 2px #000) drop-shadow(0 4px 16px rgba(0,0,0,0.8)); margin-bottom: 16px;">
        <span class="grad" style="background: linear-gradient(90deg, #FFFFFF 30%, var(--blue) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Wybierz swój pakiet</span>
      </h2>
    </div>

    <style>
      .price-feats { margin-bottom: 16px !important; }
      .price-feats.collapsed li:nth-child(n+4) { display: none !important; }
      .price-feats:not(.collapsed) li:nth-child(n+4) { display: flex !important; animation: fadeInFeat 0.35s ease forwards; }
      @keyframes fadeInFeat { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
      .feats-toggle-btn { background: transparent; border: none; color: var(--blue); font-size: 0.8rem; font-weight: 700; cursor: pointer; padding: 4px 0 12px; display: flex; align-items: center; gap: 6px; transition: color 0.2s; text-transform: uppercase; letter-spacing: 0.05em; width: 100%; }
      .feats-toggle-btn:hover { color: var(--pink); }
      .feats-toggle-btn svg { transition: transform 0.3s; }
      .feats-toggle-btn.expanded svg { transform: rotate(180deg); }
      .price-bestseller-badge { position: absolute; top: 20px; right: 20px; background: linear-gradient(135deg, var(--pink), var(--purple)); color: #FFF; font-size: 0.62rem; font-weight: 800; letter-spacing: 0.12em; text-transform: uppercase; padding: 4px 10px; border-radius: var(--r-pill); display: inline-flex; align-items: center; gap: 4px; box-shadow: 0 0 12px rgba(244,63,148,0.3); border: 1px solid rgba(255,255,255,0.15); z-index: 5; }
      #s-pricing .price-card { background: linear-gradient(160deg, rgba(255,255,255,0.015) 0%, rgba(255,255,255,0.002) 100%), var(--bg-c); border: 1px solid rgba(255,255,255,0.06); box-shadow: 0 30px 60px -15px rgba(0,0,0,0.8), inset 0 1px 0 rgba(255,255,255,0.05); transition: all 0.4s cubic-bezier(0.16,1,0.3,1); position: relative; }
      #s-pricing .price-card:hover { transform: translateY(-6px); border-color: rgba(244,63,94,0.25); box-shadow: 0 40px 80px -20px rgba(0,0,0,0.95), 0 0 40px rgba(244,63,94,0.05), inset 0 1px 0 rgba(255,255,255,0.08); }
      #s-pricing .price-card .price-tier { background: linear-gradient(90deg, #FFFFFF, rgba(255,255,255,0.7)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; font-weight: 900; }
      #s-pricing .price-card .price-val { background: linear-gradient(to bottom, #FFFFFF 40%, rgba(255,255,255,0.8) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    </style>

    <script>
      function toggleFeats(tier, btnEl) {
        var list = document.getElementById(tier === 'starter' ? 'feats-starter' : 'feats-prof');
        if (!list) return;
        var isCollapsed = list.classList.toggle('collapsed');
        var btn = btnEl || (window.event ? window.event.currentTarget : null);
        if (btn) {
          var label = btn.querySelector('span');
          if (label) label.textContent = isCollapsed ? 'Pokaż więcej' : 'Pokaż mniej';
          btn.classList.toggle('expanded', !isCollapsed);
        }
      }
    </script>

    <div class="price-grid">
      <!-- Starter -->
      <div class="price-card rev d1">
        <div class="price-bestseller-badge">⚡ Bestseller</div>
        <div class="price-tier">Starter</div>
        <div class="price-val">2 000<span style="font-size:1rem;color:rgba(255,255,255,0.6);margin-left:4px;vertical-align:super;font-family:var(--fb);font-weight:600;letter-spacing:normal;">zł</span></div>
        <div class="price-per">netto / jednorazowo</div>
        <div class="price-div"></div>
        <ul class="price-feats collapsed" id="feats-starter">
          <li><span class="pf-ic">✓</span>Udział w 1 wybranej kampanii (konkursie)</li>
          <li><span class="pf-ic">✓</span>Pełna ekspozycja i promowanie profilu</li>
          <li><span class="pf-ic">✓</span>Dostęp do zakładki Dashboard (przyrosty live)</li>
          <li><span class="pf-ic">✓</span>Dedykowany opiekun kampanii</li>
          <li><span class="pf-ic">✓</span>Faktura VAT + raport z edycji</li>
        </ul>
        <button class="feats-toggle-btn" onclick="toggleFeats('starter', this)">
          <span>Pokaż więcej</span>
          <svg width="10" height="6" viewBox="0 0 10 6" fill="none" style="stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;"><path d="M1 1L5 5L9 1"/></svg>
        </button>
        <?php
        $starter_id = am_field('am_starter_product_id', '');
        // Generujemy bezpośredni link do strony kasy (/checkout/) z parametrem dodania do koszyka
        $starter_url = $starter_id 
            ? home_url('/checkout/?add-to-cart=' . intval($starter_id))
            : 'mailto:kontakt@aftermarket.pl?subject=Rezerwacja pakietu Starter';
        ?>
        <a href="<?php echo esc_url($starter_url); ?>" class="btn btn-p btn-block" style="text-align:center;display:block;line-height:1;">Wybierz Starter</a>
      </div>

      <!-- Professional -->
      <div class="price-card rev d2">
        <div class="price-tier">Professional</div>
        <div class="price-val">3 000<span style="font-size:1rem;color:rgba(255,255,255,0.6);margin-left:4px;vertical-align:super;font-family:var(--fb);font-weight:600;letter-spacing:normal;">zł</span></div>
        <div class="price-per">netto / jednorazowo</div>
        <div class="price-div"></div>
        <ul class="price-feats collapsed" id="feats-prof">
          <li><span class="pf-ic">✓</span>Udział w 2 kampaniach (konkursach)</li>
          <li><span class="pf-ic">✓</span>Pełna ekspozycja i promowanie profilu</li>
          <li><span class="pf-ic">✓</span>Dostęp do zakładki Dashboard (przyrosty live)</li>
          <li><span class="pf-ic">✓</span>Wyróżnienie na InstaStories (dedykowana wzmianka)</li>
          <li><span class="pf-ic">✓</span>Dedykowany opiekun kampanii</li>
          <li><span class="pf-ic">✓</span>Konsultacja i audyt optymalizacji profilu</li>
          <li><span class="pf-ic">✓</span>Priorytetowa rezerwacja kolejnej edycji z rabatem</li>
          <li><span class="pf-ic">✓</span>Faktura VAT + raport z edycji</li>
        </ul>
        <button class="feats-toggle-btn" onclick="toggleFeats('prof', this)">
          <span>Pokaż więcej</span>
          <svg width="10" height="6" viewBox="0 0 10 6" fill="none" style="stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;"><path d="M1 1L5 5L9 1"/></svg>
        </button>
        <?php
        $prof_id = am_field('am_pro_product_id', '');
        // Generujemy bezpośredni link do strony kasy (/checkout/) z parametrem dodania do koszyka
        $prof_url = $prof_id 
            ? home_url('/checkout/?add-to-cart=' . intval($prof_id))
            : 'mailto:kontakt@aftermarket.pl?subject=Rezerwacja pakietu Professional';
        ?>
        <a href="<?php echo esc_url($prof_url); ?>" class="btn btn-p btn-block" style="text-align:center;display:block;line-height:1;">Wybierz Professional</a>
      </div>
    </div>
  </section>

</main><!-- /#view-s -->


<!-- ████████████████████████████████████████
     PAGE: CONTESTANT GIVEAWAY  (#giveaway)
████████████████████████████████████████ -->
<main id="view-g" class="pv">
  <div class="scene" id="g-hero">
    <div class="hero-bg-img" style="position: absolute; top: 48%; left: 50%; transform: translate(-50%, -50%); width: 100%; max-width: 980px; opacity: 0.45; z-index: 0; pointer-events: none;">
      <img src="<?php echo get_template_directory_uri(); ?>/prize.png" alt="Nagrody — iPhone 17 Pro i MacBook Air" style="width:100%;height:auto;filter:contrast(1.15) saturate(1.08) brightness(1.02);">
    </div>
    <section class="wrap hero rev" style="position: relative; z-index: 2; padding-top: 190px;">
      <div style="position: relative; z-index: 2; max-width: 800px; margin: 65px auto 36px;">
        <h1 style="font-size: clamp(2.4rem, 6.4vw, 4.4rem); line-height: 1.15; text-transform: uppercase; font-weight: 900; text-align: center; margin-bottom: 20px; filter: drop-shadow(0 0 2px #000) drop-shadow(0 4px 16px rgba(0,0,0,0.8));">
          <span style="white-space: nowrap;">Zgarnij <span class="grad" style="background: linear-gradient(90deg, #FFFFFF 20%, var(--pink) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo esc_html(am_field('prize_name_1', 'iPhone 17 Pro')); ?></span></span><br>
          <span style="white-space: nowrap;">LUB <span class="grad" style="background: linear-gradient(90deg, #FFFFFF 20%, var(--blue) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"><?php echo esc_html(am_field('prize_name_2', 'MacBook Air')); ?></span></span>
        </h1>
        <p class="hero-sub" style="font-size: 1.18rem; color: var(--t1); line-height: 1.6; font-weight: 500; max-width: 600px; margin: 0 auto; filter: drop-shadow(0 0 2px #000) drop-shadow(0 2px 10px rgba(0,0,0,0.9));">
          <?php echo esc_html(am_field('g_hero_subtitle', 'Dołącz do prestiżowego rozdania Aftermarket. Spełnij proste warunki w 15 sekund i zgarnij nagrodę z puli o łącznej wartości przekraczającej 10 000 PLN.')); ?>
        </p>
      </div>
      <?php
      // Pobierz datę z bazy (np. 2026-07-15T20:00 lub 2026-07-15 20:00)
      $end_date_raw = get_option('am_global_campaign_end', '');
      $end_ts = 0;
      
      if (!empty($end_date_raw)) {
          // Zamień litery 'T' na spację, by strtotime poprawnie zinterpretował czas
          $end_date_clean = str_replace('T', ' ', $end_date_raw);
          $ts = strtotime($end_date_clean);
          if ($ts !== false) {
              $end_ts = $ts * 1000; // sekundy na milisekundy dla JS
          }
      }
      ?>
      <div class="countdown" id="main-countdown" data-end="<?php echo esc_attr($end_ts); ?>" style="margin-top: 150px; transform: scale(0.85); transform-origin: center;">
        <div class="cd-u"><div class="cd-v" id="cd-d">00</div><div class="cd-l">Dni</div></div>
        <div class="cd-sep">:</div>
        <div class="cd-u"><div class="cd-v" id="cd-h">00</div><div class="cd-l">Godz</div></div>
        <div class="cd-sep">:</div>
        <div class="cd-u"><div class="cd-v" id="cd-m">00</div><div class="cd-l">Min</div></div>
        <div class="cd-sep">:</div>
        <div class="cd-u"><div class="cd-v" id="cd-s">00</div><div class="cd-l">Sek</div></div>
      </div>
    </section>
  </div>

  <!-- Steps -->
  <section class="wrap sec" id="g-steps" style="padding-top: 10px;">
    <div class="sh center rev">
      <h2 style="filter: drop-shadow(0 0 2px #000) drop-shadow(0 2px 8px rgba(0,0,0,0.85));">Zasady wzięcia udziału</h2>
    </div>
    <div class="g12">
      <div class="c6 rev" style="position: relative; display: flex; flex-direction: column;">
        <div class="widget-glow glow-blue" style="position: absolute; inset: 20px; background: radial-gradient(circle, rgba(59, 130, 246, 0.16) 0%, transparent 70%); filter: blur(55px); z-index: 0; pointer-events: none;"></div>
        <div class="card tilt" style="display:flex;flex-direction:column;height:100%;position:relative;z-index:1;flex:1;">
          <div class="ti" style="display:flex;flex-direction:column;height:100%;flex:1;">
            <div class="step-num">01</div>
            <h3 style="margin-top:14px;margin-bottom:12px;"><?php echo esc_html(am_field('g_step_1_title', 'Zaobserwuj profile')); ?></h3>
            <p><?php echo esc_html(am_field('g_step_1_desc', 'Wejdź na profil @aftermarket.ag i zaobserwuj konta z naszej listy obserwowanych.')); ?></p>
            <a href="https://www.instagram.com/aftermarket.ag/" target="_blank" rel="noopener noreferrer" class="btn-ig-follow">
              <svg viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
              Obserwuj @aftermarket.ag
            </a>
          </div>
        </div>
      </div>
      <div class="c6 rev d1" style="position: relative; display: flex; flex-direction: column;">
        <div class="widget-glow glow-purple" style="position: absolute; inset: 20px; background: radial-gradient(circle, rgba(168, 85, 247, 0.16) 0%, transparent 70%); filter: blur(60px); z-index: 0; pointer-events: none;"></div>
        <div class="card tilt" style="display:flex;flex-direction:column;height:100%;position:relative;z-index:1;flex:1;">
          <div class="ti" style="display:flex;flex-direction:column;height:100%;flex:1;position:relative;z-index:2;">
            <div class="step-num">02</div>
            <h3 style="margin-top:14px;margin-bottom:12px;"><?php echo esc_html(am_field('g_step_2_title', 'Oczekuj losowania na żywo')); ?></h3>
            <p><?php echo esc_html(am_field('g_step_2_desc', 'Transmisja live wyłoni zwycięzcę. Wyślemy Ci e-mail 1 godzinę przed losowaniem.')); ?></p>
            <div class="status-radar-widget">
              <div class="radar-ping"></div>
              <div>
                <span class="radar-text">Status: LIVE WAITING</span>
                <span class="radar-desc">Transmisja wkrótce</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</main><!-- /#view-g -->

<!-- FLOATING SIDE NAVIGATION -->
<div id="side-nav-container" class="side-nav-wrap">
  <div class="side-progress-track">
    <div class="side-progress-fill" id="side-progress-bar"></div>
  </div>
  <div class="side-dots-list" id="side-nav-dots"></div>
</div>

<?php get_footer(); ?>
