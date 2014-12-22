 var modalBox = $("#box-modal"),
        modalBoxObject = modalBox[0],
        modaltitle = modalBoxObject.getElementsByClassName('modal-title')[0],
        modalContent = modalBoxObject.getElementsByClassName('modal-body')[0],
        modalFooter = modalBoxObject.getElementsByClassName('modal-footer')[0];
var debug = true;
var dataAjax='';
$("document").ready(function(){
   $("select#selectgama").change(function()
   {
       $("#inputproduct").parent().parent().removeClass("hidden");
       $("button[type=submit]").parent().removeClass("hidden");
       $("button[type=submit]").attr("disabled");
       
   });
   $("#date1").datepicker();
   $("#date2").datepicker();
   $("button#insert-order").click(function()
   {
      document.location.href = "/orders/insert"; 
   });
   
 $("#filtro_estado").change(function(){
     var optionselected = $(this).find("option:selected");
     var valueselected = optionselected.val();
     var filtro = $(this).attr('id');
    if(valueselected == '-1')
    {
         alert("tienes que seleccinar un estado");
    }
    else
    {
        filterCallback(valueselected, filtro);
    }
 });
 $("#filtro_referencia").keypress(function(event)
 {
    event.preventDefault();
    var key = event.witch;
    if(key == 13)
    {
        event.preventDefault();
        var value_ref = $(this).val();
        var filtro = $(this).attr('id');
        var is_valid = validarCampo(value_ref,/^d{3,6}$/);
        if (is_valid)
        {
            filterCallback(value_ref,filtro);
        }
        else
        {
             alert("solo puede aceptar el campo de filtro referencia digitos entre 3 y 6 caracteres");
        } 
    }
    
 });
 $("#search-provider").bind("focus keypress", function(){

        var valuesearch =$(this).val(); //Obtengo el valor de mi campo de ajax
        if (valuesearch.length > 2)
        {
            $.ajax({
                type:'GET',
                url:'/orders/provider/'+valuesearch,
                success: function(data)
                {
                    $("#proveedorindex").html(data);
              
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
                }});
         }
 });  
     
   $("#box-modal").on("shown",function(event)
   {
       $(this).find('.modal-body').css({
              width:'auto', 
              height:'auto',
              maxheight:'100%'
       });
     
       $("button#add-cart").click(function()
       {
            
           if($("#selectproductname").val()!=='-1')
           {
                var valueidproduct = $("#inputidproducto").val();
                var request = $.ajax({
                    type:'POST',
                    url: '/lineorder/addCart/'+valueidproduct,
                    data: $("#form-product").serialize(),
                    datatype:'html'
                    });
                request.done(function(html) {
                    $("#editor-cart").html( html);
                });
 
                request.fail(function( jqXHR, textStatus ) {
                    alert( "Request failed: " + textStatus );
                });
            }
       });
       
       
       $("#selectproductname").change(function(){
       var valueProduct = $(this).val();

       var oculto = $("#add-cart").hasClass('hidden');
       var descatalogado = $("#inputdescatalogado").is(":checked");
       if(descatalogado)
       {
            if(!oculto)
            {
                $("#add-cart").addClass('hidden');
            }
       }
       else
       {
           if(oculto)
           {
                $("#add-cart").removeClass('hidden');
           }
       }
       if(valueProduct ==='-1')
       {
           alert("Tienes que seleccionar un producto");
           $("#editor-panel").addClass("hidden");
           $("#editor-actions").addClass("hidden");
       }
       else
       {
       $("#editor-actions").removeClass("hidden");
       $("#editor-panel").removeClass("hidden");
       event.preventDefault();
       $.ajax({
          type:'GET',
          url:'/orders/products/list/'+valueProduct,
          async:true,
          datatype:'html',
          success: function(data)
          { 
              $("#editor-panel").html(data);
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
       }
    });     
});
 
   $("button#order-search").click(function(event){
      var str = $("#es_valid").val();
      var validacion = (str === "true") ? true : false;
      if(validacion)
      {
       //Recojo el valor del input
      var value=$("#inputproduct").val();
      //Recojo el valor del select
      var value1 = $('#selectgama').val();
      //elimino el comportamiento por defecto del enlace
      event.preventDefault();
      //Quito la clase oculta para mostrar la ventana modal
      $("#editor-header").removeClass('hidden');
      modalBox.modal('show'); //Muestro la ventana modal
      modaltitle.innerHTML = "Visualizar productos";
      //modalFooter.innerHTML = "<button type='button' class='btn btn-default' data-dismiss='modal'>Cerrar</button>";
      //Aquí pongo el codigo para llamar a los página de productos en ajax
      $.ajax({
      type:"POST",
      url:"/ajax/products_ajax.php",
      data: "parametro1="+value+"&parametro2="+value1+"&opcion=1",
      async:true,
      datatype:'html',
      success: function(data)
      {
          if(data.indexOf('alert')!==-1)
          {
              $("#editor-header").innerHTML='';
              $("#editor-panel").addClass('hidden');
              $("#editor-actions").addClass('hidden');
          }
          document.getElementById('editor-header').innerHTML = data;
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
      }
   }); 
});
filterCallback = function(value, filter)
{
    var url = "/orders/list/"+value+"/"+filter;
    $(location).attr('href',url); 
}
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
        sw = false;
    }
    else
    {
        $subcad = (valores.length === 1) ? "el" : "los";
        $subcad1 = (valores.length>1) ? "es ":" ";
        return confirm("Quieres eliminar "+$subcad+" indentificador"+$subcad1+""+valores.join(" , "));
        document.forms[0].submit();
    } 
    return sw; 
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
function mostrarErrores(arrayerror)
{
    var uniqueserror = [];
    $.each(arrayerror, function(i,el)
    {
        if($.inArray(el, uniqueserror) === -1)
           uniqueserror.push(el);
        
    });
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

function validarCampo(valor, expr_reg)
{
    error = true;
    //Compruebo que el valor del campo no este vacio
    if(valor.length === 0)
    {
        error = false;
    }
    else
    {
        if(!contieneEspacios(valor))
        {
            if(!expr_reg.test(valor))
            {
                error = false;
            }
        }
        else
        {
            error = false;
        }
    }
    return error;
}
/*Fin*/

function confirmationDelete(valor, identificador)
{

    var enviar;
    var cadena="¿Realmente desea eliminar el";
    //Hago un switch por si hago mas casos
    switch(identificador)
    {
        case 'empleado':
        {
            cadena+=" empleado:"+valor+"?";
           
        }
    }
    var confirmar = confirm(cadena);
    if(confirmar)
    {
       enviar = true;
        
    }
    else
    {
       enviar = false;
    }
    return enviar;
}


