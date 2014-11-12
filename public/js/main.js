function getcheckBoxValues()
{
   

    var valores=[];
    var inputs = document.getElementsByTagName('input');
    sw = false;
    for(var i=0; i<inputs.length; i++)
    {
        if(inputs[i].checked)
        {
            valores.push(inputs[i].id);
            sw = true;
        }
    }
    if(!sw)
    {
        alert("Tienes que seleccionar los campos que quieres eliminar");
    }
    else
    {
        $subcad = (valores.length == 1) ? "el" : "los";
        $subcad1 = (valores.length>1) ? "es ":" ";
        return confirm("Quieres eliminar "+$subcad+" indentificador"+$subcad1+""+valores.join(" , "));
    } 
   
}

