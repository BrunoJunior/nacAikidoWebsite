<?php
	include_once('classes/Stage.class.php');
	include_once('classes/Horaire.class.php');
        include_once('classes/Membre.class.php');
        
        $isAdmin = 0;
    
        if(isset($_SESSION['userid']))
        {
            $membreConnect = Membre::findById($_SESSION['userid']);
            $isAdmin = $membreConnect->getAdmin();
        }
	$stagepage = 1;
	$nbhoraires = 0;
	$numberOfPages = Stage::getNumberOfPages();
	if((isset($_GET['stagepage'])&& $_GET['stagepage'] <= $numberOfPages) || (isset($_POST['stagepage'])&& $_POST['stagepage'] <= $numberOfPages))
	{
		if(isset($_GET['stagepage']))
		{
			$stagepage = $_GET['stagepage'];
		}
		else
		{
			$stagepage = $_POST['stagepage'];
		}
	}
	
	$tosave = true;
	$erreur = false;
	
	$champsverif = array('titre', 'emplacement', 'details', 'jour', 'heuredeb', 'heurefin');
	$champs;
	$debut;
	
	foreach($champsverif as $element)
	{
		if(isset($_POST[$element]))
		{
			if(is_array($_POST[$element]))
			{
				foreach($_POST[$element] as $element2)
				{
					if("jour" == $element)
					{
						$nbhoraires++;
					}
					$champs[$element][] = strip_tags($element2);
				}
			}
			else
			{
				$champs[$element]= strip_tags($_POST[$element]);
			}
			
		}
		else
		{
			$tosave = false;
			break;
		}
	}
	
	if($nbhoraires == 0)
	{
		$nbhoraires = 1;
	}
	
	if($tosave && $isAdmin)
	{
            try
            {
                $stage = new Stage($champs['titre'], $champs['emplacement'], $champs['details']);

                for ($index = 0; $index < $nbhoraires; $index++)
                {
                        $stage->addHoraire(new Horaire($champs['jour'][$index], $champs['heuredeb'][$index], $champs['heurefin'][$index]));
                }

                $stage->save($_FILES['image'], $_FILES['document']); 

                $subject = 'Nouveau stage : ' . $stage->titre();

                $headers = "From: \"Stage site Web\"<$emailfrom> \r\n";
                $headers .= "Reply-To: nac.aikido@gmail.com \r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=utf8\r\n";

                $message = $stage->getContentForEmail($parser);
                
                foreach (Membre::getMembresAbonnesStages() as $membreabostage)
                {
                    $to = $membreabostage->getEmail();
                    mail($to, $subject, $message, $headers);
                }
                echo "<div class='alert alert-success'>Stage créé.</div>";
            }
            catch(Exception $e)
            {
                    $erreur = true;
                    echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
            }

            $nbhoraires = 1;
	}
	elseif(!$isAdmin)
	{
		echo '<div class="alert alert-danger">Vous n\'êtes pas autorisé à envoyer des stages !</div>';
	}
?>
<div class="row">
	<div class="col-lg-12">
		<ul class="pager">
		  <li class="previous<?php if($stagepage == 1){echo ' disabled';} ?>">
			<?php if($stagepage > 1){echo '<a href="index.php?page=stages&stagepage=' . ($stagepage-1) . '">';}else{echo '<a href="#">';}?>&larr; Plus récents</a>
		  </li>
		  <li class="next<?php if($stagepage == $numberOfPages){echo ' disabled';} ?>">
		    <?php if($stagepage < $numberOfPages){echo '<a href="index.php?page=stages&stagepage=' . ($stagepage+1) . '">';}else{echo '<a href="#">';}?>Plus anciens &rarr;</a>
		  </li>
		</ul>
	</div>
