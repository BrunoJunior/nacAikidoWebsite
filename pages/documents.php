<div class="row">
	<div class="col-lg-12">
		<h1>Les documents</h1>
		<ul>
			<?php
				$dir = './doc';
				if($dossier = opendir($dir))
				{
					while($fichier = readdir($dossier))
					{
						if($fichier != '.' && $fichier != '..')
						{
							$pathfile = $dir.'/'.$fichier;
							$nbFichier = 0;
							echo "<h3>$fichier</h3>";
							if(filetype($pathfile) == 'dir'){
								if($dossier2 = opendir($pathfile))
								{
									while($fichier2 = readdir($dossier2))
									{
										if($fichier2 != '.' && $fichier2 != '..')
										{
											$pathfile2 = $pathfile.'/'.$fichier2;
											if(($nbFichier % 6) == 0)
											{
												if($nbFichier > 0)
												{
													echo '</div>';
												}
												echo '<div class="row">';
											}
											$nbFichier = $nbFichier +1;
											$info = new SplFileInfo($pathfile2);
											echo "<div class='col-md-2'><a href='$pathfile2' target='_blank'><img class='img-responsive center-block' title='$fichier2' alt='$fichier2' src='img/mini/".$info->getExtension().".png' /></a><p class='text-center'>$fichier2</p></div>";
										}
									}
									closedir($dossier2);
								}           
							}
							echo '</div>'; 
						}
					}
					closedir($dossier);
				}
			?>
		</ul>
	</div>
</div>
