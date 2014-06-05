<?php
    include_once('classes/Membre.class.php');
    
    if(isset($_SESSION['userid']) && isset($_GET['action']) && $_GET['action'] == 'deconnexion')
    {
        unset($_SESSION['userid']);
        session_destroy();
    }
    
    // on teste si le visiteur a soumis le formulaire de connexion
    if (isset($_POST['connexion']))
    {
        if ((isset($_POST['email']) && !empty($_POST['email'])) && (isset($_POST['password']) && !empty($_POST['password'])))
        {
            $membre = Membre::connect($_POST['email'], $_POST['password']);

            // si on obtient une réponse, alors l'utilisateur est un membre
            if (isset($membre) && !empty($membre))
            {
                $_SESSION['username'] = $membre->getEmail();
                $_SESSION['userid'] = $membre->getId();
            }
            else
            {
                $erreur = 'Veuillez vérifier vos informations de connexion.';
            }
        }
        else
        {
            $erreur = 'Au moins un des champs est vide.';
        }
    }
    elseif(isset($_POST['pertemdp']))
    {
        if (isset($_POST['email']))
        {
            $membretrouve = Membre::findByEmail($_POST['email']);
            if(empty($membretrouve))
            {
                $erreur = 'Cette adresse email est inconnue.';
            }
            else
            {
                $membretrouve->randomPass();
                echo '<div class="alert alert-success">Votre nouveau mot de passe vous a été envoyé par e-mail.</div>';
            }
        }
        else
        {
            $erreur = 'Veuillez fournir votre adresse e-mail.';
        }
    }
    else
    {
        if(isset($_SESSION['userid']))
        {
            $membre = Membre::findById($_SESSION['userid']);
        }
    }
    
    if(!isset($_SESSION['userid']) || (!isset($_POST['connexion']) && !isset($_SESSION['userid'])) || isset($erreur))
    {
        if(isset($erreur))
        {
            echo '<div class="alert alert-danger">' . $erreur . '</div>';
        }
?>

<div class="row">
    <div class="col-lg-12">
        <div class="page-header"><h3>Connexion à l'espace membre</h3></div>
	<div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Connexion</h3>
            </div>
            <div class="panel-body text-center">
                <form class="form-inline" role="form" method="post" action="index.php">
                    <div class="form-group">
                      <label class="sr-only" for="email">Adresse e-mail</label>
                      <input type="email" class="form-control" id="email" name="email" placeholder="Votre adresse email" data-validation="email" required="">
                    </div>
                    <div class="form-group">
                      <label class="sr-only" for="password">Password</label>
                      <input type="password" class="form-control" name="password" placeholder="Mot de passe" required="">
                    </div>
                    <button type="submit" name="connexion" class="btn btn-success">Connexion</button>
		  <input type="hidden" id="page" name="page" value="connexion" />
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="page-header"><h3>Oubli de mot de passe</h3></div>
	<div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Je suis tête en l'air et j'ai oublié mon mot de passe</h3>
            </div>
            <div class="panel-body text-center">
                <form class="form-inline" role="form" method="post" action="index.php">
                    <div class="form-group">
                      <label class="sr-only" for="email">Adresse e-mail</label>
                      <input type="email" class="form-control" id="email" name="email" placeholder="Votre adresse email" data-validation="email" required="">
                    </div>
                    <button type="submit" name="pertemdp" class="btn btn-success">Envoyer</button>
		  <input type="hidden" id="page" name="page" value="connexion" />
                </form>
            </div>
        </div>
    </div>
</div>
<?php
    }
    else
    {
?>
<div class="page-header"><h3>Votre fiche de membre</h3></div>
<?php
        afficherMembre($membre);
    }
?>
<?php
    function afficherMembre($membre)
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
                                    <div class="page-header"><h4>Personne à contacter en cas d\'urgence</h4></div>
                                    <label>Nom</label>
                                    <p><?php echo $nompers?></p>
                                    <label>Prénom</label>
                                    <p><?php echo $membre->getPrenompers()?></p>
                                    <label>N° de téléphone</label>
                                    <p><?php echo $membre->getTelpers()?></p>
<?php       }?>
                                </div>
                            </div>
                            <div class="row text-center">
                                <div class="col-sm-12">
                                    <a class="btn btn-danger" href="index.php?page=connexion&action=deconnexion" role="button"><span class="glyphicon glyphicon-remove"></span> Déconnexion</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
<?php
    }
?>