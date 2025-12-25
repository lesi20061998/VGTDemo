<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 50px auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Test Login for Widget Access</h2>
    
    <?php if (isset($_GET['success'])): ?>
        <p class="success">Login successful! <a href="/SiVGT/admin/widgets">Go to Widget Builder</a></p>
    <?php endif; ?>
    
    <?php if (isset($_GET['error'])): ?>
        <p class="error">Login failed. Please try again.</p>
    <?php endif; ?>
    
    <form method="POST" action="/login">
        <div class="form-group">
            <label>Email:</label>
            <input type="email" name="email" value="admin@test.com" required>
        </div>
        
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" value="password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>
    
    <hr>
    <h3>Test Credentials:</h3>
    <p><strong>Email:</strong> admin@test.com</p>
    <p><strong>Password:</strong> password</p>
    <p><strong>Role:</strong> admin</p>
    <p><strong>Level:</strong> 100</p>
    
    <hr>
    <h3>Debug Links:</h3>
    <ul>
        <li><a href="/SiVGT/admin/widgets/debug-permission">Debug Permission</a></li>
        <li><a href="/SiVGT/admin/widgets/test-access">Test Access</a></li>
        <li><a href="/SiVGT/admin/widgets">Widget Builder</a></li>
    </ul>
</body>
</html>