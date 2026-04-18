<?php
require_once 'master/api_keys.php';

$cat_slug = isset($_GET['cat']) ? $_GET['cat'] : '';

if (empty($cat_slug)) {
    die("Invalid Category.");
}

$category_news = [];
$api_error = '';

// News API sirf in specific categories ko support karta hai
$valid_categories = ['business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology'];
$search_cat = in_array(strtolower($cat_slug), $valid_categories) ? strtolower($cat_slug) : 'general';
$display_cat = ucfirst($cat_slug);

foreach ($api_keys as $key) {
    $url = "https://newsapi.org/v2/top-headlines?category=" . urlencode($search_cat) . "&language=en&pageSize=15&apiKey=" . $key;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'NewsEdge/1.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    if (isset($data['status']) && $data['status'] === 'ok' && !empty($data['articles'])) {
        $category_news = $data['articles'];
        break;
    } elseif (isset($data['status']) && $data['status'] === 'error') {
        $api_error = $data['message'] ?? 'API Limit Reached';
        if (isset($data['code']) && in_array($data['code'], ['rateLimited', 'apiKeyExhausted', 'apiKeyMissing', 'apiKeyInvalid'])) {
            continue;
        }
    }
}
?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <?php include 'master/link.php'; ?>
    <title><?= htmlspecialchars($display_cat) ?> News | NewsEdge</title>
</head>
<body>
    <?php include 'master/header.php'; ?>

    <!-- Main Content Area Start Here -->
    <section class="bg-body section-space-default">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="topic-border color-scampi mb-30 width-100">
                        <div class="topic-box-lg color-scampi"><?= htmlspecialchars($display_cat) ?> News</div>
                    </div>
                </div>
            </div>
            <div class="row">
                <?php
                if (!empty($category_news)) {
                    foreach ($category_news as $article) {
                        if (empty($article['title']) || empty($article['url'])) continue;
                        
                        $slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                        $newsUrl = 'news-' . $slug;
                        $imageUrl = !empty($article['urlToImage']) ? htmlspecialchars($article['urlToImage']) : 'https://picsum.photos/400/250';
                        $date = !empty($article['publishedAt']) ? date('M d, Y', strtotime($article['publishedAt'])) : '';
                        $title = htmlspecialchars($article['title']);
                        $desc = htmlspecialchars(substr($article['description'] ?? '', 0, 100)) . '...';
                        
                        echo '
                        <div class="col-lg-4 col-md-6 col-sm-12 mb-30">
                            <div class="position-relative mb-20">
                                <a href="' . $newsUrl . '">
                                    <img src="' . $imageUrl . '" alt="news" class="img-fluid width-100" style="height: 220px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/400/250\';">
                                </a>
                            </div>
                            <h3 class="title-medium-dark size-md mb-10">
                                <a href="' . $newsUrl . '">' . substr($title, 0, 60) . '...</a>
                            </h3>
                        </div>';
                    }
                } else {
                    echo '<div class="col-12"><p class="text-danger">No news found for this category. ' . htmlspecialchars($api_error) . '</p></div>';
                }
                ?>
            </div>
        </div>
    </section>
    <?php include 'master/footer.php'; ?>
</body>
</html>