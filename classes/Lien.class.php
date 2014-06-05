<?php
	class Lien
	{
		private $parent;
		private $nom;
		private $enfants = array();
		private $url;
		private $actif;
		
		private static $liens;
                const MIN_COL_FOOT = 2;
                const NB_COL = 12;
		
		public function __construct($lienParent, $name)
		{
			$this->actif = false;
			$this->nom = $name;
			if($lienParent != null)
			{
				$this->parent = $lienParent;
				$lienParent->addEnfant($this);
			}
		}
		
		private function addEnfant($enfant)
		{
			$this->enfants[] = $enfant;
		}
		
		public function isActive()
		{
			return $this->actif;
		}
		
		public function setUrl($newUrl)
		{
			$this->url = $newUrl;
		}
		
		public function activate()
		{
			$this->actif = true;
			if($this->parent != null)
			{
				$this->parent->activate();
			}
		}
		
		public function getParent()
		{
			return $this->parent;
		}
		
		public function getUrl()
		{
			return $this->url;
		}
		
		public function getNom()
		{
			return $this->nom;
		}
		
		public function getEnfants()
		{
			return $this->enfants;
		}
		
		public function toString()
		{
			$chaine = '';
			if($this->actif)
			{
				$chaine .= '~';
			}
			$chaine .= 'nom : '.$this->nom.' url : '.$this->url.' ';
			$nbEnfants = count($this->enfants);
			
			if($nbEnfants > 0)
			{
				$chaine .= 'enfants : [';
			}
			
			for ($index = 0; $index < $nbEnfants; $index++)
			{
				$chaine .= $this->enfants[$index]->toString()." ; ";
			}
			
			if($nbEnfants > 0)
			{
				$chaine .= ']';
			}
			return $chaine;
		}
		
		private static function initLinks($path, $activepage, $level = 0, &$lienparent = null)
		{
			foreach(scandir($path) as $fichier)
			{
				if($fichier != '.' && $fichier != '..')
				{
					$pathfile = $path.'/'.$fichier;
					if(filetype($pathfile) == 'dir')
					{
						$decoupe = explode("_", $fichier);
						$name = null;
						if(count($decoupe) > 1)
						{
							$name = $decoupe[1];
						}
						else
						{
							$name = $decoupe[0];
						}
						$lien = new Lien($lienparent, $name);
						if($level == 0)
						{
							self::$liens[] = $lien;
							$lien->setUrl('./index.php#');
						}
						self::initLinks($pathfile, $activepage, 1, $lien);
					}
					else
					{
						$pathfileinfo = pathinfo($fichier);
						$url = $pathfileinfo['filename'];
						
						if($url == $activepage)
						{
							$lienparent->activate();
						}
						
						if($pathfileinfo['extension'] == 'interne')
						{
							$url = '"index.php?page='.$url.'"';
						}
						elseif($pathfileinfo['extension'] == 'externe')
						{
							$url = '"http://'.$url.'" target="_blank"';
						}
						
						$lienparent->setUrl($url);
					}
				}
			}
		}
		
		private static function getFooterEnfant($lien)
		{
			$footermenu = '<a href=' . $lien->getUrl(). ' class="list-group-item small">' . $lien->getNom() . '</a>';
			$children = $lien->getEnfants();
			foreach($children as $child)
			{
				$footermenu .= self::getFooterEnfant($child);
			}
			return $footermenu;
		}
		
		public static function getFooter($path, $activepage)
		{
                    
                    if(!isset(self::$liens))
                    {
                        self::initLinks($path, $activepage);
                    }
                    
                    $largCol1 = floor((self::NB_COL-3) / count(self::$liens));
                    if($largCol1 < self::MIN_COL_FOOT)
                    {
                        $largCol1 = self::MIN_COL_FOOT;
                    }
                    $nbCol1 = floor((self::NB_COL-3) / $largCol1);
                    
                    $largCol2 = floor(self::NB_COL / count(self::$liens));
                    if($largCol2 < self::MIN_COL_FOOT)
                    {
                        $largCol2 = self::MIN_COL_FOOT;
                    }
                    $nbCol2 = floor(self::NB_COL / $largCol2);
                    
                    $footermenu = '<footer>';
                    $nbCol = $nbCol1;
                    $largCol = $largCol1;
                    $numligne = 0;
                    for ($index = 0; $index < count(self::$liens); $index++) 
                    {
                        $lien = self::$liens[$index];

                        if($index % $nbCol == 0)
                        {
                            if($index > 0)
                            {
                                $nbCol = $nbCol2;
                                $largCol = $largCol2;
                                $footermenu .= '</div>';
                            }
                            $footermenu .= '<div class="row">';
                        }
                        $footermenu .= '<div class="col-sm-'.$largCol.' text-center"><div class="list-group">';
                        foreach($lien->getEnfants() as $child)
                        {
                                $footermenu .= self::getFooterEnfant($child);
                        }
                        $footermenu .= '</div></div>';
                        
                        if((($index + 1) % $nbCol == 0) && $numligne == 0)
                        {
                            $footermenu .= '<div class="col-sm-3 text-right">© N.A.C Aïkido - Tous droits réservés</div>';
                            $numligne++;
                        }
                    }
                        
                    $footermenu .= '</div>';
                    $footermenu .= '</footer>';

                    return $footermenu;
		}
		
		public static function getMenu($path, $activepage)
		{
                    
                    if(!isset(self::$liens))
                    {
                        self::initLinks($path, $activepage);
                    }
                    
			$menu = '<nav class="navbar navbar-inverse navbar-fixed-top"><div class="container"><div class="navbar-header"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button><a class="navbar-brand logo-nav" href="index.php"><img class="hidden-xs hidden-sm" src="img/logo_inverse.png" /><img class="visible-sm" src="img/logo_inverse_sm.png" /><img class="visible-xs" src="img/logo_inverse_xs.png" /></a></div><div class="collapse navbar-collapse navbar-ex1-collapse"><ul class="nav navbar-nav navbar-right">';
			foreach(self::$liens as $niv0)
			{
				$menu .= '<li class="dropdown';
				if($niv0->isActive())
				{
					$menu .= ' active';
				}
				
				$menu .= '" ><a href='.$niv0->getUrl().' class="dropdown-toggle" data-toggle="dropdown">'.$niv0->getNom().'<b class="caret"></b></a><ul class="dropdown-menu">';
				foreach($niv0->getEnfants() as $child)
				{
					$menu .= self::getMenuEnfant($child, $level+1);
				}
				$menu .= '</ul></li>';
			}
			
			$menu .= '</ul></div></div></nav>';
			
			return $menu;
		}
		
		private static function getMenuEnfant($lien, $level)
		{
			$menuenfant = '<li><a href='.$lien->getUrl().'>';
			for ($i = 1; $i <= $level; $i++)
			{
				$menuenfant .= '&nbsp;&nbsp;';
			}
			$menuenfant .= $lien->getNom().'</a></li>';
			foreach($lien->getEnfants() as $child)
			{
				$menuenfant .= self::getMenuEnfant($child, $level+1);
			}
			return $menuenfant;
		}
	}
?>