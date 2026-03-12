<?php
session_start();
require_once 'contact_process.php';

require_once __DIR__ . '/../resources/icons.php';

// Generate CSRF token if not present
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error_message = '';
$form_data = ['firstName' => '', 'lastName' => '', 'email' => '', 'subject' => 'general', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Verify CSRF token
  if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
      $error_message = 'Invalid request. Please refresh the page and try again.';
  } else {
      $result = process_contact_form();
      if ($result['success']) {
    header('Location: message-sent-successfully/index.php');
    exit;
  }
      $error_message = $result['message'];
      $form_data = $result['form_data'];
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="author" content="Argo">

  <!-- SEO Meta Tags -->
  <meta name="description"
    content="Contact Argo Books support team in Saskatoon, Canada. Get help with finance management software, report bugs, request features, or ask questions. Fast response times within 1-8 business hours.">
  <meta name="keywords"
    content="contact argo books, customer support saskatoon, business software help, finance management, sales tracker support, contact form, technical support, saskatoon software company contact, customer service">

  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="Contact Us - Argo Books Support">
  <meta property="og:description"
    content="Contact Argo Books support team in Saskatoon, Canada. Get help with finance management software, report bugs, request features, or ask questions. Fast response times within 1-8 business hours.">
  <meta property="og:url" content="https://argorobots.com/contact-us/">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Argo Books">
  <meta property="og:locale" content="en_CA">

  <!-- Twitter Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Contact Us - Argo Books Support">
  <meta name="twitter:description"
    content="Contact Argo Books support team in Saskatoon, Canada. Get help with finance management software, report bugs, request features, or ask questions. Fast response times within 1-8 business hours.">

  <!-- Additional SEO Meta Tags -->
  <meta name="geo.region" content="CA-SK">
  <meta name="geo.placename" content="Saskatoon">
  <meta name="geo.position" content="52.1579;-106.6702">
  <meta name="ICBM" content="52.1579, -106.6702">

  <!-- Canonical URL -->
  <link rel="canonical" href="https://argorobots.com/contact-us/">

  <link rel="shortcut icon" type="image/x-icon" href="../resources/images/argo-logo/argo-icon.ico">
  <title>Contact Us - Argo Books Support | Saskatoon Software Company</title>

  <script src="../resources/scripts/jquery-3.6.0.js"></script>
  <script src="../resources/scripts/main.js"></script>

  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="../resources/styles/custom-colors.css">
  <link rel="stylesheet" href="../resources/header/style.css">
  <link rel="stylesheet" href="../resources/footer/style.css">
</head>

<body>
  <header>
    <div id="includeHeader"></div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-bg">
      <div class="hero-gradient-orb hero-orb-1"></div>
      <div class="hero-gradient-orb hero-orb-2"></div>
    </div>
    <div class="container">
      <h1 class="animate-fade-in">Get in Touch</h1>
      <p class="hero-subtitle animate-fade-in">Have a question or need help? We're here for you.</p>
    </div>
  </section>

  <!-- Contact Options -->
  <section class="contact-options">
    <div class="container">
      <div class="options-grid">
        <div class="option-card animate-on-scroll">
          <div class="option-icon">
            <?= svg_icon('mail', null, '', 1.5) ?>
          </div>
          <h3>Email Support</h3>
          <p>Get help with technical issues, account questions, or general inquiries.</p>
          <a href="mailto:support@argorobots.com" class="option-link">
            support@argorobots.com
            <?= svg_icon('arrow-right', 16) ?>
          </a>
          <span class="response-time">
            <?= svg_icon('clock', 14) ?>
            1-8 business hours
          </span>
        </div>

        <div class="option-card animate-on-scroll">
          <div class="option-icon feedback">
            <?= svg_icon('chat', null, '', 1.5) ?>
          </div>
          <h3>Send Feedback</h3>
          <p>Share ideas, feature requests, or suggestions to help us improve Argo Books.</p>
          <a href="mailto:feedback@argorobots.com" class="option-link">
            feedback@argorobots.com
            <?= svg_icon('arrow-right', 16) ?>
          </a>
          <span class="response-time">
            <?= svg_icon('thumbs-up', 14) ?>
            We read every message
          </span>
        </div>

        <div class="option-card animate-on-scroll">
          <div class="option-icon community">
            <?= svg_icon('users', null, '', 1.5) ?>
          </div>
          <h3>Community Forum</h3>
          <p>Connect with other users, share tips, and get help from the community.</p>
          <a href="../community/" class="option-link">
            Visit Community
            <?= svg_icon('arrow-right', 16) ?>
          </a>
          <span class="response-time">
            <?= svg_icon('user', 14) ?>
            Active community
          </span>
        </div>
      </div>
    </div>
  </section>

  <!-- Contact Form Section -->
  <section class="contact-form-section" id="contact">
    <div class="container">
      <div class="form-wrapper animate-on-scroll">
        <div class="form-header">
          <h2>Send us a Message</h2>
          <p>Fill out the form below and we'll get back to you as soon as possible.</p>
        </div>

        <?php if (!empty($error_message)): ?>
        <div class="error-message">
          <?= svg_icon('alert-circle', 20) ?>
          <?php echo htmlspecialchars($error_message); ?>
        </div>
        <?php endif; ?>

        <form action="#contact" method="POST" id="contact-form" autocomplete="off">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
          <div class="form-row">
            <div class="form-group">
              <label for="firstName">First Name</label>
              <input type="text" id="firstName" name="firstName" maxlength="35" placeholder="John" value="<?php echo htmlspecialchars($form_data['firstName']); ?>" required>
            </div>
            <div class="form-group">
              <label for="lastName">Last Name</label>
              <input type="text" id="lastName" name="lastName" maxlength="35" placeholder="Doe" value="<?php echo htmlspecialchars($form_data['lastName']); ?>" required>
            </div>
          </div>

          <div class="form-group">
            <label for="email">Email Address</label>
            <input type="text" id="email" name="email" placeholder="john@example.com" value="<?php echo htmlspecialchars($form_data['email']); ?>" pattern=".+@.+\..+" title="Please enter a valid email address (e.g. john@example.com)" required>
          </div>

          <div class="form-group">
            <label for="subject">Subject</label>
            <select id="subject" name="subject">
              <option value="general" <?php echo $form_data['subject'] === 'general' ? 'selected' : ''; ?>>General Inquiry</option>
              <option value="support" <?php echo $form_data['subject'] === 'support' ? 'selected' : ''; ?>>Technical Support</option>
              <option value="billing" <?php echo $form_data['subject'] === 'billing' ? 'selected' : ''; ?>>Billing Question</option>
              <option value="feature" <?php echo $form_data['subject'] === 'feature' ? 'selected' : ''; ?>>Feature Request</option>
              <option value="bug" <?php echo $form_data['subject'] === 'bug' ? 'selected' : ''; ?>>Bug Report</option>
              <option value="other" <?php echo $form_data['subject'] === 'other' ? 'selected' : ''; ?>>Other</option>
            </select>
          </div>

          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" maxlength="3000" rows="6" placeholder="How can we help you?" required><?php echo htmlspecialchars($form_data['message']); ?></textarea>
          </div>

          <button type="submit" class="submit-btn">
            <span>Send Message</span>
            <?= svg_icon('send', 20) ?>
          </button>
        </form>
      </div>

      <div class="form-sidebar animate-on-scroll">
        <div class="sidebar-card">
          <div class="sidebar-icon">
            <?= svg_icon('help-circle', null, '', 1.5) ?>
          </div>
          <h4>Check the Docs First</h4>
          <p>Many common questions are already answered in our documentation.</p>
          <a href="../documentation/" class="sidebar-link">View Documentation</a>
        </div>

        <div class="sidebar-card">
          <div class="sidebar-icon">
            <?= svg_icon('map-pin', null, '', 1.5) ?>
          </div>
          <h4>Based in Canada</h4>
          <p>We're a Canadian company proudly serving businesses worldwide.</p>
          <span class="location-badge">
            <?= svg_icon('map-pin', 14) ?>
            Saskatoon, SK, Canada
          </span>
        </div>
      </div>
    </div>
  </section>

  <footer class="footer">
    <div id="includeFooter"></div>
  </footer>

  <script>
    // Scroll animations
    document.addEventListener('DOMContentLoaded', function() {
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.classList.add('animate-visible');
          }
        });
      }, observerOptions);

      document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
      });

    });
  </script>
</body>

</html>
