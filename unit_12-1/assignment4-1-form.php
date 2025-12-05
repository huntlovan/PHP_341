<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Assignment 4-1 Signup Form</title>
    <style>
        body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,'Open Sans','Helvetica Neue',sans-serif;margin:0;background:#f0f2f5;color:#333}
        .container{max-width:600px;margin:60px auto;background:#fff;padding:32px;border-radius:12px;box-shadow:0 4px 20px rgba(0,0,0,.1)}
        h1{margin:0 0 24px;font-size:28px;font-weight:600;color:#1a1a2e}
        .form-group{margin-bottom:18px}
        label{display:block;font-weight:600;margin-bottom:6px;color:#444}
        input[type="text"],input[type="email"],input[type="tel"],textarea{width:100%;padding:12px 14px;border:1px solid #ddd;border-radius:8px;font-size:16px;box-sizing:border-box;font-family:inherit}
        input:focus,textarea:focus{outline:none;border-color:#4f46e5}
        textarea{min-height:120px;resize:vertical}
        button{width:100%;padding:14px;background:#4f46e5;color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;margin-top:8px}
        button:hover{background:#4338ca}
        .note{font-size:14px;color:#666;margin-top:8px}
        /* Add Honeypot security to the form page. */
        /* Honeypot - hidden from real users */
        .honeypot{position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden}
    </style>
</head>
<body>
    <div class="container">
        <h1>Event Signup Form</h1>
        <p class="note">Please fill out all fields to register for our upcoming events.</p>
        
        <form method="post" action="formHandler.php">
            <!-- Honeypot field - bots will fill this, real users won't see it -->
            <div class="honeypot" aria-hidden="true">
                <label for="website">Leave this field empty</label>
                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
            </div>
            
            <div class="form-group">
                <label for="firstName">First Name *</label>
                <input type="text" id="firstName" name="firstName" required>
            </div>
            
            <div class="form-group">
                <label for="lastName">Last Name *</label>
                <input type="text" id="lastName" name="lastName" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="(555) 123-4567">
            </div>
            
            <div class="form-group">
                <label for="eventInterest">Event Interest</label>
                <input type="text" id="eventInterest" name="eventInterest" placeholder="Which events interest you?">
            </div>
            
            <div class="form-group">
                <label for="comments">Additional Comments</label>
                <textarea id="comments" name="comments" placeholder="Any questions or special requests?"></textarea>
            </div>
            
            <button type="submit" name="submit">Submit Registration</button>
        </form>
    </div>
</body>
</html>
