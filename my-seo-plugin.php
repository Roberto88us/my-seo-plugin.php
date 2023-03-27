<?php
if (!function_exists('settings_fields')) {
    require_once ABSPATH . 'wp-admin/includes/template.php';
}
/*
Plugin Name: My SEO Plugin
Plugin URI: https://www.example.com/my-seo-plugin
Description: Improve your website's SEO with this powerful plugin!
Version: 1.0
Author: Roberto Olivieri
Author URI: https://www.example.com/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: my-seo-plugin
*/
require_once(ABSPATH . 'wp-admin/includes/template.php');

function my_seo_plugin_add_meta_tags() {
  if ( is_singular() ) {
    global $post;
    $excerpt = strip_tags( $post->post_excerpt );
    if ( ! $excerpt ) {
      $excerpt = strip_tags( $post->post_content );
      $excerpt = substr( $excerpt, 0, 160 );
      $excerpt = preg_replace( '/\s+/', ' ', $excerpt );
      $excerpt = trim( $excerpt );
      $excerpt = rtrim( $excerpt, ',.;:' );
      $excerpt .= '...';
    }
    echo '<meta name="description" content="' . esc_attr( $excerpt ) . '" />' . "\n";
  } else {
    echo '<meta name="description" content="This is a custom meta tag added by My SEO Plugin." />' . "\n";
  }
}
add_action( 'wp_head', 'my_seo_plugin_add_meta_tags' );

// Function for adding structured data to Pages
function my_seo_plugin_add_page_schema() {
    if ( is_page() ) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
        );
        echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'my_seo_plugin_add_page_schema' );

// Function for adding structured data to Posts
function my_seo_plugin_add_post_schema() {
    if ( is_single() ) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
            'datePublished' => get_the_date( 'c' ),
            'dateModified' => get_the_modified_date( 'c' ),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author(),
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo( 'name' ),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_theme_mod( 'custom_logo' ),
                ),
            ),
            'image' => array(
                '@type' => 'ImageObject',
                'url' => get_the_post_thumbnail_url(),
            ),
        );
        echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'my_seo_plugin_add_post_schema' );

// Function for adding structured data to Products
function my_seo_plugin_add_product_schema() {
    if ( is_singular( 'product' ) ) {
        global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
            'image' => array(
                '@type' => 'ImageObject',
                'url' => get_the_post_thumbnail_url(),
            ),
            'sku' => get_post_meta( get_the_ID(), '_sku', true ),
            'brand' => array(
                '@type' => 'Brand',
                'name' => get_post_meta( get_the_ID(), '_brand_name', true ),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_post_meta( get_the_ID(), '_brand_logo', true ),
                ),
            ),
            'offers' => array(
                '@type' => 'Offer',
                'price' => get_post_meta( get_the_ID(), '_price', true ),
                'priceCurrency' => get_post_meta( get_the_ID(), '_currency', true ),
                'availability' => 'https://schema.org/InStock',
                'url' => get_permalink(),
            ),
        );
        echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'my_seo_plugin_add_product_schema' );

// Function for adding structured data to Services
function my_seo_plugin_add_service_markup() {
  if ( is_singular( 'services' ) ) {
    global $post;
    $service_name = get_the_title();
    $service_description = strip_tags( $post->post_content );
    $service_price = get_post_meta( $post->ID, '_service_price', true );
    
    $markup = '<script type="application/ld+json">';
    $markup .= '{';
    $markup .= '"@context": "https://schema.org",';
    $markup .= '"@type": "Service",';
    $markup .= '"name": "' . esc_attr( $service_name ) . '",';
    $markup .= '"description": "' . esc_attr( $service_description ) . '",';
    $markup .= '"offers": {';
    $markup .= '"@type": "Offer",';
    $markup .= '"price": "' . esc_attr( $service_price ) . '",';
    $markup .= '"priceCurrency": "USD"';
    $markup .= '}';
    $markup .= '}';
    $markup .= '</script>';
    
    echo $markup;
  }
}

add_action( 'wp_head', 'my_seo_plugin_add_service_markup' );

