<?php
// Agar global news data already set nahi hai (jaise single-page.php mein), to footer khud API call karega
if (!isset($api_ok) || empty($global_newsData['articles'])) {
    require_once __DIR__ . '/api_keys.php';
    $api_ok = false;
    foreach ($api_keys as $key) {
        $url = "https://newsapi.org/v2/top-headlines?language=en&pageSize=20&apiKey=" . $key;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'NewsEdge/1.0');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] === 'ok' && !empty($data['articles'])) {
            $global_newsData = $data;
            $api_ok = true;
            break;
        }
    }
}
?>
<!-- Footer Area Start Here -->
            <footer>
                <div class="footer-area-top">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-12">
                                <div class="footer-box">
                                    <h2 class="title-bold-light title-bar-left text-uppercase">Most Viewed Posts</h2>
                                    <ul class="most-view-post">
                                        <?php
                                        if (isset($api_ok) && $api_ok && !empty($global_newsData['articles'])) {
                                            $footer_posts = array_slice($global_newsData['articles'], 8, 3);
                                            foreach ($footer_posts as $article) {
                                                $title = htmlspecialchars($article['title'] ?? '');
                                                $slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                                $newsUrl = 'news-' . $slug;
                                                $imageUrl = !empty($article['urlToImage']) ? htmlspecialchars($article['urlToImage']) : 'https://picsum.photos/150/150';
                                                $date = !empty($article['publishedAt']) ? date('F d, Y', strtotime($article['publishedAt'])) : '';
                                                echo '<li>
                                                    <div class="media">
                                                        <a href="' . $newsUrl . '">
                                                            <img src="' . $imageUrl . '" alt="post" class="img-fluid" style="width: 80px; height: 80px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/150/150\';">
                                                        </a>
                                                        <div class="media-body">
                                                            <h3 class="title-medium-light size-md mb-10">
                                                                <a href="' . $newsUrl . '">' . substr($title, 0, 50) . '...</a>
                                                            </h3>
                                                            <div class="post-date-light">
                                                                <ul>
                                                                    <li>
                                                                        <span>
                                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                                        </span>' . $date . '</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>';
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-3 col-md-6 col-sm-12">
                                <div class="footer-box">
                                    <h2 class="title-bold-light title-bar-left text-uppercase">Popular Categories</h2>
                                    <ul class="popular-categories">
                                        <li>
                                            <a href="#">Gadgets
                                                <span>15</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">Architecture
                                                <span>10</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">New look 2017
                                                <span>14</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">Reviews
                                                <span>13</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">Mobile and Phones
                                                <span>19</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">Recipes
                                                <span>26</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">Decorating
                                                <span>21</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#">IStreet fashion
                                                <span>09</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-5 col-md-12 col-sm-12">
                                <div class="footer-box">
                                    <h2 class="title-bold-light title-bar-left text-uppercase">Post Gallery</h2>
                                    <ul class="post-gallery shine-hover ">
                                        <?php
                                        if (isset($api_ok) && $api_ok && !empty($global_newsData['articles'])) {
                                            $footer_gallery = array_slice($global_newsData['articles'], 11, 9);
                                            foreach ($footer_gallery as $article) {
                                                $slug = trim(preg_replace('/[^a-zA-Z0-9]+/', '-', strtolower($article['title'] ?? '')), '-');
                                                $newsUrl = 'news-' . $slug;
                                                $imageUrl = !empty($article['urlToImage']) ? htmlspecialchars($article['urlToImage']) : 'https://picsum.photos/150/150';
                                                echo '<li>
                                                    <a href="' . $newsUrl . '">
                                                        <figure>
                                                            <img src="' . $imageUrl . '" alt="post" class="img-fluid" style="width: 100px; height: 80px; object-fit: cover;" onerror="this.onerror=null;this.src=\'https://picsum.photos/150/150\';">
                                                        </figure>
                                                    </a>
                                                </li>';
                                            }
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="footer-area-bottom">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 text-center">
                                <a href="index-2.html" class="footer-logo img-fluid">
                                    <img src="img/logo.png" alt="logo" class="img-fluid">
                                </a>
                                <ul class="footer-social">
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
                                <p>© 2017 newsedge Designed by RadiusTheme. All Rights Reserved</p>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            <!-- Footer Area End Here -->
            <!-- Modal Start-->
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <div class="title-login-form">Login</div>
                        </div>
                        <div class="modal-body">
                            <div class="login-form">
                                <form>
                                    <label>Username or email address *</label>
                                    <input type="text" placeholder="Name or E-mail" />
                                    <label>Password *</label>
                                    <input type="password" placeholder="Password" />
                                    <div class="checkbox checkbox-primary">
                                        <input id="checkbox" type="checkbox" checked>
                                        <label for="checkbox">Remember Me</label>
                                    </div>
                                    <button type="submit" value="Login">Login</button>
                                    <button class="form-cancel" type="submit" value="">Cancel</button>
                                    <label class="lost-password">
                                        <a href="#">Lost your password?</a>
                                    </label>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal End-->
            <!-- Offcanvas Menu Start -->
            <div id="offcanvas-body-wrapper" class="offcanvas-body-wrapper">
                <div id="offcanvas-nav-close" class="offcanvas-nav-close offcanvas-menu-btn">
                    <a href="#" class="menu-times re-point">
                        <span></span>
                        <span></span>
                    </a>
                </div>
                <div class="offcanvas-main-body">
                    <ul id="accordion" class="offcanvas-nav panel-group">
                        <li class="panel panel-default">
                            <div class="panel-heading">
                                <a aria-expanded="false" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
                                    <i class="fa fa-home" aria-hidden="true"></i>Home Pages</a>
                            </div>
                            <div aria-expanded="false" id="collapseOne" role="tabpanel" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="offcanvas-sub-nav">
                                        <li>
                                            <a href="index-2.html">Home 1</a>
                                        </li>
                                        <li>
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
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="author-post.html">
                                <i class="fa fa-user" aria-hidden="true"></i>Author Post Page</a>
                        </li>
                        <li class="panel panel-default">
                            <div class="panel-heading">
                                <a aria-expanded="false" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
                                    <i class="fa fa-file-text" aria-hidden="true"></i>Post Pages</a>
                            </div>
                            <div aria-expanded="false" id="collapseTwo" role="tabpanel" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="offcanvas-sub-nav">
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
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li class="panel panel-default">
                            <div class="panel-heading">
                                <a aria-expanded="false" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>News Details Pages</a>
                            </div>
                            <div aria-expanded="false" id="collapseThree" role="tabpanel" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="offcanvas-sub-nav">
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
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="archive.html">
                                <i class="fa fa-archive" aria-hidden="true"></i>Archive Page</a>
                        </li>
                        <li class="panel panel-default">
                            <div class="panel-heading">
                                <a aria-expanded="false" class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
                                    <i class="fa fa-picture-o" aria-hidden="true"></i>Gallery Pages</a>
                            </div>
                            <div aria-expanded="false" id="collapseFour" role="tabpanel" class="panel-collapse collapse">
                                <div class="panel-body">
                                    <ul class="offcanvas-sub-nav">
                                        <li>
                                            <a href="gallery-style-1.html">Gallery Style 1</a>
                                        </li>
                                        <li>
                                            <a href="gallery-style-2.html">Gallery Style 2</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </li>
                        <li>
                            <a href="404.html">
                                <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>404 Error Page</a>
                        </li>
                        <li>
                            <a href="contact.html">
                                <i class="fa fa-phone" aria-hidden="true"></i>Contact Page</a>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- Offcanvas Menu End -->

        <!-- Global Preloader Start -->
        <div id="global-loader" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #ffffff; z-index: 99999; display: flex; align-items: center; justify-content: center;">
            <!-- Replace this inner HTML with your actual loader design -->
            <i class="fa fa-spinner fa-spin fa-4x" style="color: #e74c3c;"></i>
        </div>
        <script>
            window.addEventListener('load', function() {
                var loader = document.getElementById('global-loader');
                if (loader) {
                    loader.style.transition = 'opacity 0.5s ease';
                    loader.style.opacity = '0';
                    setTimeout(function() { loader.style.display = 'none'; }, 500);
                }
            });
        </script>
        <!-- Global Preloader End -->

        </div>
        <!-- jquery-->
        <script src="js/jquery-2.2.4.min.js" type="text/javascript"></script>
        <!-- Plugins js -->
        <script src="js/plugins.js" type="text/javascript"></script>
        <!-- Popper js -->
        <script src="js/popper.js" type="text/javascript"></script>
        <!-- Bootstrap js -->
        <script src="js/bootstrap.min.js" type="text/javascript"></script>
        <!-- WOW JS -->
        <script src="js/modernizr-2.8.3.min.js"></script>
        
        <script src="js/wow.min.js"></script>
        <!-- Owl Cauosel JS -->
        <script src="vendor/OwlCarousel/owl.carousel.min.js" type="text/javascript"></script>
        <!-- Meanmenu Js -->
        <script src="js/jquery.meanmenu.min.js" type="text/javascript"></script>
        <!-- Srollup js -->
        <script src="js/jquery.scrollUp.min.js" type="text/javascript"></script>
        <!-- jquery.counterup js -->
        <script src="js/jquery.counterup.min.js"></script>
        <script src="js/waypoints.min.js"></script>
        <!-- Isotope js -->
        <script src="js/isotope.pkgd.min.js" type="text/javascript"></script>
        <!-- Magnific Popup -->
        <script src="js/jquery.magnific-popup.min.js"></script>
        <!-- Ticker Js -->
        <script src="js/ticker.js" type="text/javascript"></script>
        <!-- Custom Js -->
        <script src="js/main.js" type="text/javascript"></script>