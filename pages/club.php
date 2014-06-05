<?php
    $champsverif = array('titre', 'message', 'contact');
    $champs;
    $tosave = true;

    foreach($champsverif as $element)
    {
        if(isset($_POST[$element]))
        {
            $champs[$element]= strip_tags($_POST[$element]);
        }
        else
        {
            $tosave = false;
            break;
        }
    }
    
    if($tosave)
    {
        $passage_ligne = "\n";
        $destinataire;
        switch ($champs['contact']) {
        case "profs":
          $destinataire = "nac.aikido+professeurs@gmail.com";
          break;
        case "bureau":
          $destinataire = "nac.aikido+bureau@gmail.com";
          break;
        case "webmaster":
          $destinataire = "nac.aikido+webmaster@gmail.com";
          break;
        default:
          $destinataire = "nac.aikido@gmail.com";
      }

        //=====Création du header de l'e-mail
        $header = "From: \"Contact site Web\"<$emailfrom>".$passage_ligne;
        $header .= "Reply-to: \"Webmaster\" <nac.aikido+webmaster@gmail.com>".$passage_ligne;
        $header .= $passage_ligne;
        //==========

        //=====Envoi de l'e-mail.
        mail($destinataire,$champs['titre'],$champs['message'],$header);
        //==========
        echo "<div class='alert alert-success'>Email envoyé.</div>";
    }
