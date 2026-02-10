Add-Type -AssemblyName System.Drawing

$base = "C:\Users\rayel\OneDrive\Documents\Business\RayelsConsulting\Business code projet 1\rayels-loi25\packages\chrome-extension"

# ─── Helper: Draw rounded rectangle ───
function Add-RoundedRect($gp, $x, $y, $w, $h, $r) {
    $d = $r * 2
    $gp.AddArc($x, $y, $d, $d, 180, 90)
    $gp.AddArc($x + $w - $d, $y, $d, $d, 270, 90)
    $gp.AddArc($x + $w - $d, $y + $h - $d, $d, $d, 0, 90)
    $gp.AddArc($x, $y + $h - $d, $d, $d, 90, 90)
    $gp.CloseFigure()
}

# ═══════════════════════════════════════════════
# SCREENSHOT 1: 1280x800 — Extension popup on a website
# ═══════════════════════════════════════════════
$w = 1280; $h = 800
$bmp = New-Object System.Drawing.Bitmap($w, $h)
$g = [System.Drawing.Graphics]::FromImage($bmp)
$g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
$g.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::AntiAliasGridFit

# Background - fake browser
$g.Clear([System.Drawing.Color]::FromArgb(248, 250, 252))

# Browser top bar
$barBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(30, 41, 59))
$g.FillRectangle($barBrush, 0, 0, $w, 52)

# Browser dots
$colors = @(
    [System.Drawing.Color]::FromArgb(255, 95, 87),
    [System.Drawing.Color]::FromArgb(255, 189, 46),
    [System.Drawing.Color]::FromArgb(40, 200, 64)
)
for ($i = 0; $i -lt 3; $i++) {
    $dotBrush = New-Object System.Drawing.SolidBrush($colors[$i])
    $g.FillEllipse($dotBrush, (16 + $i * 20), 18, 14, 14)
}

# URL bar
$urlBg = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(51, 65, 85))
$urlPath = New-Object System.Drawing.Drawing2D.GraphicsPath
Add-RoundedRect $urlPath 100 12 900 28 6
$g.FillPath($urlBg, $urlPath)
$urlFont = New-Object System.Drawing.Font("Segoe UI", 11)
$urlBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(148, 163, 184))
$g.DrawString("example-quebec-business.ca", $urlFont, $urlBrush, 116, 15)

# Fake website content area
$contentBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)
$g.FillRectangle($contentBrush, 0, 52, $w, $h - 52)

# Fake site heading
$siteFont = New-Object System.Drawing.Font("Segoe UI", 28, [System.Drawing.FontStyle]::Bold)
$siteBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(30, 41, 59))
$g.DrawString("Mon Entreprise Quebec", $siteFont, $siteBrush, 80, 100)

# Fake content blocks
$blockBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(241, 245, 249))
$g.FillRectangle($blockBrush, 80, 160, 500, 16)
$g.FillRectangle($blockBrush, 80, 185, 400, 16)
$g.FillRectangle($blockBrush, 80, 230, 700, 120)
$g.FillRectangle($blockBrush, 80, 370, 550, 16)
$g.FillRectangle($blockBrush, 80, 395, 450, 16)

# ── Extension popup (right side, overlaying the page) ──
$px = 820; $py = 60; $pw = 380; $ph = 520
# Shadow
$shadowBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(40, 0, 0, 0))
$g.FillRectangle($shadowBrush, ($px+4), ($py+4), $pw, $ph)

# Popup background
$popupPath = New-Object System.Drawing.Drawing2D.GraphicsPath
Add-RoundedRect $popupPath $px $py $pw $ph 12
$popupBg = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)
$g.FillPath($popupBg, $popupPath)

# Extension header bar
$extBarBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(240, 240, 240))
$g.FillRectangle($extBarBrush, $px, $py, $pw, 36)
$extBarFont = New-Object System.Drawing.Font("Segoe UI", 10)
$extBarTextBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(102, 102, 102))

# L25 icon in header
$iconBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(29, 78, 216))
$iconPath = New-Object System.Drawing.Drawing2D.GraphicsPath
Add-RoundedRect $iconPath ($px+12) ($py+8) 20 20 4
$g.FillPath($iconBrush, $iconPath)
$iconFont = New-Object System.Drawing.Font("Segoe UI", 6, [System.Drawing.FontStyle]::Bold)
$iconTextBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)
$g.DrawString("L25", $iconFont, $iconTextBrush, ($px+14), ($py+13))
$g.DrawString("Loi 25 Compliance Checker", $extBarFont, $extBarTextBrush, ($px+38), ($py+10))

