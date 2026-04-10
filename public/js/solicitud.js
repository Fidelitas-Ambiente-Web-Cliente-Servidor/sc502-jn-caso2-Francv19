
function mostrarMensaje(msg, tipo = "success") {
    let div = $("#mensaje");
    div.text(msg)
        .removeClass()
        .addClass(tipo)
        .fadeIn();

    setTimeout(() => {
        div.fadeOut();
    }, 3000);
}
