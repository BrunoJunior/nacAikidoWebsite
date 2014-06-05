<?php
    include_once('classes/Membre.class.php');
    
    $isAdmin = 0;
    
    if(isset($_SESSION['userid']))
    {
        $membreConnect = Membre::findById($_SESSION['userid']);
        $isAdmin = $membreConnect->getAdmin();
    }
    
    if(isset($_GET['id']) || isset($_POST['id']))
    {
        if(isset($_GET['id']))
        {
            $id = $_GET['id'];
        }
        else
        {
            $id = $_POST['id'];
        }
        
        if($isAdmin != 1 && (!isset($membreConnect) || $membreConnect->getId() != $id))
        {
            echo '<div class="alert alert-danger">Vous n\'êtes pas autorisé à voir cette page !</div>';
        }
        else
        {
        $membre = Membre::findById($id);
        if(!empty($membre))
        {
            $imageSrc = 'img/membres/' . $membre->getId() . '.jpg';
            if(!file_exists($imageSrc))
            {
                $imageSrc = 'img/membres/unknow.jpg';
            }
?>

<div class="row">
	<div class="col-lg-12">
            <div class="page-header"><h3>Modification d'un membre</h3></div>
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title">Modification des informations</h3>
	  </div>
	  <div class="panel-body">
              *Obligatoire
		<form role="form" method="post" action="index.php" enctype="multipart/form-data">
                    <div class="page-header"><h4>Identité</h4></div>
		  <div class="form-group">
                      <div class="row">
                          <div class="col-sm-4">
                              <label for=nom">Nom*</label>
                              <input type="text" class="form-control" name="nom" placeholder="Votre nom" required="" value="<?php echo $membre->getNom(); ?>">
                          </div>
                          <div class="col-sm-4">
                              <label for=nom">Nom de jeune fille</label>
			<input type="text" class="form-control" name="nomjf" placeholder="Votre nom de jeune fille" value="<?php echo $membre->getNomjf(); ?>">
                          </div>
                          <div class="col-sm-4">
                              <label for="prenom">Prénom*</label>
                              <input type="text" class="form-control" name="prenom" placeholder="Votre prénom" required="" value="<?php echo $membre->getPrenom(); ?>">
                          </div>
                      </div>
		  </div>
                  <div class="form-group">
                      <div class="row">
                      <div class="col-sm-12">
                        <div class="pull-left">
                            <img class="img-responsive img-thumbnail img-marged" title="Photo membre" alt="Photo membre" src="<?php echo $imageSrc ?>" />
                        </div>
			<label for="photo">Photo</label>
                        <p>
                            <input type="radio" name="photochange" value="rien" checked="">Laisser<br />
                            <input type="radio" name="photochange" value="effacer">Effacer<br />
                            <input type="radio" id="rempphoto" name="photochange" value="remplacer">Remplacer
                        </p>
                        <input data-validation="size" data-validation-max-size="128kb" type="file" id="photo" name="photo">
			<p class="help-block">Photo d'identité (jpg, png, gif) - Max 100Ko.</p>
                      </div>
                      </div>
		  </div>
		  <div class="form-group clear">
                      <div class="row">
                      <div class="col-sm-6">
			<label for="naissance">Date de naissance*</label>
                        <input type="text" class="form-control" name="naissance" placeholder="jj/mm/aaaa" data-validation="date" data-validation-format="dd/mm/yyyy" required="" value="<?php echo $membre->getNaissance()->format('d/m/Y'); ?>">
                      </div>
                      <div class="col-sm-6">
			<label for="lieunaissance">Lieu de naissance</label>
                        <input type="text" class="form-control" name="lieunaissance" value="<?php echo $membre->getLieunaisance(); ?>">
                      </div>
                      </div>
		  </div>
                  <div class="form-group">
                      <div class="row">
                          <div class="col-sm-12">
                              <label for="adresse">Adresse*</label>
                              <input type="text" class="form-control" name="adresse" placeholder="N° de rue, rue, code postal, ville" required="" value="<?php echo $membre->getAdresse(); ?>">
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-sm-6">
                              <label for="tel">N° de téléphone*</label>
                              <input type="tel" class="form-control" name="tel" placeholder="0000000000" data-validation-length="10" data-validation="number" required="" value="<?php echo $membre->getTel(); ?>">
                          </div>
                          <div class="col-sm-6">
                              <label for="email">Adresse e-mail*</label>
                              <input type="email" class="form-control" name="email" placeholder="@" data-validation="email" required="" value="<?php echo $membre->getEmail(); ?>">
                          </div>
                      </div>
                  </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="profession">Profession</label>
                                <input type="text" class="form-control" name="profession" value="<?php echo $membre->getProfession(); ?>">
                            </div>
                            <div class="col-sm-6">
                                <label for="gpsang">Groupe sanguin</label>
                                <select name="gpsang" class="form-control">
                                    <?php 
                                        foreach (Membre::$GPS_SANG as $gpsang) 
                                        {
                                    ?>
                                    <option value="<?php echo $gpsang ?>" <?php if($membre->getGpsang() == $gpsang ) {echo 'selected=""';} ?>><?php echo $gpsang ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
		  </div>
		  <div class="page-header"><h4>Information de compte</h4></div>
			<div class="row">
                            <div class="col-sm-4">
                                <label for="oldpassword">Ancien mot de passe</label>
                                <input type="password" class="form-control" name="oldpassword">
                            </div>
                            <div class="col-sm-4">
                                <label for="password">Nouveau mot de passe</label>
                                <input type="password" class="form-control" name="password">
                            </div>
                            <div class="col-sm-4">
                                <label for="password2">Retapez le mot de passe</label>
                                <input type="password" class="form-control" name="password2">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
				<div class="checkbox">
				    <label>
                                        <input type="checkbox" name="abonews" <?php if($membre->getAbonews() == 1){echo 'checked=""';} ?>> Abonnement aux flux de news
				    </label>
				</div>
			    </div>
                            <div class="col-sm-6">
				<div class="checkbox">
				    <label>
                                        <input type="checkbox" name="abostages" <?php if($membre->getAbostages() == 1){echo 'checked=""';} ?>> Abonnement au flux de stages
				    </label>
				</div>
			    </div>
			</div>
			<div class="row">
                            <div class="col-sm-12">
				<div class="checkbox">
				    <label>
				      <input type="checkbox" name="admin" <?php if(!$isAdmin){echo 'disabled=""';} if($membre->getAdmin() == 1){echo 'checked=""';} ?>> Administrateur
				    </label>
				</div>
			    </div>
			</div>
                    <div class="page-header"><h4>Personne à contacter en cas de problème</h4></div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="nompers">Nom</label>
                                <input type="text" class="form-control" name="nompers" value="<?php echo $membre->getNompers(); ?>">
                            </div>
                            <div class="col-sm-4">
                                <label for="prenompers">Prénom</label>
                                <input type="text" class="form-control" name="prenompers" value="<?php echo $membre->getPrenompers(); ?>">
                            </div>
                            <div class="col-sm-4">
                                <label for="telpers">N° de téléphone</label>
                                <input type="tel" class="form-control" name="telpers" value="<?php echo $membre->getTelpers(); ?>">
                            </div>
                        </div>
		  </div>
                    <button type="submit" name="modifier" class="btn btn-success">Modifier</button>
                    <input type="hidden" id="page" name="page" value="gestion" />
                    <input type="hidden" name="id" value="<?php echo $membre->getId(); ?>" />
		</form>
	  </div>
	</div>
	</div>
</div>
<?php 
    }
    else {
        ?>
<div class="alert alert-danger">Membre inconnu !</div>
<?php
        }
    }
    }
