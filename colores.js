document.addEventListener('DOMContentLoaded', function() {
    const objetivo = document.getElementById('objetivo');
    const botones = document.querySelectorAll('button');

    // Selecciona al azar un color como objetivo y lo aplica al botón correspondiente
    let colores = [
        { nombre: 'Rojo', clase_css: 'bg-red-500' },
        { nombre: 'Verde', clase_css: 'bg-green-500' },
        { nombre: 'Azul', clase_css: 'bg-blue-500' },
        { nombre: 'Amarillo', clase_css: 'bg-yellow-500' }
    ];

    let colorObjetivo = colores[Math.floor(Math.random() * colores.length)];
    objetivo.classList.add(colorObjetivo.clase_css);

    // Añade eventos de clic a cada botón para verificar la selección del usuario
    botones.forEach(boton => {
        boton.addEventListener('click', function() {
            let colorSeleccionado = this.getAttribute('data-color');
            if (colorSeleccionado === colorObjetivo.nombre) {
                alert('¡Increíble! ¡Has acertado!');
                // Aquí puedes agregar la lógica para guardar los puntos o el logro correspondiente
            } else {
                alert('¡Casi! Intenta otra vez.');
            }
        });
    });
});