# Popup content
$titleFont = New-Object System.Drawing.Font("Segoe UI", 16, [System.Drawing.FontStyle]::Bold)
$titleBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(30, 41, 59))
$g.DrawString("Loi 25 Compliance Checker", $titleFont, $titleBrush, ($px+20), ($py+52))

$subFont = New-Object System.Drawing.Font("Segoe UI", 10)
$subBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(100, 116, 139))
$g.DrawString("Check if this website complies with Quebec's Law 25", $subFont, $subBrush, ($px+20), ($py+80))

# Score box
$scoreBgBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(240, 253, 244))
$scorePath = New-Object System.Drawing.Drawing2D.GraphicsPath
Add-RoundedRect $scorePath ($px+20) ($py+115) ($pw-40) 70 10
$g.FillPath($scoreBgBrush, $scorePath)
$scoreFont = New-Object System.Drawing.Font("Segoe UI", 28, [System.Drawing.FontStyle]::Bold)
$scoreBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(22, 163, 106))
$sf = New-Object System.Drawing.StringFormat
$sf.Alignment = [System.Drawing.StringAlignment]::Center
$g.DrawString("87%", $scoreFont, $scoreBrush, [System.Drawing.RectangleF]::new(($px+20), ($py+118), ($pw-40), 45), $sf)
$scoreLabelFont = New-Object System.Drawing.Font("Segoe UI", 9)
$g.DrawString("Compliance Score", $scoreLabelFont, $subBrush, [System.Drawing.RectangleF]::new(($px+20), ($py+158), ($pw-40), 20), $sf)

# Check items
$checkFont = New-Object System.Drawing.Font("Segoe UI", 11)
$passColor = [System.Drawing.Color]::FromArgb(22, 163, 106)
$warnColor = [System.Drawing.Color]::FromArgb(245, 158, 11)
$checkTextBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(30, 41, 59))

$checks = @(
    @{text="Cookie consent banner detected"; color=$passColor; symbol=[char]0x2713},
    @{text="Privacy policy link found"; color=$passColor; symbol=[char]0x2713},
    @{text="Google Analytics detected (needs consent)"; color=$warnColor; symbol="!"},
    @{text="Meta/Facebook Pixel (not found)"; color=$passColor; symbol="-"},
    @{text="Consent management code found"; color=$passColor; symbol=[char]0x2713}
)

$cy = $py + 205
foreach ($check in $checks) {
    # Icon circle
    $circBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(30, $check.color.R, $check.color.G, $check.color.B))
    $g.FillEllipse($circBrush, ($px+24), $cy, 22, 22)
    $symFont = New-Object System.Drawing.Font("Segoe UI", 10, [System.Drawing.FontStyle]::Bold)
    $symBrush = New-Object System.Drawing.SolidBrush($check.color)
    $g.DrawString([string]$check.symbol, $symFont, $symBrush, ($px+29), ($cy+2))
    $g.DrawString($check.text, $checkFont, $checkTextBrush, ($px+54), ($cy+2))

    # Separator line
    $linePen = New-Object System.Drawing.Pen([System.Drawing.Color]::FromArgb(241, 245, 249), 1)
    $g.DrawLine($linePen, ($px+20), ($cy+30), ($px+$pw-20), ($cy+30))
    $cy += 38
}

# Footer
$footerFont = New-Object System.Drawing.Font("Segoe UI", 9)
$footerBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(148, 163, 184))
$linePen2 = New-Object System.Drawing.Pen([System.Drawing.Color]::FromArgb(226, 232, 240), 1)
$g.DrawLine($linePen2, ($px+20), ($py+$ph-40), ($px+$pw-20), ($py+$ph-40))
$g.DrawString("Powered by Rayels Consulting", $footerFont, $footerBrush, [System.Drawing.RectangleF]::new($px, ($py+$ph-32), $pw, 20), $sf)

$g.Dispose()
$bmp.Save("$base\screenshot-1.png", [System.Drawing.Imaging.ImageFormat]::Png)
$bmp.Dispose()
Write-Host "Created screenshot-1.png (1280x800)"


# ═══════════════════════════════════════════════
# SMALL PROMO: 440x280
# ═══════════════════════════════════════════════
$w = 440; $h = 280
$bmp = New-Object System.Drawing.Bitmap($w, $h)
$g = [System.Drawing.Graphics]::FromImage($bmp)
$g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
$g.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::AntiAliasGridFit

