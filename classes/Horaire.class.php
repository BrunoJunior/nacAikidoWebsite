<?php
	include_once('Connection.class.php');
	include_once('Stage.class.php');
	
	class Horaire
	{
		private $id;
		private $stage;
		private $datestage;
		private $heuredebut;
		private $heurefin;
		
		const INSERT = 'INSERT INTO horaires(idstage, datestage, heuredebut, heurefin) VALUES(:idstage, :datestage, :heuredebut, :heurefin)';
		const SELECT_ALL_BY_STAGE = 'SELECT * from horaires where idstage = :idstage ORDER BY datestage, heuredebut ASC';
		
		const MAX_BY_PAGE = 20;
		
		public function __construct()
		{
			$ctp = func_num_args();
			$args = func_get_args();
			
			if($ctp == 3 OR $ctp == 4)
			{
				if(!is_numeric($args[0]) || strlen($args[0]) != 8)
				{
					throw new Exception('Non respect du format jjmmaaaa');
				}
				if(!is_numeric($args[1]) || !is_numeric($args[2]) || strlen($args[1]) != 4 || strlen($args[2]) != 4)
				{
					throw new Exception('Non respect du format hhmm');
				}
				if($args[2] < $args[1])
				{
					throw new Exception('L\'heure de fin doit être plus grande que l\'heure de début !');
				}
				
				$jour = intval(substr($args[0], 0, 2));
				$mois = intval(substr($args[0], 2, 2));
				$annee = intval(substr($args[0], 4, 4));
				
				if($jour < 1 || $jour > 31 || $mois < 1 || $mois > 12)
				{
					throw new Exception('La date n\'est pas cohérente !');
				}
				
				$heured = substr($args[1], 0, 2);
				$minutesd = substr($args[1], 2, 2);
				
				if(intval($heured) > 23 || intval($minutesd) > 59)
				{
					throw new Exception('L\'heure de début n\'est pas cohérente !');
				}
				
				$heuref = substr($args[2], 0, 2);
				$minutesf = substr($args[2], 2, 2);
				
				if(intval($heuref) > 23 || intval($minutesf) > 59)
				{
					throw new Exception('L\'heure de fin n\'est pas cohérente !');
				}
				
				$this->datestage = new DateTime($annee . '-' . $mois . '-' . $jour);
				$this->heuredebut = new DateTime($heured . ':' . $minutesd);
				$this->heurefin = new DateTime($heuref . ':' . $minutesf);
				
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
		
		public function stage()
		{
			return $this->stage;
		}
		
		public function setStage(Stage &$stage)
		{
			$this->stage = $stage;
		}
		
		public function datestage()
		{
			return $this->datestage;
		}
		
		public function heuredebut()
		{
			return $this->heuredebut;
		}
		
		public function heurefin()
		{
			return $this->heurefin;
		}
		
		public static function getHorairesOfStage(Stage &$stage)
		{
			$bdd = Connection::getConnection();
			$req = $bdd->prepare(self::SELECT_ALL_BY_STAGE);
			$req->bindValue('idstage', $stage->id(), PDO::PARAM_INT);
			$req->execute() or die(print_r($req->errorInfo()));
			while ($donnees = $req->fetch())
			{
				$dtStg = new DateTime($donnees['datestage']);
				$hdeb = new DateTime($donnees['heuredebut']);
				$hfin = new DateTime($donnees['heurefin']);
				
				$stage->addHoraire(new Horaire($dtStg->format('dmY'), $hdeb->format('Hi'), $hfin->format('Hi'), $donnees['id']));
			}
			$req->closeCursor();
		}
		
		public function save()
		{
                    $stage = $this->stage;
                    if(empty($this->id) && isset($stage))
                    {
                            $idstage = $stage->id();
                            if(!empty($idstage))
                            {
                                    $bdd = Connection::getConnection();
                                    $req = $bdd->prepare(self::INSERT);
                                    $req->bindValue('idstage', $this->stage->id(), PDO::PARAM_INT);
                                    $req->bindValue('datestage', $this->datestage->format('Y-m-d'));
                                    $req->bindValue('heuredebut', $this->heuredebut->format('H:i'));
                                    $req->bindValue('heurefin', $this->heurefin->format('H:i'));
                                    $req->execute() or die(print_r($req->errorInfo()));
                                    $this->id = $bdd->lastInsertId();
                                    $req->closeCursor();
                            }
                    }
		}
	}
?>