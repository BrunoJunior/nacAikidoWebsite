<?php
    session_start();
	require_once "jbbcode/Parser.php";
	include_once('classes/Lien.class.php');
	
	// The BBCode parser for all pages
	$parser = new JBBCode\Parser();
	$parser->addCodeDefinitionSet(new JBBCode\DefaultCodeDefinitionSet());
        
        $emailfrom = "perso@bdesprez.com";

	$page = "accueil";
	if(isset($_GET['page']))
	{
		$page = $_GET['page'];
	}
	
	if(isset($_POST['page']))
	{
		$page = strip_tags($_POST['page']);
	}
	
	if(!file_exists("pages/$page.php")) 
	{
		$page = "404";
	}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NAC Aïkido</title>

    <!-- Bootstrap -->
    <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="nacaikido.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <?php echo Lien::getMenu('./menu', $page); ?>
	
    <div class="container">
            <?php include("pages/$page.php"); ?>
    </div><!-- /.container -->

    <?php echo Lien::getFooter('./menu', $page); ?>
	
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="bootstrap/jquery/jquery-1.11.0.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-form-validator/2.1.47/jquery.form-validator.min.js"></script> 
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="bootstrap/js/bootstrap.min.js"></script>
	<!-- Include js associated at the actual page if exists -->
	<?php
		if(file_exists("scripts/$page.js")) 
		{
			echo '<script src="scripts/'.$page.'.js"></script>';
		}
	?>
  </body>
</html>