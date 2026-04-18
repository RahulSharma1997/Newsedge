<?php
    $ajax_section = $_GET['ajax_section'] ?? '';
    $is_ajax = !empty($ajax_section);

    // GLOBAL API FETCH - Hum API ko sirf 1 bar top par call karenge taake website fast load ho aur API limit bachi rahe.
    $api_keys = ['51e6b14859734b30a38646fbaaad2600', '79da4acf3f134d8f94cea28e6a5b9fe1'];
    
    $api_ok = false;
    $global_newsData = [];
    $global_response = '';
    $global_curl_err = '';

    // Only run global API fetch on main load or if 'more_news' section needs it for sidebar
    if (!$is_ajax || $ajax_section === 'more_news') {
        foreach ($api_keys as $key) {
        // Using 'everything' endpoint for more reliable results as 'top-headlines' for 'in' can sometimes return 0 results on the free plan.
        $url = 'https://newsapi.org/v2/everything?q=india&language=en&sortBy=publishedAt&apiKey=' . $key . '&pageSize=20';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $global_response = curl_exec($ch);
        $global_curl_err = curl_error($ch);
        curl_close($ch);

        $global_newsData = json_decode($global_response, true);
        
        // Agar is key se data mil gaya, toh loop rok dein
        if (isset($global_newsData['status']) && $global_newsData['status'] === 'ok' && !empty($global_newsData['articles'])) {
            $api_ok = true;
            break; 
        }
        }
    }

    // Helper function to fetch news by category (Isme bhi dono keys try hongi)
    function fetchNewsByCategory($category, $apiKeysArray, $pageSize = 4) {
        foreach ($apiKeysArray as $key) {
            // Using 'everything' endpoint here as well because 'top-headlines' can be unreliable on the free plan.
            $category_url = "https://newsapi.org/v2/everything?q=" . urlencode("india " . $category) . "&language=en&sortBy=publishedAt&apiKey=" . $key . "&pageSize=" . $pageSize;
            
            $ch_cat = curl_init();
            curl_setopt($ch_cat, CURLOPT_URL, $category_url);
            curl_setopt($ch_cat, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch_cat, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/115.0.0.0 Safari/537.36');
            curl_setopt($ch_cat, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch_cat, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch_cat);
            curl_close($ch_cat);
            
            $data = json_decode($response, true);
            
            if (isset($data['status']) && $data['status'] === 'ok' && !empty($data['articles'])) {
                return $data['articles']; // Success mil jaye to return karden
            }
        }
        return []; // Agar dono keys fail ho jayen tab jaa kar empty return ho
    }

if (!$is_ajax) {
?>
<!doctype html>
<html class="no-js" lang="">

<head>
    <?php include 'master/link.php'; ?>
    <style>
        .loader-container { padding: 60px 0; text-align: center; color: #777; }
        .loader-container i { color: #e74c3c; margin-bottom: 15px; }
        .loader-container p { font-size: 16px; font-weight: 500; }
    </style>
</head>

<body>

    <div id="wrapper">

        <!-- Header Area Start Here -->
        <header>
            <div id="header-layout2" class="header-style2">
                <div class="header-top-bar">
                    <div class="top-bar-top bg-accent border-bottom">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-8 col-md-12">
                                    <ul class="news-info-list text-center--md">
                                        <li>
                                            <i class="fa fa-map-marker" aria-hidden="true"></i>Australia
                                        </li>
                                        <li>
                                            <i class="fa fa-calendar" aria-hidden="true"></i><span
                                                id="current_date"></span>
                                        </li>
                                        <li>
                                            <i class="fa fa-clock-o" aria-hidden="true"></i>Last Update 11.30 am
                                        </li>
                                        <li>
                                            <i class="fa fa-cloud" aria-hidden="true"></i>29&#8451; Sydney, Australia
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-lg-4 d-none d-lg-block">
                                    <ul class="header-social">
                                        <li>
                                            <a href="#" title="facebook">
                                                <i class="fa fa-facebook" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" title="twitter">
                                                <i class="fa fa-twitter" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" title="google-plus">
                                                <i class="fa fa-google-plus" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" title="linkedin">
                                                <i class="fa fa-linkedin" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" title="pinterest">
                                                <i class="fa fa-pinterest" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" title="rss">
                                                <i class="fa fa-rss" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" title="vimeo">
                                                <i class="fa fa-vimeo" aria-hidden="true"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="top-bar-bottom bg-body pt-20 d-none d-lg-block">
                        <div class="container">
                            <div class="row d-flex align-items-center">
                                <div class="col-lg-4 col-md-4 col-sm-4">
                                    <div class="logo-area">
                                        <a href="index-2.html" class="img-fluid">
                                            <img src="img/logo-dark.png" alt="logo">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-8 col-md-8 col-sm-8">
                                    <div class="ne-banner-layout1 pull-right">
                                        <a href="#">
                                            <img src="img/banner/banner2.jpg" alt="ad" class="img-fluid">
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="main-menu-area bg-body" id="sticker">
                    <div class="container">
                        <div class="row no-gutters d-flex align-items-center">
                            <div class="col-lg-10 position-static d-none d-lg-block">
                                <div class="ne-main-menu">
                                    <nav id="dropdown">
                                        <ul>
                                            <li class="active">
                                                <a href="#">Home</a>
                                                <ul class="ne-dropdown-menu">
                                                    <li>
                                                        <a href="index-2.html">Home 1</a>
                                                    </li>
                                                    <li class="active">
                                                        <a href="index2.html">Home 2</a>
                                                    </li>
                                                    <li>
                                                        <a href="index3.html">Home 3</a>
                                                    </li>
                                                    <li>
                                                        <a href="index4.html">Home 4</a>
                                                    </li>
                                                    <li>
                                                        <a href="index5.html">Home 5</a>
                                                    </li>
                                                    <li>
                                                        <a href="index6.html">Home 6</a>
                                                    </li>
                                                    <li>
                                                        <a href="index7.html">Home 7</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="#">Post</a>
                                                <ul class="ne-dropdown-menu">
                                                    <li>
                                                        <a href="post-style-1.html">Post Style 1</a>
                                                    </li>
                                                    <li>
                                                        <a href="post-style-2.html">Post Style 2</a>
                                                    </li>
                                                    <li>
                                                        <a href="post-style-3.html">Post Style 3</a>
                                                    </li>
                                                    <li>
                                                        <a href="post-style-4.html">Post Style 4</a>
                                                    </li>
                                                    <li>
                                                        <a href="single-news-1.html">News Details 1</a>
                                                    </li>
                                                    <li>
                                                        <a href="single-news-2.html">News Details 2</a>
                                                    </li>
                                                    <li>
                                                        <a href="single-news-3.html">News Details 3</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="#">Pages</a>
                                                <ul class="ne-dropdown-menu">
                                                    <li>
                                                        <a href="author-post.html">Author Post Page</a>
                                                    </li>
                                                    <li>
                                                        <a href="archive.html">Archive Page</a>
                                                    </li>
                                                    <li>
                                                        <a href="gallery-style-1.html">Gallery Style 1</a>
                                                    </li>
                                                    <li>
                                                        <a href="gallery-style-2.html">Gallery Style 2</a>
                                                    </li>
                                                    <li>
                                                        <a href="404.html">404 Error Page</a>
                                                    </li>
                                                    <li>
                                                        <a href="contact.html">Contact Page</a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li>
                                                <a href="post-style-1.html">Politics</a>
                                            </li>
                                            <li>
                                                <a href="post-style-2.html">Business</a>
                                            </li>
                                            <li>
                                                <a href="post-style-3.html">Sports</a>
                                            </li>
                                            <li>
                                                <a href="post-style-4.html">Fashion</a>
                                            </li>
                                            <li>
                                                <a href="post-style-1.html">Travel</a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-12 text-right position-static">
                                <div class="header-action-item on-mobile-fixed">
                                    <ul>
                                        <li>
                                            <form id="top-search-form" class="header-search-light">
                                                <input type="text" class="search-input" placeholder="Search...."
                                                    required="" style="display: none;">
                                                <button class="search-button">
                                                    <i class="fa fa-search" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </li>
                                        <li class="d-none d-sm-block d-md-block d-lg-none">
                                            <button type="button" class="login-btn" data-toggle="modal"
                                                data-target="#myModal">
                                                <i class="fa fa-user" aria-hidden="true"></i>Sign in
                                            </button>
                                        </li>
                                        <li>
                                            <div id="side-menu-trigger"
                                                class="offcanvas-menu-btn offcanvas-btn-repoint">
                                                <a href="#" class="menu-bar">
                                                    <span></span>
                                                    <span></span>
                                                    <span></span>
                                                </a>
                                                <a href="#" class="menu-times close">
                                                    <span></span>
                                                    <span></span>
                                                </a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Header Area End Here -->
        <!-- News Feed Area Start Here -->
        <section class="bg-accent add-top-margin">
            <div class="container">
                <div class="row no-gutters d-flex align-items-center">
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="topic-box mt-10 mb-10">Top Stories</div>
                    </div>
                    <div class="col-lg-10 col-md-9 col-sm-8 col-6">
                        <div class="feeding-text-dark">
                            <ol id="sample" class="ticker">

                                <?php
                                    if ($api_ok) {
                                        // Ticker ke liye pehli 5 news nikal lein (0 se 5)
                                        $articles = array_slice($global_newsData['articles'], 0, 5);

                                    foreach ($articles as $article) {
                                        if (empty($article['title']) || empty($article['url']))
                                            continue;
                                        $title = htmlspecialchars($article['title'] ?? '');
                                        $slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                        $newsUrl = 'news-' . $slug;
                                        $date = !empty($article['publishedAt']) ? date('F d, Y', strtotime($article['publishedAt'])) : '';

                                        echo '<li>
                                                    <a href="' . $newsUrl . '">' . substr($title, 0, 60) . '...</a> 
                                                </li>';
                                    }
                                } else {
                                        $api_msg = isset($global_newsData['message']) ? $global_newsData['message'] : ($global_response ? 'Raw: ' . htmlspecialchars(substr($global_response, 0, 100)) : 'No data received');
                                        echo '<li><span class="text-danger">Ticker Load Nahi Ho Raha: ' . $api_msg . '</span></li>';
                                }
                                ?>

                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- News Feed Area End Here -->
        <!-- News Slider Area Start Here -->
        <section class="bg-accent section-space-bottom-less4">
            <div class="container">
                <div class="row tab-space2">
                    <div class="col-md-8 col-sm-12 mb-4">
                        <?php
                        if ($api_ok) {
                            // Main Slider Banner ke liye 1 bari news set karein (Index 5)
                            $articles = array_slice($global_newsData['articles'], 5, 1);

                            foreach ($articles as $article) {
                                if (empty($article['title']) || empty($article['url']))
                                    continue;
                                $imageUrl = !empty($article['urlToImage']) ? htmlspecialchars($article['urlToImage']) : 'https://picsum.photos/300/200';
                                $author = htmlspecialchars($article['author'] ?? 'Unknown Author');
                                $title = htmlspecialchars($article['title'] ?? '');
                                $slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                $newsUrl = 'news-' . $slug;
                                $date = !empty($article['publishedAt']) ? date('F d, Y', strtotime($article['publishedAt'])) : '';

                                echo '<div class="img-overlay-70 img-scale-animate">
                            <img src="' . $imageUrl . '" alt="news" class="img-fluid width-100" onerror="this.onerror=null;this.src=\'https://picsum.photos/300/200\';">
                            <div class="mask-content-lg">
                                <div class="topic-box-sm color-cinnabar mb-20">Top Story</div>
                                <div class="post-date-light">
                                    <ul>
                                        <li>
                                            <span>by</span>
                                             <a> ' . $author . '</a>
                                        </li>
                                        <li>
                                            <span>
                                                <i class="fa fa-calendar" aria-hidden="true"></i>
                                            </span> '. $date.'
                                        </li>
                                    </ul>
                                </div>
                                <h1 class="title-medium-light d-none d-sm-block">
                                    <a href="' . $newsUrl . '"> ' . substr($title, 0, 160) . '...</a>
                                </h1>
                            </div>
                        </div>';
                            }
                        } else {
                            $api_msg = isset($global_newsData['message']) ? $global_newsData['message'] : ($global_response ? 'Raw: ' . htmlspecialchars(substr($global_response, 0, 200)) : 'No data received');
                            echo '<div class="alert alert-danger w-100" style="padding: 15px;"><strong>News Load Nahi Ho Rahi!</strong><br><b>cURL Error:</b> ' . ($global_curl_err ? $global_curl_err : 'None') . '<br><b>API Error:</b> ' . $api_msg . '</div>';
                        }
                        ?>

                    </div>
                    <div class="col-md-4 col-sm-12">
                        <?php
                        if ($api_ok) {
                            // Side ke 2 chote banners ke liye next 2 news set karein (Index 6 aur 7)
                            $side_articles = array_slice($global_newsData['articles'], 6, 2);
                            foreach ($side_articles as $article) {
                                $imageUrl = !empty($article['urlToImage']) ? htmlspecialchars($article['urlToImage']) : 'https://picsum.photos/300/200';
                                $title = htmlspecialchars($article['title'] ?? '');
                                $newsUrl = 'single-page.php?title=' . urlencode($article['title'] ?? '');
                                $slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                $newsUrl = 'news-' . $slug;
                                
                                echo '<div class="img-overlay-70 img-scale-animate mb-4">
                                    <div class="mask-content-sm">
                                        <div class="topic-box-sm color-apple mb-10">Trending</div>
                                        <h3 class="title-medium-light">
                                            <a href="' . $newsUrl . '">' . substr($title, 0, 55) . '...</a>
                                        </h3>
                                    </div>
                                    <img src="' . $imageUrl . '" alt="news" class="img-fluid width-100" style="height: 220px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/300/200\';">
                                </div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
        <!-- News Slider Area End Here -->
        
<?php } // end if (!$is_ajax) ?>
<?php if ($ajax_section === 'top_story') { ?>        
        <!-- Top Story Area Start Here -->
        <section class="bg-accent section-space-bottom">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="ne-isotope">
                            <div class="topic-border color-cinnabar mb-30">
                                <div class="topic-box-lg color-cinnabar">Top Stories</div>
                                <div class="isotope-classes-tab isotop-btn">
                                    <a href="#" data-filter=".politics" class="current">Politics</a>
                                    <a href="#" data-filter=".fashion">Fashion</a>
                                    <a href="#" data-filter=".health">Health &amp; Fitness</a>
                                    <a href="#" data-filter=".travel">Travel</a>
                                    <a href="#" data-filter=".gadget">Gadget</a>
                                </div>
                                <div class="more-info-link">
                                    <a href="post-style-1.html">More
                                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="featuredContainer">
                                <?php
                                $isotope_categories = [
                                    'politics' => 'Politics',
                                    'fashion'  => 'Fashion',
                                    'health'   => 'Health & Fitness',
                                    'travel'   => 'Travel',
                                    'gadget'   => 'Technology'
                                ];

                                foreach ($isotope_categories as $class_name => $query_term) {
                                    $cat_news = fetchNewsByCategory($query_term, $api_keys, 7);
                                    
                                    echo '<div class="row ' . $class_name . '">';
                                    
                                    if (!empty($cat_news)) {
                                        $main_article = isset($cat_news[0]) ? $cat_news[0] : null;
                                        $sub_articles = array_slice($cat_news, 1, 6);
                                        
                                        if ($main_article) {
                                            $main_img = !empty($main_article['urlToImage']) ? htmlspecialchars($main_article['urlToImage']) : 'https://picsum.photos/380/450';
                                            $main_title = htmlspecialchars($main_article['title'] ?? 'No Title');
                                            $main_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($main_article['title'] ?? '')), '-');
                                            $main_url = 'news-' . $main_slug;
                                            $main_author = htmlspecialchars($main_article['author'] ?? 'Unknown');
                                            $main_date = !empty($main_article['publishedAt']) ? date('F d, Y', strtotime($main_article['publishedAt'])) : '';

                                            echo '
                                            <div class="col-xl-4 col-lg-5 col-md-12 mb-30">
                                                <div class="img-overlay-70 img-scale-animate">
                                                    <a href="' . $main_url . '">
                                                        <img src="' . $main_img . '" alt="news" class="img-fluid width-100" style="height: 450px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/380/450\';">
                                                    </a>
                                                    <div class="mask-content-lg">
                                                        <div class="topic-box-sm color-apple mb-20">' . htmlspecialchars($query_term) . '</div>
                                                        <div class="post-date-light">
                                                            <ul>
                                                                <li>
                                                                    <span>by</span>
                                                                    <a>' . $main_author . '</a>
                                                                </li>
                                                                <li>
                                                                    <span>
                                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </span>' . $main_date . '
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <h2 class="title-medium-light size-lg">
                                                            <a href="' . $main_url . '">' . substr($main_title, 0, 60) . '...</a>
                                                        </h2>
                                                    </div>
                                                </div>
                                            </div>';
                                        }

                                        echo '<div class="col-xl-8 col-lg-7 col-md-12"><div class="row">';
                                        
                                        $col1_articles = array_slice($sub_articles, 0, 3);
                                        $col2_articles = array_slice($sub_articles, 3, 3);
                                        
                                        foreach ([$col1_articles, $col2_articles] as $col_articles) {
                                            echo '<div class="col-sm-6 col-12">';
                                            foreach ($col_articles as $article) {
                                                $sub_img = !empty($article['urlToImage']) ? htmlspecialchars($article['urlToImage']) : 'https://picsum.photos/150/150';
                                                $sub_title = htmlspecialchars($article['title'] ?? 'No Title');
                                                $sub_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                                $sub_url = 'news-' . $sub_slug;
                                                $sub_date = !empty($article['publishedAt']) ? date('F d, Y', strtotime($article['publishedAt'])) : '';
                                                
                                                echo '
                                                <div class="media bg-body item-shadow-gray mb-30">
                                                    <a class="img-opacity-hover" href="' . $sub_url . '">
                                                        <img src="' . $sub_img . '" alt="news" class="img-fluid" style="width: 120px; height: 100px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/150/150\';">
                                                    </a>
                                                    <div class="media-body media-padding10">
                                                        <div class="post-date-dark">
                                                            <ul>
                                                                <li>
                                                                    <span>
                                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                    </span>' . $sub_date . '
                                                                </li>
                                                            </ul>
                                                        </div>
                                                        <h3 class="title-medium-dark mb-none">
                                                            <a href="' . $sub_url . '">' . substr($sub_title, 0, 50) . '...</a>
                                                        </h3>
                                                    </div>
                                                </div>';
                                            }
                                            echo '</div>';
                                        }

                                        echo '</div></div>';
                                    } else {
                                        echo '<div class="col-12"><p>No news found for ' . htmlspecialchars($query_term) . '.</p></div>';
                                    }
                                    
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="ne-banner-layout1 mt-20-r text-center">
                            <a href="#">
                                <img src="img/banner/banner2.jpg" alt="ad" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Top Story Area End Here -->
        <script>
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.isotope !== 'undefined') {
            jQuery('.ne-isotope').each(function() {
                var $container = jQuery(this).find('.featuredContainer');
                var defaultFilter = jQuery(this).find('.isotop-btn a.current').attr('data-filter') || '*';
                $container.isotope({ filter: defaultFilter });
                jQuery(this).find('.isotop-btn a').off('click').on('click', function() {
                    jQuery(this).addClass('current').siblings().removeClass('current');
                    var selector = jQuery(this).attr('data-filter');
                    $container.isotope({ filter: selector });
                    return false;
                });
            });
        }
        </script>
        <?php exit; ?>
<?php } elseif (!$is_ajax) { ?>
    <div class="lazy-load-section" data-section="top_story"><div class="loader-container"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading Top Stories...</p></div></div>
<?php } ?>

<?php if ($ajax_section === 'international_story') { ?>
        <!-- International Story Area Start Here -->
        <section class="bg-accent section-space-bottom-less30">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-12">
                        <div class="topic-border color-persian-green mb-30">
                            <div class="topic-box-lg color-persian-green">International</div>
                        </div>
                        <div class="row">
                            <?php
                            $international_news = [];
                            foreach ($api_keys as $key) {
                                $url = "https://newsapi.org/v2/everything?q=world&language=en&sortBy=publishedAt&apiKey=" . $key . "&pageSize=4";
                                
                                $ch_cat = curl_init();
                                curl_setopt($ch_cat, CURLOPT_URL, $url);
                                curl_setopt($ch_cat, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch_cat, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
                                curl_setopt($ch_cat, CURLOPT_FOLLOWLOCATION, true);
                                curl_setopt($ch_cat, CURLOPT_SSL_VERIFYPEER, false);
                                $response = curl_exec($ch_cat);
                                curl_close($ch_cat);
                                
                                $data = json_decode($response, true);
                                if (isset($data['status']) && $data['status'] === 'ok' && !empty($data['articles'])) {
                                    $international_news = $data['articles'];
                                    break;
                                }
                            }
                            
                            if (!empty($international_news)) {
                                $main_article = array_slice($international_news, 0, 1)[0];
                                $sub_articles = array_slice($international_news, 1, 3);
                                
                                $main_img = !empty($main_article['urlToImage']) ? htmlspecialchars($main_article['urlToImage']) : 'https://picsum.photos/380/270';
                                $main_title = htmlspecialchars($main_article['title'] ?? 'No Title');
                                $main_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($main_article['title'] ?? '')), '-');
                                $main_url = 'news-' . $main_slug;
                                $main_date = !empty($main_article['publishedAt']) ? date('F d, Y', strtotime($main_article['publishedAt'])) : '';

                                echo '<div class="col-md-6 col-sm-12 mb-30">
                                    <div class="img-overlay-70 img-scale-animate">
                                        <a href="' . $main_url . '">
                                            <img src="' . $main_img . '" alt="news" class="img-fluid width-100" style="height: 380px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/380/270\';">
                                        </a>
                                    </div>
                                    <ul class="item-box-light-mix item-shadow-gray">
                                        <li>
                                            <div class="post-date-dark">
                                                <ul>
                                                    <li>
                                                        <span>
                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                        </span>' . $main_date . '
                                                    </li>
                                                </ul>
                                            </div>
                                            <h3 class="title-medium-dark">
                                                <a href="' . $main_url . '">' . substr($main_title, 0, 80) . '...</a>
                                            </h3>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6 col-sm-12">';
                                
                                foreach ($sub_articles as $article) {
                                    $sub_img = !empty($article['urlToImage']) ? htmlspecialchars($article['urlToImage']) : 'https://picsum.photos/150/150';
                                    $sub_title = htmlspecialchars($article['title'] ?? 'No Title');
                                    $sub_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                    $sub_url = 'news-' . $sub_slug;
                                    $sub_date = !empty($article['publishedAt']) ? date('F d, Y', strtotime($article['publishedAt'])) : '';

                                    echo '<div class="media bg-body item-shadow-gray mb-30">
                                        <a class="img-opacity-hover width34-lg width30-md" href="' . $sub_url . '">
                                            <img src="' . $sub_img . '" alt="news" class="img-fluid" style="width: 150px; height: 110px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/150/150\';">
                                        </a>
                                        <div class="media-body media-padding15">
                                            <div class="post-date-dark">
                                                <ul>
                                                    <li>
                                                        <span>
                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                        </span>' . $sub_date . '
                                                    </li>
                                                </ul>
                                            </div>
                                            <h3 class="title-medium-dark mb-none">
                                                <a href="' . $sub_url . '">' . substr($sub_title, 0, 60) . '...</a>
                                            </h3>
                                        </div>
                                    </div>';
                                }
                                echo '</div>';
                            } else {
                                echo '<div class="col-12"><p>Could not load international news.</p></div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="ne-sidebar sidebar-break-md col-lg-4 col-md-12">
                        <div class="sidebar-box">
                            <div class="topic-border color-cod-gray mb-30">
                                <div class="topic-box-lg color-cod-gray">Stay Connected</div>
                            </div>
                            <ul class="stay-connected-light overflow-hidden">
                                <li class="facebook">
                                    <a href="#">
                                        <i class="fa fa-facebook" aria-hidden="true"></i>
                                        <div class="connection-quantity">50.2 k</div>
                                        <p>Fans</p>
                                    </a>
                                </li>
                                <li class="twitter">
                                    <a href="#">
                                        <i class="fa fa-twitter" aria-hidden="true"></i>
                                        <div class="connection-quantity">10.3 k</div>
                                        <p>Followers</p>
                                    </a>
                                </li>
                                <li class="linkedin">
                                    <a href="#">
                                        <i class="fa fa-linkedin" aria-hidden="true"></i>
                                        <div class="connection-quantity">25.4 k</div>
                                        <p>Fans</p>
                                    </a>
                                </li>
                                <li class="rss">
                                    <a href="#">
                                        <i class="fa fa-rss" aria-hidden="true"></i>
                                        <div class="connection-quantity">20.8 k</div>
                                        <p>Subscriber</p>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="sidebar-box">
                            <div class="ne-banner-layout1 text-center">
                                <a href="#">
                                    <img src="img/banner/banner11.jpg" alt="ad" class="img-fluid">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- International Story Area End Here -->
        <?php exit; ?>
<?php } elseif (!$is_ajax) { ?>
    <div class="lazy-load-section" data-section="international_story"><div class="loader-container"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading International News...</p></div></div>
<?php } ?>

<?php if (!$is_ajax) { ?>
        <!-- Latest News Area Start Here -->
        <section class="bg-secondary-accent section-space-less30">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="topic-border color-scampi mb-30 width-100">
                            <div class="topic-box-lg color-scampi">Latest News (Live)</div>
                        </div>
                    </div>
                </div>
                <div class="ne-carousel nav-control-top2 color-scampi" data-loop="true" data-items="4" data-margin="20"
                    data-autoplay="true" data-autoplay-timeout="5000" data-smart-speed="2000" data-dots="false"
                    data-nav="true" data-nav-speed="false" data-r-x-small="1" data-r-x-small-nav="true"
                    data-r-x-small-dots="false" data-r-x-medium="2" data-r-x-medium-nav="true"
                    data-r-x-medium-dots="false" data-r-small="2" data-r-small-nav="true" data-r-small-dots="false"
                    data-r-medium="3" data-r-medium-nav="true" data-r-medium-dots="false" data-r-Large="4"
                    data-r-Large-nav="true" data-r-Large-dots="false">
                    <?php
                    if ($api_ok) {
                        // Carousel ke liye baki bachi hui 6 news (Index 8 se 13)
                        $articles = array_slice($global_newsData['articles'], 8, 6);

                        foreach ($articles as $article) {
                            if (empty($article['title']) || empty($article['url']))
                                continue;

                            $imageUrl = !empty($article['urlToImage']) ? htmlspecialchars($article['urlToImage']) : 'https://picsum.photos/300/200';
                            $title = htmlspecialchars($article['title'] ?? '');
                                $slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                $newsUrl = 'news-' . $slug;
                            $date = !empty($article['publishedAt']) ? date('F d, Y', strtotime($article['publishedAt'])) : '';

                            echo '
                                <div class="hover-show-play-btn item-shadow-gray mb-30">
                                    <div class="img-overlay-70">
                                        <img src="' . $imageUrl . '" alt="news" class="img-fluid width-100" style="height: 200px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/300/200\';">
                                    </div>
                                    <div class="box-padding30 bg-body item-shadow-gray">
                                        <div class="post-date-dark">
                                            <ul>
                                                <li>
                                                    <span>
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </span>' . $date . '</li>
                                            </ul>
                                        </div>
                                        <h3 class="title-medium-dark">
                                            <a href="' . $newsUrl . '">' . substr($title, 0, 60) . '...</a>
                                        </h3>
                                    </div>
                                </div>';
                        }
                    } else {
                        $api_msg = isset($global_newsData['message']) ? $global_newsData['message'] : ($global_response ? 'Raw: ' . htmlspecialchars(substr($global_response, 0, 200)) : 'No data received');
                        echo '<div class="alert alert-danger w-100" style="padding: 15px;"><strong>News Load Nahi Ho Rahi!</strong><br><b>cURL Error:</b> ' . ($global_curl_err ? $global_curl_err : 'None') . '<br><b>API Error:</b> ' . $api_msg . '</div>';
                    }
                    ?>
                </div>
            </div>
        </section>
        <!-- Latest News Area End Here -->
<?php } ?>

<?php if ($ajax_section === 'category_news') { ?>
        <!-- Category News Area Start Here -->
        <section class="bg-accent section-space-default">
            <div class="container">
                <div class="row">
                    <!-- Fashion / Entertainment News Column -->
                    <div class="col-md-4 col-sm-12 mb-30">
                        <div class="topic-border color-apple mb-30 width-100">
                            <div class="topic-box-lg color-apple">Entertainment</div>
                        </div>
                        <?php
                        $entertainment_news = fetchNewsByCategory('entertainment', $api_keys, 4);
                        if (!empty($entertainment_news)) {
                            $main_article = array_slice($entertainment_news, 0, 1)[0];
                            $sub_articles = array_slice($entertainment_news, 1, 3);

                            $main_img = !empty($main_article['urlToImage']) ? htmlspecialchars($main_article['urlToImage']) : 'https://picsum.photos/380/270';
                            $main_title = htmlspecialchars($main_article['title'] ?? 'No Title');
                                $main_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($main_article['title'] ?? '')), '-');
                                $main_url = 'news-' . $main_slug;
                            $main_author = htmlspecialchars($main_article['author'] ?? 'N/A');
                            $main_date = !empty($main_article['publishedAt']) ? date('F d, Y', strtotime($main_article['publishedAt'])) : '';

                            echo '
                            <div class="img-overlay-70 img-scale-animate">
                                <div class="mask-content-sm">
                                    <div class="post-date-light">
                                        <ul>
                                            <li><span>by</span><a>' . $main_author . '</a></li>
                                            <li><span><i class="fa fa-calendar" aria-hidden="true"></i></span>' . $main_date . '</li>
                                        </ul>
                                    </div>
                                    <h3 class="title-medium-light"><a href="' . $main_url . '">' . substr($main_title, 0, 70) . '...</a></h3>
                                </div>
                                <img src="' . $main_img . '" alt="news" class="img-fluid width-100" style="height: 270px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/380/270\';">
                            </div>
                            <ul class="border-bottom-child p-20-r h3-mb-none-child bg-body item-shadow-gray">';
                            
                            foreach ($sub_articles as $article) {
                                $sub_title = htmlspecialchars($article['title'] ?? 'No Title');
                                    $sub_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                    $sub_url = 'news-' . $sub_slug;
                                $sub_date = !empty($article['publishedAt']) ? date('F d, Y', strtotime($article['publishedAt'])) : '';
                                echo '
                                <li>
                                    <div class="post-date-dark">
                                        <ul><li><span><i class="fa fa-calendar" aria-hidden="true"></i></span>' . $sub_date . '</li></ul>
                                    </div>
                                        <h3 class="title-medium-dark"><a href="' . $sub_url . '">' . substr($sub_title, 0, 80) . '...</a></h3>
                                </li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>Could not load entertainment news.</p>';
                        }
                        ?>
                    </div>

                    <!-- Tech News Column -->
                    <div class="col-md-4 col-sm-12 mb-30">
                        <div class="topic-border color-cutty-sark mb-30 width-100">
                            <div class="topic-box-lg color-cutty-sark">Tech World</div>
                        </div>
                        <?php
                        $tech_news = fetchNewsByCategory('technology', $api_keys, 4);
                        if (!empty($tech_news)) {
                            $main_article = array_slice($tech_news, 0, 1)[0];
                            $sub_articles = array_slice($tech_news, 1, 3);

                            $main_img = !empty($main_article['urlToImage']) ? htmlspecialchars($main_article['urlToImage']) : 'https://picsum.photos/380/270';
                            $main_title = htmlspecialchars($main_article['title'] ?? 'No Title');
                                $main_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($main_article['title'] ?? '')), '-');
                                $main_url = 'news-' . $main_slug;
                            $main_author = htmlspecialchars($main_article['author'] ?? 'N/A');
                            $main_date = !empty($main_article['publishedAt']) ? date('F d, Y', strtotime($main_article['publishedAt'])) : '';

                            echo '
                            <div class="img-overlay-70 img-scale-animate">
                                <div class="mask-content-sm">
                                    <div class="post-date-light">
                                        <ul>
                                            <li><span>by</span><a>' . $main_author . '</a></li>
                                            <li><span><i class="fa fa-calendar" aria-hidden="true"></i></span>' . $main_date . '</li>
                                        </ul>
                                    </div>
                                    <h3 class="title-medium-light"><a href="' . $main_url . '">' . substr($main_title, 0, 70) . '...</a></h3>
                                </div>
                                <img src="' . $main_img . '" alt="news" class="img-fluid width-100" style="height: 270px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/380/270\';">
                            </div>
                            <ul class="border-bottom-child p-20-r h3-mb-none-child bg-body item-shadow-gray">';
                            
                            foreach ($sub_articles as $article) {
                                $sub_title = htmlspecialchars($article['title'] ?? 'No Title');
                                    $sub_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                    $sub_url = 'news-' . $sub_slug;
                                $sub_date = !empty($article['publishedAt']) ? date('F d, Y', strtotime($article['publishedAt'])) : '';
                                echo '
                                <li>
                                    <div class="post-date-dark">
                                        <ul><li><span><i class="fa fa-calendar" aria-hidden="true"></i></span>' . $sub_date . '</li></ul>
                                    </div>
                                        <h3 class="title-medium-dark"><a href="' . $sub_url . '">' . substr($sub_title, 0, 80) . '...</a></h3>
                                </li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>Could not load tech news.</p>';
                        }
                        ?>
                    </div>

                    <!-- Business News Column -->
                    <div class="col-md-4 col-sm-12 mb-30">
                        <div class="topic-border color-web-orange mb-30 width-100">
                            <div class="topic-box-lg color-web-orange">Business</div>
                        </div>
                        <?php
                        $business_news = fetchNewsByCategory('business', $api_keys, 4);
                        if (!empty($business_news)) {
                            $main_article = array_slice($business_news, 0, 1)[0];
                            $sub_articles = array_slice($business_news, 1, 3);

                            $main_img = !empty($main_article['urlToImage']) ? htmlspecialchars($main_article['urlToImage']) : 'https://picsum.photos/380/270';
                            $main_title = htmlspecialchars($main_article['title'] ?? 'No Title');
                                $main_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($main_article['title'] ?? '')), '-');
                                $main_url = 'news-' . $main_slug;
                            $main_author = htmlspecialchars($main_article['author'] ?? 'N/A');
                            $main_date = !empty($main_article['publishedAt']) ? date('F d, Y', strtotime($main_article['publishedAt'])) : '';

                            echo '
                            <div class="img-overlay-70 img-scale-animate">
                                <div class="mask-content-sm">
                                    <div class="post-date-light">
                                        <ul>
                                            <li><span>by</span><a>' . $main_author . '</a></li>
                                            <li><span><i class="fa fa-calendar" aria-hidden="true"></i></span>' . $main_date . '</li>
                                        </ul>
                                    </div>
                                    <h3 class="title-medium-light"><a href="' . $main_url . '">' . substr($main_title, 0, 70) . '...</a></h3>
                                </div>
                                <img src="' . $main_img . '" alt="news" class="img-fluid width-100" style="height: 270px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/380/270\';">
                            </div>
                            <ul class="border-bottom-child p-20-r h3-mb-none-child bg-body item-shadow-gray">';
                            
                            foreach ($sub_articles as $article) {
                                $sub_title = htmlspecialchars($article['title'] ?? 'No Title');
                                    $sub_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                    $sub_url = 'news-' . $sub_slug;
                                $sub_date = !empty($article['publishedAt']) ? date('F d, Y', strtotime($article['publishedAt'])) : '';
                                echo '
                                <li>
                                    <div class="post-date-dark">
                                        <ul><li><span><i class="fa fa-calendar" aria-hidden="true"></i></span>' . $sub_date . '</li></ul>
                                    </div>
                                        <h3 class="title-medium-dark"><a href="' . $sub_url . '">' . substr($sub_title, 0, 80) . '...</a></h3>
                                </li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>Could not load business news.</p>';
                        }
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="ne-banner-layout1 mt-20-r text-center">
                            <a href="#">
                                <img src="img/banner/banner2.jpg" alt="ad" class="img-fluid">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Category News Area End Here -->
        <?php exit; ?>
<?php } elseif (!$is_ajax) { ?>
    <div class="lazy-load-section" data-section="category_news"><div class="loader-container"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading Category News...</p></div></div>
<?php } ?>