// Function for adding structured data to Events
function my_seo_plugin_add_event_markup() {
  if ( is_singular( 'event' ) ) {
    global $post;
    $start_date = get_post_meta( $post->ID, 'start_date', true );
    $end_date = get_post_meta( $post->ID, 'end_date', true );
    $location = get_post_meta( $post->ID, 'location', true );
    $organizer = get_post_meta( $post->ID, 'organizer', true );

    $markup = '<script type="application/ld+json">';
    $markup .= '{';
    $markup .= '"@context": "https://schema.org",';
    $markup .= '"@type": "Event",';
    $markup .= '"name": "' . get_the_title() . '",';
    $markup .= '"startDate": "' . date( 'c', strtotime( $start_date ) ) . '",';
    $markup .= '"endDate": "' . date( 'c', strtotime( $end_date ) ) . '",';
    $markup .= '"location": {';
    $markup .= '"@type": "Place",';
    $markup .= '"name": "' . $location . '"';
    $markup .= '}';
    if ( $organizer ) {
      $markup .= ',"organizer": {';
      $markup .= '"@type": "Organization",';
      $markup .= '"name": "' . $organizer . '"';
      $markup .= '}';
    }
    $markup .= '}';
    $markup .= '</script>';

    echo $markup;
  }
}
add_action( 'wp_head', 'my_seo_plugin_add_event_markup' );

// Function for adding structured data to Jobs
function my_seo_plugin_add_job_markup() {
  if ( is_singular( 'job_listing' ) ) {
    global $post;
    $job_type = get_the_terms( $post->ID, 'job_listing_type' );
    $job_location = get_the_terms( $post->ID, 'job_listing_region' );

    $markup = '<script type="application/ld+json">';
    $markup .= '{';
    $markup .= '"@context": "https://schema.org",';
    $markup .= '"@type": "JobPosting",';
    $markup .= '"title": "' . get_the_title() . '",';
    $markup .= '"datePosted": "' . get_the_date( 'c' ) . '",';
    $markup .= '"description": "' . get_the_excerpt() . '",';
    $markup .= '"employmentType": "' . $job_type[0]->name . '",';
    $markup .= '"jobLocation": {';
    $markup .= '"@type": "Place",';
    $markup .= '"address": {';
    $markup .= '"@type": "PostalAddress",';
    $markup .= '"addressLocality": "' . $job_location[0]->name . '"';
    $markup .= '}';
    $markup .= '},';
    $markup .= '"hiringOrganization": {';
    $markup .= '"@type": "Organization",';
    $markup .= '"name": "' . get_bloginfo( 'name' ) . '"';
    $markup .= '}';
    $markup .= '}';
    $markup .= '</script>';

    echo $markup;
  }
}
add_action( 'wp_head', 'my_seo_plugin_add_job_markup' );

 // Function for adding structured data to FAQs
function my_seo_plugin_add_faq_schema() {
    if ( is_singular( 'faq' ) ) {
       global $post;
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array(),
        );
        $faqs = get_field( 'faqs', $post->ID );
        if ( $faqs ) {
            foreach ( $faqs as $faq ) {
                $faq_schema = array(
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => array(
                        '@type' => 'Answer',
                        'text' => $faq['answer'],
                    ),
                );
                array_push( $schema['mainEntity'], $faq_schema );
            }
        }
        echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'my_seo_plugin_add_faq_schema' );

// Function for adding structured data to Portfolio Items
function my_seo_plugin_add_portfolio_schema() {
    if ( is_singular( 'portfolio_item' ) ) {
        global $post;
        $schema = array(
            '@context' => 'http://schema.org',
            '@type' => 'CreativeWork',
            'name' => $post->post_title,
            'description' => $post->post_excerpt,
            'image' => array(
                '@type' => 'ImageObject',
                'url' => get_the_post_thumbnail_url( $post, 'large' ),
                'width' => '1200',
                'height' => '630'
            ),
            'datePublished' => get_the_date( 'c' ),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta( 'display_name', $post->post_author )
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo( 'name' ),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_field( 'site_logo', 'options' ),
                    'width' => 600,
                    'height' => 60
                )
            )
        );
        echo '<script type="application/ld+json">' . json_encode( $schema ) . '</script>';
    }
}
add_action( 'wp_head', 'my_seo_plugin_add_portfolio_schema' );

