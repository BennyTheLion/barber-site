<?php
class HomeController {
    public function index() {
        require_once "views/layout/header.php";
        ?>
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1><?php echo SITE_NAME; ?></h1>
                    <p class="slogan">תספורות גברים וילדים ברמה גבוהה</p>
                    <a href="<?php echo SITE_URL; ?>/views/booking.php" class="btn-hero">קבע תור עכשיו</a>
                </div>
            </div>
        </section>

        <section class="services-section">
            <div class="container">
                <h2 class="section-title">השירותים שלנו</h2>
                <p class="section-subtitle">מבחר שירותים מקצועיים במחירים נוחים</p>
                <div class="row">
                    <?php
                    $db = Database::getInstance()->getConnection();
                    $stmt = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order LIMIT 4");
                    $services = $stmt->fetchAll();
                    
                    foreach($services as $service): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="service-card">
                            <div class="service-image-wrapper">
                                <?php if(!empty($service['image'])): ?>
                                    <!-- ✅ Services images from /uploads/services/ -->
                                    <img src="<?php echo SITE_URL; ?>/uploads/services/<?= htmlspecialchars($service['image']) ?>" 
                                         alt="<?= htmlspecialchars($service['name']) ?>"
                                         class="service-image">
                                <?php else: ?>
                                    <div class="service-placeholder">
                                        <i class="fas fa-cut"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="service-content">
                                <h3 class="service-title"><?php echo htmlspecialchars($service['name']); ?></h3>
                                <p class="service-description"><?php echo htmlspecialchars(substr($service['description'] ?? '', 0, 80)); ?>...</p>
                                <div class="service-footer">
                                    <span class="service-price">₪<?php echo number_format($service['price'], 2); ?></span>
                                    <span class="service-duration"><i class="far fa-clock"></i> <?php echo $service['duration']; ?> דקות</span>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/views/booking.php?service=<?= $service['id'] ?>" class="btn-book-service">
                                    <i class="fas fa-calendar-plus"></i> קבע תור
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="<?php echo SITE_URL; ?>/services" class="btn-outline-danger">לכל השירותים <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </section>

        <section class="gallery-section">
            <div class="container">
                <h2 class="section-title">העבודות שלנו</h2>
                <p class="section-subtitle">גלריית תספורות מובילות</p>
                <div class="gallery-grid">
                    <?php
                    // ✅ Gallery images from /uploads/Images/
                    $galleryPath = __DIR__ . '/../uploads/Images/';
                    $galleryImages = [];
                    
                    if(is_dir($galleryPath)) {
                        $files = scandir($galleryPath);
                        foreach($files as $file) {
                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
                            if(in_array($extension, $allowedExtensions)) {
                                $galleryImages[] = $file;
                            }
                        }
                        shuffle($galleryImages);
                        $galleryImages = array_slice($galleryImages, 0, 12);
                    }
                    
                    if(count($galleryImages) > 0): 
                        foreach($galleryImages as $image): ?>
                            <div class="gallery-item">
                                <img src="<?php echo SITE_URL; ?>/uploads/Images/<?= htmlspecialchars($image) ?>" 
                                     alt="<?= htmlspecialchars(pathinfo($image, PATHINFO_FILENAME)) ?>"
                                     class="gallery-image"
                                     loading="lazy">
                            </div>
                        <?php endforeach; 
                    else: 
                        for($i = 1; $i <= 6; $i++): ?>
                            <div class="gallery-item">
                                <div class="gallery-placeholder">
                                    <i class="fas fa-cut"></i>
                                    <span>תמונה <?php echo $i; ?></span>
                                </div>
                            </div>
                        <?php endfor; 
                    endif; ?>
                </div>
            </div>
        </section>

        <section class="contact-section">
            <div class="container">
                <h2 class="section-title">צור קשר</h2>
                <p class="section-subtitle">אנחנו כאן בשבילך</p>
                <div class="row">
                    <div class="col-md-4">
                        <div class="contact-info">
                            <i class="fas fa-map-marker-alt"></i>
                            <h4>כתובת</h4>
                            <p>[כתובת תעודכן בהמשך]</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-info">
                            <i class="fas fa-phone"></i>
                            <h4>טלפון</h4>
                            <p>[טלפון יעודכן בהמשך]</p>
                            <a href="https://wa.me/0500000000" class="btn-success mt-2" target="_blank">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="contact-info">
                            <i class="fas fa-envelope"></i>
                            <h4>אימייל</h4>
                            <p>[אימייל יעודכן בהמשך]</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <style>
        /* ========== RESET & BASE ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* ========== HERO SECTION ========== */
        .hero {
            background: linear-gradient(135deg, #000 0%, #1a1a1a 100%);
            min-height: 60vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            padding: 60px 0;
        }

        .hero-content h1 {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .hero-content .slogan {
            font-size: 1.2rem;
            color: #dc2626;
            margin-bottom: 20px;
        }

        .btn-hero {
            display: inline-block;
            background: #dc2626;
            color: white;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            font-size: 1rem;
        }

        .btn-hero:hover {
            background: #b91c1c;
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.4);
            color: white;
        }

        /* ========== SERVICES SECTION ========== */
        .services-section {
            padding: 50px 0;
            background: #f8f9fa;
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 5px;
            color: #1a1a1a;
        }

        .section-subtitle {
            text-align: center;
            color: #6c757d;
            margin-bottom: 30px;
            font-size: 0.95rem;
        }

        /* ========== SERVICE CARD ========== */
        .service-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.06);
            transition: all 0.3s;
            height: 100%;
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .service-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(220, 38, 38, 0.10);
        }