</div>
<div class="row">
	<div class="page-header"><h2>Les stages</h2></div>
	<div class="col-lg-12">
            <?php

                    $stages = Stage::getAllStages($stagepage);
                    foreach($stages as $element)
                    {
                        $imageSrc = 'stages/images/mini/' . $element->id() . '.jpg';
                        $imageLink = 'stages/images/' . $element->id() . '.jpg';
                        $parser->parse(nl2br($element->details()));
                        $details = $parser->getAsHtml();
                        $docSrc = 'stages/documents/' . $element->id();
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
                                <div class="col-sm-2">
                                    <div class="hidden-xs"><a href="<?php echo $imageLink; ?>"><img class="img-responsive img-thumbnail img-marged" id="imgstg<?php echo $element->id(); ?>" title="Image de la news <?php echo $element->id(); ?>" alt="Image de la news <?php echo $element->id(); ?>" src="<?php echo $imageSrc; ?>" /></a></div>
                                    <div class="visible-xs"><a href="<?php echo $imageLink; ?>">Affiche du stage</a></div>
                                </div>
                                <div class="col-sm-10">
				<?php
                                }
                                else
                                {
                                ?>
                                <div class="col-sm-12">  
                                <?php
                                }
                                ?>
                                    <label>Lieu du stage</label>
                                    <p><?php echo $element->emplacement(); ?></p>
                                    <label>Détails</label>
                                    <p><?php echo $details; ?></p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-lg-12">
                                    <label>Horaires</label>
                                <?php
				foreach($element->horaires() as $horaires)
				{
                                ?>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class="input-group">
                                                        <span class="input-group-addon">Le</span>
                                                        <input type="text" class="form-control" value="<?php echo $horaires->datestage()->format('d/m/Y'); ?>" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-xs-6">
                                                            <div class="input-group">
                                                                <span class="input-group-addon">De</span>
                                                                <input type="text" class="form-control" value="<?php echo $horaires->heuredebut()->format('H:i'); ?>" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-xs-6">
                                                            <div class="input-group">
                                                                <span class="input-group-addon">à</span>
                                                                <input type="text" class="form-control" value="<?php echo $horaires->heurefin()->format('H:i'); ?>" disabled>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
					</div>
                                <?php
				}
                                ?>
				</div>
                            </div>
                            <?php
				if(file_exists($docSrc . '.pdf') || file_exists($docSrc . '.doc') || file_exists($docSrc . '.docx'))
				{
                                    $docExt = 'pdf';
                                    if(file_exists($docSrc . '.doc'))
                                    {
                                        $docExt = 'doc';
                                    }
                                    elseif(file_exists($docSrc . '.docx'))
                                    {
                                        $docExt = 'docx';
                                    }
                            ?>
                            <label>Documents associés</label>
                            <a href='<?php echo $docSrc . '.' . $docExt ?>' target='_blank'><img class='img-responsive' title='Document stage <?php echo $element->id(); ?>' alt='Document stage <?php echo $element->id(); ?>' src='img/mini/<?php echo $docExt; ?>.png' /></a>
                            <?php
                                }
                            ?>
			</div>
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
		  <li class="previous<?php if($stagepage == 1){echo ' disabled';} ?>">
			<?php if($stagepage > 1){echo '<a href="index.php?page=stages&stagepage=' . ($stagepage-1) . '">';}else{echo '<a href="#">';}?>&larr; Plus récents</a>
		  </li>
		  <li class="next<?php if($stagepage == $numberOfPages){echo ' disabled';} ?>">
		    <?php if($stagepage < $numberOfPages){echo '<a href="index.php?page=stages&stagepage=' . ($stagepage+1) . '">';}else{echo '<a href="#">';}?>Plus anciens &rarr;</a>
		  </li>
		</ul>
	</div>