# Blue gradient bg
$bgBrush = New-Object System.Drawing.Drawing2D.LinearGradientBrush(
    (New-Object System.Drawing.Point(0, 0)),
    (New-Object System.Drawing.Point($w, $h)),
    [System.Drawing.Color]::FromArgb(29, 78, 216),
    [System.Drawing.Color]::FromArgb(21, 56, 160)
)
$g.FillRectangle($bgBrush, 0, 0, $w, $h)

# Title
$promoTitleFont = New-Object System.Drawing.Font("Segoe UI", 20, [System.Drawing.FontStyle]::Bold)
$whiteBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::White)
$g.DrawString("Loi 25", $promoTitleFont, $whiteBrush, 30, 40)
$promoTitle2Font = New-Object System.Drawing.Font("Segoe UI", 14)
$lightBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(180, 255, 255, 255))
$g.DrawString("Verificateur de conformite", $promoTitle2Font, $lightBrush, 30, 72)

# Subtitle
$promoSubFont = New-Object System.Drawing.Font("Segoe UI", 10)
$g.DrawString("Verifiez la conformite de tout site", $promoSubFont, $lightBrush, 30, 110)
$g.DrawString("web a la Loi 25 du Quebec", $promoSubFont, $lightBrush, 30, 128)

# Score badge
$badgeBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(40, 255, 255, 255))
$badgePath = New-Object System.Drawing.Drawing2D.GraphicsPath
Add-RoundedRect $badgePath 300 30 110 110 16
$g.FillPath($badgeBrush, $badgePath)
$badgeScoreFont = New-Object System.Drawing.Font("Segoe UI", 32, [System.Drawing.FontStyle]::Bold)
$g.DrawString("87%", $badgeScoreFont, $whiteBrush, [System.Drawing.RectangleF]::new(300, 42, 110, 50), $sf)
$badgeLabelFont = New-Object System.Drawing.Font("Segoe UI", 8)
$g.DrawString("Compliance", $badgeLabelFont, $lightBrush, [System.Drawing.RectangleF]::new(300, 90, 110, 20), $sf)
$g.DrawString("Score", $badgeLabelFont, $lightBrush, [System.Drawing.RectangleF]::new(300, 104, 110, 20), $sf)

# Checkmarks
$checkSmFont = New-Object System.Drawing.Font("Segoe UI", 10)
$items = @("Detection de banniere cookie", "Verification politique de confidentialite", "Alertes scripts de suivi")
$iy = 170
foreach ($item in $items) {
    $g.DrawString([string][char]0x2713 + "  " + $item, $checkSmFont, $lightBrush, 30, $iy)
    $iy += 24
}

# Footer
$footerSmFont = New-Object System.Drawing.Font("Segoe UI", 8)
$veryLight = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(120, 255, 255, 255))
$g.DrawString("by Rayels Consulting", $footerSmFont, $veryLight, 30, 252)

$g.Dispose()
$bmp.Save("$base\promo-small.png", [System.Drawing.Imaging.ImageFormat]::Png)
$bmp.Dispose()
Write-Host "Created promo-small.png (440x280)"


# ═══════════════════════════════════════════════
# MARQUEE PROMO: 1400x560
# ═══════════════════════════════════════════════
$w = 1400; $h = 560
$bmp = New-Object System.Drawing.Bitmap($w, $h)
$g = [System.Drawing.Graphics]::FromImage($bmp)
$g.SmoothingMode = [System.Drawing.Drawing2D.SmoothingMode]::AntiAlias
$g.TextRenderingHint = [System.Drawing.Text.TextRenderingHint]::AntiAliasGridFit

# Dark blue bg
$bgBrush2 = New-Object System.Drawing.Drawing2D.LinearGradientBrush(
    (New-Object System.Drawing.Point(0, 0)),
    (New-Object System.Drawing.Point($w, $h)),
    [System.Drawing.Color]::FromArgb(15, 23, 42),
    [System.Drawing.Color]::FromArgb(30, 58, 95)
)
$g.FillRectangle($bgBrush2, 0, 0, $w, $h)

# Accent circle
$accentBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(15, 59, 130, 246))
$g.FillEllipse($accentBrush, 800, -100, 700, 700)

# Left side text
$heroFont = New-Object System.Drawing.Font("Segoe UI", 42, [System.Drawing.FontStyle]::Bold)
$g.DrawString("Loi 25 Compliance", $heroFont, $whiteBrush, 80, 120)
$g.DrawString("Checker", $heroFont, $whiteBrush, 80, 180)

$heroSubFont = New-Object System.Drawing.Font("Segoe UI", 16)
$heroSubBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(148, 163, 184))
$g.DrawString("Verifiez instantanement la conformite de tout site", $heroSubFont, $heroSubBrush, 80, 260)
$g.DrawString("web a la Loi 25 sur la protection de la vie privee", $heroSubFont, $heroSubBrush, 80, 288)

