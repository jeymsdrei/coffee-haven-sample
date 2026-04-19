<?php
require_once __DIR__ . '/login/db_connect.php';
require_once __DIR__ . '/login/auth.php';

$user = null;
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: my.html');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>It all starts with a Coffee Haven</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Additional styles for the video section and carousel */
        
        .video-section {
            text-align: center;
            padding: 50px 20px;
            background-color: #f9f9f9;
        }
        .video-section video {
            max-width: 100%;
            height: auto;
        }
        .products-section {
            padding: 100px 20px; /* Increased padding to make the section bigger */
        }
        .products-section .product-carousel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carousel-container {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            gap: 20px;
            padding: 20px;
            max-width: 100%;
        }
        .product-card {
            flex: 0 0 400px; /* Increased width to make cards bigger */
            text-align: center;
        }
        .product-card img {
            width: 400px; /* Ensure images match the card width */
            height: 400px;
        }
        .carousel-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 18px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1;
        }
        .prev-btn {
            left: 10px;
        }
        .next-btn {
            right: 10px;
        }
        .carousel-btn:hover {
            background-color: #0056b3;
        }
        /* Cart button styles (minimal) */
        .nav-actions{ display:flex; align-items:center; gap:12px; }
        .cart-btn{ display:inline-flex; align-items:center; gap:6px; background:transparent; border:none; color:#3b2f2f; text-decoration:none; padding:6px 8px; border-radius:8px; }
        .cart-count{ background:#ff7a00; color:#fff; font-weight:700; font-size:12px; padding:2px 7px; border-radius:12px; min-width:20px; text-align:center; display:inline-block; }
        .cart-count.hidden{ display:none; }
        /* Cart notification animation */
        .cart-btn.notify{ animation: cart-pop 700ms ease; }
        @keyframes cart-pop{
            0% { transform: translateY(0) scale(1); }
            30% { transform: translateY(-6px) scale(1.08); }
            60% { transform: translateY(0) scale(1.02); }
            100% { transform: translateY(0) scale(1); }
        }
        /* Modal styles for product details */
        .modal-overlay{ position:fixed; inset:0; background:rgba(0,0,0,0.6); display:none; align-items:center; justify-content:center; z-index:9999; }
        .modal{ background:#fff; width:90%; max-width:900px; border-radius:8px; box-shadow:0 10px 40px rgba(0,0,0,0.2); overflow:hidden; display:flex; gap:20px; }
        .modal .modal-image{ flex:1; min-width:300px; max-height:520px; overflow:hidden; }
        .modal .modal-image img{ width:100%; height:100%; object-fit:cover; }
        .modal .modal-body{ flex:1; padding:20px; display:flex; flex-direction:column; gap:12px; }
        .modal .modal-body h3{ margin:0; }
        .modal .modal-body p{ margin:0; color:#333; }
        /* small transient notice next to cart button */
        .nav-actions{ display:flex; align-items:center; gap:12px; position:relative; }
        .cart-notice{ position:absolute; top:40px; right:0; background:var(--accent, #10b981); color:#fff; padding:6px 10px; border-radius:8px; font-size:13px; box-shadow:0 6px 20px rgba(2,6,23,0.08); opacity:0; transform:translateY(-6px); transition:opacity 220ms ease, transform 220ms ease; pointer-events:none; }
        .cart-notice.visible{ opacity:1; transform:translateY(0); }
        .modal .modal-actions{ margin-top:auto; display:flex; gap:8px; flex-wrap:wrap; }
        .market-btn{ background:#111; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
        .market-btn.tiktok{ background:#010101; }
        .market-btn.lazada{ background:#0066cc; }
        .market-btn.shopee{ background:#ff5722; }
        .close-modal{ background:transparent; border:none; font-size:20px; cursor:pointer; position:absolute; right:12px; top:8px; }
        .modal-header{ position:relative; }
        .qty-input{ width:80px; padding:6px; }
        /* Payment modal specific styles */
        .pay-methods { display:flex; gap:8px; margin-top:12px; }
        .pay-method { padding:10px 14px; border-radius:6px; background:#f1f1f1; cursor:pointer; border:1px solid #ddd; }
        .pay-method:hover { background:#e9ecef; }
        .payment-success { padding:16px; background:#e6ffed; border:1px solid #b7f0c9; border-radius:6px; color:#0a7a2a; font-weight:700; }
    </style>
</head>
<body>

    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <img src="https://i.imgur.com/OxQ011F.jpg" alt="Coffee Haven's Logo" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                <a href="index.html">Coffee Haven</a>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="index.html" class="nav-link ">Home</a></li>
                <li class="nav-item"><a href="#products" class="nav-link">Products</a></li>
                <li class="nav-item"><a href="C:\xampp\htdocs\coffee_haven\about_us.html" class="nav-link">About</a></li>
             <div class="nav-actions">
                <a href="cart.html" class="cart-btn" title="View cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
                 <?php if ($user): ?>
                    <span class="btn" style="text-decoration:none;">Hello, <?php echo htmlspecialchars($user['username']); ?></span>
                    <a href="login/logout.php" class="btn" style="text-decoration:none;">Log out</a>
                <?php endif; ?>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>It all starts with a Coffee Haven.</h1>
                <p>“A perfect brews makes your day magical and every sip feels like a
 heaven.”</p>
                <a href="my.html" class="btn btn-primary">Find Your Perfect Cup</a>
            </div>
        </section>

        <!-- New video section added before products -->
        <section class="video-section">
            <h2>Watch Our Story</h2>
            <video width="50%" autoplay muted loop controls>
                <source src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\ads.mp4" type="video/mp4">
            </section>     
            </video>
        <section class="video-section">
          <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\Screenshot 2025-11-18 014440.png" alt="Coffee Story Image" height="500" width="1000">
        </section>

        <!-- Changed to products section with carousel functionality -->
        <section id="products" class="products-section">
            <h2 class="section-title">Our Products</h2>
            <div class="product-carousel">
                <button class="carousel-btn prev-btn">&lt;</button>
                <div class="carousel-container">
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 221001.png" alt="Coffee Haven Nature Sweet">
                        <h3>Coffee Haven Nature Sweet</h3>
                        
                        <button class="btn btn-secondary learn-more" data-id="nature-sweet" data-name="Coffee Haven Nature Sweet" data-price="80" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 221001.png" data-desc="A premium blend of beans and herbal plants for a healthy, rich taste.">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="https://imgur.com/Thr19zj.jpg" alt="Coffee Haven Classic">
                        <h3>Coffee Haven Original</h3>
                        <p>The original, full-flavored coffee that started it all.</p>
                        <button class="btn btn-secondary learn-more" data-id="original" data-name="Coffee Haven Original" data-price="149" data-img="https://imgur.com/Thr19zj.jpg" data-desc="The original, full-flavored coffee that started it all.">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 221019.png" alt="Coffee Haven Azera Americano">
                        <h3>Coffee Haven Creamy Coffee</h3>
                        <p>Barista-style instant coffee with a rich crema.</p>
                        <button class="btn btn-secondary learn-more" data-id="creamy" data-name="Coffee Haven Creamy Coffee" data-price="179" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 221019.png" data-desc="Barista-style instant coffee with a rich crema.">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235049.png" alt="Coffee Haven Gold Blend">
                        <h3>Coffee Haven Twin Pack Creamy Coffee</h3>
                        
                        <button class="btn btn-secondary learn-more" data-id="twin-creamy" data-name="Twin Pack Creamy Coffee" data-price="249" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235049.png" data-desc="A premium blend of milk and coffee for a smooth, rich taste on twin pack sachet for two cups of coffee.">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235108.png" alt="Coffee Haven Gold Blend">
                        <h3>Coffee Haven Twin Pack Original</h3>
                        <p>The Original coffee that started it all, in twin pack for two cups of coffee.</p>
                        <button class="btn btn-secondary learn-more" data-id="twin-original" data-name="Twin Pack Original" data-price="229" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235108.png" data-desc="The Original coffee that started it all, in twin pack for two cups of coffee.">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235128.png" alt="Coffee Haven Gold Blend">
                        <h3>Coffee Haven Cappuccino Ready to Drink Coffee</h3>
                        <p>Indulge in the smooth, velvety richness of a cool cappuccino. Delivers a smooth blend of rich espresso and creamy milk, crafted for a refreshing pick-me-up anytime, anywhere.

                        </p>
                        <button class="btn btn-secondary learn-more" data-id="cappuccino" data-name="Cappuccino Ready to Drink" data-price="139" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235128.png" data-desc="Indulge in the smooth, velvety richness of a cool cappuccino. Delivers a smooth blend of rich espresso and creamy milk, crafted for a refreshing pick-me-up anytime, anywhere.">Learn More</button>
                    </div>
                </div>
                <button class="carousel-btn next-btn">&gt;</button>
            </div>

            <!-- Product details modal (hidden until opened) -->
            <div class="modal-overlay" id="product-modal-overlay" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-name">
                    <div class="modal-image">
                        <img id="modal-img" src="" alt="">
                    </div>
                    <div class="modal-body">
                        <div class="modal-header">
                            <h3 id="modal-name">Product Name</h3>
                            <button class="close-modal" id="modal-close" aria-label="Close">&times;</button>
                        </div>
                        <p id="modal-desc">Product description</p>
                        <p><strong>Price:</strong> ₱<span id="modal-price">0</span></p>
                        <label>Quantity: <input type="number" id="modal-qty" class="qty-input" value="1" min="1"></label>
                        <div class="modal-actions">
                            <button id="add-to-cart" class="market-btn">Add to Cart</button>
                            <button id="buy-tiktok" class="market-btn tiktok">Buy on TikTok</button>
                            <button id="buy-lazada" class="market-btn lazada">Buy on Lazada</button>
                            <button id="buy-shopee" class="market-btn shopee">Buy on Shopee</button>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section id="sustainability" class="sustainability-section">
            <div class="sustainability-content">
                <h2 class="section-title">Grown Respectfully</h2>
                <p>We believe in a sustainable future for coffee. From supporting our farmers to pioneering recyclable packaging, we're committed to making a difference, one cup at a time.</p>
                <a href="#sustainability-page" class="btn btn-primary">Our Commitments</a>
            </div>
        </section>
    </main>

    <footer class="system-footer">
        <div class="footer-content">
            
            <div class="footer-column contact-info">
                <h4>Contact & Location</h4>
                <p><strong>Main Store:</strong> Zone4 Ket Saan, Tabaco City</p>
                <p><strong>Phone:</strong> 09947991234</p>
                <a href="mailto:your.email@example.com" class="fas fa-envelope">
                 : coffeehaven@gmail.com </a>
            </div>
            
            <div class="footer-column quick-links">
                <h4>About Us</h4>
                <a href="C:\xampp\htdocs\coffee_haven\about_us.html">Welcome to Coffee Haven
At Coffee Haven, our name is our promise. We strive to be your ultimate destination... See more.
                </a>
            </div>
            
            <div class="footer-column legal-info">
                <h4>System & Legal Info</h3>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
               <div class="social-icons">
          <div class="social-icons">
                <a href="#" class="fab fa-facebook-f">
                 : COFFEE HAVEN Official Page </a>
                <a href="#" class="fab fa-instagram">
                 : coffee_haven </a>
</div>
</div>
            </div>
        </div>
        
         <div class="footer-legal">

            <p>&copy; 2025 Nestlé. All trademarks are owned by Société des Produits Nestlé S.A.</p>

        </div>
    </footer>

<script src="script.js"></script>
<script src="cart.js"></script>
    <script>
        // Carousel functionality
        const carousel = document.querySelector('.carousel-container');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');

        if (prevBtn && nextBtn && carousel) {
            prevBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -420, behavior: 'smooth' });
            });

            nextBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: 420, behavior: 'smooth' });
            });
        }

        // Modal and product interactions
        const learnButtons = document.querySelectorAll('.learn-more');
        const overlay = document.getElementById('product-modal-overlay');
        const modalImg = document.getElementById('modal-img');
        const modalName = document.getElementById('modal-name');
        const modalDesc = document.getElementById('modal-desc');
        const modalPrice = document.getElementById('modal-price');
        const modalQty = document.getElementById('modal-qty');
        const addToCartBtn = document.getElementById('add-to-cart');
        const closeBtn = document.getElementById('modal-close');
        const buyTiktok = document.getElementById('buy-tiktok');
        const buyLazada = document.getElementById('buy-lazada');
        const buyShopee = document.getElementById('buy-shopee');
        let currentProduct = null;

        // Helper: compute total quantity across all cart items
        function getTotalCartQuantity(){
            try{
                if(!window.CoffeeCart) return 0;
                const cart = CoffeeCart.getCart() || [];
                return cart.reduce((sum, it) => sum + (parseInt(it.quantity) || 0), 0);
            }catch(e){ console.error(e); return 0; }
        }

        // Track last cart count so we can show a notification on increment
        let lastCartCount = getTotalCartQuantity();
        function updateCartCount() {
            try {
                const countEl = document.querySelector('.cart-count');
                const cartBtn = document.querySelector('.cart-btn');
                if (countEl) {
                    const newCount = getTotalCartQuantity();
                    // update the badge with total quantity and hide when zero
                    countEl.textContent = newCount;
                    if (newCount > 0) countEl.classList.remove('hidden'); else countEl.classList.add('hidden');
                    // if increased, flash the cart button
                    if (newCount > lastCartCount && cartBtn) {
                        cartBtn.classList.add('notify');
                        setTimeout(() => cartBtn.classList.remove('notify'), 800);
                    }
                    lastCartCount = newCount;
                }
            } catch (e) { console.error(e); }
        }

        function openModal(data) {
            currentProduct = data;
            modalImg.src = data.img || '';
            modalImg.alt = data.name || 'Product image';
            modalName.textContent = data.name || '';
            modalDesc.textContent = data.desc || '';
            modalPrice.textContent = data.price || '0';
            modalQty.value = 1;
            overlay.style.display = 'flex';
            overlay.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            overlay.style.display = 'none';
            overlay.setAttribute('aria-hidden', 'true');
            currentProduct = null;
        }

        learnButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const el = e.currentTarget;
                const data = {
                    id: el.dataset.id,
                    name: el.dataset.name,
                    price: el.dataset.price,
                    img: el.dataset.img,
                    desc: el.dataset.desc || el.closest('.product-card')?.querySelector('p')?.textContent || ''
                };
                openModal(data);
            });
        });

        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });

        addToCartBtn.addEventListener('click', () => {
            if (!currentProduct) return;
            const qty = parseInt(modalQty.value) || 1;
            const item = {
                id: currentProduct.id,
                name: currentProduct.name,
                price: parseFloat(currentProduct.price) || 0,
                quantity: qty,
                variant: '',
                img: currentProduct.img || ''
            };
            try {
                CoffeeCart.addItem(item);
                updateCartCount();
                closeModal();
                showCartNotice('Added to cart');
            } catch (err) {
                console.error(err);
                alert('Unable to add to cart');
            }
        });

        // Payment flow for marketplace purchases (in-page)
        // Insert payment modal markup into the DOM
        const paymentOverlayHtml = `
            <div class="modal-overlay" id="payment-modal-overlay" aria-hidden="true" style="display:none;">
                <div class="modal" role="dialog" aria-modal="true">
                    <div class="modal-body">
                        <div class="modal-header">
                            <h3 id="payment-title">Complete Purchase</h3>
                            <button class="close-modal" id="payment-close" aria-label="Close">&times;</button>
                        </div>
                        <p id="payment-info">Buying <strong id="payment-product-name"></strong> via <strong id="payment-marketplace"></strong></p>
                        <p><strong>Price:</strong> ₱<span id="payment-price">0</span></p>
                        <div class="pay-methods" id="pay-methods">
                            <button class="pay-method" data-method="gcash">GCash</button>
                            <button class="pay-method" data-method="card">Credit Card</button>
                            <button class="pay-method" data-method="cod">Cash on Delivery</button>
                        </div>
                        <div id="payment-result" style="margin-top:12px;"></div>
                    </div>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', paymentOverlayHtml);
        const paymentOverlay = document.getElementById('payment-modal-overlay');
        const paymentClose = document.getElementById('payment-close');
        const paymentProductName = document.getElementById('payment-product-name');
        const paymentMarketplace = document.getElementById('payment-marketplace');
        const paymentPrice = document.getElementById('payment-price');
        const paymentResult = document.getElementById('payment-result');

        function openPaymentModal(marketplace) {
            if (!currentProduct) return;
            paymentProductName.textContent = currentProduct.name || '';
            paymentMarketplace.textContent = marketplace || '';
            paymentPrice.textContent = currentProduct.price || '0';
            paymentResult.innerHTML = '';
            paymentOverlay.style.display = 'flex';
            paymentOverlay.setAttribute('aria-hidden', 'false');
        }

        function closePaymentModal() {
            paymentOverlay.style.display = 'none';
            paymentOverlay.setAttribute('aria-hidden', 'true');
        }

        paymentClose.addEventListener('click', closePaymentModal);
        paymentOverlay.addEventListener('click', (e) => { if (e.target === paymentOverlay) closePaymentModal(); });

        // Delegate pay method clicks
        document.getElementById('pay-methods').addEventListener('click', (e) => {
            const btn = e.target.closest('.pay-method');
            if (!btn) return;
            const method = btn.dataset.method;
            // Simulate payment processing and show success
            paymentResult.innerHTML = '<div class="payment-success">Processing payment via ' + method.toUpperCase() + '...</div>';
            setTimeout(() => {
                paymentResult.innerHTML = '<div class="payment-success">Payment successful! Thanks for purchasing</div>';
                // Optionally close after a short delay
                setTimeout(() => {
                    closePaymentModal();
                    closeModal();
                }, 1600);
            }, 1000);
        });

        buyTiktok.addEventListener('click', () => openPaymentModal('TikTok'));
        buyLazada.addEventListener('click', () => openPaymentModal('Lazada'));
        buyShopee.addEventListener('click', () => openPaymentModal('Shopee'));

        // Initialize cart count on page load
        updateCartCount();

        // React to cart updates dispatched by `cart.js` (same-tab) and to storage events (cross-tab)
        window.addEventListener('cartUpdated', (e) => {
            try { updateCartCount(); } catch (err) { console.error(err); }
        });

        window.addEventListener('storage', (e) => {
            try {
                if (e.key === 'coffeeHavenCart') updateCartCount();
            } catch (err) { console.error(err); }
        });<?php
require_once __DIR__ . '/login/db_connect.php';
require_once __DIR__ . '/login/auth.php';

$user = null;
if (!empty($_SESSION['user_id'])) {
    $stmt = $pdo->prepare('SELECT id, username, email FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>It all starts with a Coffee Haven</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        /* Additional styles for the video section and carousel */
        
        .video-section {
            text-align: center;
            padding: 50px 20px;
            background-color: #f9f9f9;
        }
        .video-section video {
            max-width: 100%;
            height: auto;
        }
        .products-section {
            padding: 100px 20px; /* Increased padding to make the section bigger */
        }
        .products-section .product-carousel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .carousel-container {
            display: flex;
            overflow-x: auto;
            scroll-behavior: smooth;
            gap: 20px;
            padding: 20px;
            max-width: 100%;
        }
        .product-card {
            flex: 0 0 400px; /* Increased width to make cards bigger */
            text-align: center;
        }
        .product-card img {
            width: 400px; /* Ensure images match the card width */
            height: 400px;
        }
        .carousel-btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 18px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 1;
        }
        .prev-btn {
            left: 10px;
        }
        .next-btn {
            right: 10px;
        }
        .carousel-btn:hover {
            background-color: #0056b3;
        }
        /* Cart button styles (minimal) */
        .nav-actions{ display:flex; align-items:center; gap:12px; }
        .cart-btn{ display:inline-flex; align-items:center; gap:6px; background:transparent; border:none; color:#3b2f2f; text-decoration:none; padding:6px 8px; border-radius:8px; }
        .cart-count{ background:#ff7a00; color:#fff; font-weight:700; font-size:12px; padding:2px 7px; border-radius:12px; min-width:20px; text-align:center; display:inline-block; }
        .cart-count.hidden{ display:none; }
        /* Cart notification animation */
        .cart-btn.notify{ animation: cart-pop 700ms ease; }
        @keyframes cart-pop{
            0% { transform: translateY(0) scale(1); }
            30% { transform: translateY(-6px) scale(1.08); }
            60% { transform: translateY(0) scale(1.02); }
            100% { transform: translateY(0) scale(1); }
        }
        /* Modal styles for product details */
        .modal-overlay{ position:fixed; inset:0; background:rgba(0,0,0,0.6); display:none; align-items:center; justify-content:center; z-index:9999; }
        .modal{ background:#fff; width:90%; max-width:900px; border-radius:8px; box-shadow:0 10px 40px rgba(0,0,0,0.2); overflow:hidden; display:flex; gap:20px; }
        .modal .modal-image{ flex:1; min-width:300px; max-height:520px; overflow:hidden; }
        .modal .modal-image img{ width:100%; height:100%; object-fit:cover; }
        .modal .modal-body{ flex:1; padding:20px; display:flex; flex-direction:column; gap:12px; }
        .modal .modal-body h3{ margin:0; }
        .modal .modal-body p{ margin:0; color:#333; }
        /* small transient notice next to cart button */
        .nav-actions{ display:flex; align-items:center; gap:12px; position:relative; }
        .cart-notice{ position:absolute; top:40px; right:0; background:var(--accent, #10b981); color:#fff; padding:6px 10px; border-radius:8px; font-size:13px; box-shadow:0 6px 20px rgba(2,6,23,0.08); opacity:0; transform:translateY(-6px); transition:opacity 220ms ease, transform 220ms ease; pointer-events:none; }
        .cart-notice.visible{ opacity:1; transform:translateY(0); }
        .modal .modal-actions{ margin-top:auto; display:flex; gap:8px; flex-wrap:wrap; }
        .market-btn{ background:#111; color:#fff; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; }
        .market-btn.tiktok{ background:#010101; }
        .market-btn.lazada{ background:#0066cc; }
        .market-btn.shopee{ background:#ff5722; }
        .close-modal{ background:transparent; border:none; font-size:20px; cursor:pointer; position:absolute; right:12px; top:8px; }
        .modal-header{ position:relative; }
        .qty-input{ width:80px; padding:6px; }
        /* Payment modal specific styles */
        .pay-methods { display:flex; gap:8px; margin-top:12px; }
        .pay-method { padding:10px 14px; border-radius:6px; background:#f1f1f1; cursor:pointer; border:1px solid #ddd; }
        .pay-method:hover { background:#e9ecef; }
        .payment-success { padding:16px; background:#e6ffed; border:1px solid #b7f0c9; border-radius:6px; color:#0a7a2a; font-weight:700; }
    </style>
</head>
<body>

    <header>
        <nav class="navbar">
            <div class="nav-brand">
                <img src="https://i.imgur.com/OxQ011F.jpg" alt="Coffee Haven's Logo" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                <a href="index.html">Coffee Haven</a>
            </div>
            <ul class="nav-menu">
                <li class="nav-item"><a href="index.html" class="nav-link ">Home</a></li>
                <li class="nav-item"><a href="#products" class="nav-link">Products</a></li>
                <li class="nav-item"><a href="#about" class="nav-link">About</a></li>
             <div class="nav-actions">
                <a href="cart.html" class="cart-btn" title="View cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count">0</span>
                </a>
                 <?php if ($user): ?>
                    <span class="btn" style="text-decoration:none;">Hello, <?php echo htmlspecialchars($user['username']); ?></span>
                    <a href="login/logout.php" class="btn" style="text-decoration:none;">Log out</a>
                <?php else: ?>
                    <a href="login/login.html" class="btn" style="text-decoration:none;">Log in / Sign up</a>
                <?php endif; ?>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>It all starts with a Coffee Haven.</h1>
                <p>“A perfect brews makes your day magical and every sip feels like a
 heaven.”</p>
                <a href="my.html" class="btn btn-primary">Find Your Perfect Cup</a>
            </div>
        </section>

        <!-- New video section added before products -->
        <section class="video-section">
            <h2>Watch Our Story</h2>
            <video width="50%" autoplay muted loop controls>
                <source src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\ads.mp4" type="video/mp4">
            </section>     
            </video>
        <section class="video-section">
          <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\Screenshot 2025-11-18 014440.png" alt="Coffee Story Image" height="500" width="1000">
        </section>

        <!-- Changed to products section with carousel functionality -->
        <section id="products" class="products-section">
            <h2 class="section-title">Our Products</h2>
            <div class="product-carousel">
                <button class="carousel-btn prev-btn">&lt;</button>
                <div class="carousel-container">
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 221001.png" alt="Coffee Haven Nature Sweet">
                        <h3>Coffee Haven Nature Sweet</h3>
                        <button class="btn btn-secondary learn-more" data-id="nature-sweet" data-name="Coffee Haven Nature Sweet" data-price="80" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 221001.png">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="https://imgur.com/Thr19zj.jpg" alt="Coffee Haven Classic">
                        <h3>Coffee Haven Original</h3>
                        <button class="btn btn-secondary learn-more" data-id="original" data-name="Coffee Haven Original" data-price="149" data-img="https://imgur.com/Thr19zj.jpg">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 221019.png" alt="Coffee Haven Azera Americano">
                        <h3>Coffee Haven Creamy Coffee</h3>
                        <button class="btn btn-secondary learn-more" data-id="creamy" data-name="Coffee Haven Creamy Coffee" data-price="179" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 221019.png">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235049.png" alt="Coffee Haven Gold Blend">
                        <h3>Coffee Haven Twin Pack Creamy Coffee</h3>
                        <button class="btn btn-secondary learn-more" data-id="twin-creamy" data-name="Twin Pack Creamy Coffee" data-price="249" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235049.png">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235108.png" alt="Coffee Haven Gold Blend">
                        <h3>Coffee Haven Twin Pack Original</h3>
                        <button class="btn btn-secondary learn-more" data-id="twin-original" data-name="Twin Pack Original" data-price="229" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235108.png">Learn More</button>
                    </div>
                    <div class="product-card">
                        <img src="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235128.png" alt="Coffee Haven Gold Blend">
                        <h3>Coffee Haven Cappuccino Ready to Drink Coffee</h3>
                        <button class="btn btn-secondary learn-more" data-id="cappuccino" data-name="Cappuccino Ready to Drink" data-price="139" data-img="C:\Users\Ryzen\Desktop\Coffee Haven\panduwa\PICTURES\Screenshot 2025-11-20 235128.png">Learn More</button>
                    </div>
                </div>
                <button class="carousel-btn next-btn">&gt;</button>
            </div>

            <!-- Product details modal (hidden until opened) -->
            <div class="modal-overlay" id="product-modal-overlay" aria-hidden="true">
                <div class="modal" role="dialog" aria-modal="true" aria-labelledby="modal-name">
                    <div class="modal-image">
                        <img id="modal-img" src="" alt="">
                    </div>
                    <div class="modal-body">
                        <div class="modal-header">
                            <h3 id="modal-name">Product Name</h3>
                            <button class="close-modal" id="modal-close" aria-label="Close">&times;</button>
                        </div>
                        <p id="modal-desc">Product description</p>
                        <p><strong>Price:</strong> ₱<span id="modal-price">0</span></p>
                        <label>Quantity: <input type="number" id="modal-qty" class="qty-input" value="1" min="1"></label>
                        <div class="modal-actions">
                            <button id="add-to-cart" class="market-btn">Add to Cart</button>
                            <button id="buy-tiktok" class="market-btn tiktok">Buy on TikTok</button>
                            <button id="buy-lazada" class="market-btn lazada">Buy on Lazada</button>
                            <button id="buy-shopee" class="market-btn shopee">Buy on Shopee</button>
                        </div>
                    </div>
                </div>
            </div>

        </section>

        <section id="sustainability" class="sustainability-section">
            <div class="sustainability-content">
                <h2 class="section-title">Grown Respectfully</h2>
                <p>We believe in a sustainable future for coffee. From supporting our farmers to pioneering recyclable packaging, we're committed to making a difference, one cup at a time.</p>
                <a href="#sustainability-page" class="btn btn-primary">Our Commitments</a>
            </div>
        </section>
    </main>

    <footer class="system-footer">
        <div class="footer-content">
            
            <div class="footer-column contact-info">
                <h4>Contact & Location</h4>
                <p><strong>Main Store:</strong> Zone4 Ket Saan, Tabaco City</p>
                <p><strong>Phone:</strong> 09947991234</p>
                <a href="mailto:your.email@example.com" class="fas fa-envelope">
                 : coffeehaven@gmail.com </a>
            </div>
            
            <div class="footer-column quick-links">
                <h4>About Us</h4>
                <a href="c:\Users\Ryzen\Desktop\Coffee Haven\panduwa\about_us.html">Welcome to Coffee Haven
At Coffee Haven, our name is our promise. We strive to be your ultimate destination... See more.
                </a>
            </div>
            
            <div class="footer-column legal-info">
                <h4>System & Legal Info</h3>
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
               <div class="social-icons">
          <div class="social-icons">
                <a href="#" class="fab fa-facebook-f">
                 : COFFEE HAVEN Official Page </a>
                <a href="#" class="fab fa-instagram">
                 : coffee_haven </a>
</div>
</div>
            </div>
        </div>
        
         <div class="footer-legal">

            <p>&copy; 2025 Nestlé. All trademarks are owned by Société des Produits Nestlé S.A.</p>

        </div>
    </footer>

<script src="script.js"></script>
<script src="cart.js"></script>
    <script>
        // Carousel functionality
        const carousel = document.querySelector('.carousel-container');
        const prevBtn = document.querySelector('.prev-btn');
        const nextBtn = document.querySelector('.next-btn');

        if (prevBtn && nextBtn && carousel) {
            prevBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: -420, behavior: 'smooth' });
            });

            nextBtn.addEventListener('click', () => {
                carousel.scrollBy({ left: 420, behavior: 'smooth' });
            });
        }

        // Modal and product interactions
        const learnButtons = document.querySelectorAll('.learn-more');
        const overlay = document.getElementById('product-modal-overlay');
        const modalImg = document.getElementById('modal-img');
        const modalName = document.getElementById('modal-name');
        const modalDesc = document.getElementById('modal-desc');
        const modalPrice = document.getElementById('modal-price');
        const modalQty = document.getElementById('modal-qty');
        const addToCartBtn = document.getElementById('add-to-cart');
        const closeBtn = document.getElementById('modal-close');
        const buyTiktok = document.getElementById('buy-tiktok');
        const buyLazada = document.getElementById('buy-lazada');
        const buyShopee = document.getElementById('buy-shopee');
        let currentProduct = null;

        // Helper: compute total quantity across all cart items
        function getTotalCartQuantity(){
            try{
                if(!window.CoffeeCart) return 0;
                const cart = CoffeeCart.getCart() || [];
                return cart.reduce((sum, it) => sum + (parseInt(it.quantity) || 0), 0);
            }catch(e){ console.error(e); return 0; }
        }

        // Track last cart count so we can show a notification on increment
        let lastCartCount = getTotalCartQuantity();
        function updateCartCount() {
            try {
                const countEl = document.querySelector('.cart-count');
                const cartBtn = document.querySelector('.cart-btn');
                if (countEl) {
                    const newCount = getTotalCartQuantity();
                    // update the badge with total quantity and hide when zero
                    countEl.textContent = newCount;
                    if (newCount > 0) countEl.classList.remove('hidden'); else countEl.classList.add('hidden');
                    // if increased, flash the cart button
                    if (newCount > lastCartCount && cartBtn) {
                        cartBtn.classList.add('notify');
                        setTimeout(() => cartBtn.classList.remove('notify'), 800);
                    }
                    lastCartCount = newCount;
                }
            } catch (e) { console.error(e); }
        }

        function openModal(data) {
            currentProduct = data;
            modalImg.src = data.img || '';
            modalImg.alt = data.name || 'Product image';
            modalName.textContent = data.name || '';
            modalDesc.textContent = data.desc || '';
            modalPrice.textContent = data.price || '0';
            modalQty.value = 1;
            overlay.style.display = 'flex';
            overlay.setAttribute('aria-hidden', 'false');
        }

        function closeModal() {
            overlay.style.display = 'none';
            overlay.setAttribute('aria-hidden', 'true');
            currentProduct = null;
        }

        learnButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const el = e.currentTarget;
                const data = {
                    id: el.dataset.id,
                    name: el.dataset.name,
                    price: el.dataset.price,
                    img: el.dataset.img,
                    desc: el.closest('.product-card')?.querySelector('p')?.textContent || ''
                };
                openModal(data);
            });
        });

        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', (e) => { if (e.target === overlay) closeModal(); });

        addToCartBtn.addEventListener('click', () => {
            if (!currentProduct) return;
            const qty = parseInt(modalQty.value) || 1;
            const item = {
                id: currentProduct.id,
                name: currentProduct.name,
                price: parseFloat(currentProduct.price) || 0,
                quantity: qty,
                variant: '',
                img: currentProduct.img || ''
            };
            try {
                CoffeeCart.addItem(item);
                updateCartCount();
                closeModal();
                showCartNotice('Added to cart');
            } catch (err) {
                console.error(err);
                alert('Unable to add to cart');
            }
        });

        // Payment flow for marketplace purchases (in-page)
        // Insert payment modal markup into the DOM
        const paymentOverlayHtml = `
            <div class="modal-overlay" id="payment-modal-overlay" aria-hidden="true" style="display:none;">
                <div class="modal" role="dialog" aria-modal="true">
                    <div class="modal-body">
                        <div class="modal-header">
                            <h3 id="payment-title">Complete Purchase</h3>
                            <button class="close-modal" id="payment-close" aria-label="Close">&times;</button>
                        </div>
                        <p id="payment-info">Buying <strong id="payment-product-name"></strong> via <strong id="payment-marketplace"></strong></p>
                        <p><strong>Price:</strong> ₱<span id="payment-price">0</span></p>
                        <div class="pay-methods" id="pay-methods">
                            <button class="pay-method" data-method="gcash">GCash</button>
                            <button class="pay-method" data-method="card">Credit Card</button>
                            <button class="pay-method" data-method="cod">Cash on Delivery</button>
                        </div>
                        <div id="payment-result" style="margin-top:12px;"></div>
                    </div>
                </div>
            </div>`;

        document.body.insertAdjacentHTML('beforeend', paymentOverlayHtml);
        const paymentOverlay = document.getElementById('payment-modal-overlay');
        const paymentClose = document.getElementById('payment-close');
        const paymentProductName = document.getElementById('payment-product-name');
        const paymentMarketplace = document.getElementById('payment-marketplace');
        const paymentPrice = document.getElementById('payment-price');
        const paymentResult = document.getElementById('payment-result');

        function openPaymentModal(marketplace) {
            if (!currentProduct) return;
            paymentProductName.textContent = currentProduct.name || '';
            paymentMarketplace.textContent = marketplace || '';
            paymentPrice.textContent = currentProduct.price || '0';
            paymentResult.innerHTML = '';
            paymentOverlay.style.display = 'flex';
            paymentOverlay.setAttribute('aria-hidden', 'false');
        }

        function closePaymentModal() {
            paymentOverlay.style.display = 'none';
            paymentOverlay.setAttribute('aria-hidden', 'true');
        }

        paymentClose.addEventListener('click', closePaymentModal);
        paymentOverlay.addEventListener('click', (e) => { if (e.target === paymentOverlay) closePaymentModal(); });

        // Delegate pay method clicks
        document.getElementById('pay-methods').addEventListener('click', (e) => {
            const btn = e.target.closest('.pay-method');
            if (!btn) return;
            const method = btn.dataset.method;
            // Simulate payment processing and show success
            paymentResult.innerHTML = '<div class="payment-success">Processing payment via ' + method.toUpperCase() + '...</div>';
            setTimeout(() => {
                paymentResult.innerHTML = '<div class="payment-success">Payment successful! Thanks for purchasing</div>';
                // Optionally close after a short delay
                setTimeout(() => {
                    closePaymentModal();
                    closeModal();
                }, 1600);
            }, 1000);
        });

        buyTiktok.addEventListener('click', () => openPaymentModal('TikTok'));
        buyLazada.addEventListener('click', () => openPaymentModal('Lazada'));
        buyShopee.addEventListener('click', () => openPaymentModal('Shopee'));

        // Initialize cart count on page load
        updateCartCount();

        // React to cart updates dispatched by `cart.js` (same-tab) and to storage events (cross-tab)
        window.addEventListener('cartUpdated', (e) => {
            try { updateCartCount(); } catch (err) { console.error(err); }
        });

        window.addEventListener('storage', (e) => {
            try {
                if (e.key === 'coffeeHavenCart') updateCartCount();
            } catch (err) { console.error(err); }
        });

        // Transient cart notice logic
        let cartNoticeTimer = null;
        function showCartNotice(text, duration = 1800){
            try{
                const el = document.getElementById('cart-notice');
                if(!el) return;
                el.textContent = text || 'Added to cart';
                el.classList.add('visible');
                el.setAttribute('aria-hidden', 'false');
                // clear previous timer
                if(cartNoticeTimer) clearTimeout(cartNoticeTimer);
                cartNoticeTimer = setTimeout(()=>{
                    el.classList.remove('visible');
                    el.setAttribute('aria-hidden', 'true');
                    cartNoticeTimer = null;
                }, duration);
            }catch(e){console.error(e)}
        }
    </script>
</body>
</html>



        // Transient cart notice logic
        let cartNoticeTimer = null;
        function showCartNotice(text, duration = 1800){
            try{
                const el = document.getElementById('cart-notice');
                if(!el) return;
                el.textContent = text || 'Added to cart';
                el.classList.add('visible');
                el.setAttribute('aria-hidden', 'false');
                // clear previous timer
                if(cartNoticeTimer) clearTimeout(cartNoticeTimer);
                cartNoticeTimer = setTimeout(()=>{
                    el.classList.remove('visible');
                    el.setAttribute('aria-hidden', 'true');
                    cartNoticeTimer = null;
                }, duration);
            }catch(e){console.error(e)}
        }
    </script>
</body>
</html>