</div>
<?php
    if($isAdmin == 1)
    {
?>
<div class="row">
	<div class="page-header"><h2>Insérer un stage</h2></div>
	<div class="col-lg-12">
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title">Nouveau stage</h3>
	  </div>
	  <div class="panel-body">
		<form role="form" method="post" action="index.php" enctype="multipart/form-data">
		  <div class="form-group">
			<label for="titre">Titre</label>
			<input type="text" class="form-control" id="titre" name="titre" placeholder="Un titre pour le stage" required="" <?php if($erreur){echo 'value="' . $champs['titre'] . '"';} ?>>
		  </div>
		  <div class="form-group">
			<label for="emplacement">Lieu du stage</label>
			<input type="text" class="form-control" id="emplacement" name="emplacement" placeholder="Où se déroule le stage" required="" <?php if($erreur){echo 'value="' . $champs['emplacement'] . '"';} ?>>
		  </div>
		  <div class="form-group" id="horaires">
			<label for="jour">Horaires</label>
			<?php 
				for ($index = 0; $index < $nbhoraires; $index++)
				{
					echo '<div class="row" id="ligne' . $index . '">';
					echo '<div class="col-sm-5">';
					echo '<div class="input-group form-group">';
					echo '<span class="input-group-addon">Date</span>';
					echo '<input type="text" class="form-control" placeholder="jjmmaaaa" name="jour[]" required="" ';
					if($erreur){echo 'value="' . $champs['jour'][$index] . '"';}
					echo '>';
					echo '</div>';
					echo '</div>';
					echo '<div class="col-sm-5">';
					echo '<div class="row">';
					echo '<div class="col-xs-6">';
					echo '<div class="input-group form-group">';
					echo '<span class="input-group-addon">De</span>';
					echo '<input type="text" class="form-control" placeholder="hhmm" name="heuredeb[]" required="" ';
					if($erreur){echo 'value="' . $champs['heuredeb'][$index] . '"';}
					echo '>';
					echo '</div>';
					echo '</div>';
					echo '<div class="col-xs-6">';
					echo '<div class="input-group form-group">';
					echo '<span class="input-group-addon">à</span>';
					echo '<input type="text" class="form-control" placeholder="hhmm" name="heurefin[]" required="" ';
					if($erreur){echo 'value="' . $champs['heurefin'][$index] . '"';}
					echo '>';
					echo '</div>';
					echo '</div>';
					echo '</div>';
					echo '</div>';
					if($index == ($nbhoraires-1))
					{
						echo '<div class="col-sm-2" id="gesth">';
						echo '<div class="row">';
						echo '<div class="col-xs-6">';
						echo '<div class="btn-group">';
						echo '<button type="button" class="btn btn-primary" id="addh" onclick="addHoraire(' . $nbhoraires . ')">+</button>';
						echo '</div>';
						echo '</div>';
						echo '<div class="col-xs-6">';
						echo '<div class="btn-group">';
						echo '<button type="button" class="btn btn-primary" id="remh" onclick="removeHoraire(' . $nbhoraires . ')" disabled="" >-</button>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
						echo '</div>';
					}
					echo '</div>';
				}
			
			?>
			</div>
		  <div class="form-group">
			<label for="details">Détails</label>
			<textarea class="form-control" rows="3" id="details" name="details" required=""><?php if($erreur){echo $champs['details'];}else{echo 'Les détails du stage (ligue, tarifs, professeur ...)';}?></textarea>
		  </div>
		  <div class="form-group">
			<label for="image">Affiche</label>
			<input data-validation="size" data-validation-max-size="512kb" type="file" id="image" name="image">
			<p class="help-block">Sélectionnez l'affiche du stage (jpg, png, gif).</p>
		  </div>
		  <div class="form-group">
			<label for="image">Document</label>
			<input data-validation="size" data-validation-max-size="1M" type="file" id="document" name="document">
			<p class="help-block">Sélectionnez un document lié au stage (pdf, doc, docx).</p>
		  </div>
                    <button type="submit" class="btn btn-success">Envoyer</button>
		  <input type="hidden" id="stagepage" name="stagepage" value="<?php echo $stagepage; ?>" />
		  <input type="hidden" id="page" name="page" value="stages" />
		</form>
	  </div>
	</div>
	</div>
</div>
<?php
    }
?>