// Function for adding structured data to Recipes
function my_seo_plugin_add_recipe_markup() {
    if ( is_singular('recipe') ) {
        global $post;
        $markup = array(
            '@context' => 'https://schema.org/',
            '@type' => 'Recipe',
            'name' => get_the_title(),
            'description' => wp_strip_all_tags( get_the_excerpt() ),
            'image' => get_the_post_thumbnail_url( $post, 'full' ),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author_meta( 'display_name' )
            ),
            'datePublished' => get_the_date( 'c' ),
            'prepTime' => 'PT' . get_post_meta( $post->ID, '_recipe_prep_time', true ) . 'M',
            'cookTime' => 'PT' . get_post_meta( $post->ID, '_recipe_cook_time', true ) . 'M',
            'totalTime' => 'PT' . get_post_meta( $post->ID, '_recipe_total_time', true ) . 'M',
            'recipeYield' => get_post_meta( $post->ID, '_recipe_yield', true ),
            'recipeIngredient' => explode( PHP_EOL, get_post_meta( $post->ID, '_recipe_ingredients', true ) ),
            'recipeInstructions' => explode( PHP_EOL, get_post_meta( $post->ID, '_recipe_directions', true ) ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $markup ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'my_seo_plugin_add_recipe_markup' );

// Function for adding structured data to Videos
function add_video_markup() {
    if (is_singular('video')) {
        global $post;
        $video_url = get_post_meta($post->ID, 'video_url', true);
        $video_title = get_the_title();
        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
        $description = get_the_excerpt();
        echo '<script type="application/ld+json">';
        echo '{';
        echo '"@context": "https://schema.org/",';
        echo '"@type": "VideoObject",';
        echo '"name": "' . $video_title . '",';
        echo '"description": "' . $description . '",';
        echo '"thumbnailUrl": "' . $thumbnail_url . '",';
        echo '"uploadDate": "' . get_the_date('c') . '",';
        echo '"contentUrl": "' . $video_url . '"';
        echo '}';
        echo '</script>';
    }
}
add_action('wp_head', 'add_video_markup');

// Function for adding structured data to Podcasts
function add_podcast_markup() {
    if (is_singular('podcast')) {
        global $post;
        $podcast_title = get_the_title();
        $description = get_the_excerpt();
        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
        $audio_url = get_post_meta($post->ID, 'audio_url', true);
        echo '<script type="application/ld+json">';
        echo '{';
        echo '"@context": "https://schema.org/",';
        echo '"@type": "PodcastEpisode",';
        echo '"name": "' . $podcast_title . '",';
        echo '"description": "' . $description . '",';
        echo '"thumbnailUrl": "' . $thumbnail_url . '",';
        echo '"uploadDate": "' . get_the_date('c') . '",';
        echo '"contentUrl": "' . $audio_url . '"';
        echo '}';
        echo '</script>';
    }
}
add_action('wp_head', 'add_podcast_markup');

// Function for adding structured data to News
function add_news_markup() {
    if (is_singular('news')) {
        global $post;
        $news_title = get_the_title();
        $description = get_the_excerpt();
        $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
        echo '<script type="application/ld+json">';
        echo '{';
        echo '"@context": "https://schema.org/",';
        echo '"@type": "NewsArticle",';
        echo '"headline": "' . $news_title . '",';
        echo '"description": "' . $description . '",';
        echo '"image": {';
        echo '"@type": "ImageObject",';
        echo '"url": "' . $thumbnail_url . '"';
        echo '},';
        echo '"datePublished": "' . get_the_date('c') . '",';
        echo '"mainEntityOfPage": "' . get_permalink() . '"';
        echo '}';
        echo '</script>';
    }
}
add_action('wp_head', 'add_news_markup');

// Function for adding structured data to Infographics
function my_seo_plugin_add_infographic_schema() {
    if ( is_singular('infographics') ) {
        global $post;
        $image = get_the_post_thumbnail_url($post, 'full');
        $url = get_permalink($post);
        $title = get_the_title($post);
        $description = get_the_excerpt($post);
        $date = get_the_date('c', $post);

        $markup = [
            '@context' => 'https://schema.org',
            '@type' => 'ImageObject',
            'url' => $image,
            'contentUrl' => $image,
            'description' => $description,
            'name' => $title,
            'datePublished' => $date,
            'associatedMedia' => [
                '@type' => 'MediaObject',
                'contentUrl' => $url,
                'description' => $description,
                'url' => $url,
            ],
        ];

        echo '<script type="application/ld+json">' . json_encode($markup) . '</script>';
    }
}
add_action('wp_head', 'my_seo_plugin_add_infographic_schema');

// Function for adding structured data to Tutorials
function my_seo_plugin_add_tutorial_schema() {
    if ( is_singular('tutorials') ) {
        global $post;
        $image = get_the_post_thumbnail_url($post, 'full');
        $url = get_permalink($post);
        $title = get_the_title($post);
        $description = get_the_excerpt($post);
        $date = get_the_date('c', $post);
        $author = get_the_author();

        $markup = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => $title,
            'description' => $description,
            'image' => [
                '@type' => 'ImageObject',
                'url' => $image,
                'contentUrl' => $image,
            ],
            'step' => [],
            'totalTime' => '',
            'estimatedCost' => '',
            'tool' => '',
            'supply' => '',
            'performTime' => '',
            'performValue' => '',
            'yield' => '',
            'author' => [
                '@type' => 'Person',
                'name' => $author,
            ],
            'datePublished' => $date,
            'dateModified' => $date,
        ];

        $steps = get_field('tutorial_steps', $post);
        if ( $steps ) {
            foreach ( $steps as $step ) {
                $step_markup = [
                    '@type' => 'HowToStep',
                    'text' => $step['step_text'],
                    'image' => [
                        '@type' => 'ImageObject',
                        'url' => $step['step_image']['url'],
                        'contentUrl' => $step['step_image']['url'],
                    ],
                ];
                $markup['step'][] = $step_markup;
            }
        }

        echo '<script type="application/ld+json">' . json_encode($markup) . '</script>';
    }
}
add_action('wp_head', 'my_seo_plugin_add_tutorial_schema');

// Function for adding structured data to E-Books
function add_ebook_structured_data() {
    if (is_singular('ebook')) {
        $post_id = get_the_ID();
        $title = get_the_title();
        $content = get_the_content();
        $author_name = get_post_meta($post_id, 'ebook_author_name', true);
        $publisher_name = get_post_meta($post_id, 'ebook_publisher_name', true);
        $publisher_logo = get_post_meta($post_id, 'ebook_publisher_logo', true);
        $date_published = get_post_meta($post_id, 'ebook_date_published', true);
        $isbn = get_post_meta($post_id, 'ebook_isbn', true);
        $price = get_post_meta($post_id, 'ebook_price', true);

        $json_ld = array(
            '@context' => 'http://schema.org',
            '@type' => 'Book',
            'name' => $title,
            'description' => $content,
            'author' => array(
                '@type' => 'Person',
                'name' => $author_name,
            ),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => $publisher_name,
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => $publisher_logo,
                ),
            ),
            'datePublished' => $date_published,
            'isbn' => $isbn,
            'offers' => array(
                '@type' => 'Offer',
                'price' => $price,
                'priceCurrency' => 'USD',
            ),
        );

        echo '<script type="application/ld+json">' . json_encode($json_ld) . '</script>';
    }
}
add_action('wp_head', 'add_ebook_structured_data');

