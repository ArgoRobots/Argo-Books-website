<?php require_once __DIR__ . '/../../resources/icons.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="">
  <meta name="keywords" content="">
  <meta name="author" content="Argo">

  <!-- Open Graph Meta Tags -->
  <meta property="og:title" content="Argo Books - Message Sent Successfully">
  <meta property="og:description" content="Your message has been sent successfully to the Argo Books support team.">
  <meta property="og:type" content="website">
  <meta property="og:site_name" content="Argo Books">
  <meta property="og:image" content="https://ogimage.io/templates/brand?title=Argo+Books&subtitle=Simple%2C+modern+accounting+software+built+for+small+businesses+%E2%80%94+with+automation+that+saves+time+and+keeps+your+finances+organized&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">
  <meta property="og:image:width" content="1200">
  <meta property="og:image:height" content="630">

  <!-- Twitter Meta Tags -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="Argo Books - Message Sent Successfully">
  <meta name="twitter:description" content="Your message has been sent successfully to the Argo Books support team.">
  <meta name="twitter:image" content="https://ogimage.io/templates/brand?title=Argo+Books&subtitle=Simple%2C+modern+accounting+software+built+for+small+businesses+%E2%80%94+with+automation+that+saves+time+and+keeps+your+finances+organized&logo=https%3A%2F%2Fargorobots.com%2Fresources%2Fimages%2Fargo-logo%2Fargo-icon.ico">

  <link rel="shortcut icon" type="image/x-icon" href="../../resources/images/argo-logo/argo-icon.ico">
  <title>Argo Books - Message Sent Successfully</title>

  <script src="../../resources/scripts/jquery-3.6.0.js"></script>
  <script src="../../resources/scripts/main.js"></script>

  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="../../resources/styles/custom-colors.css">
  <link rel="stylesheet" href="../../resources/header/style.css">
  <link rel="stylesheet" href="../../resources/header/dark.css">
  <link rel="stylesheet" href="../../resources/footer/style.css">
</head>

<body>
  <header>
    <div id="includeHeader"></div>
  </header>
  <main>

  <section class="first">
    <div class="success-container">
      <div class="success-icon">
        <?= svg_icon('circle-check', null, '', null, 'stroke-linecap="round" stroke-linejoin="round"') ?>
      </div>

      <div class="success-content">
        <h1>Message Sent Successfully!</h1>
        <p class="success-message">Thank you for reaching out. Our team will review your message and get back to you as
          soon as possible.</p>

        <div class="info-box">
          <h3>What happens next?</h3>
          <ul>
            <li>Your message has been delivered to our support team</li>
            <li>We typically respond within 1-8 business hours</li>
          </ul>
        </div>

        <div class="action-buttons">
          <a href="/" class="btn primary-btn">Return to Home</a>
          <a href="../" class="btn secondary-btn">Back to Contact</a>
        </div>
      </div>
    </div>
  </section>

  </main>

  <footer class="footer">
    <div id="includeFooter"></div>
  </footer>
</body>

</html>