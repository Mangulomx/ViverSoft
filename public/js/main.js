function deleteconfirm(identificador)
{
    var confirmar = confirm("Quieres borrar el identificador "+identificador);
    if(!confirmar)
    {
       return false; 
    }
    else
    {
        document.forms[0].action = "/users/"+identificador; 
    }
}