?>
<div class="row">
    <div class="col-lg-12">
        <h1>Le Club</h1>
        <div class="entry-summary">
            <div class="page-header"><h3>Les cours</h3></div>
            <p>Afin de pratiquer, nous vous accueillons au Dojo Paul Doumer situé face à l’ Erdre.<br>
            262 m<sup>2</sup> de tatamis sont à notre disposition pour nos ébats.<br>
            Pour découvrir, essayer ou continuer à pratiquer l’Aïkido, nous vous attendons rue des Ecole à Nort Sur Erdre à l’angle de la rue des Orionnais.
            </p>
            
            <div class="hidden-xs"><iframe style="border: 0;" src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d2698.742564470395!2d-1.499483628311158!3d47.436462919049696!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x4805f7fcebaaa1d9%3A0x2b1f512b4b1df4e6!2sRue+des+Orionnais!5e0!3m2!1sfr!2sfr!4v1392299424510" frameborder="0" height="300" width="400"></iframe></div>
            <div class="visible-xs"><a href="https://www.google.com/maps/preview?ll=47.435883,-1.497305&z=16&t=m&hl=fr&gl=FR&mapclient=embed&q=Rue+des+Orionnais+44390+Nort-sur-Erdre&source=newuser-ws" target="_blank">Plan d'accès</a></div>
            
            <h4>Horaires des cours :</h4>
            <p>
                <em>Lundi</em> 20:30 – 22:30<br />
                <em>Vendredi </em>20:45 – 22h45
            </p>

            <h4>Assister à un cours :</h4>
            <p>
                Vous souhaitez assistez à un cours en simple observateur afin de découvrir l'Aïkido et notre manière de pratiquer ?<br />
                Vous êtes les bienvenus ! (Vous serez priés d'éviter de faire du bruit afin de ne pas entâcher le déroulement normal du cours)
            </p>

            <div class="page-header"><h3>Les professeurs</h3></div>
            <div class="row text-center">
                <div class="col-sm-6">
                    Olivier Coussot – 2 <sup>ème </sup>dan BF<br />
                    <img class="img-responsive img-thumbnail img-marged" title="Olivier Coussot" alt="Olivier Coussot 2e DAN BF" src="img/membres/olivier.jpg" />
                </div>
                <div class="col-sm-6">
                    Pascal Voile – 2 <sup>ème </sup>dan BF<br />
                    <img class="img-responsive img-thumbnail img-marged" title="Pascal Voile" alt="Pascal Voile 2e DAN BF" src="img/membres/pascal.jpg" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                      <div class="panel-heading">
                          Contacter les professeurs
                      </div>
                      <div class="panel-body">
                            <form role="form" method="post" action="index.php">
                              <div class="form-group">
                                    <label for="titre">Titre</label>
                                    <input type="text" class="form-control" name="titre" placeholder="Le titre du message" required="" <?php if($erreur){echo 'value="' . $titre . '"';} ?>>
                              </div>
                              <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea class="form-control" rows="3" name="message" required=""><?php if($erreur){echo $message;}else{echo 'Insérez ici le message';}?></textarea>
                              </div>
                              <input type="hidden" name="page" value="club" />
                              <input type="hidden" name="contact" value="profs" />
                              <button type="submit" class="btn btn-success">Envoyer</button>
                            </form>
                      </div>
                    </div>
                </div>
            </div>
            <div class="page-header"><h3>Les membres du bureau</h3></div>
			<div class="row text-center">
				<div class="col-sm-12">
					Le bureau au complet<br />
					<img class="img-responsive img-thumbnail img-marged" title="Le bureau" alt="Le bureau" src="img/membres/bureau.jpg" />
				</div>
			</div>
            <div class="row text-center">
                <div class="col-sm-4">
                    Président : Emmanuel Martin<br />
                    <img class="img-responsive img-thumbnail img-marged" title="Emmanuel Martin" alt="Emmanuel Martin" src="img/membres/manu.jpg" />
                </div>
                <div class="col-sm-4">
                    Trésorière : Sophie Herrault<br />
                    <img class="img-responsive img-thumbnail img-marged" title="Sophie Herrault" alt="Sophie Herrault" src="img/membres/sophie.jpg" />
                </div>
                <div class="col-sm-4">
                    Secrétaire : Gaëlle Leparoux<br />
                    <img class="img-responsive img-thumbnail img-marged" title="Gaëlle Leparoux" alt="Gaëlle Leparoux" src="img/membres/gaelle.jpg" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                      <div class="panel-heading">
                          Contacter le bureau
                      </div>
                      <div class="panel-body">
                            <form role="form" method="post" action="index.php">
                              <div class="form-group">
                                    <label for="titre">Titre</label>
                                    <input type="text" class="form-control" name="titre" placeholder="Le titre du message" required="" <?php if($erreur){echo 'value="' . $titre . '"';} ?>>
                              </div>
                              <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea class="form-control" rows="3" name="message" required=""><?php if($erreur){echo $message;}else{echo 'Insérez ici le message';}?></textarea>
                              </div>
                              <input type="hidden" name="page" value="club" />
                              <input type="hidden" name="contact" value="bureau" />
                              <button type="submit" class="btn btn-success">Envoyer</button>
                            </form>
                      </div>
                    </div>
                </div>
            </div>
            <div class="page-header"><h3>Le webmaster</h3></div>
            <div class="row text-center">
                <div class="col-sm-12">
                    Bruno Desprez<br />
                    <img class="img-responsive img-thumbnail img-marged" title="Bruno Desprez" alt="Bruno Desprez" src="img/membres/bruno.jpg" />
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                      <div class="panel-heading">
                          Contacter le webmaster
                      </div>
                      <div class="panel-body">
                            <form role="form" method="post" action="index.php">
                              <div class="form-group">
                                    <label for="titre">Titre</label>
                                    <input type="text" class="form-control" name="titre" placeholder="Le titre du message" required="" <?php if($erreur){echo 'value="' . $titre . '"';} ?>>
                              </div>
                              <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea class="form-control" rows="3" name="message" required=""><?php if($erreur){echo $message;}else{echo 'Insérez ici le message';}?></textarea>
                              </div>
                              <input type="hidden" name="page" value="club" />
                              <input type="hidden" name="contact" value="webmaster" />
                              <button type="submit" class="btn btn-success">Envoyer</button>
                            </form>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