<?php if ($ajax_section === 'video_area') { ?>
        <!-- Video Area Start Here -->
        <section class="bg-secondary-accent section-space-less4">
            <div class="container">
                <div class="row tab-space2">
                    <?php
                    $video_news = fetchNewsByCategory('videos', $api_keys, 5);
                    if (!empty($video_news)) {
                        $main_video = isset($video_news[0]) ? $video_news[0] : null;
                        $sub_videos = array_slice($video_news, 1, 4);

                        if ($main_video) {
                            $main_img = !empty($main_video['urlToImage']) ? htmlspecialchars($main_video['urlToImage']) : 'https://picsum.photos/380/450';
                            $main_title = htmlspecialchars($main_video['title'] ?? 'No Title');
                            $main_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($main_video['title'] ?? '')), '-');
                            $main_url = 'news-' . $main_slug;

                            echo '<div class="col-lg-4 col-md-12 mb-4">
                                <div class="img-overlay-70">
                                    <div class="mask-content-sm">
                                        <div class="topic-box-sm color-pomegranate mb-20">Trending</div>
                                        <h3 class="title-medium-light">
                                            <a href="' . $main_url . '">' . substr($main_title, 0, 70) . '...</a>
                                        </h3>
                                    </div>
                                    <div class="text-center">
                                        <a class="play-btn" href="' . $main_url . '" target="_blank">
                                            <img src="img/banner/play.png" alt="play" class="img-fluid">
                                        </a>
                                    </div>
                                    <img src="' . $main_img . '" alt="news" class="img-fluid width-100" style="height: 450px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/380/450\';">
                                </div>
                            </div>';
                        }

                        echo '<div class="col-lg-8 col-md-12">
                            <div class="row tab-space2">';
                        
                        $colors = ['color-web-orange', 'color-azure-radiance', 'color-persian-green', 'color-hollywood-cerise'];
                        foreach ($sub_videos as $index => $video) {
                            $sub_img = !empty($video['urlToImage']) ? htmlspecialchars($video['urlToImage']) : 'https://picsum.photos/380/250';
                            $sub_title = htmlspecialchars($video['title'] ?? 'No Title');
                            $sub_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($video['title'] ?? '')), '-');
                            $sub_url = 'news-' . $sub_slug;
                            $color_class = $colors[$index % count($colors)];

                            echo '<div class="col-sm-6 col-12 mb-4">
                                <div class="img-overlay-70">
                                    <div class="mask-content-sm">
                                        <div class="topic-box-sm ' . $color_class . ' mb-20">Videos</div>
                                        <h3 class="title-medium-light">
                                            <a href="' . $sub_url . '">' . substr($sub_title, 0, 60) . '...</a>
                                        </h3>
                                    </div>
                                    <div class="text-center">
                                        <a class="play-btn" href="' . $sub_url . '" target="_blank">
                                            <img src="img/banner/play.png" alt="play" class="img-fluid">
                                        </a>
                                    </div>
                                    <img src="' . $sub_img . '" alt="news" class="img-fluid width-100" style="height: 215px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/380/250\';">
                                </div>
                            </div>';
                        }
                        echo '    </div>
                        </div>';
                    } else {
                        echo '<div class="col-12"><p>Could not load video news.</p></div>';
                    }
                    ?>
                </div>
            </div>
        </section>
        <!-- Video Area End Here -->
        <?php exit; ?>
