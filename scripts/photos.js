var nombrePhoto;
var helpblock = '<p class="help-block" id="help">Sélectionnez les photos (jpg, png, gif).</p>';
var newligne = '<div class="row" id="ligne?"><div class="col-lg-12"><div class="form-group"><input type="file" name="photo[]" id="file?" required=""><p class="help-block" id="help">Sélectionnez les photos (jpg, png, gif).</p></div></div></div>';
var idLignePrec = '#ligne?';

function addPhoto(nbphoto) {
	if(!nombrePhoto)
	{
		nombrePhoto = nbphoto;
	}
	
	if(nombrePhoto < 10)
	{
		$("#remp").prop('disabled', false);
		$("#help").remove();
		$(newligne.replace('?', nombrePhoto).replace('?', nombrePhoto)).insertAfter(idLignePrec.replace('?', nombrePhoto - 1));
		nombrePhoto++;
	}
	
	if(nombrePhoto < 10)
	{
		$("#addp").prop('disabled', false);
	}
	else
	{
		$("#addp").prop('disabled', true);
	}
}

function removePhoto(nbphoto) {
	if(!nombrePhoto)
	{
		nombrePhoto = nbphoto;
	}
	
	if(nombrePhoto > 1)
	{
		nombrePhoto--;
		$(idLignePrec.replace('?', nombrePhoto)).remove();
		$(helpblock).insertAfter('#file' + (nombrePhoto - 1));
		$("#addp").prop('disabled', false);
	}
	
	if(nombrePhoto > 1)
	{
		$("#remp").prop('disabled', false);
	}
	else
	{
		$("#remp").prop('disabled', true);
	}
}