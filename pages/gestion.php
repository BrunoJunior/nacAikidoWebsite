<?php
    include_once('classes/Membre.class.php');
    // On provient de la page modmembre
    
    $isAdmin = 0;
    
    if(isset($_SESSION['userid']))
    {
        $membreConnect = Membre::findById($_SESSION['userid']);
        $isAdmin = $membreConnect->getAdmin();
    }
    
    if($isAdmin == 0)
    {
        echo '<div class="alert alert-danger">Vous n\'êtes pas autorisé à voir cette page !</div>';
    }
    else
    {
    $champs = array();
    if(isset($_POST['modifier']))
    {
	$checkpwd = false;
        $champs = array();
        if(!isset($_POST['id']) || empty($_POST['id']))
        {
            $champs['error'] = 'ID obligatoire !';
        }
	else
	{
	    $membre = Membre::findById($_POST['id']);

	    $password = $membre->getPassword();
	    if(isset($_POST['oldpassword']) && isset($_POST['password']) && isset($_POST['password2']))
	    {
		if(!empty($_POST['oldpassword']) && !empty($_POST['password']) && !empty($_POST['password2']))
		{
		    $oldpassword = md5($_POST['oldpassword']);
		    if($password != $oldpassword)
		    {
			$champs['error'] = 'Ancien mot de passe incorrect !';
		    }
		    else
		    {
			$checkpwd = true;
		    }
		}
	    }

	    $champs = Membre::checkPOST($_POST, $checkpwd);
	    if(!$isAdmin && $membre->getAdmin() != $champs['admin'])
	    {
		$champs['admin'] = $membre->getAdmin();
	    }
	}

        if(!isset($champs['error']))
        {
            try
            {
                $membre->modifier($champs, $checkpwd);
                $effacerPhoto = isset($_POST['photochange']) && 'effacer' == $_POST['photochange'];
                
                $membre->save($_FILES['photo'], $effacerPhoto);
                echo "<div class='alert alert-success'>Modifications effectuées.</div>";
            }
            catch(Exception $e)
            {
                $champs['error'] = $e->getMessage();
            }
        }
    }
    else if(isset($_GET['action']))
    {
        if(isset($_GET['id']))
        {
            $action = $_GET['action'];
            $membre = Membre::findById($_GET['id']);
            if($action == 'valider')
            {
                $membre->valider();
            }
            elseif($action == 'invalider')
            {
                $membre->invalider();
            }
            elseif($action == 'desinscrire')
            {
                $membre->desinscrire();
            }
            else
            {
                $champs['error'] = 'Action inconnue !';
            }
        }
        else
        {
            $champs['error'] = 'ID obligatoire !';
        }
    }
    
    if(isset($champs['error']))
    {
        echo '<div class="alert alert-danger">' . $champs['error'] . '</div>';
    }
    
    if(!isset($_POST['modifier']) || !isset($champs['error']))
    {
        $membres_inact = Membre::getMembresInactifs();
        $membres_actifs = Membre::getMembresActifs();
        $membres_desinscrits = Membre::getMembresDesinscrits();
        $membres_invalides = Membre::getMembresInvalides();
?>
<div class="page-header"><h3>Membres en attente d'inscription</h3></div>
<?php 
        afficherMembres($membres_inact);
?>
<div class="page-header"><h3>Membres inscrits</h3></div>
<?php 
        afficherMembres($membres_actifs);
?>
<div class="page-header"><h3>Membres désinscrits</h3></div>
<?php 
        afficherMembres($membres_desinscrits);
?>
<div class="page-header"><h3>Membres invalidés</h3></div>
<?php 
        afficherMembres($membres_invalides);
    }
    else 
    {
        include("modmembre.php");
    }
    }
