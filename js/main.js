document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const mensajesLista = document.getElementById('mensajesLista');

    // Cargar mensajes al iniciar
    cargarMensajes();

    contactForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('nombre', document.getElementById('nombre').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('mensaje', document.getElementById('mensaje').value);

        fetch('procesar.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Mensaje enviado correctamente');
                contactForm.reset();
                cargarMensajes();
            } else {
                alert('Error al enviar el mensaje');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al enviar el mensaje');
        });
    });

    function cargarMensajes() {
        fetch('obtener_mensajes.php')
            .then(response => response.json())
            .then(data => {
                mensajesLista.innerHTML = '';
                data.forEach(mensaje => {
                    const mensajeElement = document.createElement('div');
                    mensajeElement.className = 'mensaje-item';
                    mensajeElement.innerHTML = `
                        <strong>${mensaje.nombre}</strong> (${mensaje.email})<br>
                        <p>${mensaje.mensaje}</p>
                        <small>${mensaje.fecha}</small>
                    `;
                    mensajesLista.appendChild(mensajeElement);
                });
            })
            .catch(error => {
                console.error('Error al cargar mensajes:', error);
            });
    }
}); 