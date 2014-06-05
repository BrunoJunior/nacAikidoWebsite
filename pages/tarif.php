<?php
	include_once('classes/Membre.class.php');
        if(isset($_POST['envoi']))
        {
            $champs = Membre::checkPOST($_POST);
            if(!isset($champs['error']))
            {
                try
                {
                    $membre = new Membre($champs);
                    $membre->save($_FILES['photo']); 
                    
                    foreach (Membre::getAdministrateurs() as $admin) 
                    {
                        mail($admin->getEmail(), "NAC Aïkido - Pré inscription reçue", "Vous avez reçu une préinscription qu'il faut traiter !");
                    }
                    
                    echo "<div class='alert alert-success'>Pré-inscription envoyée.</div>";
                }
                catch(Exception $e)
                {
                    $champs['error'] = $e->getMessage();
                }
            }
            
            $erreur = isset($champs['error']);

            if($erreur)
            {
                echo '<div class="alert alert-danger">' . $champs['error'] . '</div>';
            }
        }
?>
<div class="row">
	<div class="col-lg-12">
            <h1>Tarifs & Inscription</h1>
<div class="page-header"><h3>Tarifs</h3></div>
                <p>Cotisation : 110€ pour les adultes, 95€ pour les enfants.</p>
		<p>Le paiement est à réaliser en un maximum de trois chèques à l’ordre de N.A.C. Aïkido.<br>
		Le premier chèque sera d'un minimum de 50€, les suivants seront encaissés à un mois d'intervalle chacun.</p>
                <p>Attention, toute cotisation fournie ne pourra être en aucun cas remboursée, même partiellement, en cas d'abandon de la pratique en cours d'année.</p>
                <p>Pour les mineurs (de plus de 14 ans), une attestation parentale est requise. Attestation disponible plus bas sur cette page.</p>
</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<a href="doc/Inscription au NAC Aikido/autorisation-parentael2012.doc" target="_blank"><img class="img-responsive center-block" title="Autorisation parentale" alt="Autorisation parentale" src="img/carte-de-visite-word-150x150.png" /></a>
		<p class="text-center">Autorisation parentale</p>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
            <div class="page-header"><h3>Formulaire d'inscription</h3></div>
	<div class="panel panel-primary">
	  <div class="panel-heading">
		<h3 class="panel-title">Formulaire de pré-inscription</h3>
	  </div>
	  <div class="panel-body">
              *Obligatoire
		<form role="form" method="post" action="index.php" enctype="multipart/form-data">
                    <div class="page-header"><h4>Identité</h4></div>
		  <div class="form-group">
                      <div class="row">
                          <div class="col-sm-4">
                              <label for=nom">Nom*</label>
                              <input type="text" class="form-control" name="nom" placeholder="Votre nom" required="" <?php if($erreur){echo 'value="' . $champs['nom'] . '"';} ?>>
                          </div>
                          <div class="col-sm-4">
                              <label for=nom">Nom de jeune fille</label>
			<input type="text" class="form-control" name="nomjf" placeholder="Votre nom de jeune fille" <?php if($erreur){echo 'value="' . $champs['nomjf'] . '"';} ?>>
                          </div>
                          <div class="col-sm-4">
                              <label for="prenom">Prénom*</label>
			<input type="text" class="form-control" name="prenom" placeholder="Votre prénom" required="" <?php if($erreur){echo 'value="' . $champs['prenom'] . '"';} ?>>
                          </div>
                      </div>
		  </div>
                  <div class="form-group">
                      <div class="row">
                      <div class="col-sm-12">
			<label for="photo">Photo</label>
                        <input data-validation="size" data-validation-max-size="128kb" type="file" name="photo">
			<p class="help-block">Photo d'identité (jpg, png, gif) - Max 100Ko.</p>
                      </div>
                      </div>
		  </div>
		  <div class="form-group">
                      <div class="row">
                      <div class="col-sm-6">
			<label for="naissance">Date de naissance*</label>
			<input type="text" class="form-control" name="naissance" placeholder="jj/mm/aaaa" data-validation="date" data-validation-format="dd/mm/yyyy" required="" <?php if($erreur){echo 'value="' . $champs['naissance']->format('d/m/Y') . '"';} ?>>
                      </div>
                      <div class="col-sm-6">
			<label for="lieunaissance">Lieu de naissance</label>
			<input type="text" class="form-control" name="lieunaissance"  <?php if($erreur){echo 'value="' . $champs['lieunaissance'] . '"';} ?>>
                      </div>
                      </div>
		  </div>
                  <div class="form-group">
                      <div class="row">
                          <div class="col-sm-12">
                              <label for="adresse">Adresse*</label>
			<input type="text" class="form-control" name="adresse" placeholder="N° de rue, rue, code postal, ville" required="" <?php if($erreur){echo 'value="' . $champs['adresse'] . '"';} ?>>
                          </div>
                      </div>
                      <div class="row">
                          <div class="col-sm-6">
                              <label for="tel">N° de téléphone*</label>
                              <input type="tel" class="form-control" name="tel" placeholder="0000000000" data-validation-length="10" data-validation="number" required="" <?php if($erreur){echo 'value="' . $champs['tel'] . '"';} ?>>
                          </div>
                          <div class="col-sm-6">
                              <label for="email">Adresse e-mail*</label>
                              <input type="email" class="form-control" name="email" placeholder="@" data-validation="email" required="" <?php if($erreur){echo 'value="' . $champs['email'] . '"';} ?>>
                          </div>
                      </div>
                  </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="profession">Profession</label>
                                <input type="text" class="form-control" name="profession" <?php if($erreur){echo 'value="' . $champs['profession'] . '"';} ?>>
                            </div>
                            <div class="col-sm-6">
                                <label for="gpsang">Groupe sanguin</label>
                                <select name="gpsang" class="form-control">
                                    <?php 
                                        foreach (Membre::$GPS_SANG as $gpsang) 
                                        {
                                    ?>
                                    <option value="<?php echo $gpsang ?>" <?php if($erreur && $champs['gpsang'] == $gpsang ) {echo 'selected=""';} ?>><?php echo $gpsang ?></option>
                                    <?php
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>
		  </div>
                    <div class="page-header"><h4>Informations de connexion</h4></div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="password">Choisissez un mot de passe*</label>
                                <input type="password" class="form-control" name="password" required="">
                            </div>
                            <div class="col-sm-6">
                                <label for="password2">Retapez le mot de passe*</label>
                                <input type="password" class="form-control" name="password2" required="">
                            </div>
                        </div>
		  </div>
                    <div class="page-header"><h4>Personne à contacter en cas de problème</h4></div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="nompers">Nom</label>
                                <input type="text" class="form-control" name="nompers" <?php if($erreur){echo 'value="' . $champs['nompers'] . '"';} ?>>
                            </div>
                            <div class="col-sm-4">
                                <label for="prenompers">Prénom</label>
                                <input type="text" class="form-control" name="prenompers" <?php if($erreur){echo 'value="' . $champs['prenompers'] . '"';} ?>>
                            </div>
                            <div class="col-sm-4">
                                <label for="telpers">N° de téléphone</label>
                                <input type="tel" class="form-control" name="telpers" <?php if($erreur){echo 'value="' . $champs['telpers'] . '"';} ?>>
                            </div>
                        </div>
		  </div>
                  <button type="submit" name="envoi" class="btn btn-success">Envoyer</button>
		  <input type="hidden" id="page" name="page" value="tarif" />
		</form>
	  </div>
	</div>
	</div>
</div>
