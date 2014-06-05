var nombreHoraire;

function addHoraire(nbhoraire) {
	if(!nombreHoraire)
	{
		nombreHoraire = nbhoraire;
	}
	
	$("#gesth").remove();
	$('<div class="row" id="ligne'+(nombreHoraire)+'"><div class="col-sm-5"><div class="input-group form-group"><span class="input-group-addon">Date</span><input type="text" class="form-control" placeholder="jjmmaaaa" name="jour[]" required="" ></div></div><div class="col-sm-5"><div class="row"><div class="col-xs-6"><div class="input-group form-group"><span class="input-group-addon">De</span><input type="text" class="form-control" placeholder="hhmm" name="heuredeb[]" required="" ></div></div><div class="col-xs-6"><div class="input-group form-group"><span class="input-group-addon">à</span><input type="text" class="form-control" placeholder="hhmm" name="heurefin[]" required="" ></div></div></div></div><div class="col-sm-2" id="gesth"><div class="row"><div class="col-xs-6"><div class="btn-group"><button type="button" class="btn btn-primary" id="addh" onclick="addHoraire()">+</button></div></div><div class="col-xs-6"><div class="btn-group"><button type="button" class="btn btn-primary" id="remh" onclick="removeHoraire()">-</button></div></div></div></div></div>').appendTo("#horaires");
	nombreHoraire++;
	
	if(nombreHoraire < 10)
	{
		$("#addh").prop('disabled', false);
	}
	else
	{
		$("#addh").prop('disabled', true);
	}
}

function removeHoraire(nbhoraire) {
	if(!nombreHoraire)
	{
		nombreHoraire = nbhoraire;
	}
	
	if(nombreHoraire > 1)
	{
		nombreHoraire--;
		$("#gesth").remove();
		$("#ligne"+nombreHoraire).remove();
		$('<div class="col-sm-2" id="gesth"><div class="row"><div class="col-xs-6"><div class="btn-group"><button type="button" class="btn btn-primary" id="addh" onclick="addHoraire()">+</button></div></div><div class="col-xs-6"><div class="btn-group"><button type="button" class="btn btn-primary" id="remh" onclick="removeHoraire()">-</button></div></div></div></div>').appendTo("#ligne"+(nombreHoraire-1));
	}
	
	if(nombreHoraire > 1)
	{
		$("#remh").prop('disabled', false);
	}
	else
	{
		$("#remh").prop('disabled', true);
	}
}