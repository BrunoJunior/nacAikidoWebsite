<?php
    include_once('classes/Membre.class.php');
    if(isset($_SESSION['userid']))
    {
        $membres = Membre::getMembresActifs();
    }
    else 
    {
        echo '<div class="alert alert-danger">Vous devez être connecté afin de visualiser cette page !</div>';
    }
    
    $index = 0;
?>
<div class="page-header"><h3>Trombinoscope</h3></div>
<?php
    foreach ($membres as $membre)
    {
        $prenomnom = $membre->getPrenom() . ' ' . $membre->getNom();
        $imageSrc = "img/membres/" . $membre->getId() . ".jpg";
        if(file_exists($imageSrc))
        {
            if($index % 6 == 0)
            {
                if($index > 0)
                {
?>
</div>
<?php
                }
?>
<div class="row text-center">
<?php
            }
?>
    <div class="col-xs-6 col-sm-4 col-md-2 col-lg-2">
        <img class="img-responsive img-thumbnail img-marged" title="<?php echo $prenomnom; ?>" alt="<?php echo $prenomnom; ?>" src="<?php echo $imageSrc; ?>" /><br />
        <?php echo $prenomnom; ?>
    </div>
<?php
            $index++;
        }
    }
?>
</div>