<?php
require_once 'master/api_keys.php';

// Get article title securely from URL (e.g., single-page.php?title=Encoded_Title)
$article_slug = isset($_GET['title']) ? $_GET['title'] : '';

if (empty($article_slug)) {
    die("Invalid Article. No title provided.");
}

$article = null;
$related_news = [];
$api_error = '';
$curl_err = '';

// Convert slug back to search string (e.g. "groww-vs-angel-one" -> "groww vs angel one")
$search_query = trim(str_replace('-', ' ', $article_slug));

// Remove special characters (like ', %, etc.) to avoid API breaking
$clean_query = trim(preg_replace('/[^a-zA-Z0-9]/', ' ', $search_query));

// Get clean words, ignoring 1-letter words (like 's' from Karnataka's)
$words = array_values(array_filter(explode(' ', $clean_query), function($w) { return strlen($w) > 1; }));

// Use the first 4 words for a highly accurate but safe search
$short_search = implode(' ', array_slice($words, 0, 4));
$super_short = implode(' ', array_slice($words, 0, 2));
$ultra_short = isset($words[0]) ? $words[0] : 'news';

// 1. Fetch Main Article Details via News API using Title
foreach ($api_keys as $key) {
    // Try multiple search strategies to ensure we find the article
    $urls_to_try = [
        "https://newsapi.org/v2/everything?qInTitle=" . rawurlencode($short_search) . "&language=en&apiKey=" . $key,
        "https://newsapi.org/v2/everything?q=" . rawurlencode($short_search) . "&language=en&apiKey=" . $key,
        "https://newsapi.org/v2/everything?q=" . rawurlencode($super_short) . "&language=en&apiKey=" . $key,
        "https://newsapi.org/v2/everything?q=" . rawurlencode($ultra_short) . "&language=en&apiKey=" . $key
    ];

    foreach ($urls_to_try as $url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NewsEdge/1.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // WAMP Localhost ke liye zaroori
        $response = curl_exec($ch);
        if(curl_errno($ch)){
            $curl_err = curl_error($ch);
        }
        curl_close($ch);
        
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] === 'ok' && !empty($data['articles'])) {
            $article = $data['articles'][0]; // Match mil gaya
            break;
        } elseif (isset($data['status']) && $data['status'] === 'error') {
            $api_error = $data['message'] ?? 'API Limit Reached';
            if (isset($data['code']) && in_array($data['code'], ['rateLimited', 'apiKeyExhausted', 'apiKeyMissing', 'apiKeyInvalid'])) {
                break; // Agar current key ki limit khatam hai, to turant next key par switch karein
            }
        }
    }

    if ($article) {
        // 2. Fetch Related News (Usi API key se top headlines utha lenge related section ke liye)
        $rel_url = "https://newsapi.org/v2/top-headlines?language=en&pageSize=5&apiKey=" . $key;
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $rel_url);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch2, CURLOPT_USERAGENT, 'NewsEdge/1.0');
        curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false); // WAMP Localhost ke liye zaroori
        $rel_response = curl_exec($ch2);
        curl_close($ch2);
        
        $rel_data = json_decode($rel_response, true);
        if (isset($rel_data['status']) && $rel_data['status'] === 'ok' && !empty($rel_data['articles'])) {
            $related_news = $rel_data['articles'];
        }
        break;
    }
}

if (!$article) {
    $err = "Article not found. It might have been removed by the source.";
    if (!empty($api_error)) {
        $err .= "<br><br><b style='color:red;'>API Error:</b> " . htmlspecialchars($api_error);
    }
    if (!empty($curl_err)) {
        $err .= "<br><br><b style='color:red;'>cURL Error:</b> " . htmlspecialchars($curl_err);
    }
    die($err);
}
?>
<!doctype html>
<html class="no-js" lang="">
<head>
    <?php include 'master/link.php'; ?>
    <title><?= htmlspecialchars($article['title']) ?> | NewsEdge</title>
