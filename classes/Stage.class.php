<?php
	include_once('Connection.class.php');
	include_once('Utils.class.php');
	include_once('Horaire.class.php');

	class Stage
	{
		private $id;
		private $titre;
		private $emplacement;
		private $details;
		private $debut;
		private $horaires = array();
		
		const INSERT = 'INSERT INTO stages(titre, emplacement, details, debut) VALUES(:titre, :emplacement, :details, :debut)';
		const SELECT_ALL = 'SELECT * from stages ORDER BY debut DESC LIMIT :debut, :max';
		const SELECT_COUNT = 'SELECT COUNT(*) as numstages from stages';
		
		const MAX_BY_PAGE = 20;
		
		public function __construct()
		{
			$ctp = func_num_args();
			$args = func_get_args();
			
			if($ctp == 3 OR $ctp == 4)
			{
				$this->titre = $args[0];
				$this->emplacement = $args[1];
				$this->details = $args[2];
				
				if($ctp == 4)
				{
					$this->id = $args[3];
				}
			}
		}
		
		public function id()
		{
			return $this->id;
		}
		
		public function emplacement()
		{
			return $this->emplacement;
		}
		
		public function titre()
		{
			return $this->titre;
		}
		
		public function details()
		{
			return $this->details;
		}
		
		public function debut()
		{
			return $this->debut;
		}
		
		public function addHoraire(Horaire &$horaire)
		{
			$horaire->setStage($this);
			
			if(!isset($this->debut))
			{
				$this->debut = $horaire->datestage();
			}
			else
			{
				$actual = $this->debut;
				$actual = $actual->format('Ymd');
				$new = $horaire->datestage();
				$new = $new->format('Ymd');
				if($new < $actual)
				{
					$this->debut = $horaire->datestage();
				}
			}
			
			$this->horaires[] = $horaire;
		}
		
		public function horaires()
		{
			return $this->horaires;
		}
                
                public function getContentForEmail(&$parser)
                {
                    $content = '<html><body>';
                    $content .= '<table rules="all" style="border-color: #8B2A18;" cellpadding="10">';
                    $content .= '<tr style="background: #D70000;"><td colspan="4"><strong style="color: #FFF;">' . $this->titre . '</strong></td></tr>';
                    $content .= '<tr style="background: #FFF;">';
                    $content .= '<td><a href = "http://www.nac.aikido.bdesprez.com/stages/images/' . $this->id . '.jpg"><img src="http://www.nac.aikido.bdesprez.com/stages/images/mini/' . $this->id . '.jpg" /></a></td>';
                    $parser->parse(nl2br($this->details));
                    $content .= '<td colspan="3"><h4>Lieu du stage</h4><p>' . $this->emplacement . '</p><h4>Détails</h4><p>' . $parser->getAsHtml() . '</p>';
                    $content .= '<h4>Horaires</h4>';
                    foreach ($this->horaires as $horaire)
                    {
                        $content .= '<p>Le ' . $horaire->datestage()->format('d/m/Y') . ' de ' . $horaire->heuredebut()->format('H:i') . ' à ' . $horaire->heurefin()->format('H:i') . '</p>';
                    }
                    $content .= '</td>';
                    $content .= '</tr>';
                    
                    $docSrc = 'stages/documents/' . $this->id();
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
                        $content .= '<tr style="background: #FFF;"><td colspan ="4"><h4>Document associé</h4><p><a href="http://www.nac.aikido.bdesprez.com/' . $docSrc . '.' . $docExt .'" target="_blank"><img src="http://www.nac.aikido.bdesprez.com/img/mini/' . $docExt . '.png" /></a></p></td>';
                    }
                    $content .= '</table></body></html>';
                    
                    return $content;
                }
		
		public function save(&$image, &$document)
		{
                    if(empty($this->id))
                    {
                        $bdd = Connection::getConnection();
                        $req = $bdd->prepare(self::INSERT);
                        $req->bindValue('titre', $this->titre);
                        $req->bindValue('emplacement', $this->emplacement);
                        $req->bindValue('details', $this->details);
                        $req->bindValue('debut', $this->debut->format('Y-m-d'));
                        $req->execute() or die(print_r($req->errorInfo()));
                        $this->id = $bdd->lastInsertId();
                        $req->closeCursor();

                        foreach($this->horaires as $horaire)
                        {
                            $horaire->save();
                        }

                        if (isset($image) AND $image['error'] == 0)
                        {
                            // Testons si le fichier n'est pas trop gros
                            if ($image['size'] <= 1000000)
                            {
                                // Testons si l'extension est autorisée
                                $infosfichier = pathinfo($image['name']);
                                $extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
                                if (in_array(strtolower($infosfichier['extension']), $extensions_autorisees))
                                {
                                        // On peut valider le fichier et le stocker définitivement
                                        $urlTmp = 'stages/images/tmp/' . $this->id() . '.' . strtolower($infosfichier['extension']);
                                        $urlNorm = 'stages/images/' . $this->id() . '.jpg';
                                        $urlMini = 'stages/images/mini/' . $this->id() . '.jpg';
                                        // Deplacement en temp
                                        move_uploaded_file($image['tmp_name'], $urlTmp);

                                        try
                                        {
                                            // Conversion en jpg et deplacement dans bonne url
                                            Utils::darkroom($urlTmp, $urlNorm);

                                            // Reduction en mini à 250 pixels de hauteur
                                            Utils::darkroom($urlTmp, $urlMini, 0, 250);
                                        }
                                        catch(Exception $e)
                                        {
                                            echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
                                        }

                                        unlink($urlTmp);
                                }
                            }
                        }

                        if (isset($document) AND $document['error'] == 0)
                        {
                                // Testons si l'extension est autorisée
                                $infosfichier = pathinfo($document['name']);
                                $extensions_autorisees = array('pdf', 'doc', 'docx');
                                if (in_array(strtolower($infosfichier['extension']), $extensions_autorisees))
                                {
                                    try
                                    {
                                        // On peut valider le fichier et le stocker définitivement
                                        move_uploaded_file($document['tmp_name'], 'stages/documents/' . $this->id() . '.' . strtolower($infosfichier['extension']));
                                    }
                                    catch(Exception $e)
                                    {
                                            echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
                                    }
                                }
                        }
                    }
		}
		
		public static function getAllStages($indexPage)
		{
			$bdd = Connection::getConnection();
			$req = $bdd->prepare(self::SELECT_ALL);
			$req->bindValue('debut', (($indexPage - 1) * self::MAX_BY_PAGE), PDO::PARAM_INT);
			$req->bindValue('max', self::MAX_BY_PAGE, PDO::PARAM_INT);
			$req->execute() or die(print_r($req->errorInfo()));
			while ($donnees = $req->fetch())
			{
				$stage = new Stage($donnees['titre'], $donnees['emplacement'], $donnees['details'], $donnees['id']);
				Horaire::getHorairesOfStage($stage);
				$stages[] = $stage;
			}
			$req->closeCursor();
			
			return $stages;
		}
		
		public static function getNumberOfPages()
		{
			$bdd = Connection::getConnection();
			$req = $bdd->prepare(self::SELECT_COUNT);
			$req->execute() or die(print_r($req->errorInfo()));
			if ($donnees = $req->fetch())
			{
				$count = $donnees['numstages'];
			}
			$req->closeCursor();
			
			$numOfPage = ceil($count/self::MAX_BY_PAGE);
			
			if($numOfPage == 0)
				$numOfPage++;
			
			return $numOfPage;
		}
	}
?>