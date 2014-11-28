 var modalBox = $("#box-modal"),
        modalBoxObject = modalBox[0],
        modaltitle = modalBoxObject.getElementsByClassName('modal-title')[0],
        modalContent = modalBoxObject.getElementsByClassName('modal-body')[0],
        modalFooter = modalBoxObject.getElementsByClassName('modal-footer')[0];

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
        $subcad = (valores.length === 1) ? "el" : "los";
        $subcad1 = (valores.length>1) ? "es ":" ";
        return confirm("Quieres eliminar "+$subcad+" indentificador"+$subcad1+""+valores.join(" , "));
        document.forms[0].submit();
    } 
   
}
function showEmployee(element)
{
    $("#editor-panel").removeClass('hidden'); 
    modalBox.modal("show");
    modaltitle.innerHTML = "Visualizar datos empleado";
    nie = element.getAttribute('data-id');
    $.get("ajax.php?id="+nie+"&opcion=1",function(data)
    {
        document.getElementById('editor-panel').innerHTML = data;
    });
}
