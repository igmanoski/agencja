<?php
/*
Template Name: Regulamin
*/
get_header();
?>

<main id="view-s" style="padding-top: 80px; padding-bottom: 80px; min-height: 80vh;">
  <section class="wrap sec" style="max-width: 800px; margin: 0 auto;">
    
    <div class="sh center" style="margin-bottom: 30px; text-align: center;">
      <div class="chip" style="margin: 0 auto;"><span class="chip-dot"></span>DOKUMENTY PRAWNE</div>
      <h1 style="font-size: clamp(2rem, 5vw, 3rem); text-transform: uppercase; margin: 15px 0 10px; color: #ffffff; text-shadow: 0 0 20px rgba(255,255,255,0.05);">Regulamin Serwisu</h1>
      <p style="color: var(--t3); margin: 0;">Ostatnia aktualizacja: <?php echo date('d.m.Y'); ?></p>
    </div>

    <div class="card" style="padding: 40px 30px; line-height: 1.8; color: #d4d4d8; font-size: 0.95rem; border: 1px solid rgba(255,255,255,0.06); background: linear-gradient(160deg, rgba(255,255,255,0.015) 0%, rgba(255,255,255,0.002) 100%), var(--bg-c); border-radius: 16px;">
      <div class="ti">
        
        <?php 
        // 💡 SPOSÓB NA PODMIANĘ TREŚCI:
        // Jeśli wpiszesz własną treść w edytorze WordPress na tej podstronie, wyświetli się ona tutaj.
        // Jeśli zostawisz edytor pusty, wyświetli się poniższy przykładowy regulamin:
        
        $wp_content = get_the_content();
        if (have_posts() && !empty(trim($wp_content))) {
            while (have_posts()) {
                the_post();
                the_content();
            }
        } else {
        ?>
            <div style="font-size: 0.85rem; color: #F43F5E; background: rgba(244,63,94,0.08); border: 1px dashed rgba(244,63,94,0.3); padding: 12px 16px; border-radius: 8px; margin-bottom: 25px;">
                💡 <strong>Wskazówka administratora:</strong> Aby edytować lub podmienić tę treść, wejdź w panelu WordPressa do zakładki <strong>Strony</strong>, kliknij edycję strony <strong>Regulamin</strong> i po prostu wpisz tam swój tekst.
            </div>

            <h3 style="color: #fff; margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">§ 1. Postanowienia Ogólne</h3>
            <p>1. Niniejszy Regulamin określa zasady korzystania z serwisu internetowego Aftermarket dostępnego pod adresem <strong><?php echo esc_url(home_url()); ?></strong>, zwanego dalej „Serwisem”.</p>
            <p>2. Właścicielem Serwisu i Usługodawcą jest firma <strong>[WPISZ_NAZWE_FIRMY]</strong> z siedzibą w [MIEJSCOWOSC], pod adresem: [ADRES], NIP: [NIP], REGON: [REGON], zwana dalej „Usługodawcą”.</p>
            <p>3. Kontakt z Usługodawcą jest możliwy za pośrednictwem adresu e-mail: <strong>[WPISZ_EMAIL]</strong>.</p>
            <p>4. Serwis świadczy usługi na rzecz Partnerów handlowych (Sponsorów) chcących promować swoje profile społecznościowe w organizowanych przez Usługodawcę kampaniach oraz dla Uczestników konkursów.</p>

            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">§ 2. Definicje</h3>
            <p>1. <strong>Sponsor (Partner)</strong> – osoba fizyczna prowadząca działalność gospodarczą lub osoba prawna, która kupuje Pakiet Promocyjny w celu zwiększenia zasięgu swojego konta Instagram.</p>
            <p>2. <strong>Uczestnik</strong> – osoba fizyczna, która bierze udział w organizowanym giveaway (konkursie) zgodnie z wytycznymi na stronie głównej.</p>
            <p>3. <strong>Pakiet Promocyjny</strong> – usługa marketingowa świadczona przez Usługodawcę, polegająca na umieszczeniu profilu Sponsora na liście kont do zaobserwowania przez Uczestników.</p>

            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">§ 3. Zakup Usług i Płatności</h3>
            <p>1. Zakup Pakietu Promocyjnego przez Sponsora odbywa się za pośrednictwem formularza zamówienia w Serwisie.</p>
            <p>2. Ceny pakietów podane w Serwisie są cenami netto i należy do nich doliczyć podatek VAT w wysokości 23%.</p>
            <p>3. Płatność realizowana jest za pośrednictwem bramek płatniczych dostępnych w procesie zakupowym.</p>
            <p>4. Po dokonaniu płatności Sponsor otrzymuje dane dostępowe do Panelu Statystyk (Dashboard) oraz fakturę VAT wysłaną drogą elektroniczną.</p>

            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">§ 4. Odpowiedzialność i Gwarancje</h3>
            <p>1. Usługodawca dokłada wszelkich starań, aby kampanie promocyjne przynosiły jak największe przyrosty rzeczywistych obserwujących dla profili Sponsorów.</p>
            <p>2. Usługodawca nie gwarantuje stałego i określonego przyrostu liczby obserwujących, ponieważ jest to zależne od woli i aktywności Uczestników konkursu.</p>
            <p>3. Sponsor zobowiązuje się do utrzymania aktywnego i publicznego profilu na Instagramie przez cały okres trwania kampanii.</p>

            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">§ 5. Reklamacje i Odstąpienie od Umowy</h3>
            <p>1. Ze względu na charakter świadczonej usługi (usługa dostarczania treści cyfrowych i działań promocyjnych rozpoczynających się natychmiast po opłaceniu), Sponsorowi będącemu przedsiębiorcą nie przysługuje prawo do odstąpienia od umowy po uruchomieniu działań konfiguracyjnych.</p>
            <p>2. Reklamacje dotyczące działania Serwisu lub przebiegu kampanii można składać pod adresem e-mail: <strong>[WPISZ_EMAIL]</strong> w terminie 14 dni od zakończenia kampanii.</p>

            <h3 style="color: #fff; margin-top: 30px; margin-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.08); padding-bottom: 10px;">§ 6. Postanowienia Końcowe</h3>
            <p>1. Usługodawca zastrzega sobie prawo do zmiany niniejszego Regulaminu z ważnych przyczyn technicznych lub prawnych.</p>
            <p>2. W sprawach nieuregulowanych regulaminem zastosowanie mają przepisy Kodeksu Cywilnego.</p>
        <?php 
        } 
        ?>

      </div>
    </div>

  </section>
</main>

<?php get_footer(); ?>