<?php } elseif (!$is_ajax) { ?>
    <div class="lazy-load-section" data-section="video_area"><div class="loader-container"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading Videos...</p></div></div>
<?php } ?>

<?php if ($ajax_section === 'more_news') { ?>
        <!-- More News Area Start Here -->
        <section class="bg-accent section-space-less30">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-md-12">
                        <div class="ne-isotope">
                            <div class="topic-border color-azure-radiance mb-30">
                                <div class="topic-box-lg color-azure-radiance">More News</div>
                                <div class="isotope-classes-tab isotop-btn">
                                    <a href="#" data-filter=".football" class="current">Football</a>
                                    <a href="#" data-filter=".cricket">Cricket</a>
                                    <a href="#" data-filter=".tenies">Tenies</a>
                                    <a href="#" data-filter=".cycling">Cycling</a>
                                    <a href="#" data-filter=".gadget">Gadget</a>
                                </div>
                                <div class="more-info-link">
                                    <a href="post-style-1.html">More
                                        <i class="fa fa-angle-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="featuredContainer">
                                <?php
                                $categories = ['football', 'cricket', 'tenies', 'cycling', 'gadget'];
                                foreach ($categories as $category) {
                                    $articles = fetchNewsByCategory($category, $api_keys, 4);
                                    if (!empty($articles)) {
                                        echo '<div class="' . $category . '">';
                                        echo '<div class="row">';
                                        foreach ($articles as $article) {
                                            $image = $article['urlToImage'] ?: 'https://picsum.photos/300/200'; // random placeholder image
                                            $author = $article['author'] ?: 'Unknown';
                                            $date = date('M d, Y', strtotime($article['publishedAt']));
                                            $title = $article['title'];
                                            $desc = substr($article['description'] ?: '', 0, 100) . '...';
                                            $slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                            $url = 'news-' . $slug;
                                            $cat_display = ucfirst($category);
                                            if ($category == 'tenies') $cat_display = 'Tennis';
                                            echo '
                                        <div class="col-md-12 col-sm-6 col-12 mb-30">
                                            <div class="media item-shadow-gray bg-body media-none--sm">
                                                <div class="position-relative width-36 width43-lg">
                                                    <a href="' . $url . '" class="img-opacity-hover img-overlay-70">
                                                        <img src="' . $image . '" alt="news" class="img-fluid" onerror="this.onerror=null;this.src=\'https://picsum.photos/300/200\';">
                                                    </a>
                                                    <div class="topic-box-top-xs">
                                                        <div class="topic-box-sm color-cod-gray mb-20">' . $cat_display . '</div>
                                                    </div>
                                                </div>
                                                <div class="media-body media-padding30 p-mb-none-child">
                                                    <div class="post-date-dark">
                                                        <ul>
                                                            <li>
                                                                <span>by</span>
                                                                <a href="' . $url . '">' . $author . '</a>
                                                            </li>
                                                            <li>
                                                                <span>
                                                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                </span>' . $date . '
                                                            </li>
                                                        </ul>
                                                    </div>
                                                    <h3 class="title-semibold-dark size-lg mb-15">
                                                        <a href="' . $url . '">' . $title . '</a>
                                                    </h3>
                                                    <p>' . $desc . '</p>
                                                </div>
                                            </div>
                                        </div>';
                                        }
                                        echo '</div></div>';
                                    } else {
                                        echo '<div class="' . $category . '"><div class="row"><div class="col-12"><p>No news available for ' . ucfirst($category) . '.</p></div></div></div>';
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="ne-sidebar sidebar-break-md col-lg-4 col-md-12">
                        <div class="sidebar-box">
                            <div class="topic-border color-cod-gray mb-30">
                                <div class="topic-box-lg color-cod-gray">Latest Reviews</div>
                            </div>
                            <div class="d-inline-block">
                                <?php
                                if ($api_ok && !empty($global_newsData['articles'])) {
                                    $review_articles = array_slice($global_newsData['articles'], 14, 3);
                                    foreach ($review_articles as $article) {
                                        $image = $article['urlToImage'] ?: 'https://picsum.photos/300/200';
                                        $date = date('F d, Y', strtotime($article['publishedAt']));
                                        $title = htmlspecialchars($article['title']);
                                        $slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                        $url = 'news-' . $slug;
                                        echo '
                                <div class="media mb30-list bg-body">
                                    <a class="img-opacity-hover" href="' . $url . '">
                                        <img src="' . $image . '" alt="news" class="img-fluid" onerror="this.onerror=null;this.src=\'https://picsum.photos/300/200\';">
                                    </a>
                                    <div class="media-body media-padding15">
                                        <div class="post-date-dark">
                                            <ul>
                                                <li>
                                                    <span>
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </span>' . $date . '
                                                </li>
                                            </ul>
                                        </div>
                                        <h3 class="title-medium-dark mb-none">
                                            <a href="' . $url . '">' . $title . '</a>
                                        </h3>
                                    </div>
                                </div>';
                                    }
                                } else {
                                    echo '<p>No reviews available.</p>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="sidebar-box">
                            <div class="topic-border color-cod-gray mb-30">
                                <div class="topic-box-lg color-cod-gray">Newsletter</div>
                            </div>
                            <div class="newsletter-area bg-primary">
                                <h2 class="title-medium-light size-xl line-height-custom">Subscribe to our mailing list
                                    to get the new updates!</h2>
                                <img src="img/banner/newsletter.png" alt="newsletter" class="img-fluid mb-10">
                                <p>Subscribe our newsletter to stay updated</p>
                                <div class="input-group stylish-input-group">
                                    <input type="text" placeholder="Enter your mail" class="form-control">
                                    <span class="input-group-addon">
                                        <button type="submit">
                                            <i class="fa fa-angle-right" aria-hidden="true"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- More News Area End Here -->
        <script>
        if (typeof jQuery !== 'undefined' && typeof jQuery.fn.isotope !== 'undefined') {
            jQuery('.ne-isotope').each(function() {
                var $container = jQuery(this).find('.featuredContainer');
                var defaultFilter = jQuery(this).find('.isotop-btn a.current').attr('data-filter') || '*';
                $container.isotope({ filter: defaultFilter });
                jQuery(this).find('.isotop-btn a').off('click').on('click', function() {
                    jQuery(this).addClass('current').siblings().removeClass('current');
                    var selector = jQuery(this).attr('data-filter');
                    $container.isotope({ filter: selector });
                    return false;
                });
            });
        }
        </script>
        <?php exit; ?>
<?php } elseif (!$is_ajax) { ?>
    <div class="lazy-load-section" data-section="more_news"><div class="loader-container"><i class="fa fa-spinner fa-spin fa-3x"></i><p>Loading More News...</p></div></div>
<?php } ?>

<?php if (!$is_ajax) { ?>
        <?php include 'master/footer.php'; ?>
</body>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var lazySections = document.querySelectorAll('.lazy-load-section');
    if ('IntersectionObserver' in window) {
        var sectionObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var sectionDiv = entry.target;
                    var sectionName = sectionDiv.getAttribute('data-section');
                    loadSection(sectionName, sectionDiv);
                    observer.unobserve(sectionDiv);
                }
            });
        }, { rootMargin: "300px 0px" }); // Start loading 300px before reaching it
        lazySections.forEach(function(section) {
            sectionObserver.observe(section);
        });
    } else {
        lazySections.forEach(function(section) {
            loadSection(section.getAttribute('data-section'), section);
        });
    }

    function loadSection(sectionName, container) {
        fetch('?ajax_section=' + sectionName)
            .then(response => response.text())
            .then(html => {
                var temp = document.createElement('div');
                temp.innerHTML = html;
                var scripts = temp.querySelectorAll('script');
                var scriptsCodes = [];
                scripts.forEach(function(script) {
                    scriptsCodes.push(script.innerHTML);
                    script.parentNode.removeChild(script);
                });
                container.outerHTML = temp.innerHTML;
                scriptsCodes.forEach(function(code) {
                    var newScript = document.createElement('script');
                    newScript.text = code;
                    document.body.appendChild(newScript);
                });
            })
            .catch(err => {
                container.innerHTML = '<div class="text-center py-5 text-danger">Failed to load section.</div>';
            });
    }
});
</script>

</html>
<?php } ?>