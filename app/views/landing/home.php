<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SmartCare</title>
  <link rel="stylesheet" href="/CMA/public/css/landing.css?v=1.1">
   <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
  
</style>
</head>
<body>
  <!-- Navbar -->
  <header>
    <nav class="navbar">
       
       <div class="logo">
        <img src="<?php echo URLROOT; ?>/public/images/logo.jpg" alt="SmartCare Logo">
       SmartCare</div>
      <ul class="nav-links">
        <li><a href="#about">About</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#contact">Contact</a></li>
        <li><a href="http://localhost/CMA/public/?url=auth/login" class="btn-login">Login</a></li>
      </ul>
    </nav>
  </header>

  <!-- Hero Section -->
<section class="hero">
  <div class="hero-slider">
    <img src="../public/images/hero.png" class="slide active" alt="Elder Care">
    <img src="../public/images/baby1.webp" class="slide" alt="Babysitting">
    <img src="../public/images/cooking.avif" class="slide" alt="Home Support">

    <div class="hero-text">
      <h1>Your Partner in Caregiving</h1>
      <p>Reliable caretakers for your home, loved ones, and daily needs.</p>
    </div>
    
    <div class="nav-arrows">
    <span class="prev">&#10094;</span>
    <span class="next">&#10095;</span>
    </div>
    <div class="dots"></div>


  </div>
</section>


  <!-- About -->
  <section class="about" id="about">
    <h2>About SmartCare</h2>
    <p>
      SmartCare is a digital platform that connects families with trusted caregivers for elder care, babysitting, and home support. 
      Our mission is to make caregiving simple, safe, and reliable by offering background-checked caretakers, easy booking, and 
      24/7 support — all in one place.
    </p>
    <div class="features">
      <div class="feature">✅ Verified Caretakers</div>
      <div class="feature">⚡ Easy Booking System</div>
      <div class="feature">📞 24/7 Customer Support</div>
      <div class="feature">💰 Affordable Packages</div>
    </div>
  </section>

  <!-- Services -->
  <section class="services" id="services">
    <h2>Our Services</h2>
    <div class="service-cards">
      <div class="card">
        <img src="../public/images/elder-care.jpg" alt="Elder Care">
        <h3>Elder Care</h3>
        <p>Compassionate and reliable caregivers to support your loved ones with daily tasks, medical needs, and companionship.</p>
      </div>
      <div class="card">
        <img src="../public/images/baby-sitting.webp" alt="Babysitting">
        <h3>Babysitting</h3>
        <p>Trusted babysitters to ensure your child’s safety, comfort, and care while you’re away.</p>
      </div>
      <div class="card">
        <img src="../public/images/cleaning.webp" alt="Cleaning & Cooking">
        <h3>Cleaning & Cooking</h3>
        <p>Skilled maids to keep your home clean and meals prepared for a stress-free living environment.</p>
      </div>
    </div>
  </section>

  <!-- How it Works -->
  <section class="how-it-works">
    <h2>How It Works</h2>
    <div class="steps">
      <div class="step">📝 Sign Up</div>
      <div class="step">📌 Choose a Service</div>
      <div class="step">🏡 Relax and Track</div>
    </div>
  </section>

  <!-- Pricing -->
  <section class="pricing">
    <h2>Pricing Options</h2>
    <h3>For Baby Care</h3>
    <div class="pricing-cards">
      <div class="price-card">
        <h4>Daily</h4>
        <p class="price">LKR 6000 <span>per hour</span></p>
        <ul>
          <li style="list-style:none">✔ Flexible scheduling</li>
          <li style="list-style:none">✔ Pay as you go</li>
        </ul>
        <button class="select">Select</button>
      </div>
      <div class="price-card">
        <h4>Weekly</h4>
        <p class="price">LKR 90,000 <span>per week</span></p>
        <ul>
          <li style="list-style:none">✔ Consistent care</li>
          <li style="list-style:none">✔ Cost-effective</li>
        </ul>
        <button class="select">Select</button>
      </div>
      <div class="price-card">
        <h4>Monthly</h4>
        <p class="price">LKR 250,000 <span>per month</span></p>
        <ul>
          <li style="list-style:none">✔ Long-term care</li>
          <li style="list-style:none">✔ Best value</li>
        </ul>
        <button class="select">Select</button>
      </div>
    </div>

    
  </section>

  <!-- Testimonials -->
  <section class="testimonials">
    <h2>Testimonials</h2>
    <div class="testimonial">
      <p><strong>Olivia Bennett</strong> ⭐⭐⭐⭐⭐</p>
      <p>"I’m so grateful for the wonderful elder care service provided by CareConnect. Highly recommend!"</p>
    </div>
    <div class="testimonial">
      <p><strong>Liam Harper</strong> ⭐⭐⭐⭐</p>
      <p>"The babysitter was fantastic! Our kids loved her, and we felt completely at ease leaving them in her care."</p>
    </div>
  </section>

  <!-- FAQ -->
  <section class="faq">
    <h2>Frequently Asked Questions</h2>
    <details>
      <summary>How do I book a caregiver?</summary>
      <p>Simply sign up, select your service, browse caregivers, and book directly.</p>
    </details>
    <details>
      <summary>What are the payment options?</summary>
      <p>We support flexible payment options including hourly, weekly, and monthly packages.</p>
    </details>
    <details>
      <summary>Is there a cancellation policy?</summary>
      <p>Yes, you can cancel anytime with prior notice as per our terms.</p>
    </details>
  </section>

 <section class="contact" id="contact">
  <h2>Contact Us</h2>
  <p>Have questions or need support? Reach out to us anytime!</p>
  
  <div class="contact-container">
    <!-- Contact Form -->
    <form class="contact-form" action="#" method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <textarea name="message" rows="5" placeholder="Your Message" required></textarea>
      <button type="submit" class="btn-start">Send Message</button>
    </form>

    <!-- Map & Social -->
    <div class="contact-info">
      <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3960.921306041527!2d79.85392287930699!3d6.900014875306098!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3ae2596309dfdd3f%3A0x45a4b0e7834ac0d4!2sUniversity%20of%20Colombo!5e0!3m2!1sen!2slk!4v1757388167234!5m2!1sen!2slk" width="400" height="300" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      <div class="social-icons">
        <a href="#"><i class='bx bxl-facebook-square'></i></a>
        <a href="#"><i class='bx bxl-twitter' ></i></a>
        <a href="#"><i class='bx bxl-instagram' ></i></a>
      </div>
    </div>
  </div>
</section>

  <!-- CTA -->
  <section class="cta">
    <h2>Are You Ready?</h2>
    <button class="btn-start"><a href="/CMA/public/?url=auth/login" class="login_button">Get Started</a></button>
  </section>

  <!-- Footer -->
  <footer>
    <p>© 2025 SmartCare. All rights reserved.</p>
    <ul class="footer-links">
      <li><a href="#about">About</a></li>
      <li><a href="#services">Services</a></li>
      <li><a href="#">Terms</a></li>
      <li><a href="#">Privacy</a></li>
    </ul>
  </footer>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
  let slides = document.querySelectorAll(".hero-slider img");
  let current = 0;

  function changeSlide() {
    slides[current].classList.remove("active");
    current = (current + 1) % slides.length;
    slides[current].classList.add("active");
  }

  setInterval(changeSlide, 5000);
});


  document.querySelectorAll(".select").forEach(btn => {
    btn.addEventListener("click", () => {
      window.location.href = "/CMA/app/views/auth/login.php";
    });
  });

</script>
</body>
</html>
