<?php
	class Utils
	{
		/**
		* La fonction darkroom() renomme et redimensionne les photos envoyées lors de l'ajout d'un objet.
		* @param $img String Chemin absolu de l'image d'origine.
		* @param $to String Chemin absolu de l'image générée (.jpg).
		* @param $width Int Largeur de l'image générée. Si 0, valeur calculée en fonction de $height.
		* @param $height Int Hauteur de l'image génétée. Si 0, valeur calculée en fonction de $width.
		* Si $height = 0 et $width = 0, dimensions conservées mais conversion en .jpg
		*/
		public static function darkroom($img, $to, $width = 0, $height = 0){
		 
			$useGD 		= TRUE;
			$dimensions = getimagesize($img);
			$ratio		= $dimensions[0] / $dimensions[1];
		 
			// Calcul des dimensions si 0 passé en paramètre
			if($width == 0 && $height == 0){
				$width = $dimensions[0];
				$height = $dimensions[1];
			}elseif($height == 0){
				$height = round($width / $ratio);
			}elseif ($width == 0){
				$width = round($height * $ratio);
			}
		 
			if($dimensions[0] > ($width / $height) * $dimensions[1]){
				$dimY = $height;
				$dimX = round($height * $dimensions[0] / $dimensions[1]);
				$decalX = ($dimX - $width) / 2;
				$decalY = 0;
			}
			if($dimensions[0] < ($width / $height) * $dimensions[1]){
				$dimX = $width;
				$dimY = round($width * $dimensions[1] / $dimensions[0]);
				$decalY = ($dimY - $height) / 2;
				$decalX = 0;
			}
			if($dimensions[0] == ($width / $height) * $dimensions[1]){
				$dimX = $width;
				$dimY = $height;
				$decalX = 0;
				$decalY = 0;
			}
		 
			// Création de l'image avec la librairie GD
			if($useGD){
				$pattern = imagecreatetruecolor($width, $height);
				$type = mime_content_type($img);
				switch (substr($type, 6)) {
					case 'jpeg':
						$image = imagecreatefromjpeg($img);
						break;
					case 'gif':
						$image = imagecreatefromgif($img);
						break;
					case 'png':
						$image = imagecreatefrompng($img);
						break;
				}
				imagecopyresampled($pattern, $image, 0, 0, 0, 0, $dimX, $dimY, $dimensions[0], $dimensions[1]);
				imagedestroy($image);
				imagejpeg($pattern, $to, 100);
		 
				return TRUE;
		 
				// Création de l'image avec ImageMagick
			}else{
				$cmd = '/usr/bin/convert -resize '.$dimX.'x'.$dimY.' "'.$img.'" "'.$dest.'"';
					shell_exec($cmd);
		 
					$cmd = '/usr/bin/convert -gravity Center -quality '.self::$quality.' -crop '.$largeur.'x'.$hauteur.'+0+0 -page '.$largeur.'x'.$hauteur.' "'.$dest.'" "'.$dest.'"';
							shell_exec($cmd);	
			}
			return TRUE;
		}
	}
?>