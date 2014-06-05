<?php
	include_once('Connection.class.php');
	include_once('Utils.class.php');

	class Membre
	{
		private $id;
		private $nom;
		private $prenom;
		private $nomjf;
		private $naissance;
		private $adresse;
                private $tel;
                private $email;
                private $profession;
                private $gpsang;
                private $nompers;
                private $prenompers;
                private $telpers;
                private $lieunaissance;
                private $actif;
                private $champmodif = array();
                private $admin;
                private $password;
                private $abonews;
                private $abostages;
                
                public static $GPS_SANG = array('', 'AB+', 'AB-', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-');
                private static $MAP_STATIC = array('en attente', 'actif', 'invalide', 'désinscrit');
		
		const INSERT = 'INSERT INTO membres(nom, prenom, nomjf, naissance, adresse, tel, email, profession, gpsang, nompers, prenompers, telpers, lieunaissance, password) VALUES(:nom, :prenom, :nomjf, :naissance, :adresse, :tel, :email, :profession, :gpsang, :nompers, :prenompers, :telpers, :lieunaissance, :password)';
		const SELECT_BY_ACTIF = 'SELECT * from membres WHERE actif = :actif';
                const SELECT_BY_ID = 'SELECT * from membres WHERE id = :id';
                const SELECT_BY_EMAIL = 'SELECT * from membres WHERE email = :email';
                const SELECT_BY_ABO_NEWS = 'SELECT * from membres WHERE abonews = 1 AND actif = 1';
                const SELECT_BY_ABO_STAGES = 'SELECT * from membres WHERE abostages = 1 AND actif = 1';
		const SELECT_COUNT = 'SELECT COUNT(*) as nummembres from membres';
                const UPDATE_ACTIVITY = 'UPDATE membres SET actif = :actif WHERE id = :id';
                const CONNECT = 'SELECT * from membres WHERE email = :email AND password = :password AND actif = 1';
                const SELECT_ADMINS = 'SELECT * from membres WHERE actif = 1 AND admin = 1';
		
		public function __construct()
		{
                    $ctp = func_num_args();
                    $args = func_get_args();

                    if($ctp == 1)
                    {
                        $this->nom = $args[0]['nom'];
                        $this->prenom = $args[0]['prenom'];
                        $this->nomjf = $args[0]['nomjf'];
                        $this->naissance = $args[0]['naissance'];
                        $this->adresse = $args[0]['adresse'];
                        $this->tel = $args[0]['tel'];
                        $this->email = $args[0]['email'];
                        $this->profession = $args[0]['profession'];
                        $this->gpsang = $args[0]['gpsang'];
                        $this->prenompers = $args[0]['prenompers'];
                        $this->nompers = $args[0]['nompers'];
                        $this->telpers = $args[0]['telpers'];
                        $this->lieunaissance = $args[0]['lieunaissance'];
                        $this->password = $args[0]['password'];
                        $this->admin = $args[0]['admin'];
                        
                        if(isset($args[0]['id']))
                        {
                            $this->id = $args[0]['id'];
                        }
                        
                        if(isset($args[0]['abonews']))
                        {
                            $this->abonews = $args[0]['abonews'];
                        }
                        else
                        {
                            $this->abonews = 1;
                        }
                        
                        if(isset($args[0]['abostages']))
                        {
                            $this->abostages = $args[0]['abostages'];
                        }
                        else
                        {
                            $this->abostages = 1;
                        }
                        
                        if(isset($args[0]['actif']))
                        {
                            $this->actif = $args[0]['actif'];
                        }
                        else
                        {
                            $this->actif = 0;
                        }
                    }
		}
                
                public function getId() {
                    return $this->id;
                }

                public function getNom() {
                    return $this->nom;
                }

                public function getPrenom() {
                    return $this->prenom;
                }

                public function getNomjf() {
                    return $this->nomjf;
                }

                public function getNaissance() {
                    return $this->naissance;
                }

                public function getAdresse() {
                    return $this->adresse;
                }

                public function getTel() {
                    return $this->tel;
                }

                public function getEmail() {
                    return $this->email;
                }

                public function getProfession() {
                    return $this->profession;
                }

                public function getGpsang() {
                    return $this->gpsang;
                }

                public function getNompers() {
                    return $this->nompers;
                }
                
                public function getPrenompers() {
                    return $this->prenompers;
                }

                public function getTelpers() {
                    return $this->telpers;
                }

                public function getLieunaisance() {
                    return $this->lieunaissance;
                }

                public function getActif() {
                    return $this->actif;
                }
                
                public function getAdmin() {
                    return $this->admin;
                }
                
                public function getPassword() {
                    return $this->password;
                }
                
                public function getAbonews() {
                    return $this->abonews;
                }

                public function getAbostages() {
                    return $this->abostages;
                }

                private function savePhoto(&$photo)
                {
                    if (isset($photo) AND $photo['error'] == 0)
                    {
                        // Testons si le fichier n'est pas trop gros
                        if ($photo['size'] <= 100000)
                        {
                            // Testons si l'extension est autorisée
                            $infosfichier = pathinfo($photo['name']);
                            $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
                            if (in_array(strtolower($infosfichier['extension']), $extensions_autorisees))
                            {
                                // On peut valider le fichier et le stocker définitivement
                                $urlTmp = 'img/membres/tmp/' . $this->id . '.' . strtolower($infosfichier['extension']);
                                $urlNorm = 'img/membres/' . $this->id . '.jpg';
                                // Deplacement en temp
                                move_uploaded_file($photo['tmp_name'], $urlTmp);
                                // Conversion en jpg et deplacement dans bonne url
                                Utils::darkroom($urlTmp, $urlNorm, 0, 300);

                                // Get dimensions of the original image
                                list($current_width, $current_height) = getimagesize($urlNorm);

                                // This will be the final size of the image (e.g. how many pixels
                                // left and down we will be going)
                                $crop_width = 265;
                                $crop_height = 300;
                                
                                // The x and y coordinates on the original image where we
                                // will begin cropping the image
                                $left = ceil(($current_width - $crop_width) / 2);
                                $top = 0;

                                // Resample the image
                                $canvas = imagecreatetruecolor($crop_width, $crop_height);
                                $current_image = imagecreatefromjpeg($urlNorm);
                                
                                imagecopy($canvas, $current_image, 0, 0, $left, $top, ($current_width - $left), $current_height);
                                imagejpeg($canvas, $urlNorm, 75);
                                
                                imagedestroy($canvas);
                                imagedestroy($current_image);

                                unlink($urlTmp);
                            }
                        }
                    }
                }
                
		public function save(&$photo = null, $effacerPhoto = false)
		{
                    $chgtPhoto = $effacerPhoto || (isset($photo) && $photo['error'] == 0);
                    if(empty($this->id))
                    {
                        $bdd = Connection::getConnection();
                        $req = $bdd->prepare(self::INSERT);
                        $req->bindValue('nom', $this->nom);
                        $req->bindValue('prenom', $this->prenom);
                        $req->bindValue('nomjf', $this->nomjf);
                        $req->bindValue('naissance', $this->naissance->format('Y-m-d'));
                        $req->bindValue('adresse', $this->adresse);
                        $req->bindValue('tel', $this->tel);
                        $req->bindValue('email', $this->email);
                        $req->bindValue('profession', $this->profession);
                        $req->bindValue('gpsang', $this->gpsang);
                        $req->bindValue('nompers', $this->nompers);
                        $req->bindValue('prenompers', $this->prenompers);
                        $req->bindValue('telpers', $this->telpers);
                        $req->bindValue('lieunaissance', $this->lieunaissance);
                        $req->bindValue('password', $this->password);
                        $req->execute() or die(print_r($req->errorInfo()));
                        $this->id = $bdd->lastInsertId();
                        $req->closeCursor();

                        $this->savePhoto($photo);
                    }
                    elseif(count($this->champmodif) > 0 || $chgtPhoto)
                    {
                        $urlNorm = 'img/membres/' . $this->id . '.jpg';
                        if(file_exists($urlNorm) && $chgtPhoto)
                        {
                            unlink($urlNorm);
                        }
                        
                        $this->savePhoto($photo);
                        
                        if(count($this->champmodif) > 0)
                        {
                            $requete = 'UPDATE membres set ';
                            $nbChamps = count($this->champmodif);
                            for ($index = 0; $index < $nbChamps; $index++) 
                            {
                                $champ = $this->champmodif[$index];
                                $requete .= $champ . ' = :' . $champ;
                                if($index < ($nbChamps - 1))
                                {
                                    $requete .= ', ';
                                }
                            }
                            $requete .= ' WHERE id = :id';

                            $bdd = Connection::getConnection();
                            $req = $bdd->prepare($requete);
                            foreach ($this->champmodif as $champ) 
                            {
                                if($this->{$champ} instanceof DateTime)
                                {
                                    $req->bindValue($champ, $this->{$champ}->format('Y-m-d'));
                                }
                                else
                                {
                                    $req->bindValue($champ, $this->{$champ});
                                }
                            }
                            $req->bindValue('id', $this->id, PDO::PARAM_INT);
                            $req->execute();
                            $req->closeCursor();
                            
                            mail($this->getEmail(), "NAC Aïkido - Modifications dans l'espace membre", "Certaines modifications ont été apportées sur votre espace membre. Si vous n'avez pas modifié vos informatiosn de compte, veuillez contacter l'administrateur du site et modifiez votre mot de passe.");

                            $this->champmodif = array();
                        }
                    }
		}
		
		public static function getMembresActifs()
		{
                    return self::getByStatus(1);
		}
                
                private static function getByStatus($status)
                {
                    $bdd = Connection::getConnection();
                    $req = $bdd->prepare(self::SELECT_BY_ACTIF);
                    $req->bindValue('actif', $status, PDO::PARAM_INT);
                    $req->execute() or die(print_r($req->errorInfo()));
                    while ($donnees = $req->fetch())
                    {
                        $donnees['naissance'] = new DateTime($donnees['naissance']);
                        $membres[$donnees['id']] = new Membre($donnees);
                    }
                    $req->closeCursor();

                    return $membres;
                }
                
                public static function getMembresAbonnesNews()
                {
                    $bdd = Connection::getConnection();
                    $req = $bdd->prepare(self::SELECT_BY_ABO_NEWS);
                    $req->execute() or die(print_r($req->errorInfo()));
                    while ($donnees = $req->fetch())
                    {
                        $donnees['naissance'] = new DateTime($donnees['naissance']);
                        $membres[$donnees['id']] = new Membre($donnees);
                    }
                    $req->closeCursor();

                    return $membres;
                }
                
                public static function getMembresAbonnesStages()
                {
                    $bdd = Connection::getConnection();
                    $req = $bdd->prepare(self::SELECT_BY_ABO_STAGES);
                    $req->execute() or die(print_r($req->errorInfo()));
                    while ($donnees = $req->fetch())
                    {
                        $donnees['naissance'] = new DateTime($donnees['naissance']);
                        $membres[$donnees['id']] = new Membre($donnees);
                    }
                    $req->closeCursor();

                    return $membres;
                }
                
                public static function getAdministrateurs()
                {
                    $bdd = Connection::getConnection();
                    $req = $bdd->prepare(self::SELECT_ADMINS);
                    $req->execute() or die(print_r($req->errorInfo()));
                    while ($donnees = $req->fetch())
                    {
                        $donnees['naissance'] = new DateTime($donnees['naissance']);
                        $membres[$donnees['id']] = new Membre($donnees);
                    }
                    $req->closeCursor();

                    return $membres;
                }
                
                public static function getMembresInactifs()
		{
                    return self::getByStatus(0);
		}
                
                public static function getMembresDesinscrits()
		{
                    return self::getByStatus(3);
		}
                
                public static function getMembresInvalides()
		{
                    return self::getByStatus(2);
		}
                
                public function valider()
                {
                    $this->actif = 1;
                    $this->updateActivity();
                }
                public function invalider()
                {
                    $this->actif = 2;
                    $this->updateActivity();
                }
                public function desinscrire()
                {
                    $this->actif = 3;
                    $this->updateActivity();
                }
                
                private function updateActivity()
                {
                    $bdd = Connection::getConnection();
                    $req = $bdd->prepare(self::UPDATE_ACTIVITY);
                    $req->bindValue('actif', $this->actif, PDO::PARAM_INT);
                    $req->bindValue('id', $this->id, PDO::PARAM_INT);
                    $req->execute();
                    $req->closeCursor();
                    
                    $mode = self::$MAP_STATIC[$this->actif];
                    mail($this->getEmail(), "NAC Aïkido - Activation / Désactivation de compte", "Suite à une intervention de la part de l'administrateur du site, votre statut est désormais '" . $mode . "'");
                }
                
                public function modifier($args, $checkpwd = true)
                {
                    foreach ($args as $champ => $valeur) 
                    {
                        if($champ != 'id' && $this->{$champ} != $valeur)
                        {
			    if(($checkpwd && ($champ == 'password' || $champ == 'password2')) || ($champ != 'password' && $champ != 'password2'))
			    {
                                $this->champmodif[] = $champ;
                                $this->{$champ} = $valeur;
			    }
                        }
                    }
                }
                
                public static function findById($id)
                {
                    $bdd = Connection::getConnection();
                    $req = $bdd->prepare(self::SELECT_BY_ID);
                    $req->bindValue('id', $id, PDO::PARAM_INT);
                    $req->execute() or die(print_r($req->errorInfo()));
                    if ($donnees = $req->fetch())
                    {
                        $donnees['naissance'] = new DateTime($donnees['naissance']);
                        $membre = new Membre($donnees);
                    }
                    $req->closeCursor();

                    return $membre;
                }
                
                public static function findByEmail($email)
                {
                    $bdd = Connection::getConnection();
                    $req = $bdd->prepare(self::SELECT_BY_EMAIL);
                    $req->bindValue('email', $email);
                    $req->execute() or die(print_r($req->errorInfo()));
                    if ($donnees = $req->fetch())
                    {
                        $donnees['naissance'] = new DateTime($donnees['naissance']);
                        $membre = new Membre($donnees);
                    }
                    $req->closeCursor();

                    return $membre;
                }
                
                public function randomPass() 
                { 
                    $word = "a,b,c,d,e,f,g,h,i,j,k,l,m,1,2,3,4,5,6,7,8,9,0,_,-,!,.,*"; 
                    $array=explode(",",$word); 
                    shuffle($array); 
                    $newstring = implode($array,"");
                    $newPwd = substr($newstring, 0, 10); 
                    $hashpwd = md5($newPwd);
                    
                    $args = array('password' => $hashpwd);
                    $this->modifier($args);
                    $this->save();
                    
                    mail($this->email, "NAC Aïkido - Oubli de mot de passe", "Voici votre nouveau mot de passe : $newPwd");
                }
                
                public static function connect($email, $password)
                {
                    $passhach = md5($password);
                    $bdd = Connection::getConnection();
                    $req = $bdd->prepare(self::CONNECT);
                    $req->bindValue('email', $email);
                    $req->bindValue('password', $passhach);
                    $req->execute() or die(print_r($req->errorInfo()));
                    if ($donnees = $req->fetch())
                    {
                        $donnees['naissance'] = new DateTime($donnees['naissance']);
                        $membre = new Membre($donnees);
                    }
                    $req->closeCursor();

                    return $membre;
                }
                
                public static function checkPOST($args, $checkpwd = true)
                {
                    $champsverif = array('nom', 'prenom', 'nomjf', 'naissance', 'adresse', 'tel', 'email', 'profession', 'gpsang', 'nompers', 'prenompers', 'telpers', 'lieunaissance', 'password', 'password2', 'admin', 'abostages', 'abonews');
                    $champs;

                    foreach($champsverif as $element)
                    {
                        if(isset($_POST[$element]))
                        {
                            if($element == 'naissance')
                            {
                                $champs[$element] = strip_tags($_POST[$element]);
                                $date = explode("/", $champs[$element]);
                                $presence = count($date) == 3;
                                $longOK = strlen($date[0]) == 2 && strlen($date[1]) == 2 && strlen($date[2]) == 4;
                                $estNum = is_numeric($date[0]) && is_numeric($date[1]) && is_numeric($date[2]);
                                $jourOK = intval($date[0]) > 0 && intval($date[0]) < 32;
                                $moisOK = intval($date[1]) > 0 && intval($date[1]) < 13;
                                if($presence && $longOK && $estNum && $jourOK && $moisOK)
                                {
                                    $champs[$element] = new DateTime($date[2] . '-' . $date[1] . '-' . $date[0]);
                                }
                                else
                                {
                                    $champs['error'] = 'Format de date non respecté !';
                                    break;
                                }
                            }
                            elseif($element == 'password')
                            {
				if($checkpwd)
				{
                                    $champs[$element] = md5($_POST[$element]);
				}
                            }
                            elseif($element == 'password2')
                            {
				if($checkpwd)
				{
                                    $pass2 = md5($_POST[$element]);
                                    if($pass2 != $champs['password'])
                                    {
                                        $champs['error'] = 'Les deux mots de passe ne sont pas identiques !';
                                        break;
                                    }
				}
                            }
                            elseif($element == 'admin' || $element == 'abonews' || $element == 'abostages')
                            {
                                $champs[$element] = 1;
                            }
                            else
                            {
                                $champs[$element] = strip_tags($_POST[$element]);
                            }
                        }
                        else
                        {
                            if($element == 'admin' || $element == 'abonews' || $element == 'abostages')
                            {
                                $champs[$element] = 0;
                            }
                            else
                            {
                                $champs['error'] = 'Champ ' . $element . ' obligatoire !';
                                break;
                            }
                        }
                    }

                    $nompers = $champs['nompers'];
                    $prenompers = $champs['prenompers'];
                    $telpers = $champs['telpers'];

                    if(!(empty($nompers) && empty($prenompers) && empty($telpers)) && !(!empty($nompers) && !empty($prenompers) && !empty($telpers)))
                    {
                        $champs['error'] = 'Si une personne est à prévenir en cas d\'urgence, toutes les informations sont obligatoires (nom, prénom, n° de téléphone)';
                    }
                    
                    return $champs;
                }
	}
?>
