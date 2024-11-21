function myFunction() {
  var options = document.getElementById("navOptions");
  if (options.className === "navoptions") {
    options.className += " responsive";
  } else {
    options.className = "navoptions";
  }
}
const container = document.getElementById("container");
const registerBtn = document.getElementById("register");

const loginBtn = document.getElementById("login");

registerBtn.addEventListener("click", () => {
    container.classList.add("active");
});

loginBtn.addEventListener("click", () => {
    container.classList.remove("active");
});

// Elementos del DOM
const dropArea = document.querySelector('.upload-area');
const browseBtn = document.querySelector('.browse');
const fileInput = document.createElement('input');
fileInput.type = 'file';
fileInput.hidden = true;
fileInput.accept = 'image/*'; // Solo acepta imágenes
fileInput.multiple = false;  // Una sola imagen
document.body.appendChild(fileInput);

// Prevenir comportamiento por defecto
['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

// Efectos visuales al arrastrar
['dragenter', 'dragover'].forEach(eventName => {
    dropArea.addEventListener(eventName, highlight, false);
});

['dragleave', 'drop'].forEach(eventName => {
    dropArea.addEventListener(eventName, unhighlight, false);
});

function highlight() {
    dropArea.style.borderColor = '#3b82f6';
    dropArea.style.backgroundColor = '#f3f4f6';
}

function unhighlight() {
    dropArea.style.borderColor = '#e1e1e1';
    dropArea.style.backgroundColor = '#ffffff';
}

// Manejar la subida de archivos
dropArea.addEventListener('drop', handleDrop);
fileInput.addEventListener('change', function() {
    handleFiles(this.files);
});
browseBtn.addEventListener('click', () => fileInput.click());

function handleDrop(e) {
    const dt = e.dataTransfer;
    const files = dt.files;
    handleFiles(files);
}

function handleFiles(files) {
    const file = files[0]; // Solo tomamos el primer archivo

    // Validar que sea una imagen
    if (!file.type.startsWith('image/')) {
        showMessage('Por favor, selecciona un archivo de imagen válido (JPG, PNG, GIF)', 'error');
        return;
    }

    // Validar tamaño (máximo 5MB)
    if (file.size > 5 * 1024 * 1024) {
        showMessage('La imagen no debe superar los 5MB', 'error');
        return;
    }

    // Mostrar vista previa
    showPreview(file);
}

function showPreview(file) {
    // Crear contenedor para la vista previa
    const previewContainer = document.createElement('div');
    previewContainer.className = 'preview-container';
    previewContainer.style.marginTop = '1rem';
    previewContainer.style.position = 'relative';

    // Crear la imagen de vista previa
    const img = document.createElement('img');
    img.style.maxWidth = '100%';
    img.style.maxHeight = '200px';
    img.style.borderRadius = '8px';
    
    // Botón para eliminar
    const removeButton = document.createElement('button');
    removeButton.innerHTML = '×';
    removeButton.style.position = 'absolute';
    removeButton.style.top = '5px';
    removeButton.style.right = '5px';
    removeButton.style.backgroundColor = '#ff4444';
    removeButton.style.color = 'white';
    removeButton.style.border = 'none';
    removeButton.style.borderRadius = '50%';
    removeButton.style.width = '25px';
    removeButton.style.height = '25px';
    removeButton.style.cursor = 'pointer';
    
    // Leer y mostrar la imagen
    const reader = new FileReader();
    reader.onload = function(e) {
        img.src = e.target.result;
        
        // Remover vista previa anterior si existe
        const oldPreview = document.querySelector('.preview-container');
        if (oldPreview) oldPreview.remove();
        
        // Añadir nueva vista previa
        previewContainer.appendChild(img);
        previewContainer.appendChild(removeButton);
        dropArea.parentNode.insertBefore(previewContainer, dropArea.nextSibling);
    }
    reader.readAsDataURL(file);

    // Evento para eliminar la imagen
    removeButton.addEventListener('click', function() {
        previewContainer.remove();
        fileInput.value = ''; // Limpiar input
    });
}

function showMessage(message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.textContent = message;
    messageDiv.style.padding = '10px';
    messageDiv.style.marginTop = '10px';
    messageDiv.style.borderRadius = '4px';
    messageDiv.style.textAlign = 'center';

    if (type === 'error') {
        messageDiv.style.backgroundColor = '#fee2e2';
        messageDiv.style.color = '#dc2626';
    } else {
        messageDiv.style.backgroundColor = '#d1fae5';
        messageDiv.style.color = '#059669';
    }

    // Remover mensaje anterior si existe
    const oldMessage = document.querySelector('.message');
    if (oldMessage) oldMessage.remove();

    messageDiv.className = 'message';
    dropArea.parentNode.insertBefore(messageDiv, dropArea.nextSibling);

    // Remover mensaje después de 3 segundos
    setTimeout(() => messageDiv.remove(), 3000);
}