</head>
<body>
    <?php include 'master/header.php'; ?>

    <!-- Main Content Area Start Here -->
    <section class="bg-body section-space-default">
        <div class="container">
            <div class="row">
                <!-- Article Column -->
                <div class="col-lg-8 col-md-12">
                    <div class="news-details-layout1">
                        <div class="position-relative mb-30">
                            <img src="<?= !empty($article['urlToImage']) ? htmlspecialchars($article['urlToImage']) : 'https://picsum.photos/800/500' ?>" alt="news-details" class="img-fluid width-100" onerror="this.onerror=null;this.src='https://picsum.photos/800/500';">
                            <div class="topic-box-top-sm">
                                <div class="topic-box-sm color-cinnabar mb-20"><?= htmlspecialchars($article['source']['name'] ?? 'News') ?></div>
                            </div>
                        </div>
                        <h2 class="title-semibold-dark size-c30"><?= htmlspecialchars($article['title']) ?></h2>
                        <ul class="post-info-dark mb-30">
                            <li>
                                <a href="#">
                                    <span>By</span> <?= htmlspecialchars($article['author'] ?? 'Unknown') ?>
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="fa fa-calendar" aria-hidden="true"></i>
                                    <?= date('F d, Y', strtotime($article['publishedAt'])) ?>
                                </a>
                            </li>
                        </ul>
                        
                        <div class="article-content">
                            <p style="font-size: 18px; font-weight: 500; margin-bottom: 20px;"><?= nl2br(htmlspecialchars($article['description'] ?? '')) ?></p>
                            <p style="font-size: 16px; line-height: 1.8; color: #444;"><?= nl2br(htmlspecialchars(preg_replace('/\[\+\d+\s?chars\]$/', '', $article['content'] ?? ''))) ?></p>
                            
                            <!-- News API free tier limit is 200 chars. Redirect user to read full story -->
                            <div class="mt-4">
                                <a href="<?= htmlspecialchars($article['url']) ?>" target="_blank" class="btn btn-primary" style="background-color: #e74c3c; border-color: #e74c3c;">Read Full Story on Original Site</a>
                            </div>
                        </div>
                    </div>

                    <!-- Related News Area Start Here -->
                    <div class="related-news-wrapper mt-50 mb-50">
                        <h3 class="title-bold-dark size-c24 mb-30 border-bottom">Related News</h3>
                        <div class="row">
                            <?php
                            if (!empty($related_news)) {
                                $count = 0;
                                foreach ($related_news as $related) {
                                    // Skip the current article and articles with empty titles
                                    if (empty($related['title']) || $related['title'] === $article['title']) continue;
                                    if ($count >= 3) break;
                                    
                                    $rel_slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($related['title'])), '-');
                                    $rel_url = "news-" . $rel_slug;
                                    $rel_img = !empty($related['urlToImage']) ? htmlspecialchars($related['urlToImage']) : 'https://picsum.photos/300/200';
                                    ?>
                                    <div class="col-lg-4 col-md-4 col-sm-12">
                                        <div class="position-relative mb-30">
                                            <a href="<?= $rel_url ?>">
                                                <img src="<?= $rel_img ?>" alt="news" class="img-fluid width-100" style="height: 150px; object-fit: cover;" onerror="this.onerror=null;this.src='https://picsum.photos/300/200';">
                                            </a>
                                        </div>
                                        <h3 class="title-medium-dark size-md mb-10">
                                            <a href="<?= $rel_url ?>"><?= htmlspecialchars(substr($related['title'], 0, 60)) ?>...</a>
                                        </h3>
                                        <div class="post-date-dark">
                                            <ul>
                                                <li>
                                                    <span><i class="fa fa-calendar" aria-hidden="true"></i></span>
                                                    <?= date('M d, Y', strtotime($related['publishedAt'])) ?>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php
                                    $count++;
                                }
                            } else {
                                echo "<div class='col-12'><p>No related news found.</p></div>";
                            }
                            ?>
                        </div>
                    </div>
                    <!-- Related News Area End Here -->
                </div>
                
                <!-- Sidebar Column (Optional) -->
                <div class="col-lg-4 col-md-12">
                    <div class="sidebar-widget-area">
                       <!-- Include a sidebar widget here if you have one -->
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Main Content Area End Here -->

    <?php include 'master/footer.php'; ?>
</body>
</html>