// Function for adding structured data to Webinars
function add_webinar_structured_data() {
    if ( is_singular( 'webinar' ) ) {
        global $post;

        $webinar_meta = get_post_meta( $post->ID, 'webinar_data', true );

        $data = array(
            '@context' => 'https://schema.org',
            '@type' => 'Webinar',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
            'startDate' => date( 'c', strtotime( $webinar_meta['start_date'] ) ),
            'endDate' => date( 'c', strtotime( $webinar_meta['end_date'] ) ),
            'organizer' => array(
                '@type' => 'Organization',
                'name' => $webinar_meta['organizer_name'],
                'url' => $webinar_meta['organizer_url'],
            ),
        );

        echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE ) . '</script>';
    }
}
add_action( 'wp_head', 'add_webinar_structured_data' );

// Function for adding structured data to Courses
function add_course_structured_data() {
    if ( is_singular( 'course' ) ) {
        global $post;

        $course_meta = get_post_meta( $post->ID, 'course_data', true );

        $data = array(
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
            'provider' => array(
                '@type' => 'Organization',
                'name' => $course_meta['provider_name'],
                'url' => $course_meta['provider_url'],
            ),
        );

        echo '<script type="application/ld+json">' . wp_json_encode( $data, JSON_UNESCAPED_UNICODE ) . '</script>';
    }
}
add_action( 'wp_head', 'add_course_structured_data' );

