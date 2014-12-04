 var modalBox = $("#box-modal"),
        modalBoxObject = modalBox[0],
        modaltitle = modalBoxObject.getElementsByClassName('modal-title')[0],
        modalContent = modalBoxObject.getElementsByClassName('modal-body')[0],
        modalFooter = modalBoxObject.getElementsByClassName('modal-footer')[0];

$("document").ready(function(){
   $("#searchproducts").click(function(event){
      //Recojo el valor del input
      var value=$("#inputproducts").val();
      //elimino el comportamiento por defecto del enlace
      event.preventDefault();
      //Aquí pongo el codigo para llamar a los página de productos en ajax
      $.ajax({
      type:"POST",
      url:"products_ajax.php",
      async:true,
      data:'parametro1='+value,
      datatype:'html',
      success: function(data)
      {
          document.getElementById('editor-panel').innerHTML = data;
      },
      error: function(jqXHR, exception)
      {
        if(jqXHR.status === 500)
        {
            alert('Error interno:'+jqXHR.responseText);
        }
        else if(jqXHR.status=== 404)
        {
            alert('Pagina no encontrada[404]');
        }
        else if(exception === 'timeout')
        {
            alert("Error time out");
        }
        else if(exception === 'abort')
        {
            alert('Respuesta ajax abortada');
        }
        else
        {
            alert("Error no detectado "+jqXHR.responseText);
        }
      }
      });
   }); 
});

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

function showEmployee(element,opcion)
{
    $("#editor-panel").removeClass('hidden'); 
    modalBox.modal("show");
    modaltitle.innerHTML = "Visualizar datos empleado";
    nie = element.getAttribute('data-id');
    $.get("ajax.php?id="+nie+"&opcion="+opcion,function(data)
    {
        document.getElementById('editor-panel').innerHTML = data;
    });
}
/* Validacion de los campos del formulario */
function validar(form,fieldsForm)
{
    var arrayFields = ["email","select-one","password"];
    enviar = true;
    var errores="";
    for(var i=0; i<form.elements.length; i++)
    {
        if((arrayFields.indexOf(form.elements[i].type)!==-1)||(form.elements[i].type=="text" && form.elements[i].required==true))
        {
 
            elementClassName = form.elements[i].parentNode.parentNode.className;
            if(form.elements[i].value.length == 0)
            {
                errores+="\n El campo "+form.elements[i].name + " no puede estar vacio";
                enviar = false;  
            }  
            if(elementClassName.indexOf('has-error')!==-1)
            {
                if(form.elements[i].type==="select-one")
                {
                    errores+="\nDebes seleccionar un "+fieldsForm[i];
                }
                else
                {
                    errores+="\n Dato erroneo en el campo "+fieldsForm[i];
                }
                enviar = false;
            }
        } 
    }
    if(!enviar)
    {
        alert(errores);
    }
    return enviar;
}
function seleccionPuesto(valor)
{
    enviar = true;
    if(valor==="-1")
    {
        enviar = false;
    }
    return enviar;
}
function comprobarLetraDni(parametro)
{
    cadena = "TRWAGMYFPDXBNJZSQVHLCKET";
    correcto = true;
    var pattern = /^[0-9]{8}([A-Z]{1})$/;
    if(pattern.test(parametro))
    {
        dni = parametro.substring(0, parametro.length -1);
        charLetra = parametro.substring(parametro.length-1);
        var patternDni = /^[0-9]{8}$/;
        console.log(patternDni.test(dni));
        if(!isNaN(charLetra)||!patternDni.test(dni))
        {
            correcto = false;
        }
        else
        {
            posicion = dni % 23;
            letra = cadena.substring(posicion,posicion+1);
            if(letra !== charLetra.toUpperCase())
            {
                correcto = false;
            }
        }
    }
    else
    {
        correcto = false;
    }
    
    return correcto;
}

function comprobarTelefono(telefono)
{
   
    return /^\d{9}$/.test(telefono);
    
}
function bien(parametro)
{
    parametro.parentNode.parentNode.className = "form-group has-success";
}
function mal(parametro)
{
    parametro.parentNode.parentNode.className = "form-group has-error";
}

/*Fin */
/*Validación formulario usuarios */
function contieneEspacios(str)
{
    var espacios = false;
    for(var i=0; i<str.length; i++)
    {
        if(str.charAt(i) === " ")
        {
            espacios = true;
            break;
        }
    }
    return espacios;
}

function validarCampo(valor, expr_reg, campo)
{
    var errores="";
    error = true;
    //Compruebo que el valor del campo no este vacio
    if(valor.length === 0)
    {
        errores+="El campo "+campo+" no puede quedar vacio ";
        error = false;
    }
    else
    {
        if(!contieneEspacios(valor))
        {
            if(!expr_reg.test(valor))
            {
                errores+="\n El valor del campo "+campo+" es incorrecto\n debe contener al menos 3 caracteres y un máximo de 15 caracteres ";
                if(campo==="contraseña")
                {
                    errores+="\nDebe contener al menos 1 letra minuscula,\n 1 letra mayuscula y un numero\n cuya longitud sea entre 6 y 20 caracteres";
                }
                error = false;
            }
        }
        else
        {
            errores+="\nEl valor del campo "+campo+" tiene espacios";
            error = false;
        }
    }
    if(!error)
    {
        alert(errores);
    }
    return error;
}
/*Fin*/