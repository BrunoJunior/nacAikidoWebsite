<?php
include_once('classes/Utils.class.php');
include_once('classes/DossierPhoto.class.php');
include_once('classes/Membre.class.php');
        
        $isAdmin = 0;
    
        if(isset($_SESSION['userid']))
        {
            $membreConnect = Membre::findById($_SESSION['userid']);
            $isAdmin = $membreConnect->getAdmin();
        }

$tblPhotos = $_FILES['photo'];
$photopage = 1;
$nbphotos = 1;
$numberOfPages = DossierPhoto::getNumberOfPages();
if((isset($_GET['photopage'])&& $_GET['photopage'] <= $numberOfPages) || (isset($_POST['photopage'])&& $_POST['photopage'] <= $numberOfPages))
{
    if(isset($_GET['photopage']))
    {
        $photopage = $_GET['photopage'];
    }
    else
    {
        $photopage = $_POST['photopage'];
    }
}

$tosave = isset($tblPhotos) && count($tblPhotos) > 0;
$erreur = false;

$champsverif = array('dossier');
$champs;

if($tosave)
{
    foreach($champsverif as $element)
    {
        $postElem = $_POST[$element];
        if(isset($postElem))
        {
            if(is_array($postElem))
            {
                foreach($postElem as $element2)
                {
                    $champs[$element][] = strip_tags($element2);
                }
            }
            else
            {
                $champs[$element]= strip_tags($postElem);
            }
        }
        else
        {
            $tosave = false;
            break;
        }
    }
}
if($tosave && $isAdmin)
{
    try
    {
        $newDoss = $champs['dossier'][0];
        $oldDoss = $champs['dossier'][1];
        
        if(empty($newDoss) && empty($oldDoss))
        {
            throw new Exception('Définissez un nouveau dossier ou sélectionnez un dossier existant !');
        }
        
        $dosName = $oldDoss;
        if(!empty($newDoss))
        {
            $dosName = $newDoss;
        }
        
        $exist = empty($newDoss) && !empty($oldDoss);
        if(!$exist)
        {
            foreach (DossierPhoto::getAllDossiers() as $dossier)
            {
                if($newDoss == $dossier->titre())
                {
                    $exist = true;
                    break;
                }
            }
        }
        
        $dossier = new DossierPhoto($exist);
        $dossier->setTitre($dosName);
        $dossier->setPhotos($tblPhotos);
        $dossier->save();
        
        echo "<div class='alert alert-success'>Photos ajoutées.</div>";
    }
    catch(Exception $e)
    {
        $erreur = true;
        echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
    }
}
elseif(!$isAdmin)
{
	echo '<div class="alert alert-danger">Vous n\'êtes pas autorisé à envoyer des photos !</div>';
}

$dossiers = DossierPhoto::getAllDossiersForPage($photopage);
?>
<div class="row">
    <div class="col-lg-12">
        <ul class="pager">
            <li class="previous<?php if($photopage == 1){echo ' disabled';} ?>">
                <?php if($photopage > 1){echo '<a href="index.php?page=photos&photopage=' . ($photopage-1) . '">';}else{echo '<a href="#">';}?>&larr; Plus récentes</a>
            </li>
            <li class="next<?php if($photopage == $numberOfPages){echo ' disabled';} ?>">
                <?php if($photopage < $numberOfPages){echo '<a href="index.php?page=photos&photopage=' . ($photopage+1) . '">';}else{echo '<a href="#">';}?>Plus anciennes &rarr;</a>
            </li>
        </ul>
    </div>
</div>
<div class="row">
	<div class="col-lg-12">
		<h1>Photos</h1>
<?php
foreach ($dossiers as $dossier)
{
    $nbFichier = 0;
    echo '<h3>' . $dossier->titre() . '</h3>';
    if($dossier2 = opendir($dossier->getPathfile()))
    {
        while($fichier2 = readdir($dossier2))
        {
            if($fichier2 != '.' && $fichier2 != '..' && $fichier2 != 'miniatures')
            {
                $pathmax = $dossier->getPathfile().'/'.$fichier2;
                $pathmini = $dossier->getPathfile().'/miniatures/'.$fichier2;

                if(($nbFichier % DossierPhoto::NB_PHOTOS_LIGNE) == 0)
                {
                    if($nbFichier > 0)
                    {
                        echo '</div>';
                    }
                    echo '<div class="row">';
                }
                echo "<div class='col-sm-".DossierPhoto::TAILLE()."'><a href='$pathmax'><img class='img-responsive center-block img-thumbnail img-marged' title='$fichier2' alt='$fichier2' src='$pathmini' /></a></div>";
                $nbFichier = $nbFichier +1;
            }
        }
        closedir($dossier2);
    }
    echo '</div>';

}
?>
	</div>
</div>
<div class="row">
    <div class="col-lg-12">
        <ul class="pager">
            <li class="previous<?php if($photopage == 1){echo ' disabled';} ?>">
                <?php if($photopage > 1){echo '<a href="index.php?page=photos&photopage=' . ($photopage-1) . '">';}else{echo '<a href="#">';}?>&larr; Plus récentes</a>
            </li>
            <li class="next<?php if($photopage == $numberOfPages){echo ' disabled';} ?>">
                <?php if($photopage < $numberOfPages){echo '<a href="index.php?page=photos&photopage=' . ($photopage+1) . '">';}else{echo '<a href="#">';}?>Plus anciennes &rarr;</a>
            </li>
        </ul>
    </div>
</div>
<?php
    if($isAdmin == 1)
    {
?>
<div class="row">
    <div class="page-header"><h2>Insérer des photos</h2></div>
    <div class="col-lg-12">
        <div class="panel panel-primary">
          <div class="panel-heading">
                <h3 class="panel-title">Nouvelles photos</h3>
          </div>
          <div class="panel-body">
                <form role="form" method="post" action="index.php" enctype="multipart/form-data">
                  <div class="form-group">
                        <label for="dossier">Nouveau dossier</label>
                        <input type="text" class="form-control" id="dossiertxt" name="dossier[]" placeholder="Le nom du dossier" <?php if($erreur){echo 'value="' . $titre . '"';} ?>>
                        <label for="dossier">Ou dossier existant</label>
                        <select name="dossier[]" id="dossierlst" class="form-control" >
                            <option value=""/>
                            <?php
                            foreach (DossierPhoto::getAllDossiers() as $dossier)
                            {
                                echo '<option value="' . $dossier->titre() . '"';
                                if($dossier->titre() == $champs['dossier'])
                                {
                                    echo ' selected';
                                } 
                                echo '>' . $dossier->titre() . '</option>';
                            }
                            ?>
                        </select>
                  </div>
                  <div class="form-group">
                        <label for="photo">Photos</label>
                        <div class="row" id="ligne0"><div class="col-lg-12"><div class="form-group"><input type="file" name="photo[]" id="file0" required=""><p class="help-block" id="help">Sélectionnez les photos (jpg, png, gif).</p></div></div></div>
                        <div class="row">
                            <div class="col-xs-1">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" id="addp" onclick="addPhoto(<?php echo $nbphotos; ?>)">+</button>
                                </div>
                            </div>
                            <div class="col-xs-1">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" id="remp" onclick="removePhoto(<?php echo $nbphotos; ?>)" disabled="">-</button>
                                </div>
                            </div>
                        </div>
                  </div>
                    <button type="submit" class="btn btn-success">Envoyer</button>
                  <input type="hidden" id="page" name="page" value="photos" />
                  </div>
                </form>
          </div>
        </div>
    </div>
<?php
    }
?>
</div>