// Function for adding structured data to Landing pages
function add_ld_json_markup_to_landing_pages() {
    $template = get_page_template_slug();
    if ($template && (false !== strpos($template, 'landing-page'))) {
        $schema = array(
            '@context' => 'http://schema.org',
            '@type' => 'WebPage',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
            'image' => get_the_post_thumbnail_url(),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_theme_mod('custom_logo'),
                    'width' => 600,
                    'height' => 60,
                ),
            ),
        );
        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
    }
}
add_action('wp_head', 'add_ld_json_markup_to_landing_pages');

// Function for adding structured data to About Us page
function add_ld_json_markup_to_about_us_page() {
    if(is_page('about-us')) {
        $schema = array(
            '@context' => 'http://schema.org',
            '@type' => 'AboutPage',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
            'image' => get_the_post_thumbnail_url(),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_theme_mod('custom_logo'),
                    'width' => 600,
                    'height' => 60,
                ),
            ),
        );
        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
    }
}
add_action('wp_head', 'add_ld_json_markup_to_about_us_page');

// Function for adding structured data to Contact Us page
function add_ld_json_markup_to_contact_us_page() {
    if(is_page('contact-us')) {
        $schema = array(
            '@context' => 'http://schema.org',
            '@type' => 'ContactPage',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
            'url' => get_permalink(),
            'image' => get_the_post_thumbnail_url(),
            'publisher' => array(
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'logo' => array(
                    '@type' => 'ImageObject',
                    'url' => get_theme_mod('custom_logo'),
                    'width' => 600,
                    'height' => 60,
                ),
            ),
        );
        echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
    }
}
add_action('wp_head', 'add_ld_json_markup_to_contact_us_page');

// Function for adding structured data to Terms and Conditions
function add_terms_and_conditions_markup() {
    if ( is_page( 'terms-and-conditions' ) ) {
        $markup = array(
            '@context' => 'https://schema.org/',
            '@type' => 'WebPage',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $markup ) . '</script>';
    }
}
add_action( 'wp_head', 'add_terms_and_conditions_markup' );

// Function for adding structured data to Privacy Policy
function add_privacy_policy_markup() {
    if ( is_page( 'privacy-policy' ) ) {
        $markup = array(
            '@context' => 'https://schema.org/',
            '@type' => 'WebPage',
            'name' => get_the_title(),
            'description' => get_the_excerpt(),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $markup ) . '</script>';
    }
}
add_action( 'wp_head', 'add_privacy_policy_markup' );

// Function for adding structured data to Sitemap
function add_sitemap_structured_data() {
  if ( is_page( 'sitemap' ) ) {
    $json_ld = array(
      '@context' => 'http://schema.org',
      '@type' => 'SiteMap',
      'name' => get_bloginfo( 'name' ),
      'url' => get_permalink(),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $json_ld ) . '</script>';
  }
}
add_action( 'wp_head', 'add_sitemap_structured_data' );

// Function for adding structured data to Archives
function add_archive_structured_data() {
  if ( is_archive() ) {
    $json_ld = array(
      '@context' => 'http://schema.org',
      '@type' => 'Blog',
      'name' => get_bloginfo( 'name' ),
      'url' => get_permalink(),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $json_ld ) . '</script>';
  }
}
add_action( 'wp_head', 'add_archive_structured_data' );

// Function for adding structured data to Search Results Pages
function add_search_results_markup() {
    if ( is_search() ) {
        $markup = array(
            '@context' => 'https://schema.org/',
            '@type' => 'SearchResultsPage',
            'name' => 'Search Results for ' . get_search_query(),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $markup ) . '</script>';
    }
}
add_action( 'wp_head', 'add_search_results_markup' );

// Function for adding structured data to Error Pages 404
function add_error_page_structured_data() {
  if ( is_404() ) {
    $json_ld = array(
      '@context' => 'http://schema.org',
      '@type' => 'WebPage',
      'name' => 'Error 404 - Page Not Found',
      'description' => 'Sorry, the page you are looking for could not be found.',
      'url' => get_permalink(),
    );

    echo '<script type="application/ld+json">' . wp_json_encode( $json_ld ) . '</script>';
  }
}
add_action( 'wp_head', 'add_error_page_structured_data' );

