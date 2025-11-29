<!DOCTYPE html>
<html lang="en">
<!-- 12-1: Protect Form Processors
Part 2. For the Project Email Form do the following:
Setup and add a reCAPTCHA process to your form (highly recommended) or use a honeypot like 5-1.
If the form was submitted sucessfully process the form as directed in the project.
If the form was not submitted successfull send an email to you/support/host person 
indicating that an attempt was made to access your form. -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Form - Project Email</title>
    <!-- Google reCAPTCHA v2 -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;margin:0;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);min-height:100vh;padding:40px 20px}
        .container{max-width:650px;margin:0 auto;background:#fff;padding:40px;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,.3)}
        h1{margin:0 0 12px;font-size:32px;font-weight:700;color:#1a1a2e;text-align:center}
        .subtitle{text-align:center;color:#666;margin:0 0 32px;font-size:16px}
        .form-group{margin-bottom:20px}
        label{display:block;font-weight:600;margin-bottom:8px;color:#374151;font-size:15px}
        input[type="text"],input[type="email"],textarea,select{width:100%;padding:14px 16px;border:2px solid #e5e7eb;border-radius:10px;font-size:16px;box-sizing:border-box;font-family:inherit;transition:border-color .2s}
        input:focus,textarea:focus,select:focus{outline:none;border-color:#667eea}
        textarea{min-height:140px;resize:vertical}
        .required{color:#dc2626;font-weight:bold}
        .recaptcha-wrapper{margin:24px 0;display:flex;justify-content:center}
        button{width:100%;padding:16px;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:#fff;border:none;border-radius:10px;font-size:18px;font-weight:700;cursor:pointer;transition:transform .2s,box-shadow .2s}
        button:hover{transform:translateY(-2px);box-shadow:0 10px 25px rgba(102,126,234,.4)}
        button:active{transform:translateY(0)}
        .note{font-size:13px;color:#6b7280;margin-top:8px;line-height:1.5}
        .security-note{background:#f0fdf4;border:1px solid #86efac;padding:12px 16px;border-radius:8px;margin-top:24px;font-size:14px;color:#166534}
        /* Honeypot fallback (if reCAPTCHA fails to load) */
        .honeypot{position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden}
    </style>
</head>
<body>
    <div class="container">
        <h1>📬 Contact Us</h1>
        <p class="subtitle">We'd love to hear from you. Send us a message!</p>
        
        <form method="post" action="processEmailForm.php">
            <!-- Honeypot fallback -->
            <div class="honeypot" aria-hidden="true">
                <label for="website_url">Leave this field empty</label>
                <input type="text" id="website_url" name="website_url" tabindex="-1" autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="fullName">Full Name <span class="required">*</span></label>
                <input type="text" id="fullName" name="fullName" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address <span class="required">*</span></label>
                <input type="email" id="email" name="email" required>
                <div class="note">We'll never share your email with anyone else.</div>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" placeholder="Optional">
            </div>
            
            <div class="form-group">
                <label for="subject">Subject <span class="required">*</span></label>
                <select id="subject" name="subject" required>
                    <option value="">-- Select a subject --</option>
                    <option value="General Inquiry">General Inquiry</option>
                    <option value="Support Request">Support Request</option>
                    <option value="Event Information">Event Information</option>
                    <option value="Partnership">Partnership Opportunity</option>
                    <option value="Feedback">Feedback</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="message">Message <span class="required">*</span></label>
                <textarea id="message" name="message" required placeholder="Please enter the required full name and email..."></textarea>
            </div>
            
            <!-- Google reCAPTCHA v2 -->
            <div class="recaptcha-wrapper">
                <div class="g-recaptcha" data-sitekey="6LcrAxssAAAAAJAOGzeCWjT_jT8KV8OfwM7NEk6S"></div>
            </div>
            <div class="note" style="text-align:center;margin-top:-16px">
                Please complete the reCAPTCHA verification above.
            </div>
            
            <button type="submit" name="submit">Send Message</button>
            
            <div class="security-note">
                🔒 This form is protected by Google reCAPTCHA and honeypot security measures.
            </div>
        </form>
    </div>
</body>
</html>
