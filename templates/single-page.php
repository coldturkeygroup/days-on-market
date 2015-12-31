<?php
/**
 * Template file for displaying single Days on Market Funnel
 *
 * @package    WordPress
 * @subpackage Days on Market
 * @author     The Cold Turkey Group
 * @since      1.0.0
 */

global $days_market, $wp_query;

$id = get_the_ID();
$title = get_the_title();
$frontdesk_campaign = get_post_meta( $id, 'frontdesk_campaign', true );
$headline = get_post_meta($id, 'headline', true);
$sub_headline = get_post_meta($id, 'subheadline', true);
$broker = get_post_meta($id, 'legal_broker', true);
$cta = get_post_meta($id, 'call_to_action', true);
$retargeting = get_post_meta($id, 'retargeting', true);
$conversion = get_post_meta($id, 'conversion', true);
$photo = get_post_meta($id, 'photo', true);
$name = get_post_meta($id, 'name', true);
$valuator_link = get_post_meta($id, 'home_valuator', true);
$phone = of_get_option('phone_number');
$city_placeholder = get_post_meta($id, 'city_placeholder', true);
$show_sqft = get_post_meta($id, 'show_sqft', true);
$modal_title = get_post_meta($id, 'modal_title', true);
$modal_subtitle = get_post_meta($id, 'modal_subtitle', true);
$modal_button = get_post_meta($id, 'modal_button', true);

// Get the background image
if (has_post_thumbnail($id))
    $img = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');

// Get the page colors
if (function_exists('of_get_option')) {
    $primary_color = of_get_option('primary_color');
    $hover_color = of_get_option('secondary_color');
}

if (!$headline || $headline == '') {
    $headline = 'Listing Calculator';
}

if (!$sub_headline || $sub_headline == '') {
    $sub_headline = 'How long will it take to sell my home?';
}

$color_setting = get_post_meta($id, 'primary_color', true);
$hover_setting = get_post_meta($id, 'hover_color', true);

if ($color_setting && $color_setting != '') {
    $primary_color = $color_setting;
}

if ($hover_setting && $hover_setting != '') {
    $hover_color = $hover_setting;
}

if (!$city_placeholder || $city_placeholder == '') {
    $city_placeholder = 'Chicago';
}

if (!$modal_title || $modal_title == '') {
    $modal_title = 'Where should we send your results?';
}

if (!$modal_subtitle || $modal_subtitle == '') {
    $modal_subtitle = 'How long will it take to sell a <span id="sq_ft-answer"></span> square foot <span id="type-answer"></span> (<span id="num_beds-answer"></span> bedrooms, <span id="num_baths-answer"></span> bathrooms) located in <span id="location-answer"></span>.';
}

if (!$modal_button || $modal_button == '') {
    $modal_button = 'See My Results';
}

?>
    <!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="utf-8">
        <title><?php wp_title('&middot;', true, 'right'); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <?php wp_head(); ?>
        <style>
            .single-pf_days_on_market {
                background: url(<?= $img[0]; ?>) no-repeat fixed center center;
                background-size: cover;
                background-attachment: fixed;
            }

            <?php
            if( $primary_color != null ) {
                echo '
                .dom-page .btn-primary {
                    background-color: ' . $primary_color . ' !important;
                    border-color: ' . $primary_color . ' !important; }
                .modal-body h2,
                .dom-page .results .fa {
                    color: ' . $primary_color . ' !important; }
                ';
            }
            if( $hover_color != null ) {
                echo '
                .dom-page .btn-primary:hover,
                .dom-page .btn-primary:active {
                    background-color: ' . $hover_color . ' !important;
                    border-color: ' . $hover_color . ' !important; }
                ';
            }
            ?>
        </style>
        <!--[if lt IE 9]>
        <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <script src="assets/js/respond.min.js"></script>
        <![endif]-->
    </head>

