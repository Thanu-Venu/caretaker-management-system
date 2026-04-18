<?php
// If you already define URLROOT in your project config, keep this.
// Otherwise you can hardcode it for testing.
// define('URLROOT', 'http://localhost/CMA/public');

$mheroSlides = [
  ['src' => URLROOT . '/public/images/elderCare1.png', 'alt' => 'Caregiver with a client in a wheelchair, smiling together.'],
  ['src' => URLROOT . '/public/images/maid1.png', 'alt' => 'Professional caregiver supporting an older adult.'],
  ['src' => URLROOT . '/public/images/babySitter.png', 'alt' => 'Caregiver delivering attentive home care.'],
  ['src' => URLROOT . '/public/images/hero-image.png', 'alt' => 'Warm elder care and companionship at home.'],
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title>SmartCare</title>

  <!-- Icons + fonts (display + UI): swap URLs for self-hosted files if needed -->
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <?php $ppMoriFontBase = rtrim(URLROOT, '/') . '/public/fonts/'; ?>
  <style>
    @font-face {
      font-family: "PP Mori";
      font-style: normal;
      font-weight: 400;
      font-display: swap;
      src:
        local("PP Mori"),
        local("PP Mori Regular"),
        url("<?php echo $ppMoriFontBase; ?>PPMori-Regular.woff2") format("woff2");
    }
    @font-face {
      font-family: "PP Mori";
      font-style: normal;
      font-weight: 500;
      font-display: swap;
      src:
        local("PP Mori Medium"),
        url("<?php echo $ppMoriFontBase; ?>PPMori-Medium.woff2") format("woff2");
    }
    @font-face {
      font-family: "PP Mori";
      font-style: normal;
      font-weight: 600;
      font-display: swap;
      src:
        local("PP Mori SemiBold"),
        local("PP Mori Semibold"),
        url("<?php echo $ppMoriFontBase; ?>PPMori-Semibold.woff2") format("woff2"),
        url("<?php echo $ppMoriFontBase; ?>PPMori-SemiBold.woff2") format("woff2");
    }
    @font-face {
      font-family: "PP Mori";
      font-style: normal;
      font-weight: 700;
      font-display: swap;
      src:
        local("PP Mori Bold"),
        url("<?php echo $ppMoriFontBase; ?>PPMori-Bold.woff2") format("woff2");
    }
    @font-face {
      font-family: "PP Mori";
      font-style: normal;
      font-weight: 800;
      font-display: swap;
      src:
        local("PP Mori ExtraBold"),
        local("PP Mori Extrabold"),
        url("<?php echo $ppMoriFontBase; ?>PPMori-Extrabold.woff2") format("woff2"),
        url("<?php echo $ppMoriFontBase; ?>PPMori-ExtraBold.woff2") format("woff2");
    }
  </style>
  <link rel="stylesheet" href="<?php echo URLROOT; ?>/public/css/landing.css">

</head>

<body class="landing-page">

  <!-- Sticky site nav (outside hero section so it stays pinned for the whole page) -->
  <header class="site-header site-header--hero mhero-header">
    <div class="mhero-header-inner">
      <a class="logo mhero-brand" href="#">
        <img src="<?php echo URLROOT; ?>/public/images/logo.jpg" alt="SmartCare logo">
        <span class="logo-text">SmartCare</span>
      </a>

      <nav class="mhero-nav" aria-label="Primary">
        <ul class="nav-links" id="navLinks">
          <li><a href="#">Home</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#about">About</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </nav>

      <div class="mhero-header-right">
        <a class="mhero-book" href="#contact" aria-label="Book appointment"><span class="mhero-book-label mhero-book-label--full">Book appointment</span><span class="mhero-book-label mhero-book-label--short">Book</span> <i class='bx bx-right-arrow-alt' aria-hidden="true"></i></a>
        <a class="mhero-icon" href="tel:+94700000000" aria-label="Call"><i class='bx bxs-phone' aria-hidden="true"></i></a>
        <a class="mhero-icon" href="https://wa.me/94700000000" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><i class='bx bxl-whatsapp' aria-hidden="true"></i></a>
        <a class="mhero-icon" href="<?php echo URLROOT; ?>/public/?url=auth/login" aria-label="Login"><i class='bx bxs-lock-alt' aria-hidden="true"></i></a>
        <button type="button" class="menu-btn" id="menuBtn" aria-label="Open menu" aria-expanded="false" aria-controls="navLinks"><i class='bx bx-menu' aria-hidden="true"></i></button>
      </div>
    </div>
    <div class="nav-overlay" id="navOverlay" aria-hidden="true"></div>
  </header>

  <!-- Hero: Mediora-style full-viewport (reference layout) -->
  <div class="hero-wrap">
    <section class="hero mhero" aria-label="SmartCare">
      <div class="mhero-bg" aria-hidden="true">
        <div class="mhero-grid"></div>
      </div>

      <div class="mhero-stage">
        <div class="mhero-intro">
          <!-- Reveal: .animate + .show (hero timing in landing-scroll-reveal.js) -->
          <div class="mhero-ribbon animate" style="--reveal-y: 10px" aria-hidden="true">
            <span class="mhero-ribbon-cap mhero-ribbon-cap--tl"></span>
            <p class="mhero-ribbon-text">Professional care company · Island-wide</p>
            <span class="mhero-ribbon-cap mhero-ribbon-cap--br"></span>
          </div>

          <div class="mhero-copy animate">
            <h1 class="mhero-h1">
              <span class="mhero-h1-a">Care you can rely on</span>
              <span class="mhero-h1-b">Every day</span>
            </h1>
            <p class="mhero-lead">We deliver reliable elder care, babysitting, and home support — coordinated by our office, flexible plans, and help when you need it.</p>
            <div class="mhero-actions">
              <a class="mhero-btn mhero-btn--dark animate" href="<?php echo URLROOT; ?>/public/index.php?url=auth/login">Request care <i class='bx bx-right-arrow-alt' aria-hidden="true"></i></a>
              <a class="mhero-btn mhero-btn--ghost animate" href="#about">Learn more</a>
              <a class="mhero-btn mhero-btn--outline animate" href="<?php echo URLROOT; ?>/public/?url=auth/login"><i class='bx bxs-lock-alt' aria-hidden="true"></i> Login</a>
            </div>
          </div>
        </div>

        <figure class="mhero-figure animate-right">
          <img
            class="mhero-img"
            id="mheroSlideImg"
            src="<?php echo htmlspecialchars($mheroSlides[0]['src'], ENT_QUOTES, 'UTF-8'); ?>"
            alt="<?php echo htmlspecialchars($mheroSlides[0]['alt'], ENT_QUOTES, 'UTF-8'); ?>"
            width="800"
            height="900"
            fetchpriority="high"
            decoding="async">
        </figure>

        <aside class="mhero-spot animate-spot" aria-label="Service highlight">
          <div class="mhero-spot-visual">
            <i class='bx bx-heart-circle' aria-hidden="true"></i>
          </div>
          <div class="mhero-spot-copy">
            <strong>Family-first care</strong>
            <span>Speak with our office to arrange visits</span>
          </div>
        </aside>

        <div class="mhero-pager animate" style="--reveal-y: 10px" id="mheroPager" role="group" aria-label="Hero photo carousel">
          <span class="mhero-pager-num" aria-live="polite">
            <span id="mheroPagerCur">01</span> <span class="mhero-pager-sep">/</span> <span id="mheroPagerTot"><?php echo str_pad((string) count($mheroSlides), 2, '0', STR_PAD_LEFT); ?></span>
          </span>
          <div class="mhero-pager-track" aria-hidden="true"><span class="mhero-pager-fill" id="mheroPagerFill"></span></div>
        </div>
      </div>
    </section>

    <!-- Quick Stats Strip (data-scroll-reveal: staggered via JS) -->
    <div class="stats-strip" data-scroll-reveal data-reveal-stagger="92" aria-label="Highlights">
      <div class="stat animate"><b>Verified</b><span>Team vetted before we assign visits</span></div>
      <div class="stat animate"><b>Fast response</b><span>Our office replies quickly to requests</span></div>
      <div class="stat animate"><b>Flexible Plans</b><span>Hourly / Weekly / Monthly</span></div>
      <div class="stat animate"><b>Support</b><span>Help anytime you need</span></div>
    </div>
  </div>

  <main class="landing-main">

  <!-- ABOUT -->
  <section id="about" data-scroll-reveal data-reveal-stagger="98">
    <div class="container">
      <div class="section-head animate">
        <h2 class="section-title">About SmartCare</h2>
        <p class="section-sub">
          SmartCare is a care company: we coordinate elder care, babysitting, and home support through our office—not an open marketplace of independent profiles.
          Our mission is to make care simple and dependable with vetted staff, clear scheduling, and one accountable team.
          Tell us what you need; we match you with people who represent SmartCare and stay available for changes or questions.
        </p>
      </div>

      <div class="about-split">
        <figure class="about-visual animate-left">
          <img src="<?php echo URLROOT; ?>/public/images/about1.jpg" alt="Family receiving coordinated care from SmartCare — replace with your own photo if needed." width="720" height="540" loading="lazy" decoding="async">
        </figure>
        <div class="about-stack">
          <div class="feature-grid">
            <div class="feature animate">
              <i class='bx bxs-badge-check'></i>
              <div>
                <b>Vetted care team</b>
                <p>Recruitment, identity checks, and screening before anyone represents SmartCare on a visit.</p>
              </div>
            </div>
            <div class="feature animate">
              <i class='bx bx-calendar-check'></i>
              <div>
                <b>Office-led scheduling</b>
                <p>Tell us the service and times you need; we confirm the plan and assign staff from our team.</p>
              </div>
            </div>
            <div class="feature animate">
              <i class='bx bx-shield-quarter'></i>
              <div>
                <b>Safety &amp; standards</b>
                <p>Clear policies, supervision aligned to our service standards, and support if something needs attention.</p>
              </div>
            </div>
            <div class="feature animate">
              <i class='bx bx-headphone'></i>
              <div>
                <b>One place for answers</b>
                <p>Families reach our office for scheduling, billing, or visit changes—without juggling freelance contacts.</p>
              </div>
            </div>
          </div>

          <div class="panel animate">
            <b class="subhead">Need urgent help?</b>
            <p class="muted" style="margin-top:8px;">Contact us by phone, WhatsApp, or message—we’ll walk you through options and next steps from our office.</p>
            <div class="quick-contact">
              <a class="qc-btn" href="tel:+94700000000"><i class='bx bxs-phone'></i> Call Now</a>
              <a class="qc-btn" href="https://wa.me/94700000000" target="_blank"><i class='bx bxl-whatsapp'></i> WhatsApp</a>
              <a class="qc-btn" href="#contact"><i class='bx bxs-envelope'></i> Message</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- TRUST / SAFETY -->
  <section class="trust" id="trust" data-scroll-reveal data-reveal-stagger="98" aria-label="Trust and Safety">
    <div class="container">
      <h2 class="section-title animate">Trust & Safety</h2>
      <p class="section-sub animate">Caregiving needs trust. Here’s how SmartCare keeps families confident.</p>

      <div class="trust-cards">
        <div class="trust-card animate">
          <i class='bx bxs-id-card'></i>
          <b>ID verification</b>
          <p>We confirm identity and credentials before assigning anyone to represent SmartCare.</p>
        </div>
        <div class="trust-card animate">
          <i class='bx bxs-user-check'></i>
          <b>Role fit &amp; screening</b>
          <p>Experience, skills, and availability are reviewed for the services we provide—not public profile browsing.</p>
        </div>
        <div class="trust-card animate">
          <i class='bx bxs-star'></i>
          <b>Quality follow-up</b>
          <p>We check in on visits and service quality so standards stay consistent—not a star-rating marketplace.</p>
        </div>
        <div class="trust-card animate">
          <i class='bx bxs-lock'></i>
          <b>Secure process</b>
          <p>Clear bookings, records, and office support when you need documentation or help resolving an issue.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Placeholder gallery (horizontal strip + arrow nav) -->
  <div class="gallery-marquee" data-scroll-reveal data-reveal-stagger="82" aria-label="Care moments gallery">
    <div class="container">
    </div>
    <div class="gallery-strip">
      <button type="button" class="gallery-btn animate" id="galleryPrev" aria-label="Previous gallery photos">
        <i class="bx bx-chevron-left" aria-hidden="true"></i>
      </button>
      <div class="gallery-viewport animate" id="galleryViewport">
        <div class="gallery-track" id="galleryTrack" role="list">
          <img role="listitem" src="<?php echo URLROOT; ?>/public/images/sec3.webp" alt="Gallery placeholder 1" width="480" height="320" loading="lazy" decoding="async">
          <img role="listitem" src="<?php echo URLROOT; ?>/public/images/sec31.jpg" alt="Gallery placeholder 2" width="480" height="320" loading="lazy" decoding="async">
          <img role="listitem" src="<?php echo URLROOT; ?>/public/images/sec33.webp" alt="Gallery placeholder 3" width="480" height="320" loading="lazy" decoding="async">
          <img role="listitem" src="<?php echo URLROOT; ?>/public/images/sec34.jpg" alt="Gallery placeholder 4" width="480" height="320" loading="lazy" decoding="async">
          <img role="listitem" src="<?php echo URLROOT; ?>/public/images/sec35.jpg" alt="Gallery placeholder 5" width="480" height="320" loading="lazy" decoding="async">
          <img role="listitem" src="<?php echo URLROOT; ?>/public/images/sec36.webp" alt="Gallery placeholder 6" width="480" height="320" loading="lazy" decoding="async">
        </div>
      </div>
      <button type="button" class="gallery-btn animate" id="galleryNext" aria-label="Next gallery photos">
        <i class="bx bx-chevron-right" aria-hidden="true"></i>
      </button>
    </div>
  </div>

  <!-- IMPACT METRICS (values from DB via LandingModel) -->
  <?php
    $lm = isset($landingMetrics) && is_array($landingMetrics) ? $landingMetrics : [
      'care_hours_display' => '0',
      'families_display' => '0',
      'rating_display' => '—',
      'avg_rating' => null,
      'has_family_feedback' => false,
    ];
    $ratingTitle = '';
    if (($lm['rating_display'] ?? '—') !== '—' && empty($lm['has_family_feedback'])) {
      $ratingTitle = ' title="From vetted caregiver profile scores until client reviews are published"';
    }
    if (!empty($lm['has_family_feedback'])) {
      $ratingCaption = 'Average family rating';
    } elseif (($lm['rating_display'] ?? '—') !== '—') {
      $ratingCaption = 'Average team rating';
    } else {
      $ratingCaption = 'Average family rating';
    }
  ?>
  <div class="metrics-band" data-scroll-reveal data-reveal-stagger="110" aria-label="SmartCare at a glance">
    <div class="metrics-inner">
      <div class="metric animate"><strong><?php echo htmlspecialchars((string) ($lm['care_hours_display'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></strong><span>Care hours coordinated</span></div>
      <div class="metric animate"><strong><?php echo htmlspecialchars((string) ($lm['families_display'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?></strong><span>Families supported through our team</span></div>
      <div class="metric animate"><strong<?php echo $ratingTitle; ?>><?php echo htmlspecialchars((string) ($lm['rating_display'] ?? '—'), ENT_QUOTES, 'UTF-8'); ?></strong><span><?php echo htmlspecialchars($ratingCaption, ENT_QUOTES, 'UTF-8'); ?></span></div>
      <div class="metric animate"><strong>24/7</strong><span>Support when it matters</span></div>
    </div>
  </div>

  <!-- SERVICES -->
  <section id="services" data-scroll-reveal data-reveal-stagger="102">
    <div class="container">
      <h2 class="section-title animate">Our Services</h2>
      <p class="section-sub animate">Choose the support you need; we match you with vetted SmartCare staff and handle scheduling through our office.</p>

      <div class="cards">
        <article class="card animate">
          <div class="card-media">
            <img src="<?php echo URLROOT; ?>/public/images/elder-care.jpg" alt="Elder Care">
            <span class="tag">Most requested</span>
          </div>
          <div class="card-body">
            <h3>Elder Care</h3>
            <p>Daily assistance, companionship, medication reminders, and support tailored to your loved one—delivered by staff we assign and coordinate.</p>
            <div class="card-actions">
              <a class="link" href="#pricing"><i class='bx bx-right-arrow-alt'></i> View pricing</a>
              <a class="small-btn" href="#contact">Request</a>
            </div>
          </div>
        </article>

        <article class="card animate">
          <div class="card-media">
            <img src="<?php echo URLROOT; ?>/public/images/baby-sitting.webp" alt="Babysitting">
            <span class="tag">Flexible hours</span>
          </div>
          <div class="card-body">
            <h3>Babysitting</h3>
            <p>Safe, caring sitters from our team — hourly, weekly, or monthly options arranged by the office.</p>
            <div class="card-actions">
              <a class="link" href="#pricing"><i class='bx bx-right-arrow-alt'></i> View pricing</a>
              <a class="small-btn" href="#contact">Request</a>
            </div>
          </div>
        </article>

        <article class="card animate">
          <div class="card-media">
            <img src="<?php echo URLROOT; ?>/public/images/cleaning.webp" alt="Cleaning & Cooking">
            <span class="tag">Home support</span>
          </div>
          <div class="card-body">
            <h3>Cleaning & Cooking</h3>
            <p>Skilled home helpers for cleaning, cooking, and routine household support—assigned and supervised as SmartCare services.</p>
            <div class="card-actions">
              <a class="link" href="#pricing"><i class='bx bx-right-arrow-alt'></i> View pricing</a>
              <a class="small-btn" href="#contact">Request</a>
            </div>
          </div>
        </article>
      </div>
    </div>
  </section>

  <!-- COVERAGE -->
  <section class="areas-section" id="areas" data-scroll-reveal data-reveal-stagger="78" aria-label="Areas we serve">
    <div class="container">
      <div class="section-head animate">
        <h2 class="section-title">Coverage across Sri Lanka</h2>
        <p class="section-sub">We are expanding weekly. Families in major districts can request visits and flexible plans through our office. Outside these areas? Message us—we often confirm availability within 24–48 hours.</p>
      </div>
      <div class="area-tags" role="list">
        <span class="area-tag animate" role="listitem"><i class='bx bxs-map'></i> Colombo</span>
        <span class="area-tag animate" role="listitem"><i class='bx bxs-map'></i> Gampaha</span>
        <span class="area-tag animate" role="listitem"><i class='bx bxs-map'></i> Kalutara</span>
        <span class="area-tag animate" role="listitem"><i class='bx bxs-map'></i> Kandy</span>
        <span class="area-tag animate" role="listitem"><i class='bx bxs-map'></i> Galle</span>
        <span class="area-tag animate" role="listitem"><i class='bx bxs-map'></i> Matara</span>
        <span class="area-tag animate" role="listitem"><i class='bx bxs-map'></i> Jaffna</span>
        <span class="area-tag animate" role="listitem"><i class='bx bxs-map'></i> Kurunegala</span>
      </div>
      <div class="coverage-banner animate-right">
        <img src="<?php echo URLROOT; ?>/public/images/srilanka.jpg" alt="Placeholder: Sri Lanka coverage banner — replace with map or city skyline" width="1400" height="420" loading="lazy" decoding="async">
      </div>
      <p class="area-note animate">Need coverage in another city? Use <a href="#contact">Contact</a> or WhatsApp and we will confirm availability for your dates.</p>
    </div>
  </section>

  <!-- HOW IT WORKS -->
  <section aria-label="How SmartCare works" data-scroll-reveal data-reveal-stagger="118">
    <div class="container">
      <h2 class="section-title animate">How It Works</h2>
      <p class="section-sub animate">Three simple steps: tell us what you need, we assign your team member, then we support you through every visit.</p>

      <div class="steps">
        <div class="step animate">
          <div class="step-media">
            <!-- Unsplash (Centre for Ageing Better): older adults outdoors — https://unsplash.com/photos/lNTbuoYMSro -->
            <img src="<?php echo URLROOT; ?>/public/images/section51.webp" alt="Older adult couple walking together outdoors, representing families choosing care." width="560" height="360" loading="lazy" decoding="async">
          </div>
          <div class="step-body">
            <i class='bx bx-edit'></i>
            <div>
              <b>Choose a service</b>
              <p>Select elder care, babysitting, or home support based on your needs.</p>
            </div>
          </div>
        </div>
        <div class="step animate">
          <div class="step-media">
            <!-- Unsplash (Dulcey Lima): hands together — https://unsplash.com/photos/9MTqeBaAOlU -->
            <img src="<?php echo URLROOT; ?>/public/images/section5.jpg" alt="Close-up of two people’s hands together in a gentle, caring moment." width="560" height="360" loading="lazy" decoding="async">
          </div>
          <div class="step-body">
            <i class='bx bx-search-alt'></i>
            <div>
              <b>We assign your caregiver</b>
              <p>Our office matches you with vetted staff for your service, schedule, and preferences—no browsing freelance listings.</p>
            </div>
          </div>
        </div>
        <div class="step animate">
          <div class="step-media">
            <!-- Unsplash (Gert Stockmans): holding hands — https://unsplash.com/photos/n85taMiq0S4 -->
            <img src="<?php echo URLROOT; ?>/public/images/section52.webp" alt="Two people holding hands, suggesting trust, companionship, and ongoing support." width="560" height="360" loading="lazy" decoding="async">
          </div>
          <div class="step-body">
            <i class='bx bx-calendar-check'></i>
            <div>
              <b>Visits &amp; ongoing support</b>
              <p>We confirm visits with you and stay reachable for changes, feedback, or billing questions.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Our care team (company model — not a freelancer marketplace) -->
  <section id="our-team" class="trust" data-scroll-reveal data-reveal-stagger="104" aria-label="Our care team">
    <div class="container">
      <div class="section-head animate">
        <h2 class="section-title">Our care team &amp; standards</h2>
        <p class="section-sub">SmartCare operates as a care company. Caregivers and home-support staff work within our policies and schedules—we recruit, orient, and supervise the people who represent us. Families do not browse independent profiles or hire freelancers through this site.</p>
      </div>

      <div class="caregiver-split">
        <div class="caregiver-visual animate-left">
          <!-- Stock: Unsplash (healthcare / coordinated care). Swap for your own team photo when ready. -->
          <img src="<?php echo URLROOT; ?>/public/images/section4.webp" alt="Healthcare professional with a patient, illustrating vetted, company-coordinated care." width="640" height="800" loading="lazy" decoding="async">
        </div>
        <div class="caregiver-stack">
          <div class="caregiver-panel animate">
            <h3>How staffing works at SmartCare</h3>
            <p class="muted">Hiring and deployment go through our office. We match families with team members who meet our screening and training bar—then we stay involved for scheduling, quality, and billing questions.</p>
            <ul class="caregiver-list">
              <li><i class='bx bxs-check-shield'></i> Recruitment, background checks, and orientation aligned to SmartCare standards</li>
              <li><i class='bx bx-calendar'></i> Rosters and visits coordinated by our team—not open self-booking by individual contractors</li>
              <li><i class='bx bx-line-chart'></i> Supervision and feedback loops so service quality stays consistent visit to visit</li>
              <li><i class='bx bx-support'></i> One office for replacements, escalations, and payroll-related queries</li>
            </ul>
            <div class="caregiver-actions">
              <a class="btn btn-solid" href="#contact"><i class='bx bx-calendar-event'></i> Request care from our office</a>
              <a class="btn-outline-light" href="tel:+94700000000"><i class='bx bxs-phone'></i> Call us</a>
            </div>
          </div>
          <aside class="caregiver-aside animate-right" aria-label="For families">
            <b class="subhead subhead--sm">What families can expect</b>
            <p class="muted">Clear communication from coordinators, punctual visits, and caregivers who follow SmartCare protocols for children, elders, and home support. Share routines and preferences in one place—we document them so the team we send arrives prepared.</p>
            <p class="muted">We are not a directory of freelancers. Every assignment is backed by company standards and a single accountable contact when plans or needs change.</p>
          </aside>
        </div>
      </div>
    </div>
  </section>

  <section class="pricing" id="pricing" data-scroll-reveal data-reveal-stagger="88">
    <div class="container">
      <h2 class="section-title animate">Pricing Options</h2>
      <p class="section-sub animate">Choose a service to view sample packages—then contact our office to confirm what applies to you.</p>

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
          <button class="tab animate <?= $i === 0 ? 'active' : '' ?>" type="button" data-tab="<?= htmlspecialchars($service) ?>">
            <?= htmlspecialchars($service) ?>
          </button>
        <?php endforeach; ?>
      </div>

      <!-- Panels -->
      <?php foreach ($servicePriceRates as $serviceName => $packages): ?>
        <div class="pricing-grid <?= $serviceName === $services[0] ? '' : 'hide' ?>" id="tab-<?= htmlspecialchars($serviceName) ?>">
          <?php foreach ($packages as $plan => $price): ?>
            <div class="price-card animate">
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

              <a class="price-select" href="#contact">Request this package</a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endforeach; ?>

    </div>
  </section>

  <!-- TIPS / RESOURCES -->
  <section class="insights" id="resources" data-scroll-reveal data-reveal-stagger="110" aria-label="Care tips and resources">
    <div class="container">
      <div class="section-head animate">
        <h2 class="section-title">Practical care tips</h2>
        <p class="section-sub">Short guides families use before their first visit with SmartCare. Want a topic covered next? Tell us through <a href="#contact">Contact</a>.</p>
      </div>
      <div class="insight-grid">
        <article class="insight-card animate">
          <div class="insight-thumb">
            <img src="<?php echo URLROOT; ?>/public/images/section6.webp" alt="" width="640" height="360" loading="lazy" decoding="async">
          </div>
          <i class='bx bx-first-aid'></i>
          <h3>First visit with our team</h3>
          <p>Share routines, medications, allergies, and emergency contacts in writing. A short walkthrough with the staff we send helps everyone start confidently.</p>
          <a href="#faq">Read FAQ on requesting care <i class='bx bx-right-arrow-alt'></i></a>
        </article>
        <article class="insight-card animate">
          <div class="insight-thumb">
            <img src="<?php echo URLROOT; ?>/public/images/section61.webp" alt="" width="640" height="360" loading="lazy" decoding="async">
          </div>
          <i class='bx bx-shield-quarter'></i>
          <h3>Safety checklist for babysitting</h3>
          <p>Agree on pickup rules, screen time, snacks, and bedtime steps up front. Confirm our sitter knows your pediatrician and nearest hospital.</p>
          <a href="#trust">Trust &amp; safety <i class='bx bx-right-arrow-alt'></i></a>
        </article>
        <article class="insight-card animate">
          <div class="insight-thumb">
            <img src="<?php echo URLROOT; ?>/public/images/section62.webp" alt="" width="640" height="360" loading="lazy" decoding="async">
          </div>
          <i class='bx bx-home-circle'></i>
          <h3>Elder care handover notes</h3>
          <p>Track mobility aids, preferred meal times, and mood patterns. Small details help our team support dignity and independence day to day.</p>
          <a href="#services">Explore elder care <i class='bx bx-right-arrow-alt'></i></a>
        </article>
      </div>
    </div>
  </section>

  <!-- TESTIMONIALS -->
  <?php
  $landingTestimonials = (isset($landingTestimonials) && is_array($landingTestimonials)) ? $landingTestimonials : [];
  ?>
  <section class="testimonials" data-scroll-reveal data-reveal-stagger="122" aria-label="Testimonials">
    <div class="container">
      <h2 class="section-title animate">What Families Say</h2>
      <p class="section-sub animate">Real experiences from families who used SmartCare services.</p>

      <?php if (empty($landingTestimonials)): ?>
        <p class="t-empty animate">Client reviews will appear here after families submit feedback for completed visits.</p>
      <?php else: ?>
      <div class="t-grid">
        <?php foreach ($landingTestimonials as $t):
          $tName = trim((string) ($t['client_name'] ?? ''));
          $tLoc = trim((string) ($t['location'] ?? ''));
          $tQuote = trim((string) ($t['quote'] ?? ''));
          $tRating = (int) ($t['rating'] ?? 0);
          $tRating = max(1, min(5, $tRating));
          $tImg = (string) ($t['image_url'] ?? '');
          $starsFilled = str_repeat('★', $tRating);
          $starsEmpty = str_repeat('☆', 5 - $tRating);
          ?>
        <div class="t-card animate">
          <div class="t-head">
            <div class="t-user">
              <div class="avatar avatar-photo" aria-hidden="true">
                <img src="<?= htmlspecialchars($tImg, ENT_QUOTES, 'UTF-8'); ?>" alt="" width="40" height="40" loading="lazy" decoding="async">
              </div>
              <div>
                <b><?= htmlspecialchars($tName !== '' ? $tName : 'SmartCare client', ENT_QUOTES, 'UTF-8'); ?></b>
                <small><?= htmlspecialchars($tLoc !== '' ? $tLoc : 'Sri Lanka', ENT_QUOTES, 'UTF-8'); ?></small>
              </div>
            </div>
            <div class="stars" aria-label="<?= $tRating; ?> out of 5 stars"><?= $starsFilled . $starsEmpty; ?></div>
          </div>
          <p>“<?= htmlspecialchars($tQuote, ENT_QUOTES, 'UTF-8'); ?>”</p>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- FAQ -->
  <section id="faq" data-scroll-reveal data-reveal-stagger="76" aria-label="Frequently asked questions">
    <div class="container">
      <h2 class="section-title animate">Frequently Asked Questions</h2>
      <p class="section-sub animate">Quick answers to common questions.</p>

      <div class="faq-grid">
        <details class="animate">
          <summary>How do I request care?</summary>
          <p>Contact our office (form, phone, or WhatsApp), tell us the service and schedule you need, and we confirm a plan. We assign SmartCare staff—we do not run a public directory for families to hire freelancers independently.</p>
        </details>

        <details class="animate">
          <summary>How are staff vetted?</summary>
          <p>We run identity and reference checks, role-fit review, and orientation aligned to SmartCare standards before anyone is assigned to visits. Quality is maintained through supervision and follow-up—not open marketplace ratings.</p>
        </details>

        <details class="animate">
          <summary>Can I change the person after visits start?</summary>
          <p>Yes. Call or message the office if your assigned staff is unavailable or you need a different fit. We will offer options from our team where scheduling allows.</p>
        </details>

        <details class="animate">
          <summary>What payment options are available?</summary>
          <p>We support flexible packages such as hourly, weekly, and monthly plans depending on the service.</p>
        </details>

        <details class="animate">
          <summary>Is there a cancellation policy?</summary>
          <p>Yes. Cancellations or schedule changes are handled with prior notice according to the agreement our office confirms with you.</p>
        </details>

        <details class="animate">
          <summary>Do you cover urgent requests?</summary>
          <p>When staff are available, we try to support urgent needs. Phone or WhatsApp reaches our office fastest.</p>
        </details>

        <details class="animate">
          <summary>What happens if my assigned staff is sick?</summary>
          <p>Notify the office as soon as you can. We check availability for a replacement with similar skills and update you with options for the same window where possible.</p>
        </details>

        <details class="animate">
          <summary>Can we meet staff before the first visit?</summary>
          <p>Often yes. Many families like a short phone or video introduction arranged through our office. We coordinate consent and timing on both sides.</p>
        </details>

        <details class="animate">
          <summary>Do you offer corporate or agency packages?</summary>
          <p>For clinics, nursing homes, or employers sponsoring staff families, we can tailor volume pricing. Reach out through the contact form with your requirements.</p>
        </details>
      </div>
    </div>
  </section>

  <!-- CONTACT -->
  <section class="contact-wrap" id="contact" data-scroll-reveal data-reveal-stagger="114">
    <div class="container">
      <h2 class="section-title animate">Contact Us</h2>
      <p class="section-sub animate">Questions about visits, plans, or coverage? Message our office anytime.</p>

      <div class="contact-grid">
        <!-- Form -->
        <div class="panel animate">
          <b class="subhead">Send a Message</b>
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
        <div class="panel animate">
          <b class="subhead">Our Location</b>
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
  <section class="cta" data-scroll-reveal data-reveal-stagger="130" aria-label="Call to action">
    <div class="container">
      <div class="cta-card animate">
        <h2>Ready for coordinated care?</h2>
        <p>Tell SmartCare what you need—our office will confirm options, assign vetted staff, and stay with you for scheduling and support.</p>
        <div class="cta-actions">
          <a class="btn btn-solid" href="#contact"><i class='bx bx-calendar-event'></i> Request care</a>
          <a class="btn btn-ghost" href="tel:+94700000000"><i class='bx bxs-phone'></i> Call us</a>
        </div>
      </div>
    </div>
  </section>

  </main>

  <!-- Footer -->
  <footer data-scroll-reveal data-reveal-stagger="148">
    <div class="container">
      <div class="footer-grid">
        <div class="animate">
          <b style="font-size:18px;">© 2026 SmartCare</b>
          <p style="margin-top:6px; opacity:.95;">Company-coordinated care—simple, safe, and accountable.</p>
        </div>
        <ul class="footer-links animate-right">
          <li><a href="#about">About</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#areas">Coverage</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#our-team">Our team</a></li>
          <li><a href="#resources">Tips</a></li>
          <li><a href="#faq">FAQ</a></li>
          <li><a href="#contact">Contact</a></li>
          <li><a href="terms.php">Terms</a></li>
          <li><a href="privacy.php">Privacy</a></li>
          <li><a href="refund.php">Refund</a></li>
        </ul>
      </div>
    </div>
  </footer>

  <script>
    window.MHERO_SLIDES = <?php echo json_encode($mheroSlides, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

    // ===== Hero image carousel =====
    document.addEventListener("DOMContentLoaded", () => {
      const slides = Array.isArray(window.MHERO_SLIDES) ? window.MHERO_SLIDES : [];
      const img = document.getElementById("mheroSlideImg");
      const curEl = document.getElementById("mheroPagerCur");
      const totEl = document.getElementById("mheroPagerTot");
      const fill = document.getElementById("mheroPagerFill");
      const pager = document.getElementById("mheroPager");

      if (!slides.length || !img || !curEl || !totEl || !fill) return;

      const pad = (n) => String(n).padStart(2, "0");
      const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      /** Auto-advance hero photos (ms). */
      const MHERO_AUTO_MS = 6000;
      let cur = 0;
      let autoTimer = null;
      /** Cancels in-flight fade/swap if a newer slide was requested. */
      let mheroSwapGen = 0;
      let mheroSwapFailsafe = null;

      function stopMheroAuto() {
        if (autoTimer != null) {
          window.clearInterval(autoTimer);
          autoTimer = null;
        }
      }

      function startMheroAuto() {
        stopMheroAuto();
        if (slides.length < 2 || reduceMotion) {
          return;
        }
        autoTimer = window.setInterval(() => {
          applySlide(cur + 1);
        }, MHERO_AUTO_MS);
      }

      function applySlide(index, opts = {}) {
        const initial = Boolean(opts.initial);
        cur = (index + slides.length) % slides.length;
        const s = slides[cur];
        curEl.textContent = pad(cur + 1);
        totEl.textContent = pad(slides.length);
        fill.style.width = ((cur + 1) / slides.length) * 100 + "%";
        if (initial) return;

        const gen = ++mheroSwapGen;
        if (mheroSwapFailsafe != null) {
          window.clearTimeout(mheroSwapFailsafe);
          mheroSwapFailsafe = null;
        }

        const finishSwap = () => {
          if (gen !== mheroSwapGen) return;
          img.classList.remove("mhero-img--fade");
          if (mheroSwapFailsafe != null) {
            window.clearTimeout(mheroSwapFailsafe);
            mheroSwapFailsafe = null;
          }
        };

        if (reduceMotion) {
          img.src = s.src;
          img.alt = s.alt;
          return;
        }

        img.classList.add("mhero-img--fade");
        window.setTimeout(() => {
          if (gen !== mheroSwapGen) return;

          let settled = false;
          const onDone = () => {
            if (settled) return;
            settled = true;
            finishSwap();
          };

          img.addEventListener("load", onDone, { once: true });
          img.addEventListener("error", onDone, { once: true });

          img.alt = s.alt;
          img.src = s.src;

          // Cached images often fire `load` before our listener runs; `complete` covers that.
          if (img.complete && img.naturalWidth > 0) {
            window.queueMicrotask(onDone);
          }

          mheroSwapFailsafe = window.setTimeout(onDone, 10000);
        }, 160);
      }

      function mheroNav(delta) {
        applySlide(cur + delta);
        startMheroAuto();
      }

      const mheroPrev = document.getElementById("mheroPagerPrev");
      const mheroNext = document.getElementById("mheroPagerNext");
      mheroPrev?.addEventListener("click", () => mheroNav(-1));
      mheroNext?.addEventListener("click", () => mheroNav(1));
      pager?.addEventListener("keydown", (e) => {
        if (e.key === "ArrowLeft") {
          e.preventDefault();
          mheroNav(-1);
        }
        if (e.key === "ArrowRight") {
          e.preventDefault();
          mheroNav(1);
        }
      });

      document.addEventListener("visibilitychange", () => {
        if (document.hidden) {
          stopMheroAuto();
        } else {
          startMheroAuto();
        }
      });

      const stage = document.querySelector(".mhero-stage");
      if (stage) {
        stage.addEventListener("mouseenter", stopMheroAuto);
        stage.addEventListener("mouseleave", startMheroAuto);
      }

      applySlide(0, { initial: true });
      startMheroAuto();
    });

    // ===== Gallery strip (arrow nav, scrollbar hidden) =====
    document.addEventListener("DOMContentLoaded", () => {
      const viewport = document.getElementById("galleryViewport");
      const prev = document.getElementById("galleryPrev");
      const next = document.getElementById("galleryNext");
      if (!viewport || !prev || !next) return;

      const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

      function scrollStep() {
        const first = viewport.querySelector(".gallery-track img");
        const w = first ? first.getBoundingClientRect().width : 280;
        const gap = 14;
        return Math.min(viewport.clientWidth * 0.85, w + gap);
      }

      function syncGalleryArrows() {
        const max = viewport.scrollWidth - viewport.clientWidth;
        if (max <= 2) {
          prev.disabled = true;
          next.disabled = true;
          return;
        }
        prev.disabled = viewport.scrollLeft <= 2;
        next.disabled = viewport.scrollLeft >= max - 2;
      }

      prev.addEventListener("click", () => {
        viewport.scrollBy({ left: -scrollStep(), behavior: reduceMotion ? "auto" : "smooth" });
      });
      next.addEventListener("click", () => {
        viewport.scrollBy({ left: scrollStep(), behavior: reduceMotion ? "auto" : "smooth" });
      });
      viewport.addEventListener("scroll", syncGalleryArrows, { passive: true });
      window.addEventListener("resize", syncGalleryArrows, { passive: true });
      syncGalleryArrows();
    });

    // ===== Mobile navigation =====
    document.addEventListener("DOMContentLoaded", () => {
      const menuBtn = document.getElementById("menuBtn");
      const overlay = document.getElementById("navOverlay");
      const icon = menuBtn ? menuBtn.querySelector("i") : null;

      function setNavOpen(open) {
        document.body.classList.toggle("nav-open", open);
        if (overlay) overlay.setAttribute("aria-hidden", open ? "false" : "true");
        if (menuBtn) menuBtn.setAttribute("aria-expanded", open ? "true" : "false");
        if (menuBtn) menuBtn.setAttribute("aria-label", open ? "Close menu" : "Open menu");
        if (icon) icon.className = open ? "bx bx-x" : "bx bx-menu";
      }

      menuBtn?.addEventListener("click", () => setNavOpen(!document.body.classList.contains("nav-open")));
      overlay?.addEventListener("click", () => setNavOpen(false));
      document.querySelectorAll("#navLinks a").forEach((a) => a.addEventListener("click", () => setNavOpen(false)));
      window.addEventListener("keydown", (e) => {
        if (e.key === "Escape") setNavOpen(false);
      });
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
    });

    /**
     * Scroll reveal + hero load animations (Intersection Observer, no GSAP).
     * CSS: public/css/landing.css — .animate, .animate-left, .animate-right, .animate-spot + .show
     * Mark scroll groups with data-scroll-reveal; children use the classes above (DOM order = stagger).
     * Optional: data-reveal-stagger on that root (ms between items; clamped ~55–220 in JS).
     * Repeats on every enter/leave: leaving viewport removes .show (staggered reverse) so scrolling
     * back in runs the entrance again. Timers are cleared on direction changes to avoid races.
     */
    document.addEventListener("DOMContentLoaded", () => {
      const reduceMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

      function markShown(list) {
        list.forEach((el) => el.classList.add("show"));
      }

      const stage = document.querySelector(".mhero-stage");
      if (stage) {
        if (reduceMotion) {
          markShown(stage.querySelectorAll(".animate, .animate-left, .animate-right, .animate-spot"));
        } else {
          const q = (sel) => stage.querySelector(sel);
          const t = (fn, ms) => window.setTimeout(fn, ms);
          t(() => q(".mhero-ribbon")?.classList.add("show"), 90);
          t(() => q(".mhero-copy")?.classList.add("show"), 220);
          t(() => q(".mhero-figure")?.classList.add("show"), 280);
          t(() => q(".mhero-actions .mhero-btn:nth-child(1)")?.classList.add("show"), 400);
          t(() => q(".mhero-actions .mhero-btn:nth-child(2)")?.classList.add("show"), 540);
          t(() => q(".mhero-spot")?.classList.add("show"), 460);
          t(() => q(".mhero-pager")?.classList.add("show"), 640);
        }
      }

      const scrollRoots = document.querySelectorAll("[data-scroll-reveal]");
      const pendingByRoot = new WeakMap();

      function parseStaggerMs(root) {
        const raw = root.getAttribute("data-reveal-stagger");
        return Math.max(55, Math.min(220, Number.parseInt(raw, 10) || 105));
      }

      function clearRevealTimers(root) {
        const ids = pendingByRoot.get(root);
        if (ids && ids.length) ids.forEach((id) => window.clearTimeout(id));
        pendingByRoot.delete(root);
      }

      function revealItems(root) {
        return Array.from(
          root.querySelectorAll(".animate, .animate-left, .animate-right, .animate-spot")
        );
      }

      const io = new IntersectionObserver(
        (entries) => {
          entries.forEach((entry) => {
            const root = entry.target;
            const items = revealItems(root);
            const staggerMs = parseStaggerMs(root);

            if (reduceMotion) {
              if (entry.isIntersecting) markShown(items);
              return;
            }

            clearRevealTimers(root);

            if (!entry.isIntersecting) {
              const n = items.length;
              const ids = [];
              items.forEach((el, i) => {
                const delay = staggerMs * (n - 1 - i);
                ids.push(window.setTimeout(() => el.classList.remove("show"), delay));
              });
              pendingByRoot.set(root, ids);
              return;
            }

            const ids = [];
            items.forEach((el, i) => {
              ids.push(window.setTimeout(() => el.classList.add("show"), staggerMs * i));
            });
            pendingByRoot.set(root, ids);
          });
        },
        { root: null, rootMargin: "0px 0px -10% 0px", threshold: 0.06 }
      );
      scrollRoots.forEach((el) => io.observe(el));
    });
  </script>

</body>

</html>