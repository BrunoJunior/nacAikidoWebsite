<?php
	include_once('Connection.class.php');
	include_once('Utils.class.php');

	class News
	{
		private $id;
		private $dateCre;
		private $titre;
		private $message;
		
		const INSERT = 'INSERT INTO news(titre, message) VALUES(:titre, :message)';
		const SELECT_DATE = 'SELECT date FROM news WHERE id = :id';
		const SELECT_ALL = 'SELECT * from news ORDER BY date DESC LIMIT :debut, :max';
		const SELECT_COUNT = 'SELECT COUNT(*) as numnews from news';
		
		const MAX_BY_PAGE = 20;
		
		public function __construct()
		{
			$ctp = func_num_args();
			$args = func_get_args();
			
			if($ctp == 2 OR $ctp == 4)
			{
				$this->titre = $args[0];
				$this->message = $args[1];
				
				if($ctp == 4)
				{
					$this->id = $args[2];
					$this->dateCre = $args[3];
				}
			}
		}
		
		public function id()
		{
			return $this->id;
		}
		
		public function dateCre()
		{
			return $this->dateCre;
		}
		
		public function titre()
		{
			return $this->titre;
		}
		
		public function message()
		{
			return $this->message;
		}
		
		public function save(&$image)
		{
                    if(empty($this->id))
                    {
                        $bdd = Connection::getConnection();
                        $req = $bdd->prepare(self::INSERT);
                        $req->execute(array('titre' => $this->titre,'message' => $this->message)) or die(print_r($req->errorInfo()));
                        $this->id = $bdd->lastInsertId();
                        $req->closeCursor();

                        $req = $bdd->prepare(self::SELECT_DATE);
                        $req->execute(array('id' => $this->id)) or die(print_r($req->errorInfo()));
                        if ($donnees = $req->fetch())
                        {
                                $this->dateCre = $donnees['date'];
                        }
                        $req->closeCursor();

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
                                    $urlTmp = 'news/images/tmp/' . $this->id() . '.' . strtolower($infosfichier['extension']);
                                    $urlNorm = 'news/images/' . $this->id() . '.jpg';
                                    // Deplacement en temp
                                    move_uploaded_file($image['tmp_name'], $urlTmp);
                                    // Reduction en mini à 250 pixels de hauteur
                                    Utils::darkroom($urlTmp, $urlNorm, 300, 0);

                                    unlink($urlTmp);
                                }
                                else
                                {
                                        throw new Exception('Extension non autorisée (jpg, jpeg, gif, png) !');
                                }
                            }
                            else
                            {
                                throw new Exception('Image trop volumineuse (1Mo max) !');
                            }
                        }
                    }
		}
		
		public static function getAllNews($indexPage)
		{
			$bdd = Connection::getConnection();
			$req = $bdd->prepare(self::SELECT_ALL);
			$req->bindValue('debut', (($indexPage - 1) * self::MAX_BY_PAGE), PDO::PARAM_INT);
			$req->bindValue('max', self::MAX_BY_PAGE, PDO::PARAM_INT);
			$req->execute() or die(print_r($req->errorInfo()));
			while ($donnees = $req->fetch())
			{
				$news[] = new News($donnees['titre'], $donnees['message'], $donnees['id'], $donnees['date']);
			}
			$req->closeCursor();
			
			return $news;
		}
		
		public static function getNumberOfPages()
		{
			$bdd = Connection::getConnection();
			$req = $bdd->prepare(self::SELECT_COUNT);
			$req->execute() or die(print_r($req->errorInfo()));
			if ($donnees = $req->fetch())
			{
				$count = $donnees['numnews'];
			}
			$req->closeCursor();
			
			return ceil($count/self::MAX_BY_PAGE);
		}
                
                public function getContentForEmail(&$parser)
                {
                    $content = '<html><body>';
                    $content .= '<table rules="all" style="border-color: #8B2A18; border-collapse: collapse;" cellpadding="10">';
                    $content .= '<tr style="background: #D70000; border-top: #8B2A18; border-left: #8B2A18; border-right: #8B2A18"><td colspan="4" style="border: 0"><strong style="color: #FFF;">' . $this->titre . '</strong></td></tr>';
                    $content .= '<tr style="background: #FFF; border-left: #8B2A18; border-right: #8B2A18">';
                    $imgSrc = 'news/images/' . $this->id() . '.jpg';
                    $colspan = 4;
                    if(file_exists($imgSrc))
                    {
                        $colspan = 3;
                        $content .= '<td style="border: 0"><img src="http://www.nac.aikido.bdesprez.com/'.$imgSrc.'" /></td>';
                    }
                    $parser->parse(nl2br($this->message));
                    $content .= '<td colspan="'.$colspan.'" style="border: 0"><p>' . $parser->getAsHtml() . '</p></td>';
                    $content .= '</tr>';
                    $content .= '<tr style="background: #F5F5F5; border-bottom: #8B2A18; border-left: #8B2A18; border-right: #8B2A18"><td colspan="4" style="border: 0">News du ' . $this->dateCre . '</td></tr>';
                    $content .= '</table></body></html>';
                    
                    return $content;
                }
	}
?>