function add_internal_links() {
    // Define the number of internal links to add per post or page
    $num_links = 2;
    
    // Get all published posts and pages
    $args = array(
        'post_type' => array('post', 'page'),
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);
    
    // Loop through each post or page
    while ($query->have_posts()) {
        $query->the_post();
        
        // Get the post or page content
        $content = get_the_content();
        
        // Get all internal links in the content
        $internal_links = get_internal_links($content);
        
        // If there are fewer internal links than the desired number, add more links
        if (count($internal_links) < $num_links) {
            // Get a list of all other published posts and pages
            $args = array(
                'post_type' => array('post', 'page'),
                'post_status' => 'publish',
                'posts_per_page' => -1,
                'post__not_in' => array(get_the_ID()),
            );
            $all_posts = get_posts($args);
            
            // Choose random posts to link to
            $random_posts = array_rand($all_posts, $num_links - count($internal_links));
            if (!is_array($random_posts)) {
                $random_posts = array($random_posts);
            }
            
            // Add the internal links to the content
            foreach ($random_posts as $random_post) {
                $link_text = $all_posts[$random_post]->post_title;
                $link_url = get_permalink($all_posts[$random_post]->ID);
                $new_link = '<a href="' . $link_url . '">' . $link_text . '</a>';
                $content .= '<p>' . $new_link . '</p>';
            }
            
            // Update the post or page content
            wp_update_post(array(
                'ID' => get_the_ID(),
                'post_content' => $content,
            ));
        }
    }
    
    wp_reset_postdata();
}

function get_internal_links($content) {
    $internal_links = array();
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $links = $doc->getElementsByTagName('a');
    foreach ($links as $link) {
        $href = $link->getAttribute('href');
        if (strpos($href, home_url()) === 0) {
            $internal_links[] = $href;
        }
    }
    return $internal_links;
}

add_action('init', 'add_internal_links');

/*
 * Plugin Name: My SEO Plugin
 * Description: This plugin adds canonical tags to avoid duplicate content issues and consolidate link equity to the preferred version of your pages.
 */

function add_canonical_tag() {
    // Check if the current page has a canonical tag
    if (!is_singular()) {
        return;
    }
    $canonical_url = get_permalink();
    $canonical_url = esc_url($canonical_url);
    echo "<link rel='canonical' href='$canonical_url' />\n";
}
add_action('wp_head', 'add_canonical_tag');

function add_image_optimization() {
    // Define the maximum width of your images
    $max_width = 1200;

    // Get all published posts and pages
    $args = array(
        'post_type' => array('post', 'page'),
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    $query = new WP_Query($args);

    // Loop through each post or page
    while ($query->have_posts()) {
        $query->the_post();

        // Get the post or page content
        $content = get_the_content();

        // Get all images in the content
        $images = get_images($content);

        // Loop through each image
        foreach ($images as $image) {
            // Get the image URL
            $image_url = $image['src'];

            // Get the image alt text
            $alt_text = $image['alt'];

            // Get the image filename
            $filename = basename($image_url);

            // Get the image width and height
            list($width, $height) = getimagesize($image_url);

            // Calculate the new image dimensions
            $new_width = $width;
            $new_height = $height;
            if ($width > $max_width) {
                $new_width = $max_width;
                $new_height = $height * ($max_width / $width);
            }

            // Generate the new image URL
            $new_image_url = add_query_arg(array(
                'w' => $new_width,
                'h' => $new_height,
            ), $image_url);

            // Replace the image with the new optimized image
            $new_image_tag = '<img src="' . $new_image_url . '" alt="' . $alt_text . '" width="' . $new_width . '" height="' . $new_height . '"/>';
            $content = str_replace($image['tag'], $new_image_tag, $content);

            // Update the post or page content
            wp_update_post(array(
                'ID' => get_the_ID(),
                'post_content' => $content,
            ));
        }
    }

    wp_reset_postdata();
}

function get_images($content) {
    $images = array();
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $image_tags = $doc->getElementsByTagName('img');
    foreach ($image_tags as $image_tag) {
        $src = $image_tag->getAttribute('src');
        $alt = $image_tag->getAttribute('alt');
        $images[] = array(
            'src' => $src,
            'alt' => $alt,
            'tag' => $doc->saveHTML($image_tag),
        );
    }
    return $images;
}

/*
This function uses PHP GD library to resize images uploaded to WordPress media library.
It also adds the necessary attributes to make the images responsive.
*/
function optimize_images() {
    // Get all images in the content
    $content = get_post_field('post_content', get_the_ID());
    $doc = new DOMDocument();
    @$doc->loadHTML($content);
    $images = $doc->getElementsByTagName('img');
    
    // Loop through each image and optimize it
    foreach ($images as $image) {
        $src = $image->getAttribute('src');
        
        // Check if the image is already optimized
        if (strpos($src, '-optimized') !== false) {
            continue;
        }
        
        // Optimize the image
        $upload_dir = wp_upload_dir();
        $file_path = str_replace($upload_dir['baseurl'], $upload_dir['basedir'], $src);
        $image_editor = wp_get_image_editor($file_path);
        if (!is_wp_error($image_editor)) {
            $image_editor->resize(1200, 0, true);
            $image_editor->set_quality(80);
            $image_editor->save($file_path . '-optimized');
            $optimized_url = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $file_path . '-optimized');
            
            // Update the image tag with the optimized image URL and add responsive attributes
            $image->setAttribute('src', $optimized_url);
            $image->setAttribute('srcset', $optimized_url . ' 1200w, ' . $src . ' 600w');
            $image->setAttribute('sizes', '(max-width: 600px) 100vw, 1200px');
        }
    }
}

