let pipBack = document.querySelector("#backPip");
let pipContent = document.querySelector("#containerPip"); //Contenido que irá en la burbuja
let pipWindow = null;

const openBurbuja = () => {
    if (!("documentPictureInPicture" in window)) {
      console.log("PIP mode not supported");
      return;
    }
    openPIP();
}

const openPIP = async() => {
    
    let options = {
      width: 200,
      height: 100,
      disallowReturnToOpener: true
    };
    pipWindow = await documentPictureInPicture.requestWindow(options);
    
    //Copiar Styles
    [...document.styleSheets].forEach((styleSheet) => {
    try {
          const link = document.createElement('link');
          link.rel = 'stylesheet';
          link.type = styleSheet.type;
          link.media = styleSheet.media;
          link.href = styleSheet.href;
          pipWindow.document.head.appendChild(link);
        } catch (e) {
          console.log("No se pudo copiar los estilos");
        }
    });
    pipWindow.document.body.append(pipContent);
    
    pipWindow.addEventListener("pagehide", function () {
        console.log("SE cerro el PIP");
        pipBack.append(pipContent);
     });
}

const resizePip = () => {
    // pipWindow.resizeBy(100, 0); //Suma lo que tiene actualmente
    pipWindow.resizeTo(350, 180);
}

