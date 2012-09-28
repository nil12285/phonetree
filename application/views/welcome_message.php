<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Welcome to CodeIgniter</title>
    <link rel="stylesheet" type="text/css" href="/static/css/style.css?244" media="screen" />
	<style type="text/css">
/*
	::selection{ background-color: #E13300; color: white; }
	::moz-selection{ background-color: #E13300; color: white; }
	::webkit-selection{ background-color: #E13300; color: white; }
*/
	
	</style>
</head>
<body>

<div id="container">
	<h1>Welcome to VsVenture.com!</h1>

	<div id="body">
    
    <form method="post" enctype="multipart/form-data" action="/welcome/login">
        <div class="error">
            <? echo $error; ?>
        </div>    
        <div id="div_username">
            <label for="form_username" class="required">email</label>            
            <input type="text" id="form_username" name="email" required="required" />            
        </div>
        
        <div id="div_password">
            <label for="form_password" class="required">Password</label>            
            <input type="password" id="form_password" name="password" required="required" />            
        </div>
        
        <!--
        <input type="hidden" id="form__token" name="form[_token]" value="a0be0162f741975d9a1553a8dfc8fd03eea3b6f8" />        
        <div>
            <label for="form_terms" class=" required">Terms of Service</label>
            <input type="checkbox" id="form_terms" name="form[terms]" required="required" value="1" />
        </div>
        -->
        
        <button type="submit">Login</button>
    </form>

	</div>
</div>

</body>
</html>