// Implement HTTPS
function my_seo_plugin_https() {
    if ( !is_ssl() ) {
        wp_redirect( 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301 );
        exit();
    }
}
add_action( 'template_redirect', 'my_seo_plugin_https' );

// Add Open Graph meta tags to the head section
function add_open_graph_tags() {
    global $post;

    if ( !is_singular() ) {
        return;
    }

    if ( !has_post_thumbnail( $post->ID ) ) {
        $thumbnail = get_stylesheet_directory_uri() . '/images/default.png';
    } else {
        $thumbnail = get_the_post_thumbnail_url( $post->ID, 'large' );
    }

    $og_title = get_the_title();
    $og_url = get_permalink();
    $og_description = get_the_excerpt();
    $og_site_name = get_bloginfo( 'name' );

    echo '<meta property="og:title" content="' . esc_attr( $og_title ) . '">';
    echo '<meta property="og:url" content="' . esc_attr( $og_url ) . '">';
    echo '<meta property="og:description" content="' . esc_attr( $og_description ) . '">';
    echo '<meta property="og:site_name" content="' . esc_attr( $og_site_name ) . '">';

    if ( has_post_thumbnail( $post->ID ) ) {
        echo '<meta property="og:image" content="' . esc_attr( $thumbnail ) . '">';
    }
}
add_action( 'wp_head', 'add_open_graph_tags' );

function social_media_tags() {
    // Your Open Graph and Twitter Card tags code here
    // This code will generate tags for social media sharing
}

add_action( 'wp_head', 'social_media_tags' );

/**
 * Function for generating XML sitemap.
 */
function generate_sitemap() {
    // Get all published posts and pages
    $args = array(
        'post_type' => array('post', 'page'),
        'post_status' => 'publish',
        'posts_per_page' => -1,
    );
    $posts = get_posts($args);

    // Start XML file
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    // Loop through each post and page to add it to the sitemap
    foreach ($posts as $post) {
        $xml .= '<url>';
        $xml .= '<loc>' . get_permalink($post->ID) . '</loc>';
        $xml .= '<lastmod>' . get_post_modified_time('c', true, $post->ID) . '</lastmod>';
        $xml .= '</url>';
    }

    // End XML file
    $xml .= '</urlset>';

    // Save sitemap as a file in the root directory of the website
    $file = fopen(ABSPATH . "sitemap.xml", "w");
    fwrite($file, $xml);
    fclose($file);

    // Submit sitemap to Google and Bing
    $sitemap_url = home_url() . '/sitemap.xml';
    $google_url = "http://www.google.com/webmasters/tools/ping?sitemap=" . urlencode($sitemap_url);
    $bing_url = "http://www.bing.com/ping?sitemap=" . urlencode($sitemap_url);

    wp_remote_get($google_url);
    wp_remote_get($bing_url);
}

// Generate sitemap on plugin activation
register_activation_hook(__FILE__, 'generate_sitemap');

