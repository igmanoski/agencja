<?php
/*
Template Name: Polityka Prywatności
*/
get_header();
?>

<main id="view-s" style="padding-top: 80px; padding-bottom: 80px; min-height: 80vh;">
  <section class="wrap sec" style="max-width: 800px; margin: 0 auto;">
    
    <div class="sh center" style="margin-bottom: 30px; text-align: center;">
      <div class="chip" style="margin: 0 auto;"><span class="chip-dot"></span>DOKUMENTY PRAWNE</div>
      <h1 style="font-size: clamp(2rem, 5vw, 3rem); text-transform: uppercase; margin: 15px 0 10px; color: #ffffff; text-shadow: 0 0 20px rgba(255,255,255,0.05);">Polityka Prywatności</h1>
      <p style="color: var(--t3); margin: 0;">Ostatnia aktualizacja: <?php echo date('d.m.Y'); ?></p>
    </div>

    <div class="card" style="padding: 40px 30px; line-height: 1.8; color: #d4d4d8; font-size: 0.95rem; border: 1px solid rgba(255,255,255,0.06); background: linear-gradient(160deg, rgba(255,255,255,0.015) 0%, rgba(255,255,255,0.002) 100%), var(--bg-c); border-radius: 16px;">
      <div class="ti">
        
        <?php 
        // 💡 SPOSÓB NA PODMIANĘ TREŚCI:
        // Jeśli wpiszesz własną treść w edytorze WordPress na tej podstronie, wyświetli się ona tutaj.
        // Jeśli zostawisz edytor pusty, wyświetli się poniższa przykładowa polityka prywatności:
        
        $wp_content = get_the_content();
        if (have_posts() && !empty(trim($wp_content))) {
            while (have_posts()) {
                the_post();
                the_content();
            }
        } else {
        ?>
            <div style="font-size: 0.85rem; color: #F43F5E; background: rgba(244,63,94,0.08); border: 1px dashed rgba(244,63,94,0.3); padding: 12px 16px; border-radius: 8px; margin-bottom: 25px;">
                💡 <strong>Wskazówka administratora:</strong> Aby edytować lub podmienić tę treść, wejdź w panelu WordPressa do zakładki <strong>Strony</strong>, kliknij edycję strony <strong>Polityka prywatności</strong> i po prostu wpisz tam swój tekst.
            </div>

            <h3 style="color: #fff; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">1. Informacje Ogólne</h3>
            <p>Niniejsza Polityka Prywatności określa zasady przetwarzania i ochrony danych osobowych osób korzystających z serwisu <strong><?php echo esc_url(home_url()); ?></strong> (zwanych dalej Użytkownikami), w tym Sponsorów i Uczestników.</p>
            
            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">2. Administrator Danych Osobowych</h3>
            <p>Administratorem danych osobowych Użytkowników zbieranych za pośrednictwem Serwisu jest firma <strong>[WPISZ_NAZWE_FIRMY]</strong> z siedzibą w [MIEJSCOWOSC], pod adresem: [ADRES], NIP: [NIP], REGON: [REGON], e-mail: <strong>[WPISZ_EMAIL]</strong>.</p>

            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">3. Cel i Podstawa Przetwarzania Danych</h3>
            <p>Dane osobowe są przetwarzane w celach:</p>
            <p>1. <strong>Realizacji zamówień i umów</strong> — na podstawie art. 6 ust. 1 lit. b RODO (niezbędność do wykonania umowy).</p>
            <p>2. <strong>Rozliczeń finansowych i podatkowych</strong> — na podstawie art. 6 ust. 1 lit. c RODO (obowiązek prawny spoczywający na Administratorze).</p>
            <p>3. <strong>Wsparcia technicznego i kontaktu</strong> — na podstawie art. 6 ust. 1 lit. f RODO (prawnie uzasadniony interes Administratora).</p>
            <p>4. <strong>Udziału w konkursach</strong> — na podstawie wyrażonej zgody (art. 6 ust. 1 lit. a RODO).</p>

            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">4. Zakres Zbieranych Danych</h3>
            <p>Przetwarzamy następujące dane w zależności od charakteru interakcji:</p>
            <ul>
              <li>W przypadku Sponsorów: nazwa firmy, NIP, adres rejestrowy, adres e-mail, imię i nazwisko osoby kontaktowej, nazwa konta Instagram.</li>
              <li>W przypadku Uczestników: adres e-mail, nazwa konta Instagram.</li>
            </ul>

            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">5. Prawa Użytkowników</h3>
            <p>Każdej osobie, której dane dotyczą, przysługuje prawo do:</p>
            <p>1. Dostępu do swoich danych oraz otrzymania ich kopii.</p>
            <p>2. Sprostowania (poprawiania) swoich danych.</p>
            <p>3. Usunięcia danych („prawo do bycia zapomnianym”).</p>
            <p>4. Ograniczenia przetwarzania danych.</p>
            <p>5. Wniesienia sprzeciwu wobec przetwarzania.</p>
            <p>6. Wniesienia skargi do Prezesa Urzędu Ochrony Danych Osobowych (PUODO).</p>

            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">6. Pliki Cookies i Narzędzia Analityczne</h3>
            <p>1. Serwis korzysta z plików cookies w celu poprawnego działania interfejsu (np. koszyk WooCommerce, sesja logowania do Dashboardu) oraz w celach statystycznych.</p>
            <p>2. Użytkownik może w każdej chwili zmienić ustawienia dotyczące plików cookies w swojej przeglądarce internetowej.</p>
        <?php 
        } 
        ?>

      </div>
    </div>

  </section>
</main>

<?php get_footer(); ?>
