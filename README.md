# Kanban por proyectos

Aplicación web en PHP y JavaScript para organizar tareas en tableros kanban, con un tablero y un bloc de notas para cada proyecto. Guarda todos los datos en ficheros JSON y HTML, sin base de datos.

## Qué hace

- **Proyectos**: crear, editar y deshabilitar proyectos. Deshabilitar un proyecto no borra sus datos, solo lo oculta. Hay una vista con todos los proyectos, activos e inactivos, y se puede marcar uno como proyecto por defecto.
- **Tablero kanban**: cuatro columnas con el nombre que elijas para cada proyecto. Las tareas se mueven entre columnas arrastrándolas y se borran soltándolas en la papelera, arriba a la derecha.
- **Plantillas**: al crear una tarea puedes partir de una plantilla de texto definida en `json/plantillas.json`.
- **Editor de texto enriquecido**: la descripción de las tareas se escribe con Quill (negrita, cursiva, listas, subíndices…).
- **Chat de anotaciones**: cada proyecto tiene su propio chat para dejar notas, copiarlas o borrarlas. Se actualiza solo cada pocos segundos.
- **Multidioma**: español, inglés y francés. Los textos están en `idiomas/`.

## Tecnologías

- PHP (sin frameworks)
- JavaScript y jQuery
- [dragula](https://github.com/bevacqua/dragula) para arrastrar y soltar
- [Quill](https://quilljs.com/) y quilljs-textarea para el editor
- Bootstrap 3 y Bulma para los estilos
- Ficheros JSON y HTML para guardar los datos

## Cómo ejecutarlo en local

Necesitas PHP 7.4 o superior (probado con PHP 8.3) y conexión a internet, porque algunas librerías se cargan desde CDN.

```bash
git clone <url-del-repositorio>
cd KanbanCompleto
php -S localhost:8000
```

Después abre <http://localhost:8000> en el navegador.

Si no tienes PHP instalado, puedes usar Docker:

```bash
docker run --rm -p 8000:8000 -v "$PWD":/app -w /app php:8.3-cli php -S 0.0.0.0:8000
```

La aplicación escribe en `json/` y en `chat/chats/`, así que esas carpetas necesitan permisos de escritura. El repositorio trae tres proyectos de ejemplo con datos inventados.

## Capturas

**Lista de proyectos**

![Lista de proyectos](capturas/proyectos.png)

**Tablero de un proyecto**

![Tablero kanban](capturas/tablero.png)

**Nueva tarea con plantilla y editor Quill**

![Nueva tarea](capturas/nueva-tarea.png)

**Chat de anotaciones del proyecto**

![Chat de anotaciones](capturas/chat.png)

## Estructura

```
index.php          Vistas principales: proyectos, tablero, crear/editar/deshabilitar, idioma
kanban.php         Añadir, editar, mover y borrar tareas
kanban.js          Lógica del tablero en el navegador (arrastrar, llamadas AJAX)
json/              proyectos.json, plantillas.json y un .json por cada tablero
chat/              Chat de anotaciones; chat/chats/ guarda un .html por proyecto
idiomas/           Textos en es, en y fr
css/, images/      Estilos e imágenes
```

## Contexto

Hice este proyecto en 2023, durante mis prácticas del ciclo de SMR (Sistemas Microinformáticos y Redes), y lo programé sin ayuda de IA.

Lo publico prácticamente tal como estaba, porque refleja cómo programaba entonces. Para subirlo solo he hecho lo mínimo:

- He quitado las referencias a la empresa donde hice las prácticas.
- He sustituido los datos reales por datos de ejemplo inventados.
- He borrado archivos de librerías que no se usaban.
- He pasado al español algunos comentarios que estaban en inglés.
- He traducido cuatro textos que se habían quedado fijos en español y he corregido la pista de las columnas, que decía «separadas por comas» cuando se separan con `#`.
- He corregido el enlace al chat (`./chat` → `./chat/`). Sin la barra final, con `php -S` el chat se quedaba sin estilos y no podía enviar notas.
- He corregido cuatro detalles que impedían que funcionara en PHP 8:
  - claves de array sin comillas;
  - una coma sobrante en `idiomas/en.json`;
  - un `session_start()` en el chat que iba después de enviar HTML;
  - un `header()` en `kanban.php` que también iba después de enviar HTML.

## Créditos

- **`chat/`** es una adaptación de [minchat](https://github.com/YunoHost-Apps/minchat_ynh) (licencia MIT, © 2015 Chtixof). Minchat, a su vez, se basa en [wojtek77/chat](https://github.com/wojtek77/chat) y en un tutorial de Tuts+. Su licencia está en [`chat/LICENSE`](chat/LICENSE).
- **[Quill](https://quilljs.com/)** es una librería de terceros (licencia BSD-3-Clause). Se carga desde su CDN; `quilljs-textarea-master/quill.snow.css` es su hoja de estilos.
- **[quilljs-textarea](https://github.com/tangien/quilljs-textarea)** es una librería de terceros (licencia MIT, © 2019 Dinesh Kumar). Está en `quilljs-textarea-master/`, con su licencia en [`quilljs-textarea-master/LICENSE`](quilljs-textarea-master/LICENSE).
- Otras librerías de terceros: [Bootstrap 3.3.5](https://getbootstrap.com/) (MIT, incluida en `css/bootstrap.css`), [Bulma](https://bulma.io/) (MIT), [jQuery](https://jquery.com/) (MIT), [dragula](https://github.com/bevacqua/dragula) (MIT) y [Font Awesome 4](https://fontawesome.com/v4/). Todas salvo Bootstrap se cargan desde CDN.
- Las banderas de `images/` son de [flag-icons](https://github.com/lipis/flag-icons) (MIT).
