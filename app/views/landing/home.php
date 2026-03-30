<?php
// If you already define URLROOT in your project config, keep this.
// Otherwise you can hardcode it for testing.
// define('URLROOT', 'http://localhost/CMA/public');
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SmartCare</title>

  <!-- Icons + Font -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/landing.css">

</head>

<body>

  <!-- Header -->
  <header>
    <div class="container">
      <nav class="navbar">
        <a class="logo" href="#">
          <img src="<?php echo URLROOT; ?>/public/images/logo.jpg" alt="SmartCare Logo">
          SmartCare
        </a>

        <ul class="nav-links">
          <li><a href="#about">About</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#faq">FAQ</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>

        <div class="nav-actions">
          <a class="btn btn-ghost" href="tel:+94700000000"><i class='bx bxs-phone'></i> Call</a>
          <a class="btn btn-solid" href="#contact"><i class='bx bxl-whatsapp'></i> WhatsApp</a>
          <a class="btn btn-login" href="<?php echo URLROOT; ?>/?url=auth/login"><i class='bx bxs-lock-alt'></i> Login</a>
          <div class="menu-btn" id="menuBtn"><i class='bx bx-menu'></i></div>
        </div>
      </nav>
    </div>
  </header>

  <!-- HERO -->
  <div class="container hero-wrap">
    <section class="hero" aria-label="SmartCare hero banner">
      <div class="hero-slider">
        <!-- Use your images here -->
        <img src="<?php echo URLROOT; ?>/public/images/elderCare.jpg" class="hero-slide active" alt="Elder Care">
        <img src="<?php echo URLROOT; ?>/public/images/childCare.jpg" class="hero-slide" alt="Babysitting">
        <img src="<?php echo URLROOT; ?>/public/images/cleaningService.jpg" class="hero-slide" alt="Home Support">
      </div>

      <div class="hero-content">
        <div class="hero-box">
          <h1>Your Partner in Caregiving</h1>
          <p>Trusted caregivers for elder care, babysitting, and home support — verified profiles, easy booking, and fast help when you need it.</p>

          <div class="hero-actions">
            <a class="hero-btn primary" href="#services"><i class='bx bx-search-alt'></i> Find a Caregiver</a>
          </div>

          <div class="hero-trust">
            <span class="pill"><i class='bx bxs-badge-check'></i> Verified profiles</span>
            <span class="pill"><i class='bx bxs-star'></i> Rated service</span>
            <span class="pill"><i class='bx bx-support'></i> 24/7 support</span>
          </div>
        </div>
      </div>

      <div class="hero-nav">
        <button class="arrow prev" id="prevBtn" aria-label="Previous slide"><i class='bx bx-chevron-left'></i></button>
        <button class="arrow next" id="nextBtn" aria-label="Next slide"><i class='bx bx-chevron-right'></i></button>
        <div class="dots" id="dots"></div>
      </div>
    </section>

    <!-- Quick Stats Strip -->
    <div class="stats-strip" aria-label="Highlights">
      <div class="stat"><b>Verified</b><span>Background-checked caregivers</span></div>
      <div class="stat"><b>Fast Booking</b><span>Book in minutes, not days</span></div>
      <div class="stat"><b>Flexible Plans</b><span>Hourly / Weekly / Monthly</span></div>
      <div class="stat"><b>Support</b><span>Help anytime you need</span></div>
    </div>
  </div>

  <!-- ABOUT -->
  <section id="about">
    <div class="container">
      <h2 class="section-title">About SmartCare</h2>
      <p class="section-sub">
        SmartCare connects families with trusted caregivers for elder care, babysitting, and home support.
        Our mission is to make caregiving simple, safe, and reliable with verified caregivers, easy booking, and support.
      </p>

      <div class="grid-2" style="margin-top:22px;">
        <div>
          <div class="feature-grid">
            <div class="feature">
              <i class='bx bxs-badge-check'></i>
              <div>
                <b>Verified Caregivers</b>
                <p>Profiles reviewed with identity checks and screening.</p>
              </div>
            </div>
            <div class="feature">
              <i class='bx bx-calendar-check'></i>
              <div>
                <b>Easy Booking</b>
                <p>Choose service, select caregiver, confirm schedule.</p>
              </div>
            </div>
            <div class="feature">
              <i class='bx bx-shield-quarter'></i>
              <div>
                <b>Safety First</b>
                <p>Transparent profiles, reviews, and clear policies.</p>
              </div>
            </div>
            <div class="feature">
              <i class='bx bx-headphone'></i>
              <div>
                <b>Customer Support</b>
                <p>Quick assistance for families and caregivers.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="panel">
          <b style="color:var(--blue2); font-size:18px;">Need urgent help?</b>
          <p class="muted" style="margin-top:8px;">Contact us instantly. We’ll guide you to the best caregiver option for your need.</p>
          <div class="quick-contact">
            <a class="qc-btn" href="tel:+94700000000"><i class='bx bxs-phone'></i> Call Now</a>
            <a class="qc-btn" href="https://wa.me/94700000000" target="_blank"><i class='bx bxl-whatsapp'></i> WhatsApp</a>
            <a class="qc-btn" href="#contact"><i class='bx bxs-envelope'></i> Message</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- TRUST / SAFETY -->
  <section class="trust" aria-label="Trust and Safety">
    <div class="container">
      <h2 class="section-title">Trust & Safety</h2>
      <p class="section-sub">Caregiving needs trust. Here’s how SmartCare keeps families confident.</p>

      <div class="trust-cards">
        <div class="trust-card">
          <i class='bx bxs-id-card'></i>
          <b>ID Verification</b>
          <p>We confirm identity and basic credentials before approving profiles.</p>
        </div>
        <div class="trust-card">
          <i class='bx bxs-user-check'></i>
          <b>Profile Screening</b>
          <p>Experience, skills, and availability are reviewed.</p>
        </div>
        <div class="trust-card">
          <i class='bx bxs-star'></i>
          <b>Ratings & Reviews</b>
          <p>Real feedback to help you pick confidently.</p>
        </div>
        <div class="trust-card">
          <i class='bx bxs-lock'></i>
          <b>Secure Process</b>
          <p>Clear bookings, records, and support to handle issues fast.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- SERVICES -->
  <section id="services">
    <div class="container">
      <h2 class="section-title">Our Services</h2>
      <p class="section-sub">Choose the service you need and get matched with the right caregiver.</p>

      <div class="cards">
        <article class="card">
          <div class="card-media">
            <img src="<?php echo URLROOT; ?>/public/images/elder-care.jpg" alt="Elder Care">
            <span class="tag">Most requested</span>
          </div>
          <div class="card-body">
            <h3>Elder Care</h3>
            <p>Daily assistance, companionship, medication reminders, and support tailored to your loved one.</p>
            <div class="card-actions">
              <a class="link" href="#pricing"><i class='bx bx-right-arrow-alt'></i> View pricing</a>
              <button class="small-btn go-login" type="button">Book</button>
            </div>
          </div>
        </article>

        <article class="card">
          <div class="card-media">
            <img src="<?php echo URLROOT; ?>/public/images/baby-sitting.webp" alt="Babysitting">
            <span class="tag">Flexible hours</span>
          </div>
          <div class="card-body">
            <h3>Babysitting</h3>
            <p>Safe and caring babysitters for your child — hourly, weekly, or monthly options available.</p>
            <div class="card-actions">
              <a class="link" href="#pricing"><i class='bx bx-right-arrow-alt'></i> View pricing</a>
              <button class="small-btn go-login" type="button">Book</button>
            </div>
          </div>
        </article>

        <article class="card">
          <div class="card-media">
            <img src="<?php echo URLROOT; ?>/public/images/cleaning.webp" alt="Cleaning & Cooking">
            <span class="tag">Home support</span>
          </div>
          <div class="card-body">
            <h3>Cleaning & Cooking</h3>
            <p>Skilled home helpers for cleaning, cooking, and routine household support.</p>
            <div class="card-actions">
              <a class="link" href="#pricing"><i class='bx bx-right-arrow-alt'></i> View pricing</a>
              <button class="small-btn go-login" type="button">Book</button>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section aria-label="How SmartCare works">
    <div class="container">
      <h2 class="section-title">How It Works</h2>
      <p class="section-sub">Simple steps to find and book the right caregiver.</p>

      <div class="steps">
        <div class="step">
          <i class='bx bx-edit'></i>
          <div>
            <b>1) Choose a service</b>
            <p>Select elder care, babysitting, or home support based on your needs.</p>
          </div>
        </div>
        <div class="step">
          <i class='bx bx-search-alt'></i>
          <div>
            <b>2) Pick your caregiver</b>
            <p>Compare profiles, experience, and availability. Read ratings and reviews.</p>
          </div>
        </div>
        <div class="step">
          <i class='bx bx-calendar-check'></i>
          <div>
            <b>3) Book & get support</b>
            <p>Confirm schedule and get assistance anytime from our support team.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="pricing" id="pricing">
    <div class="container">
      <h2 class="section-title">Pricing Options</h2>
      <p class="section-sub">Choose a service to view packages.</p>

      <?php
      // Load rates (use one of these)
      // Option A: from config file
      // $servicePriceRates = require APPROOT . '/config/pricing.php';

      // Option B: directly (if you want here)
      $servicePriceRates = [
        "Elder Care" => ["Monthly" => 50000, "Yearly" => 550000],
        "Babysitter" => ["Daily" => 2200, "Monthly" => 45000, "Yearly" => 500000],
        "Maid" => ["Hourly" => 500, "Daily" => 2000, "Monthly" => 38000, "Yearly" => 420000]
      ];

      $services = array_keys($servicePriceRates);
      ?>

      <!-- Tabs -->
      <div class="tabs" role="tablist">
        <?php foreach ($services as $i => $service): ?>
          <button class="tab <?= $i === 0 ? 'active' : '' ?>" type="button" data-tab="<?= htmlspecialchars($service) ?>">
            <?= htmlspecialchars($service) ?>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- Panels -->
      <?php foreach ($servicePriceRates as $serviceName => $packages): ?>
        <div class="pricing-grid <?= $serviceName === $services[0] ? '' : 'hide' ?>" id="tab-<?= htmlspecialchars($serviceName) ?>">
          <?php foreach ($packages as $plan => $price): ?>
            <div class="price-card">
              <h4><?= htmlspecialchars($plan) ?></h4>

              <div class="price">
                LKR <?= number_format($price) ?>
                <span>
                  / <?= strtolower($plan === "Hourly" ? "hour" : ($plan === "Daily" ? "day" : ($plan === "Monthly" ? "month" : "year"))) ?>
                </span>
              </div>

              <ul>
                <li><i class='bx bxs-check-circle'></i> Verified staff</li>
                <li><i class='bx bxs-check-circle'></i> Flexible scheduling</li>
                <li><i class='bx bxs-check-circle'></i> Support available</li>
              </ul>

              <button class="go-login" type="button">Select</button>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

    </div>
  </section>


  <!-- TESTIMONIALS -->
  <section class="testimonials" aria-label="Testimonials">
    <div class="container">
      <h2 class="section-title">What Families Say</h2>
      <p class="section-sub">Real experiences from families who used SmartCare services.</p>

      <div class="t-grid">
        <div class="t-card">
          <div class="t-head">
            <div class="t-user">
              <div class="avatar">O</div>
              <div>
                <b>Olivia Bennett</b>
                <small>Colombo</small>
              </div>
            </div>
            <div class="stars">★★★★★</div>
          </div>
          <p>“We found a kind and reliable elder caregiver quickly. The process was smooth and the support was helpful.”</p>
        </div>

        <div class="t-card">
          <div class="t-head">
            <div class="t-user">
              <div class="avatar">L</div>
              <div>
                <b>Liam Harper</b>
                <small>Gampaha</small>
              </div>
            </div>
            <div class="stars">★★★★☆</div>
          </div>
          <p>“The babysitter was excellent and very attentive. We felt safe leaving our child in her care.”</p>
        </div>

        <div class="t-card">
          <div class="t-head">
            <div class="t-user">
              <div class="avatar">S</div>
              <div>
                <b>Sofia James</b>
                <small>Kandy</small>
              </div>
            </div>
            <div class="stars">★★★★★</div>
          </div>
          <p>“Home support was a lifesaver. The helper kept everything clean and meals prepared on time.”</p>
        </div>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" aria-label="Frequently asked questions">
    <div class="container">
      <h2 class="section-title">Frequently Asked Questions</h2>
      <p class="section-sub">Quick answers to common questions.</p>

      <div class="faq-grid">
        <details>
          <summary>How do I book a caregiver?</summary>
          <p>Sign up, choose a service, browse caregivers, and book based on availability and package.</p>
        </details>

        <details>
          <summary>How are caregivers verified?</summary>
          <p>We review profiles with identity checks and screening before approval, and rely on ratings and reviews.</p>
        </details>

        <details>
          <summary>Can I change a caregiver after booking?</summary>
          <p>Yes. If your caregiver becomes unavailable or you prefer another, you can request a change based on availability.</p>
        </details>

        <details>
          <summary>What payment options are available?</summary>
          <p>We support flexible packages such as hourly, weekly, and monthly plans depending on the service.</p>
        </details>

        <details>
          <summary>Is there a cancellation policy?</summary>
          <p>Yes, cancellations are allowed with prior notice according to the platform terms and booking rules.</p>
        </details>

        <details>
          <summary>Do you cover emergency bookings?</summary>
          <p>When caregivers are available, we try to support urgent bookings. Use Call/WhatsApp for faster help.</p>
        </details>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  <section class="contact-wrap" id="contact">
    <div class="container">
      <h2 class="section-title">Contact Us</h2>
      <p class="section-sub">Have questions or need support? Message us anytime.</p>

      <div class="contact-grid">
        <!-- Form -->
        <div class="panel">
          <b style="color:var(--blue2); font-size:18px;">Send a Message</b>
          <p class="muted" style="margin-top:6px;">We’ll reply as soon as possible.</p>

          <form class="contact-form" action="#" method="POST" style="margin-top:12px;">
            <input class="input" type="text" name="name" placeholder="Your Name" required>
            <input class="input" type="email" name="email" placeholder="Your Email" required>
            <textarea class="input" name="message" placeholder="Your Message" required></textarea>
            <button class="btn btn-solid" type="submit"><i class='bx bx-send'></i> Send Message</button>
          </form>

          <div class="quick-contact">
            <a class="qc-btn" href="tel:+94700000000"><i class='bx bxs-phone'></i> +94 70 000 0000</a>
            <a class="qc-btn" href="https://wa.me/94700000000" target="_blank"><i class='bx bxl-whatsapp'></i> WhatsApp</a>
          </div>
        </div>

        <!-- Map + Social -->
        <div class="panel">
          <b style="color:var(--blue2); font-size:18px;">Our Location</b>
          <p class="muted" style="margin-top:6px;">You can also reach us via social media.</p>

          <iframe
            class="map"
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.921306041527!2d79.85392287930699!3d6.900014875306098!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2596309dfdd3f%3A0x45a4b0e7834ac0d4!2sUniversity%20of%20Colombo!5e0!3m2!1sen!2slk!4v1757388167234!5m2!1sen!2slk"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>

          <div class="social-icons">
            <a href="#" aria-label="Facebook"><i class='bx bxl-facebook-square'></i></a>
            <a href="#" aria-label="Twitter"><i class='bx bxl-twitter'></i></a>
            <a href="#" aria-label="Instagram"><i class='bx bxl-instagram'></i></a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="cta" aria-label="Call to action">
    <div class="container">
      <div class="cta-card">
        <h2>Ready to find the right caregiver?</h2>
        <p>Join SmartCare today and book verified caregivers with flexible plans and quick support.</p>
        <div class="cta-actions">
          <a class="btn btn-solid" href="<?php echo URLROOT; ?>/?url=auth/login"><i class='bx bxs-lock-alt'></i> Get Started</a>
          <a class="btn btn-ghost" href="#contact"><i class='bx bx-message-dots'></i> Talk to Us</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="container">
      <div class="footer-grid">
        <div>
          <b style="font-size:18px;">© 2025 SmartCare</b>
          <p style="margin-top:6px; opacity:.95;">Caregiving made simple, safe, and reliable.</p>
        </div>
        <ul class="footer-links">
          <li><a href="#about">About</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#faq">FAQ</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>
    </div>
  </footer>

  <script>
    // ===== HERO SLIDER (auto + arrows + dots) =====
    document.addEventListener("DOMContentLoaded", () => {
      const slides = Array.from(document.querySelectorAll(".hero-slide"));
      const prevBtn = document.getElementById("prevBtn");
      const nextBtn = document.getElementById("nextBtn");
      const dotsWrap = document.getElementById("dots");

      let current = 0;
      let timer = null;

      // build dots
      slides.forEach((_, i) => {
        const d = document.createElement("div");
        d.className = "dot" + (i === 0 ? " active" : "");
        d.addEventListener("click", () => goTo(i, true));
        dotsWrap.appendChild(d);
      });

      const dots = Array.from(document.querySelectorAll(".dot"));

      function setActive(index) {
        slides[current].classList.remove("active");
        dots[current].classList.remove("active");

        current = index;

        slides[current].classList.add("active");
        dots[current].classList.add("active");
      }

      function next() {
        setActive((current + 1) % slides.length);
      }

      function prev() {
        setActive((current - 1 + slides.length) % slides.length);
      }

      function start() {
        stop();
        timer = setInterval(next, 5000);
      }

      function stop() {
        if (timer) clearInterval(timer);
      }

      function goTo(index, restart = false) {
        setActive(index);
        if (restart) start();
      }

      nextBtn.addEventListener("click", () => {
        next();
        start();
      });
      prevBtn.addEventListener("click", () => {
        prev();
        start();
      });

      // pause on hover
      const hero = document.querySelector(".hero");
      hero.addEventListener("mouseenter", stop);
      hero.addEventListener("mouseleave", start);

      start();
    });

    // ===== Pricing Tabs =====
    document.addEventListener("DOMContentLoaded", () => {
      const tabs = document.querySelectorAll(".tab");

      function normalizeId(name) {
        return "tab-" + name; // we used id="tab-Elder Care" exactly (spaces allowed)
      }

      tabs.forEach(t => {
        t.addEventListener("click", () => {
          tabs.forEach(x => x.classList.remove("active"));
          t.classList.add("active");

          document.querySelectorAll(".pricing-grid").forEach(p => p.classList.add("hide"));

          const panel = document.getElementById(normalizeId(t.dataset.tab));
          if (panel) panel.classList.remove("hide");
        });
      });

      // Select -> login redirect
      const loginUrl = "/CMA/public/?url=auth/login";
      document.querySelectorAll(".go-login").forEach(btn => {
        btn.addEventListener("click", () => window.location.href = loginUrl);
      });
    });



    // ===== Buttons to Login (Book/Select) =====
    document.addEventListener("DOMContentLoaded", () => {
      const loginUrl = "<?php echo URLROOT; ?>/?url=auth/login";
      document.querySelectorAll(".go-login").forEach(btn => {
        btn.addEventListener("click", () => {
          window.location.href = loginUrl;
        });
      });
    });
  </script>

</body>

</html>