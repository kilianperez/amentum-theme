<?php // pagina del blog ?>
<?php get_header() ?>
<main class="home" >
    <?php 
        get_template_part('template-parts/content-home', 'blog') ?>
    <?php the_content();?>
</main>

<?php get_footer() ?>