document.addEventListener('DOMContentLoaded', () => {
    const mensajeForm = document.getElementById('mensajeForm');
    const mensajesLista = document.getElementById('mensajesLista');
    const descargarPDFBtn = document.getElementById('descargarPDF');

    // Cargar mensajes al iniciar
    cargarMensajes();

    // Manejar envío de formulario
    mensajeForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            nombre: document.getElementById('nombre').value,
            email: document.getElementById('email').value,
            mensaje: document.getElementById('mensaje').value
        };

        try {
            const response = await fetch('guardar_mensaje.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            if (response.ok) {
                mensajeForm.reset();
                cargarMensajes();
                mostrarNotificacion('Mensaje enviado correctamente', 'success');
            } else {
                throw new Error('Error al enviar el mensaje');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarNotificacion('Error al enviar el mensaje', 'error');
        }
    });

    // Manejar descarga de PDF
    descargarPDFBtn.addEventListener('click', generarPDF);

    // Función para cargar mensajes
    async function cargarMensajes() {
        try {
            const response = await fetch('obtener_mensajes.php');
            const mensajes = await response.json();
            
            mensajesLista.innerHTML = mensajes.map(mensaje => `
                <div class="mensaje-card mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted">${mensaje.nombre} (${mensaje.email})</h6>
                            <p class="card-text">${mensaje.mensaje}</p>
                            <small class="text-muted">${new Date(mensaje.fecha).toLocaleString()}</small>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error al cargar mensajes:', error);
            mostrarNotificacion('Error al cargar los mensajes', 'error');
        }
    }

    // Función para generar PDF
    async function generarPDF() {
        try {
            const response = await fetch('obtener_mensajes.php');
            const mensajes = await response.json();

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            // Título
            doc.setFontSize(20);
            doc.text('Mensajes Recibidos', 105, 20, { align: 'center' });
            
            // Contenido
            doc.setFontSize(12);
            let y = 40;
            
            mensajes.forEach((mensaje, index) => {
                const fecha = new Date(mensaje.fecha).toLocaleString();
                const texto = `${mensaje.nombre} (${mensaje.email}) - ${fecha}\n${mensaje.mensaje}`;
                
                const lineas = doc.splitTextToSize(texto, 180);
                
                if (y + (lineas.length * 7) > 280) {
                    doc.addPage();
                    y = 20;
                }
                
                doc.text(lineas, 15, y);
                y += (lineas.length * 7) + 10;
            });

            // Guardar PDF
            doc.save('mensajes.pdf');
            mostrarNotificacion('PDF generado correctamente', 'success');
        } catch (error) {
            console.error('Error al generar PDF:', error);
            mostrarNotificacion('Error al generar el PDF', 'error');
        }
    }

    // Función para mostrar notificaciones
    function mostrarNotificacion(mensaje, tipo) {
        const notificacion = document.createElement('div');
        notificacion.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed top-0 end-0 m-3`;
        notificacion.style.zIndex = '1000';
        notificacion.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(notificacion);
        
        setTimeout(() => {
            notificacion.remove();
        }, 3000);
    }
}); 