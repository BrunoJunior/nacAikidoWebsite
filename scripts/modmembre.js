$( "input[type='radio']" ).change(function () 
{
    if($("#rempphoto").prop("checked"))
    {
        $("#photo").attr("disabled", false);
    }
    else
    {
        $("#photo").attr("disabled", true);
    }
}).change();