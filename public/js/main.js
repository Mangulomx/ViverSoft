 var modalBox = $("#box-modal"),
        modalBoxObject = modalBox[0],
        modaltitle = modalBoxObject.getElementsByClassName('modal-title')[0],
        modalContent = modalBoxObject.getElementsByClassName('modal-body')[0],
        modalFooter = modalBoxObject.getElementsByClassName('modal-footer')[0];
var debug = true;

$("document").ready(function(){
   $("select#selectgama").change(function()
   {
       $("#inputproduct").parent().parent().removeClass("hidden");
       $("button[type=submit]").parent().removeClass("hidden");
       $("button[type=submit]").attr("disabled");
       
   });

 $("#search-provider").bind("focus keypress", function(event){

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
       $("#add-cart").click(function(e)
       {
           alert("hola");
           e.preventDefault();
           if($("#selectproductname").val()!=='-1')
           {
               var oculto = $("#add-cart").hasClass('hidden');
               var descatalogado = $("#inputdescatalogado").is(":checked");
               if(!descatalogado)
               {
                   if(oculto)
                   {
                        $("#add-cart").removeClass('hidden');
                   }
                    var valueidproduct = $("#inputidproducto").val();
                    $.ajax({
                        type:'GET',
                        url: '/lineorder/addCart/'+valueidproduct
                    });
                }
                else
                {
                    if(!oculto)
                    {
                        $("#add-cart").addClass("hidden");
                    }
           
                }
            }
       });
       $("#selectproductname").change(function(){
       var valueProduct = $(this).val();
       if(valueProduct ==='-1')
       {
           alert("Tienes que seleccionar un producto");
       }
       else
       {
       $("#editor-panel").removeClass("hidden");
       event.preventDefault();
       $.ajax({
          type:'GET',
          url:'/ajax/products_ajax.php?idProducto='+valueProduct+'&opcion=2',
          async:true,
          datatype:'text',
          success: function(data)
          { 
              $("#editor-panel").load("/ajax/order.html", function(responseText, statusTxt, xhr)
              {
                  if(statusTxt=="success")
                  {
                      var inputs = document.getElementById('editor-panel').getElementsByTagName('input');
                      console.log("datos"+data);
                      mostrarDatos(inputs,data);
                  }
                  if(statusTxt=="error")
                  {
                      alert("Error:"+xhr.status+":"+xhr.statusText);
                  }
              });
              $("#editor-actions").removeClass("hidden");
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
    
    function mostrarDatos(inputs, data)
    {
        var descatalogado = data.substring(data.lastIndexOf('*')+1,data.lastIndexOf('@'));
        var identificador = data.substring(0, data.indexOf("#"));
        identificador = identificador.replace(/\s/g,'');
        var datos = data.substring(0,data.lastIndexOf("*"));
        inputdata = datos.substring(datos.indexOf('#')+1);
        inputdata = inputdata.split('*');
        inputs[0].value = inputdata[0];
        $("#descripciontext").val(inputdata[2]);
        var checked = (descatalogado ==='1' )?true:false;
        $("#inputdescatalogado").prop("checked",checked);
        $("#editor-actions").removeClass("hidden");
        $("#inputidproducto").val(identificador);
    }
      
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


