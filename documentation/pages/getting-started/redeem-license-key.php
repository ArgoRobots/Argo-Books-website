<?php
require_once __DIR__ . '/../../../resources/icons.php';
$pageTitle = 'Redeem a License Key';
$pageDescription = 'How to activate Argo Books Premium with your license key, whether you subscribed on argorobots.com or bought a key from a retailer. Includes finding your key, moving it to another computer, and fixing activation problems.';
$currentPage = 'redeem-license-key';
$pageCategory = 'getting-started';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Premium is unlocked with a license key that you enter inside Argo Books. This page covers where to find your key and how to activate it, whether you subscribed here on argorobots.com or bought a key somewhere else.</p>

            <h2>Before you start</h2>
            <ul>
                <li><strong>Install Argo Books first.</strong> Keys are entered inside the app, not on this website. See the <a class="link" href="installation.php">Installation Guide</a> if you have not installed it yet.</li>
                <li><strong>You need an internet connection to activate.</strong> Argo Books checks the key with our server the moment you enter it.</li>
                <li><strong>You do not need an account on this website.</strong> A key activates the copy of Argo Books already on your computer. Subscribers do have an account here, because that is where the subscription is managed, but the app itself never asks you to sign in.</li>
            </ul>

            <h2>Finding your license key</h2>

            <h3 id="subscribed-here">If you subscribed on argorobots.com</h3>
            <p>Your license key is in the payment receipt we emailed you when the subscription started. It is at the bottom of that email, labelled <strong>License Key</strong>.</p>
            <p>If you cannot find the email, sign in and open your <a class="link" href="../../../community/users/subscription.php">subscription page</a>, then use <strong>Send to Email</strong> next to License Key. We will send it to the address on your account. The key is not shown on the page itself, so that a shared screen or a browser someone else is signed into never exposes it.</p>

            <h3 id="bought-elsewhere">If you bought a key from a retailer, or were given a key</h3>
            <p>Your key comes from wherever you bought it, usually in the order confirmation or on the order page of that retailer's website. We do not have a copy, because the purchase did not go through us. If you cannot find it, the retailer is the right place to ask.</p>

            <h2>Activating your key</h2>
            <ol class="steps-list">
                <li>Open Argo Books.</li>
                <li>Click the blue upgrade button in the top right of the window, the circle with an upward arrow. It disappears once Premium is active.</li>
                <li>At the bottom of the plans window, click <strong>Enter a Key</strong>.</li>
                <li>Paste your key into the <strong>License Key</strong> box.</li>
                <li>Click <strong>Verify Key</strong>.</li>
            </ol>

            <p>You will see <strong>License Activated!</strong> and your Premium features unlock straight away.</p>

            <h2>Moving Argo Books to another computer</h2>
            <p>A license key is active on one computer at a time, and you can move it as often as you like.</p>

            <ol class="steps-list">
                <li>Install Argo Books on the new computer.</li>
                <li>Enter the same license key there, following the steps above.</li>
                <li>Premium activates on the new computer and stops working on the old one.</li>
            </ol>

            <p>There is no limit on transfers and no waiting period, so you can move back again whenever you need to. If you open Argo Books on the computer you moved away from, it will tell you the key is active on a different device. That is expected, and entering the key again on that computer brings it back.</p>

            <p>The same applies after reinstalling Argo Books. Enter the key again and Premium returns.</p>

            <h2>How long your key lasts</h2>
            <p>What happens after you activate a key depends on where it came from.</p>

            <ul>
                <li><strong>A subscription bought here.</strong> Your key stays valid for as long as the subscription is active. Renewals are handled automatically and your key does not change, so there is nothing to re-enter each month or year. If the subscription ends, Argo Books returns to the free plan.</li>
                <li><strong>A lifetime key.</strong> Premium stays active permanently, with no renewals and nothing to pay again.</li>
            </ul>

            <p>Returning to the free plan never affects your data. Your company files, history and records stay exactly as they are, and the free plan's monthly limits simply apply again. See <a class="link" href="version-comparison.php">Free vs. Paid Version</a> for what changes.</p>

            <h2>Troubleshooting</h2>

            <h3 id="invalid-key">"Invalid license key."</h3>
            <p>The key was not recognised. This is almost always a typing problem rather than a problem with the key itself.</p>
            <ul>
                <li><strong>Copy and paste rather than retyping.</strong> Retyping by eye is where most failed activations come from.</li>
                <li><strong>Check you copied the whole key.</strong> It is 20 characters, and a partial selection is a common cause.</li>
                <li><strong>Check you are using the right value.</strong> A license key always begins with <code>PREM</code>. A transaction ID or order number from a receipt is not a license key.</li>
            </ul>

            <h3 id="wrong-device">"This license key is active on a different device."</h3>
            <p>The key is valid, it is just currently in use on another computer. Enter it again on the computer you want to use it on and it will move across. See <a class="link" href="#moving-argo-books-to-another-computer">Moving Argo Books to another computer</a> above.</p>

            <h3 id="expired">"This license key's subscription has expired."</h3>
            <p>For a subscription bought here, this means the subscription is no longer active. Check your <a class="link" href="../../../community/users/subscription.php">subscription page</a>, where you can restart it. Lifetime keys do not expire.</p>

            <h3 id="no-connection">"No internet connection" or "Unable to reach Argo Books servers"</h3>
            <p>Activation has to reach our server, and Argo Books tells you which half of that failed.</p>
            <ul>
                <li><strong>"No internet connection. Please check your network and try again."</strong> Your computer could not reach the internet at all. Reconnect and try again.</li>
                <li><strong>"Unable to reach Argo Books servers."</strong> Your connection is working but our server did not answer. This is usually temporary, so wait a few minutes and try again. If you are behind a corporate firewall or connected through a VPN, that can also block it, so it is worth trying on an ordinary connection.</li>
            </ul>
            <p>Neither one affects your key. It is untouched and will still work once you can connect.</p>

            <h3 id="too-many-attempts">"Too many attempts. Please try again in a few minutes."</h3>
            <p>Activation is rate limited to slow down anyone guessing at keys. Wait a few minutes and try again, and paste the key rather than retyping it so you do not spend attempts on typos.</p>

            <h2>Getting help</h2>
            <p>If your key still will not activate, email <a class="link" href="mailto:contact@argorobots.com">contact@argorobots.com</a> with the key and where you got it, and we will sort it out. Do not post your key publicly, on a review page or anywhere else, because anyone who has it can redeem it.</p>

            <div class="page-navigation">
                <a href="version-comparison.php" class="nav-button prev">
                    <span class="nav-label">Previous</span>
                    <span class="nav-title">&larr; Free vs. Paid Version</span>
                </a>
                <a href="../features/dashboard.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Dashboard &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
