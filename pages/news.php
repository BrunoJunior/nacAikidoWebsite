<?php
	include_once('classes/News.class.php');
        include_once('classes/Membre.class.php');
        
        $isAdmin = 0;
    
        if(isset($_SESSION['userid']))
        {
            $membreConnect = Membre::findById($_SESSION['userid']);
            $isAdmin = $membreConnect->getAdmin();
        }
        
	$newspage = 1;
	$numberOfPages = News::getNumberOfPages();
	if(isset($_GET['newspage'])&& $_GET['newspage'] <= $numberOfPages)
	{
		$newspage = $_GET['newspage'];
	}
	
	$titre;
	$message;
	$tosave = true;
	$erreur = false;
	
	if(isset($_POST['titre']))
	{
		$titre = strip_tags($_POST['titre']);
		if(isset($_POST['message']))
		{
                    $message = strip_tags($_POST['message']);
		}
		else
		{
                    $tosave = false;
		}
	}
	else
	{
		$tosave = false;
	}
	
	if($tosave && $isAdmin)
	{
		$news = new News($titre, $message);
		try
		{
                    $news->save($_FILES['image']); 
                        
                    $subject = 'News : ' . $news->titre();

                    $headers = "From: \"News site Web\"<$emailfrom> \r\n";
                    $headers .= "Reply-To: nac.aikido@gmail.com \r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: text/html; charset=utf8\r\n";

                    $message = $news->getContentForEmail($parser);

                    foreach (Membre::getMembresAbonnesNews() as $membreabonews)
                    {
                        $to = $membreabonews->getEmail();
                        mail($to, $subject, $message, $headers);
                    }
                        
                    echo "<div class='alert alert-success'>News créée.</div>";
		}
		catch(Exception $e)
		{
			$erreur = true;
			echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
		}
	}
	elseif(!$isAdmin)
	{
		echo '<div class="alert alert-danger">Vous n\'êtes pas autorisé à envoyer des news !</div>';
	}
?>
<div class="row">
	<div class="col-lg-12">
		<ul class="pager">
		  <li class="previous<?php if($newspage == 1){echo ' disabled';} ?>">
			<?php if($newspage > 1){echo '<a href="index.php?page=news&newspage=' . ($newspage-1) . '">';}else{echo '<a href="#">';}?>&larr; Plus récentes</a>
		  </li>
		  <li class="next<?php if($newspage == $numberOfPages){echo ' disabled';} ?>">
		    <?php if($newspage < $numberOfPages){echo '<a href="index.php?page=news&newspage=' . ($newspage+1) . '">';}else{echo '<a href="#">';}?>Plus anciennes &rarr;</a>
		  </li>
		</ul>
	</div>
</div>
<div class="row">
	<div class="page-header"><h2>Les dernières news</h2></div>
	<div class="col-lg-12">
		<?php
			foreach(News::getAllNews($newspage) as $element)
			{
                            $imageSrc = 'news/images/' . $element->id() . '.jpg';
                            $parser->parse(nl2br($element->message()));
                            $content = $parser->getAsHtml();
                ?>
            <div class="row">
                <div class="col-sm-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo $element->titre(); ?></h3>
			</div>
			<div class="panel-body">
                            <div class="row">
                        <?php
				if(file_exists($imageSrc))
				{
                        ?>
                                <div class="col-sm-3">
                                    <div class="hidden-xs"><img class="img-responsive img-thumbnail img-marged" id="imgNews<?php echo $element->id(); ?>" title="Image de la news <?php echo $element->id(); ?>" alt="Image de la news <?php echo $element->id(); ?>" src="<?php echo $imageSrc; ?>" /></div>
                                </div>
                                <div class="col-sm-9">
                        <?php
                                }
                                else
                                {
                        ?>
                                <div class="col-sm-12">
                        <?php
                                }
                                echo $content;
                        ?>
                                </div>
                            </div>
                        </div>
			<div class="panel-footer">News du <?php echo $element->dateCre(); ?></div>
                    </div>
                </div>
            </div>
                <?php
			}
		?>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		<ul class="pager">
		  <li class="previous<?php if($newspage == 1){echo ' disabled';} ?>">
			<?php if($newspage > 1){echo '<a href="index.php?page=news&newspage=' . ($newspage-1) . '">';}else{echo '<a href="#">';}?>&larr; Plus récentes</a>
		  </li>
		  <li class="next<?php if($newspage == $numberOfPages){echo ' disabled';} ?>">
		    <?php if($newspage < $numberOfPages){echo '<a href="index.php?page=news&newspage=' . ($newspage+1) . '">';}else{echo '<a href="#">';}?>Plus anciennes &rarr;</a>
		  </li>
		</ul>
	</div>
</div>
<?php
    if($isAdmin == 1)
    {
?>
<div class="row">
	<div class="page-header"><h2>Insérer une news</h2></div>
	<div class="col-lg-12">
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title">Nouvelle news</h3>
	  </div>
	  <div class="panel-body">
		<form role="form" method="post" action="index.php" enctype="multipart/form-data">
		  <div class="form-group">
			<label for="titre">Titre</label>
			<input type="text" class="form-control" id="titre" name="titre" placeholder="Le titre de la news" required="" <?php if($erreur){echo 'value="' . $titre . '"';} ?>>
		  </div>
		  <div class="form-group">
			<label for="message">News</label>
			<textarea class="form-control" rows="3" id="message" name="message" required=""><?php if($erreur){echo $message;}else{echo 'Insérez ici le texte de la news';}?></textarea>
		  </div>
		  <div class="form-group">
			<label for="image">Image</label>
			<input data-validation="size" data-validation-max-size="512kb" type="file" id="image" name="image">
			<p class="help-block">Sélectionnez une image illustrant la news (jpg, png, gif).</p>
			<input type="hidden" id="page" name="page" value="news" />
		  </div>
                  <button type="submit" class="btn btn-success">Envoyer</button>
		</form>
	  </div>
	</div>
	</div>
</div>
<?php
    }
?>