?>
<?php
    function afficherMembres($membres)
    {
        foreach ($membres as $membre)
        {
            $prenomnom = $membre->getPrenom().' '.$membre->getNom();
            $imageSrc = 'img/membres/' . $membre->getId() . '.jpg';
            if(!file_exists($imageSrc))
            {
                $imageSrc = 'img/membres/unknow.jpg';
            }
            $nomJf = $membre->getNomjf();
            $lieunaissance = $membre->getLieunaisance();
            $profession = $membre->getProfession();
            $gpsang = $membre->getGpsang();
            $nompers = $membre->getNompers();
?>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                <?php echo $prenomnom ?>
                                <a class="btn btn-primary" href="index.php?page=modmembre&id=<?php echo $membre->getId() ?>" role="button"><span class="glyphicon glyphicon-pencil"></span> Modifier</a>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-sm-2">
                                    <img class="hidden-xs img-responsive img-thumbnail img-marged" title="<?php echo $prenomnom ?>" alt="<?php echo $prenomnom ?>" src="<?php echo $imageSrc ?>" />
                                </div>
                                <div class="col-sm-10">
<?php       if(!empty($nomJf)){?>
                                    <label>Nom de jeune fille</label>
                                    <p><?php echo $nomJf ?></p>
<?php       }?>
                                    <label>Date de naissance</label>
                                    <p><?php echo $membre->getNaissance()->format('d/m/Y')?></p>
<?php       if(!empty($lieunaissance)){?>
                                    <label>Lieu de naissance</label>
                                    <p><?php echo $lieunaissance?></p>
<?php       }?>
                                    <label>Adresse</label>
                                    <p><?php echo $membre->getAdresse()?></p>
                                    <label>N° de téléphone</label>
                                    <p><?php echo $membre->getTel()?></p>
                                    <label>Adresse e-mail</label>
                                    <p><?php echo $membre->getEmail()?></p>
<?php       if(!empty($profession)){?>
                                    <label>Profession</label>
                                    <p><?php echo $profession?></p>
<?php       }?>
<?php       if(!empty($gpsang)){?>
                                    <label>Groupe sanguin</label>
                                    <p><?php echo $gpsang?></p>
<?php       }?>
<?php       if(!empty($nompers)){?>
                                    <div class="page-header"><h4>Personne à contacter en cas d'urgence</h4></div>
                                    <label>Nom</label>
                                    <p><?php echo $nompers?></p>
                                    <label>Prénom</label>
                                    <p><?php echo $membre->getPrenompers()?></p>
                                    <label>N° de téléphone</label>
                                    <p><?php echo $membre->getTelpers()?></p>
<?php       }?>
                                </div>
                            </div>
<?php       if($membre->getActif() == 0){?>
                            <div class="row text-center">
                                <div class="col-sm-6">
                                    <a class="btn btn-success" href="index.php?page=gestion&id=<?php echo $membre->getId() ?>&action=valider" role="button"><span class="glyphicon glyphicon-ok"></span> Valider l'inscription</a>
                                </div>
                                <div class="col-sm-6">
                                    <a class="btn btn-danger" href="index.php?page=gestion&id=<?php echo $membre->getId() ?>&action=invalider" role="button"><span class="glyphicon glyphicon-remove"></span> Invalider l'inscription</a>
                                </div>
                            </div>
<?php       }elseif($membre->getActif() == 1){?>
                            <div class="row text-center">
                                <div class="col-sm-12">
                                    <a class="btn btn-danger" href="index.php?page=gestion&id=<?php echo $membre->getId() ?>&action=desinscrire" role="button"><span class="glyphicon glyphicon-remove"></span> Désinscrire</a>
                                </div>
                            </div>
<?php       }elseif($membre->getActif() == 3){?>
                            <div class="row text-center">
                                <div class="col-sm-12">
                                    <a class="btn btn-success" href="index.php?page=gestion&id=<?php echo $membre->getId() ?>&action=valider" role="button"><span class="glyphicon glyphicon-ok"></span> Réinscrire</a>
                                </div>
                            </div>
<?php       }elseif($membre->getActif() == 2){?>
                            <div class="row text-center">
                                <div class="col-sm-12">
                                    <a class="btn btn-success" href="index.php?page=gestion&id=<?php echo $membre->getId() ?>&action=valider" role="button"><span class="glyphicon glyphicon-ok"></span> Valider</a>
                                </div>
                            </div>
<?php       }?>
                        </div>
                    </div>
                </div>
            </div>
<?php
        }
    }