        .service-image-wrapper {
            width: 100%;
            height: 130px;
            overflow: hidden;
            background: #2d2d2d;
            flex-shrink: 0;
        }

        .service-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .service-card:hover .service-image {
            transform: scale(1.03);
            transition: transform 0.4s;
        }

        .service-placeholder {
            width: 100%;
            height: 130px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            flex-shrink: 0;
        }

        .service-placeholder i {
            font-size: 35px;
            color: #dc2626;
        }

        .service-content {
            padding: 10px 12px 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .service-title {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 4px;
            color: #1a1a1a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .service-description {
            color: #6c757d;
            font-size: 0.75rem;
            line-height: 1.4;
            margin-bottom: 8px;
            flex: 1;
            display: block;
            overflow: visible;
            height: auto;
        }

        .service-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 8px;
            border-top: 1px solid #f0f0f0;
            margin-top: auto;
        }

        .service-price {
            font-size: 0.95rem;
            font-weight: 800;
            color: #dc2626;
        }

        .service-duration {
            color: #6c757d;
            font-size: 0.7rem;
        }

        .service-duration i {
            margin-left: 3px;
        }

        /* ========== BOOK BUTTON ========== */
        .btn-book-service {
            display: block;
            text-align: center;
            background: #dc2626;
            color: white;
            padding: 10px 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);
            width: 100%;
            letter-spacing: 0.5px;
        }
        
        .btn-book-service i {
            margin-left: 8px;
            font-size: 0.9rem;
        }
        
        .btn-book-service:hover {
            background: #b91c1c;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 38, 38, 0.35);
            text-decoration: none;
        }
        
        .btn-book-service:active {
            transform: translateY(0px);
            box-shadow: 0 2px 5px rgba(220, 38, 38, 0.15);
        }
        
        .btn-outline-danger {
            border: 2px solid #dc2626;
            color: #dc2626;
            padding: 6px 20px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            font-size: 0.85rem;
        }

        .btn-outline-danger:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }

        /* ========== GALLERY SECTION ========== */
        .gallery-section {
            padding: 50px 0;
            background: white;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .gallery-item {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            aspect-ratio: 4/3;
            background: #f0f0f0;
        }

        .gallery-item:hover {
            transform: scale(1.02);
        }

        .gallery-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s;
        }

        .gallery-item:hover .gallery-image {
            transform: scale(1.05);
        }

        .gallery-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            color: white;
            border-radius: 10px;
        }

        .gallery-placeholder i {
            font-size: 35px;
            color: #dc2626;
            margin-bottom: 5px;
        }

        .gallery-placeholder span {
            color: #6c757d;
            font-size: 0.8rem;
        }

        /* ========== CONTACT SECTION ========== */
        .contact-section {
            padding: 50px 0;
            background: #f5f5f5;
        }

        .contact-info {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s;
            height: 100%;
        }

        .contact-info:hover {
            transform: translateY(-3px);
        }

        .contact-info i {
            font-size: 30px;
            color: #dc2626;
            margin-bottom: 10px;
        }

        .contact-info h4 {
            font-weight: 700;
            margin-bottom: 5px;
            font-size: 1rem;
        }

        .contact-info p {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 0.85rem;
        }

        .btn-success {
            background: #25D366;
            color: white;
            padding: 5px 18px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.8rem;
            transition: all 0.3s;
            display: inline-block;
            border: none;
        }

        .btn-success:hover {
            background: #1da851;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 211, 102, 0.3);
            color: white;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .hero {
                min-height: 40vh;
                padding: 40px 0;
            }
            
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .hero-content .slogan {
                font-size: 1rem;
            }
            
            .section-title {
                font-size: 1.6rem;
            }
            
            .service-image-wrapper {
                height: 160px;
            }
            
            .service-placeholder {
                height: 160px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }

        @media (max-width: 576px) {
            .hero-content h1 {
                font-size: 1.6rem;
            }
            
            .btn-hero {
                padding: 8px 20px;
                font-size: 0.9rem;
            }
            
            .section-title {
                font-size: 1.3rem;
            }
            
            .service-image-wrapper {
                height: 140px;
            }
            
            .service-placeholder {
                height: 140px;
            }
            
            .service-description {
                font-size: 0.7rem;
            }
            
            .gallery-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
        }
        </style>
        <?php
        require_once "views/layout/footer.php";
    }
    
    // ========== LEGAL PAGES ==========
    public function privacyPolicy() {
        require_once "views/privacy-policy.php";
    }
    
    public function termsOfService() {
        require_once "views/terms-of-service.php";
    }
    
    public function accessibilityStatement() {
        require_once "views/accessibility-statement.php";
    }
    
    public function cookiePolicy() {
        require_once "views/cookie-policy.php";
    }
    
    // ========== ABOUT PAGE ==========
    public function about() {
        require_once "views/layout/header.php";
        ?>
        <section class="about-section">
            <div class="container">
                <h1 class="section-title">אודות</h1>
                <p class="section-subtitle">הסיפור שלי</p>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="about-image">
                            <div class="about-placeholder">
                                <i class="fas fa-cut"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h3>הסיפור שלי</h3>
                        <p>אני מספר מקצועי עם ניסיון של מעל 10 שנים בתחום. התחלתי את דרכי בגיל צעיר ומאז אני מתמחה בתספורות גברים וילדים.</p>
                        <p>הפילוסופיה שלי היא לספק שירות מקצועי, איכותי ומדויק לכל לקוח. אני מאמין שכל לקוח הוא ייחודי ומגיע לו לקבל את התספורת המושלמת עבורו.</p>
                        <h4>ניסיון מקצועי</h4>
                        <ul>
                            <li><i class="fas fa-check-circle"></i> 10+ שנים ניסיון</li>
                            <li><i class="fas fa-check-circle"></i> השתלמויות מתקדמות בישראל ובעולם</li>
                            <li><i class="fas fa-check-circle"></i> התמחות בתספורות מודרניות וקלאסיות</li>
                        </ul>
                        <a href="<?php echo SITE_URL; ?>/views/booking.php" class="btn-hero" style="display: inline-block; margin-top: 20px;">קבע תור עכשיו</a>
                    </div>
                </div>
            </div>
        </section>
        <style>
        .about-section {
            padding: 60px 0;
            background: white;
        }
        .about-placeholder {
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            height: 350px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .about-placeholder i {
            font-size: 80px;
            color: #dc2626;
        }
        .about-section ul {
            list-style: none;
            padding: 0;
        }
        .about-section ul li {
            padding: 6px 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }
        .about-section ul li i {
            font-size: 18px;
            color: #dc2626;
        }
        @media (max-width: 768px) {
            .about-placeholder {
                height: 200px;
                margin-bottom: 20px;
            }
            .about-placeholder i {
                font-size: 50px;
            }
        }
        </style>
        <?php
        require_once "views/layout/footer.php";
    }
    
    // ========== SERVICES PAGE ==========
    public function services() {
        require_once "views/layout/header.php";
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order");
        $services = $stmt->fetchAll();
        ?>
        <section class="services-page">
            <div class="container">
                <h1 class="section-title">השירותים שלנו</h1>
                <p class="section-subtitle">מבחר שירותים מקצועיים במחירים נוחים</p>
                <div class="row">
                    <?php foreach($services as $service): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="service-card">
                            <div class="service-image-wrapper">
                                <?php if(!empty($service['image'])): ?>
                                    <!-- ✅ Services images from /uploads/services/ -->
                                    <img src="<?php echo SITE_URL; ?>/uploads/services/<?= htmlspecialchars($service['image']) ?>" 
                                         alt="<?= htmlspecialchars($service['name']) ?>"
                                         class="service-image">
                                <?php else: ?>
                                    <div class="service-placeholder">
                                        <i class="fas fa-cut"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="service-content">
                                <h3 class="service-title"><?php echo htmlspecialchars($service['name']); ?></h3>
                                <p class="service-description"><?php echo htmlspecialchars($service['description'] ?? ''); ?></p>
                                <div class="service-footer">
                                    <span class="service-price">₪<?php echo number_format($service['price'], 2); ?></span>
                                    <span class="service-duration"><i class="far fa-clock"></i> <?php echo $service['duration']; ?> דקות</span>
                                </div>
                                <a href="<?php echo SITE_URL; ?>/views/booking.php?service=<?= $service['id'] ?>" class="btn-book-service">
                                    <i class="fas fa-calendar-plus"></i> קבע תור
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        <style>
        .services-page {
            padding: 60px 0;
            background: #f8f9fa;
        }
        .services-page .service-image-wrapper {
            height: 220px;
        }
        .services-page .service-placeholder {
            height: 220px;
        }
        .services-page .service-placeholder i {
            font-size: 50px;
        }
        @media (max-width: 768px) {
            .services-page .service-image-wrapper {
                height: 180px;
            }
            .services-page .service-placeholder {
                height: 180px;
            }
        }
        </style>
        <?php
        require_once "views/layout/footer.php";
    }
    
    // ========== GALLERY PAGE ==========
    public function gallery() {
        require_once "views/layout/header.php";
        ?>
        <section class="gallery-page">
            <div class="container">
                <h1 class="section-title">גלריית עבודות</h1>
                <p class="section-subtitle">התספורות המובילות שלנו</p>
                <div class="gallery-grid">
                    <?php
                    // ✅ Gallery images from /uploads/Images/
                    $galleryPath = __DIR__ . '/../uploads/Images/';
                    $galleryImages = [];
                    
                    if(is_dir($galleryPath)) {
                        $files = scandir($galleryPath);
                        foreach($files as $file) {
                            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
                            if(in_array($extension, $allowedExtensions)) {
                                $galleryImages[] = $file;
                            }
                        }
                        shuffle($galleryImages);
                        $galleryImages = array_slice($galleryImages, 0, 12);
                    }
                    
                    if(count($galleryImages) > 0): 
                        foreach($galleryImages as $image): ?>
                            <div class="gallery-item">
                                <img src="<?php echo SITE_URL; ?>/uploads/Images/<?= htmlspecialchars($image) ?>" 
                                     alt="<?= htmlspecialchars($image) ?>"
                                     class="gallery-image"
                                     loading="lazy">
                            </div>
                        <?php endforeach; 
                    else: 
                        for($i = 1; $i <= 12; $i++): ?>
                            <div class="gallery-item">
                                <div class="gallery-placeholder">
                                    <i class="fas fa-cut"></i>
                                    <span>תספורת <?php echo $i; ?></span>
                                </div>
                            </div>
                        <?php endfor; 
                    endif; ?>
                </div>
            </div>
        </section>
        <style>
        .gallery-page {
            padding: 60px 0;
            background: white;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 15px;
        }
        .gallery-item {
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.3s;
            aspect-ratio: 4/3;
            background: #f0f0f0;
        }
        .gallery-item:hover {
            transform: scale(1.03);
        }
        .gallery-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.5s;
        }
        .gallery-item:hover .gallery-image {
            transform: scale(1.05);
        }
        .gallery-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #1a1a1a, #2d2d2d);
            color: white;
        }
        .gallery-placeholder i {
            font-size: 40px;
            color: #dc2626;
            margin-bottom: 8px;
        }
        .gallery-placeholder span {
            color: #6c757d;
            font-size: 0.9rem;
        }
        @media (max-width: 768px) {
            .gallery-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 12px;
            }
        }
        @media (max-width: 576px) {
            .gallery-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }
        }
        </style>
        <?php
        require_once "views/layout/footer.php";
    }
    
    // ========== CONTACT PAGE ==========
    public function contact() {
        require_once "views/layout/header.php";
        ?>
        <section class="contact-page">
            <div class="container">
                <h1 class="section-title">צור קשר</h1>
                <p class="section-subtitle">אנחנו כאן בשבילך</p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="contact-details">
                            <h3>פרטי התקשרות</h3>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h5>כתובת</h5>
                                    <p>[כתובת תעודכן בהמשך]</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <h5>טלפון</h5>
                                    <p>[טלפון יעודכן בהמשך]</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h5>אימייל</h5>
                                    <p>[אימייל יעודכן בהמשך]</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <h5>שעות פעילות</h5>
                                    <p>ראשון-חמישי: 09:00-20:00</p>
                                    <p>שישי: 09:00-14:00</p>
                                    <p>שבת: סגור</p>
                                </div>
                            </div>
                        </div>
                        <div class="social-links">
                            <h4>רשתות חברתיות</h4>
                            <a href="#" class="social-link" target="_blank"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link" target="_blank"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="social-link" target="_blank"><i class="fab fa-tiktok"></i></a>
                            <a href="#" class="social-link" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="map-container">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3372.2943427050423!2d34.878248173942154!3d32.303945108014815!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151d3fe96259a453%3A0x418450cfc8af1956!2sDerech%20HaPark%2013%2C%20Netanya%2C%204250310!5e0!3m2!1sen!2sil!4v1781226148771!5m2!1sen!2sil" 
                                    width="100%" 
                                    height="350" 
                                    style="border:0; border-radius: 12px;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <style>
        .contact-page {
            padding: 60px 0;
            background: #f8f9fa;
        }
        .contact-details {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .contact-details h3 {
            margin-bottom: 20px;
            font-weight: 700;
            font-size: 1.3rem;
        }
        .contact-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 15px;
        }
        .contact-item i {
            font-size: 20px;
            color: #dc2626;
            width: 35px;
            text-align: center;
            margin-top: 3px;
        }
        .contact-item h5 {
            font-weight: 600;
            margin-bottom: 2px;
            font-size: 0.95rem;
        }
        .contact-item p {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 0.9rem;
        }
        .social-links {
            margin-top: 25px;
            background: white;
            padding: 20px 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .social-links h4 {
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 1.1rem;
        }
        .social-link {
            display: inline-block;
            width: 45px;
            height: 45px;
            background: #f0f0f0;
            border-radius: 50%;
            text-align: center;
            line-height: 45px;
            color: #1a1a1a;
            font-size: 20px;
            transition: all 0.3s;
            margin-left: 8px;
        }
        .social-link:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(220, 38, 38, 0.3);
        }
        .map-container {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        @media (max-width: 768px) {
            .contact-item {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            .social-links {
                text-align: center;
            }
            .map-container iframe {
                height: 200px;
            }
        }
        </style>
        <?php
        require_once "views/layout/footer.php";
    }
}
?>