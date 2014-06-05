<?php
	class Lien
	{
		private $parent;
		private $nom;
		private $enfants = array();
		private $url;
		private $actif;
		
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
		
		public static function getLinks($path, $level = 0, $lienparent, $activepage)
		{
			$liens = array();
			foreach($scandir($path) as $fichier)
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
							$liens[] = $lien;
							$lien->setUrl('./index.php#');
						}
						$liens = array_merge($liens, self::getLinks($pathfile, 1, $lien, $activepage));
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
			return $liens;
		}
		
		public static function getFooterMenu($links, $level = 0)
		{
			$footermenu = '';
		
			foreach($links as $lien)
			{
				if($level ==0)
				{
					$footermenu .= '<div class="col-sm-2 text-center"><div class="list-group">';
				}
				else
				{
					$footermenu .= '<a href=' . $lien->getUrl(). '>' . $lien->getNom() . '</a>';
				}
				$children = $lien->getEnfants();
				foreach($children as $child)
				{
					$footermenu .= getFooterMenu($child, 1);
				}
				if($level ==0)
				{
					$footermenu .= '</div></div>';
				}
			}
			return $menu;
		}
	}
?>

<?php
	function fillLinks($path, $level = 0, &$liens, $lienparent, $activepage)
	{
		$files = scandir($path);
		$nbFiles = count($files);
		
		for ($indexFile = 0; $indexFile < $nbFiles; $indexFile++)
		{
			$fichier = $files[$indexFile];
			if($fichier != '.' && $fichier != '..')
			{
				$pathfile = $path.'/'.$fichier;
				if(filetype($pathfile) == 'dir')
				{
					$decoupe = explode("_", $fichier);
					$array_size = count($decoupe);
					$name = null;
					if($array_size > 1)
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
						$liens[] = $lien;
						$lien->setUrl('./index.php#');
					}
					fillLinks($pathfile, 1, $liens, $lien, $activepage);
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
?>

<?php

	function getMenuEnfant($lien, $level)
	{
		$menuenfant = '<li><a href='.$lien->getUrl().'>';
		for ($i = 1; $i <= $level; $i++)
		{
			$menuenfant .= '&nbsp;&nbsp;';
		}
		$menuenfant .= $lien->getNom().'</a></li>';
		$children = $lien->getEnfants();
		$nbEnfants = count($children);
		for ($index = 0; $index < $nbEnfants; $index++)
		{
			$menuenfant .= getMenuEnfant($children[$index], $level+1);
		}
		return $menuenfant;
	}

	function getMenu($path, $level = 0, $activePage)
	{
		$liens = array();
		fillLinks($path, 0, $liens, null, $activePage);
		$nbLiens = count($liens);
		
		$menu = '<nav class="navbar navbar-inverse navbar-fixed-top"><div class="container"><div class="navbar-header"><button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button><a class="navbar-brand logo-nav" href="index.php"><img class="hidden-xs" src="img/logo_inverse.png" /><img class="visible-xs" src="img/logo_inverse_xs.png" /></a></div><div class="collapse navbar-collapse navbar-ex1-collapse"><ul class="nav navbar-nav navbar-right">';
		
		for ($index = 0; $index < $nbLiens; $index++)
		{
			$niv0 = $liens[$index];
			$menu .= '<li class="dropdown';
			if($niv0->isActive())
			{
				$menu .= ' active';
			}
			
			$menu .= '" ><a href='.$niv0->getUrl().' class="dropdown-toggle" data-toggle="dropdown">'.$niv0->getNom().'<b class="caret"></b></a><ul class="dropdown-menu">';
			$children = $niv0->getEnfants();
			$nbEnfants = count($children);
			for ($index2 = 0; $index2 < $nbEnfants; $index2++)
			{
				$menu .= getMenuEnfant($children[$index2], $level+1);
			}
			$menu .= '</ul></li>';
		}
		
		$menu .= '</ul></div></div></nav>';
		
		return $menu;
	}

?>