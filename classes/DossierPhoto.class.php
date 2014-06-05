<?php
include_once('Utils.class.php');
include_once('Connection.class.php');

class DossierPhoto
{
    const NB_PHOTOS_LIGNE = 6;
    const MAX_BY_PAGE = 3;
    const URL = './photos';
    
    private $exist;
    private $titre;
    private $photos;
    
    public static function TAILLE()
    {
        return 12 / self::NB_PHOTOS_LIGNE;
    }
    
    public function __construct($exist = false) 
    {
        $this->exist = $exist;
    }

    public function setPhotos(&$photos)
    {
        $this->photos = $photos;
    }

    public function photos()
    {
        return $this->photos;
    }
		
    public function saveMetier() 
    {
        $url = self::URL . '/' . $this->titre;
        $urlmini = $url . '/miniatures';
        
        if(isset($this->photos) && count($this->photos) > 0)
        {
            $names = $this->photos['name'];
            $errors = $this->photos['error'];
            $tmpnames = $this->photos['tmp_name'];
			$size = $this->photos['size'];
            for ($index = 0; $index < count($names); $index++)
            {
                if($errors[$index] == 0)
                {
					if ($size[$index] <= 1000000)
					{
						// Testons si l'extension est autorisée
						$infosfichier = pathinfo($names[$index]);
						$extensions_autorisees = array('jpg', 'jpeg', 'gif', 'png');
						if (in_array(strtolower($infosfichier['extension']), $extensions_autorisees))
						{
							if(!$this->exist)
							{
								if(mkdir($url))
								{
									$this->exist = true;
									mkdir($urlmini);
								}
							}
							$photoname = $infosfichier['filename'];
							$urlNorm = $url . '/' . $photoname . '.jpg';
							$urlMini = $urlmini . '/' . $photoname . '.jpg';
							// Deplacement en temp
							move_uploaded_file($tmpnames[$index], $urlNorm);
							// Reduction en mini à 300 pixels de largeur
							Utils::darkroom($urlNorm, $urlMini, 300, 0);
						}
						else
						{
							throw new Exception('Format non autorisé (jpg, jpeg, gif, png) !');
						}
					}
					else
					{
						throw new Exception('Photo trop volumineuse (1Mo max) !');
					}
                }
            }
        }
    }
    
    public function getPathfile()
    {
        return self::URL . '/' . $this->titre;
    }
    
    public function exist()
    {
        return $this->exist;
    }

    public function setTitre($titre)
    {
        return $this->titre = $titre;
    }

    public function titre()
    {
        return $this->titre;
    }

    public function save()
    {
        $this->saveMetier();
    }

    public static function getAllDossiersForPage($indexPage)
    {
        $files = scandir(self::URL, 1);
        $nbFiles = count($files) - 2;
        
        $max = self::MAX_BY_PAGE;
        if($indexPage < 1)
        {
            $max = $indexPage;
        }
        
        $debut = (($indexPage-1) * $max);
        $fin = $nbFiles;
        if($max > 0)
        {
            $fin2 = $debut + $max;
            if($fin2 < $nbFiles)
            {
                $fin = $fin2;
            }
        }

        for ($index = $debut ; $index < $fin ; $index++)
        {
            $dossier = new DossierPhoto(true);
            $dossier->titre = $files[$index];
            $dossier->exist = true;
            $dossiers[] = $dossier;
        }

        return $dossiers;
    }
    
    public static function getAllDossiers()
    {
        return self::getAllDossiersForPage(0);
    }

    public static function getNumberOfPages()
    {
        $numOfPage = 1;
        if(self::MAX_BY_PAGE > 0)
        {
            $files = scandir(self::URL, 1);
            $numOfPage = ceil((count($files) - 2)/self::MAX_BY_PAGE);
        }

        if($numOfPage == 0)
        {
            $numOfPage++;
        }

        return $numOfPage;
    }
}
?>