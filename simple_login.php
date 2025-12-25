<!DOCTYPE html>
<html>
<head>
    <title>Simple Local Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; max-width: 400px; margin: 100px auto; padding: 20px; background: #f0f0f0; }
        .login-box { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { background: #28a745; color: white; padding: 12px; border: none; border-radius: 4px; width: 100%; cursor: pointer; font-size: 16px; }
        button:hover { background: #218838; }
        .info { background: #e7f3ff; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-size: 14px; }
        .quick-users { background: #f8f9fa; padding: 15px; border-radius: 4px; margin-bottom: 20px; }
        .quick-users h4 { margin-top: 0; }
        .user-btn { background: #007bff; color: white; border: none; padding: 8px 12px; margin: 2px; border-radius: 4px; cursor: pointer; font-size: 12px; }
        .user-btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>🔑 Local Development Login</h2>
        
        <div class="info">
            <strong>All passwords reset to:</strong> <code>1</code><br>
            <strong>Environment:</strong> Local Development
        </div>
        
        <div class="quick-users">
            <h4>Quick Login:</h4><button type="button" class="user-btn" onclick="quickLogin('admin@example.com')">admin@example.com (admin L1)</button> <button type="button" class="user-btn" onclick="quickLogin('hd001')">hd001 (cms L2)</button> <button type="button" class="user-btn" onclick="quickLogin('hd01')">hd01 (cms L2)</button> <button type="button" class="user-btn" onclick="quickLogin('.well-known')">.well-known (cms L2)</button> <button type="button" class="user-btn" onclick="quickLogin('cms')">cms (cms L2)</button> 
        </div>
        
        <form method="POST" action="/login">
            <input type="hidden" name="_token" value="">
            
            <div class="form-group">
                <label for="username">Username/Email:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" value="1" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
        
        <div style="margin-top: 20px; text-align: center; font-size: 12px; color: #666;">
            <a href="/superadmin">Super Admin</a> | 
            <a href="/SiVGT/admin">Project Admin</a> | 
            <a href="/admin">CMS Admin</a>
        </div>
    </div>
    
    <script>
        function quickLogin(username) {
            document.getElementById("username").value = username;
            document.getElementById("password").value = "1";
        }
    </script>
</body>
</html>