<?php
declare(strict_types=1);require __DIR__.'/app/bootstrap.php';require_once __DIR__.'/app/health-pages.php';$language=health_language();render_health_listing_header(health_page_copy('tips',$language),health_page_copy('tips_deck',$language),$language);render_health_tiles(health_tips(),$language,'tip');render_health_disclaimer($language);?></main><?php render_footer();