# Feature pills
$pillBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(20, 255, 255, 255))
$pillFont = New-Object System.Drawing.Font("Segoe UI", 11)
$pillTextBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(200, 255, 255, 255))
$pills = @("Scan en un clic", "Score de conformite", "Gratuit et open source")
$pillX = 80
foreach ($pill in $pills) {
    $pillSize = $g.MeasureString($pill, $pillFont)
    $pillW = [int]$pillSize.Width + 24
    $pillPath = New-Object System.Drawing.Drawing2D.GraphicsPath
    Add-RoundedRect $pillPath $pillX 350 $pillW 34 17
    $g.FillPath($pillBrush, $pillPath)
    $g.DrawString($pill, $pillFont, $pillTextBrush, ($pillX + 12), 357)
    $pillX += $pillW + 12
}

# Brand
$brandFont = New-Object System.Drawing.Font("Segoe UI", 12)
$g.DrawString("by Rayels Consulting  |  rayelsconsulting.com", $brandFont, $heroSubBrush, 80, 420)

# Right side - mini popup mockup
$mpx = 900; $mpy = 80; $mpw = 380; $mph = 400
$shadowB = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(30, 0, 0, 0))
$g.FillRectangle($shadowB, ($mpx+6), ($mpy+6), $mpw, $mph)
$miniPath = New-Object System.Drawing.Drawing2D.GraphicsPath
Add-RoundedRect $miniPath $mpx $mpy $mpw $mph 12
$g.FillPath($popupBg, $miniPath)

# Mini popup content
$mpTitleFont = New-Object System.Drawing.Font("Segoe UI", 14, [System.Drawing.FontStyle]::Bold)
$mpTextBrush = New-Object System.Drawing.SolidBrush([System.Drawing.Color]::FromArgb(30, 41, 59))
$g.DrawString("Loi 25 Compliance Checker", $mpTitleFont, $mpTextBrush, ($mpx+20), ($mpy+20))

# Mini score
$miniScorePath = New-Object System.Drawing.Drawing2D.GraphicsPath
Add-RoundedRect $miniScorePath ($mpx+20) ($mpy+55) ($mpw-40) 60 8
$g.FillPath($scoreBgBrush, $miniScorePath)
$miniScoreFont = New-Object System.Drawing.Font("Segoe UI", 24, [System.Drawing.FontStyle]::Bold)
$g.DrawString("87%", $miniScoreFont, $scoreBrush, [System.Drawing.RectangleF]::new(($mpx+20), ($mpy+58), ($mpw-40), 35), $sf)
$miniLabelFont = New-Object System.Drawing.Font("Segoe UI", 8)
$g.DrawString("Compliance Score", $miniLabelFont, $subBrush, [System.Drawing.RectangleF]::new(($mpx+20), ($mpy+90), ($mpw-40), 20), $sf)

# Mini check items
$miniCheckFont = New-Object System.Drawing.Font("Segoe UI", 10)
$miniChecks = @(
    @{t="Cookie consent banner detected"; c=$passColor},
    @{t="Privacy policy link found"; c=$passColor},
    @{t="Google Analytics (needs consent)"; c=$warnColor},
    @{t="Meta Pixel (not found)"; c=$passColor},
    @{t="Consent management code found"; c=$passColor}
)
$mcy = $mpy + 130
foreach ($mc in $miniChecks) {
    $mcBrush = New-Object System.Drawing.SolidBrush($mc.c)
    $g.DrawString([string][char]0x2713, $miniCheckFont, $mcBrush, ($mpx+24), $mcy)
    $g.DrawString($mc.t, $miniCheckFont, $mpTextBrush, ($mpx+46), $mcy)
    $linePen3 = New-Object System.Drawing.Pen([System.Drawing.Color]::FromArgb(241, 245, 249), 1)
    $g.DrawLine($linePen3, ($mpx+20), ($mcy+24), ($mpx+$mpw-20), ($mcy+24))
    $mcy += 32
}

# Mini footer
$g.DrawString("Powered by Rayels Consulting", $footerFont, $footerBrush, [System.Drawing.RectangleF]::new($mpx, ($mpy+$mph-30), $mpw, 20), $sf)

$g.Dispose()
$bmp.Save("$base\promo-marquee.png", [System.Drawing.Imaging.ImageFormat]::Png)
$bmp.Dispose()
Write-Host "Created promo-marquee.png (1400x560)"

Write-Host "`nAll store images generated!"