<body <?php body_class(); ?>>
<div class="dom-page">

    <div class="container-fluid">

        <div class="results" style="display:none">
            <div class="row">
                <div class="col-xs-12 col-sm-3 col-md-offset-2">
                    <img src="<?= $photo ?>" class="img-responsive img-thumbnail">
                </div>
                <div class="col-sm-5">
                    Hey, it's <?= $name ?>. I will send you an custom report within the next 24 hours. <br>
                    Thanks for using the listing calculator tool! <br>
                    <strong>I'll email you a custom link as soon as I've
                        researched
                        how long it will take to sell your home.</strong>
                    <?php if ($valuator_link != '') { ?>
                        <br> Oh, and one more thing. Would you like to find out what your home is worth?
                        <br> (Instant home value report is automatically calculated based on recent sold listings in your area.)
                        <a href="<?= $valuator_link ?>" class="btn btn-primary btn-block">See What My Home Is Worth</a>
                    <?php } ?>
                </div>
            </div>
        </div>

        <form id="days-on-market">
            <div class="row page animated fadeIn">
                <div class="col-xs-10 col-xs-offset-1 col-sm-12 col-sm-offset-0 col-md-8 col-md-offset-2" id="landing" data-model="landing">
                    <h1 style="text-align: center;" class="landing-title"><?= $headline ?></h1>

                    <h2 style="text-align: center;" id="subtitle"><?= $sub_headline ?></h2>

                    <div class="form-group">
                        <label class="control-label" for="type">What Type Of Property Are You Selling?</label>
                        <select class="form-control validate" id="type" name="type">
                            <option value="Single Family Home">Single Family Home</option>
                            <option value="Condo">Condo</option>
                            <option value="Villa/Townhouse">Villa/Townhouse</option>
                        </select>
                    </div>

                    <?php if (isset($show_sqft) && $show_sqft == 'no') { ?>
                        <div class="form-group">
                            <label class="control-label" for="location">Location of Property (City)</label>
                            <input type="text" class="form-control validate" id="location" name="location" placeholder="<?= $city_placeholder ?>">
                        </div>
                    <?php } else { ?>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <label class="control-label" for="location">Location of Property (City)</label>
                                <input type="text" class="form-control validate" id="location" name="location" placeholder="<?= $city_placeholder ?>">
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <label class="control-label" for="sq_ft">How Many Square Feet Is Your Home?</label>
                                <input type="text" class="form-control validate" id="sq_ft" name="sq_ft" placeholder="2,500">
                            </div>
                        </div>
                    <?php } ?>

                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <label class="control-label" for="num_beds">Bedrooms</label>
                            <input type="text" class="form-control validate" id="num_beds" name="num_beds" placeholder="Any # Beds">
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <label class="control-label" for="num_baths">Bathrooms</label>
                            <input type="text" class="form-control validate" id="num_baths" name="num_baths" placeholder="Any # Baths">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="features">Describe The Important Features of Your Home</label>
                        <textarea class="form-control validate" rows="5" id="features" name="features" placeholder="Pool, new flooring, updated kitchen, fenced in back yard, etc"></textarea>
                    </div>

                    <div class="form-group">
                        <label class="control-label" for="desired_price">What Is Your Desired Sales Price?</label>

                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" class="form-control validate" id="desired_price" name="desired_price" placeholder="125,000">
                        </div>
                    </div>

                    <button class="btn btn-primary btn-lg btn-block" id="get-results"><?= $cta ?></button>
                </div>
            </div>

            <div class="modal fade" id="get-results-modal" tabindex="-1" role="dialog" aria-labelledby="get-results-label" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h1><?= $modal_title ?></h1>

                            <p><?= $modal_subtitle ?></p>

                            <div class="form-group" style="margin-top:20px">
                                <label for="first_name" class="control-label">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" required="required" placeholder="Your First Name">
                            </div>
                            <div class="form-group">
                                <label for="email" class="control-label">Email Address</label>
                                <input type="text" name="email" id="email" class="form-control" required="required" placeholder="Your Email Address">
                            </div>

                            <input name="frontdesk_campaign" type="hidden" value="<?= $frontdesk_campaign ?>">
                            <input name="action" type="hidden" id="pf_days_on_market_submit_form" value="pf_days_on_market_submit_form">
                            <?php wp_nonce_field('pf_days_on_market_submit_form', 'pf_days_on_market_nonce'); ?>
                            <input name="page_id" type="hidden" value="<?= $id ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary btn-block" id="submit-results"><?= $modal_button ?></button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>

    <div class="footer">
        <?php echo $broker;
        if ($phone != null) {
            echo ' &middot; ' . $phone;
        } ?>
    </div>

    <?php
    if ( $retargeting != null ) {
        ?>
        <!-- Facebook Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
        n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
        document,'script','//connect.facebook.net/en_US/fbevents.js');

        fbq('init', '<?= $retargeting ?>');
        fbq('track', "PageView");</script>
        <noscript><img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?= $retargeting ?>&ev=PageView&noscript=1"
        /></noscript>
        <?php
        echo '<input type="hidden" id="retargeting" value="' . $retargeting . '">';
    }

    if ( $conversion != null ) {
        echo '<input type="hidden" id="conversion" value="' . $conversion . '">';
    }
    ?>
</div>

<?php wp_footer(); ?>