// Resubmit sitemap on post or page update
function resubmit_sitemap() {
    generate_sitemap();
}
add_action('publish_post', 'resubmit_sitemap');
add_action('publish_page', 'resubmit_sitemap');

// Check if the user is allowed to access the options page
function my_seo_plugin_check_user_capability() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
}

    /**
 * Registers plugin settings using register_setting()
 */
function my_seo_plugin_register_settings() {
    register_setting( 'my_seo_plugin_options', 'my_seo_plugin_settings' );
}

/**
 * Adds settings sections using add_settings_section()
 */
function my_seo_plugin_add_settings_section() {
    add_settings_section(
        'my_seo_plugin_section',
        __( 'My SEO Plugin Settings', 'my-seo-plugin' ),
        'my_seo_plugin_section_callback',
        'my_seo_plugin_options'
    );
}

/**
 * Adds settings fields using add_settings_field()
 */
function my_seo_plugin_add_settings_fields() {
    add_settings_field(
        'my_seo_plugin_field',
        __( 'My SEO Plugin Field', 'my-seo-plugin' ),
        'my_seo_plugin_field_callback',
        'my_seo_plugin_options',
        'my_seo_plugin_section'
    );
}

/**
 * Defines callback function for the settings fields
 */
function my_seo_plugin_field_callback() {
    $options = get_option( 'my_seo_plugin_settings' );
    $value = isset( $options['my_seo_plugin_field'] ) ? $options['my_seo_plugin_field'] : '';
    echo '<input type="text" name="my_seo_plugin_settings[my_seo_plugin_field]" value="' . esc_attr( $value ) . '" />';
}

// Plugin Setup and Initialization
add_action( 'plugins_loaded', 'my_seo_plugin_init' );

function my_seo_plugin_init() {
    // Define constants (if any)
    
    // Load necessary files (if any)
    
    // Add action hooks to initialize the plugin
    add_action( 'admin_menu', 'my_seo_plugin_add_options_page' );
    add_action( 'admin_init', 'my_seo_plugin_settings_init' );
}

// Plugin Options and Settings
function my_seo_plugin_settings_init() {
    // Register settings using register_setting()
    register_setting( 'my_seo_plugin_settings', 'my_seo_plugin_text_field' );
    register_setting( 'my_seo_plugin_settings', 'my_seo_plugin_checkbox_field' );
    
    // Add settings sections using add_settings_section()
    add_settings_section( 'my_seo_plugin_section', 'My SEO Plugin Settings', 'my_seo_plugin_section_callback', 'my_seo_plugin' );
    
    // Add settings fields using add_settings_field()
    add_settings_field( 'my_seo_plugin_text_field', 'Text Field', 'my_seo_plugin_display_text_field', 'my_seo_plugin', 'my_seo_plugin_section' );
    add_settings_field( 'my_seo_plugin_checkbox_field', 'Checkbox Field', 'my_seo_plugin_display_checkbox_field', 'my_seo_plugin', 'my_seo_plugin_section' );
}

// Callback Functions for Settings Fields
function my_seo_plugin_display_text_field() {
    $value = get_option( 'my_seo_plugin_text_field' );
    echo '<input type="text" name="my_seo_plugin_text_field" value="' . esc_attr( $value ) . '" />';
}

function my_seo_plugin_display_checkbox_field() {
    $value = get_option( 'my_seo_plugin_checkbox_field' );
    echo '<input type="checkbox" name="my_seo_plugin_checkbox_field" value="1" ' . checked( $value, 1, false ) . ' />';
}

// Initialization and Output of Options Page
function my_seo_plugin_add_options_page() {
    add_options_page( 'My SEO Plugin', 'My SEO Plugin', 'manage_options', 'my_seo_plugin', 'my_seo_plugin_options_page' );
}

function my_seo_plugin_options_page() {
    ?>
    <div class="wrap">
        <h1>My SEO Plugin</h1>
        <form action="options.php" method="post">
            <?php settings_fields( 'my_seo_plugin_settings' ); ?>
            <?php do_settings_sections( 'my_seo_plugin' ); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

/**
 * Add a link to the settings page on the plugin page
 */
function my_seo_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=my_seo_plugin">' . __( 'Settings', 'my_seo_plugin' ) . '</a>';
    array_unshift( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'my_seo_plugin_add_settings_link' );