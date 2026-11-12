<?php
declare(strict_types=1);require __DIR__.'/app/bootstrap.php';require_once __DIR__.'/app/health-pages.php';$language=health_language();render_health_listing_header(health_page_copy('tools',$language),health_page_copy('tools_deck',$language),$language);render_health_tiles(health_tools(),$language,'tool');render_health_disclaimer($language);?></main><?php render_footer();
