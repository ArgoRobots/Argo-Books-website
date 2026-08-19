<?php
require_once __DIR__ . '/../../../resources/icons.php';
// Load system requirements from JSON
function getSystemRequirements()
{
    $jsonPath = '../../../resources/data/system-requirements.json';
    if (file_exists($jsonPath)) {
        $json = file_get_contents($jsonPath);
        return json_decode($json, true);
    }
    return [];
}

$systemRequirements = getSystemRequirements();

$pageTitle = 'System Requirements';
$pageDescription = 'View the system requirements for running Argo Books on Windows and Linux, plus the planned requirements for the macOS build.';
$currentPage = 'system-requirements';
$pageCategory = 'getting-started';

include __DIR__ . '/../../docs-header.php';
?>

        <div class="docs-content">
            <p>Make sure your system meets these requirements before installing Argo Books.</p>

            <div class="requirements-grid">
                <?php foreach ($systemRequirements as $platform => $data): ?>
                <div class="requirement-card">
                    <h3>
                        <svg viewBox="0 0 24 24" fill="currentColor" class="req-icon">
                            <path d="<?php echo getPlatformIconPath($platform); ?>"/>
                        </svg>
                        <?php echo htmlspecialchars($data['name']); ?>
                        <?php if (empty($data['available'])): ?>
                        <span class="req-coming-soon">Coming soon</span>
                        <?php endif; ?>
                    </h3>
                    <ul>
                        <?php foreach ($data['requirements'] as $req): ?>
                        <li><?php echo htmlspecialchars($req); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="page-navigation">
                <a href="installation.php" class="nav-button next">
                    <span class="nav-label">Next</span>
                    <span class="nav-title">Installation Guide &rarr;</span>
                </a>
            </div>
        </div>

<?php include __DIR__ . '/../../docs-footer.php'; ?>
