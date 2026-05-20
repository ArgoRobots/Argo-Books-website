# Phase 3 Funnel Telemetry: Advanced Installer setup

The Argo Books website serves Windows installers with a referral token embedded
in the filename, e.g. `Argo Books Installer V.2.1.0_8c4e2f1a.exe`. The
Advanced Installer project needs a custom action that extracts that token
during install and writes it to disk so the desktop app can read it on
first-run.

## What this enables

When a user clicks a Google Ads link, downloads the installer, and runs it,
the first-run telemetry POST from the Avalonia app includes the token. The
website resolves the token back to the original ad click, completing the
funnel: **ad click -> download -> install -> first launch**.

Without this custom action, the funnel stops at "download click" with no
visibility into what fraction of clicks become installs.

## What you need

- The Advanced Installer project file for Argo Books (`.aip`)
- Admin access to edit the install sequence
- About 10 minutes

## Step 1: Add a PowerShell custom action

In Advanced Installer:

1. Open your Argo Books `.aip` project.
2. Navigate to **Custom Actions** in the left-hand tree.
3. Right-click and choose **Add Custom Action** -> **Run PowerShell inline script**.
4. Name it `WriteInstallToken`.
5. Paste this script into the body:

```powershell
# Extract the _xxxxxxxx token from the installer filename and write it to
# %LOCALAPPDATA%\ArgoBooks\install_token.txt so the Avalonia app can read it
# on first launch.
try {
    # OriginalDatabase is the full path the installer was launched from.
    # AI_SETUP_NAME / OriginalDatabase work for most Advanced Installer setups.
    $installerPath = "[OriginalDatabase]"
    if (-not $installerPath -or $installerPath -eq "[OriginalDatabase]") {
        # Fallback for older Advanced Installer versions
        $installerPath = [System.Environment]::GetCommandLineArgs()[0]
    }

    if ($installerPath -match '_([0-9a-f]{8})\.(exe|msi)$') {
        $token = $matches[1]
        $appDataDir = Join-Path $env:LOCALAPPDATA 'ArgoBooks'
        New-Item -ItemType Directory -Force -Path $appDataDir | Out-Null
        Set-Content -Path (Join-Path $appDataDir 'install_token.txt') `
                    -Value $token -Encoding ASCII -Force
    }
}
catch {
    # Never fail the install over a missing token.
}
```

6. Click **OK**.

## Step 2: Schedule the custom action

The custom action must run before file installation completes so the token
file is in place when the user launches the app.

1. Still in **Custom Actions**, select `WriteInstallToken`.
2. Set **Execution Time** to `When the system is being modified (deferred)`.
3. Set **Sequence position** to `After InstallFiles` (or any time after
   `InstallInitialize`).
4. Leave **Execution Stage** at the default (`Immediate`).
5. Set **Run condition** to `NOT Installed` so it only runs on fresh installs,
   not on uninstall or repair.

## Step 3: Sign the custom action (optional but recommended)

If your project signs the installer, also sign the PowerShell script. Otherwise
Windows SmartScreen may warn users. To sign:

1. Save the script to a `.ps1` file.
2. Sign with `signtool sign /f cert.pfx /p password script.ps1`.
3. Reference the signed file in Advanced Installer instead of inlining.

## Step 4: Test

1. Build a test installer.
2. Rename the built `.exe` to include a fake token: `Argo Books Installer V.2.1.0_abcdef12.exe`.
3. Run it on a clean VM or test machine.
4. After install, check `%LOCALAPPDATA%\ArgoBooks\install_token.txt`.
5. Expected contents: `abcdef12` (8 lowercase hex chars, no newline).

## Step 5: Graceful degradation

If the installer is renamed by the user (drag-out, virus scanner rename, manual
download), the regex won't match and the file isn't written. This is fine:

- The install still succeeds.
- The Avalonia app's `FirstRunReporter` checks for the file's existence and
  sends a request without a token if it's missing. The funnel logs the event
  as "first run without attribution" instead of dropping it.

## When to rotate

The `REFERRAL_TOKEN_SECRET` on the web side (`.env`) and the install token
format are tied together. If you rotate the server-side secret, in-flight
installers will become unresolvable until the web side scans the longer
14-day landing window for matches. After 14 days, old tokens stop matching
entirely. Plan rotations during low-traffic periods.

## Troubleshooting

**Token file isn't created:** Run the PowerShell script manually in an admin
shell with a fake `OriginalDatabase` value to check the regex. Most failures
are due to the installer being renamed or the `_xxxxxxxx` suffix missing.

**Token file is created but Avalonia doesn't see it:** Check
`%LOCALAPPDATA%\ArgoBooks\` for `first_run_reported.marker`. If that file
exists, the app has already sent a first-run event and will not retry. Delete
the marker to force a retry.
