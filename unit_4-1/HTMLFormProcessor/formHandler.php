<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width,initial-scale=1" />
	<title>Form Submission — WDV101</title>
	<style>
		body{font-family:Segoe UI, Roboto, Arial, sans-serif;background:#f4f7fb;color:#0f172a;margin:0;padding:24px}
		.card{max-width:900px;margin:16px auto;padding:20px;background:#fff;border-radius:8px;box-shadow:0 8px 24px rgba(23,31,40,0.06)}
		h1{margin:0 0 6px}
		.muted{color:#6b7280;margin-bottom:16px}
		table{width:100%;border-collapse:collapse;margin-top:12px}
		th,td{padding:10px;border:1px solid #e6eef8;text-align:left}
		th{background:#f1f9ff;color:#0b5394}
		.value-list{margin:0;padding:0;list-style:none}
	</style>
</head>
<body>
	<div style="text-align:center;margin-bottom:12px"><a href="http://kickshunter.com/WDV341/wdv341.php" target="_blank" rel="noopener" style="color:#0b5394;font-weight:600;text-decoration:none">&larr; Back to WDV341 Home</a></div>
	<div class="card">
		<h1>WDV101 — Confirmation</h1>
		<p class="muted">Thank you — below is a copy of the information you submitted.</p>

		<?php
		// ---------------------------
		// Helper & configuration
		// ---------------------------
		// h() - simple HTML-escape helper to prevent XSS when echoing user input
		function h($v){
			return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
		}

		// Maps used to translate stored values (from <input value="...">) to friendly labels
		$standingLabels = [
			'high_school' => 'High School',
			'freshman' => 'Freshman',
			'sophomore' => 'Sophomore'
		];
		$programLabels = ['CIS'=>'Computer Information Systems','GD'=>'Graphic Design','WD'=>'Web Development',''=>'Default option'];

		// ---------------------------
		// Read and sanitize inputs
		// ---------------------------
		// Use isset() checks so missing fields don't cause notices. We sanitize immediately for safe output.
		$firstName = isset($_POST['first_name']) ? h($_POST['first_name']) : '';
		$email = isset($_POST['customer_email']) ? h($_POST['customer_email']) : '';
		// For values we will map (standing/program), keep the raw value first and map later
		$standingRaw = isset($_POST['standing']) ? $_POST['standing'] : '';
		$programRaw = isset($_POST['program']) ? $_POST['program'] : '';
		$comments = isset($_POST['comments']) ? h($_POST['comments']) : '';

		// Map raw values to friendly labels; if a mapping doesn't exist show the raw (escaped) value or a placeholder
		$standing = isset($standingLabels[$standingRaw]) ? $standingLabels[$standingRaw] : ($standingRaw !== '' ? h($standingRaw) : '(not provided)');
		$program = isset($programLabels[$programRaw]) ? $programLabels[$programRaw] : ($programRaw !== '' ? h($programRaw) : '(not provided)');

		// ---------------------------
		// Handle checkbox array
		// ---------------------------
		// The checkboxes were named contact_pref[] so PHP provides an array. Convert to readable lines.
		$checkboxes = [];
		if(isset($_POST['contact_pref']) && is_array($_POST['contact_pref'])){
			foreach($_POST['contact_pref'] as $cb){
				$cb = (string)$cb;
				// Translate known values to the exact phrasing required by the spec
				if($cb === 'contact_info') $checkboxes[] = 'Please contact me with program information';
				elseif($cb === 'contact_advisor') $checkboxes[] = 'I would like to contact a program advisor';
				else $checkboxes[] = h($cb); // fall back to escaped text for unknown values
			}
		}

		// ---------------------------
		// Output: sentence-style confirmation
		// ---------------------------
		// Use concatenated echo calls and ensure all dynamic content is escaped via h() above.
		echo "<p>Dear ", ($firstName !== '' ? $firstName : '(First name not provided)'), ",</p>\n";

		echo "<p>Thank for you for your interest in DMACC.</p>\n";

		echo "<p>We have you listed as ", $standing, " starting this fall.</p>\n";

		echo "<p>You have declared ", $program, " as your major.</p>\n";

		echo "<p>Based upon your responses we will provide the following information in our confirmation email to you at ", ($email !== '' ? $email : '(no email provided)'), ".</p>\n";

		// Each selected checkbox appears on its own paragraph as requested
		if(count($checkboxes) > 0){
			foreach($checkboxes as $line){
				echo "<p>", h($line), "</p>\n";
			}
		} else {
			echo "<p>(No contact preferences selected)</p>\n";
		}

		echo "<p>You have shared the following comments which we will review:</p>\n";
		// Use nl2br to preserve simple line breaks in comments (already escaped earlier)
		echo "<blockquote style=\"background:#f8fbff;padding:12px;border-left:4px solid #dbeafe;border-radius:4px\">", ($comments !== '' ? nl2br($comments) : '<em>(No comments submitted)</em>'), "</blockquote>\n";

		?>

	</div>
</body>
</html>

