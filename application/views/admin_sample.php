<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>admin</title>
    
    <script type="text/javascript" src="/static/js/jquery-1.3.2.min.js?123"></script>
    <script type="text/javascript" src="/static/js/jquery-ui-1.7.3.custom.min.js?123"></script>
    <script type="text/javascript" src="/static/js/jqueryPager.js?123"></script>
    
    <link rel="stylesheet" type="text/css" href="/static/css/style.css?244" media="screen" />
	<link rel="stylesheet" type="text/css" href="/static/css/jqueryPager.css?244" media="screen" />
    <link rel="stylesheet" type="text/css" href="/static/css/ui-lightness/jquery-ui-1.7.3.custom.css?244" media="screen" />
        
    
    
    
</head>
<body>

<div id="container">
	<h1>Contacts for user : <? echo $email; ?></h1>

	<div id="body">
        <div class="grid">            
            <? echo $jqpager; ?>
        </div>
	</div>
</div>

</body>
</html>