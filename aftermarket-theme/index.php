<?php
/**
 * Main template file.
 * This is a required file in any WordPress theme.
 */

get_header();
?>

<main class="pv" style="padding-top: 100px; min-height: 70vh;">
  <section class="wrap">
    
    <?php if ( have_posts() ) : ?>
        <?php while ( have_posts() ) : the_post(); ?>
            
            <?php 
            // Bezpieczne sprawdzenie stron WooCommerce
            $is_woo_page = false;
            if ( class_exists('WooCommerce') ) {
                if ( is_cart() || is_checkout() || is_account_page() ) {
                    $is_woo_page = true;
                }
            }
            ?>

            <?php if ( $is_woo_page ) : ?>
                <!-- Treść dla podstron WooCommerce (pełna szerokość) -->
                <div class="woocommerce-page-wrapper" style="text-align: left; background: #0d0d18; padding: 40px; border-radius: 16px;">
                    <?php the_content(); ?>
                </div>
            <?php else : ?>
                <!-- Standardowa podstrona w kontenerze card -->
                <div class="card" style="padding: 40px; text-align: center;">
                    <h1 style="margin-bottom: 20px;"><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </div>
            <?php endif; ?>

        <?php endwhile; ?>
    <?php else : ?>
        <div class="card" style="padding: 40px; text-align: center;">
            <p>Brak zawartości do wyświetlenia.</p>
        </div>
    <?php endif; ?>

  </section>
</main>

<?php
get_footer();
