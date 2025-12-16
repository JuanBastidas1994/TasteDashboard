class Lienzo {
    constructor(nameElement,width,height) {
        this.nameElement = nameElement;
        this.width = width;
        this.height = height;
        
        /*UNDO*/
        this.numCambios = 0;
        this.objetos = [];
        this.objetosTipos = [];
        
        /*REDO*/
        this.redoNum = 0;
        this.redoObjetos = [];
        this.redoData = [];
    
        this.lienzoAncho = 0;
        this.lienzoAlto = 0;
        this.AlturaImgFondo = 0;
        this.AnchoImgFondo = 0;
        
        this.fondo = {
            width: 0,
            height: 0,
            x: 0,
            y:0,
            src: ''
        }
    
        this.StartDelete = false;
        this.sizeMiniature = 1;
        this.x1_global, this.y1_global, this.x2_global, this.y2_global;
        this.createimage = false;
        this.createline = false;
        this.createsquare = false;
        this.createcircle = false;
        this.createdraw = true;
        this.color_selected = "azul"; //NO SIRVE
        this.colorText = "blue";	//COLOR DE CUALQUIER COSA QUE SE HAGA EN EL LIENZO.
        this.colorCrop = "#04682E";
        this.mode = 'brush'; 
        this.arrow = false;
        this.dash = false;
        this.crop = false;
        this.SquareFill = false;
        this.bold = "normal";
        
        this.line_ancho = 2;
        this.underline = "underline";
        this.letsdraw =null;
        this.object_selected = null;
        this.line_selected = null;
        this.square_selected = null;
        this.circle_selected = null;
        this.layer;
        this.layerFondo;
        this.context;
        
        //ELEMENTOS
        this.CanvasFondo;
        this.imageFondo;
        this.balon;	//VARIABLE DE IMAGENES PREDISEÑADAS DEL CANVAS.
        this.simpleText;
        this.imageCanvas;
        this.Line;
        this.stage;
        this.canvas;
        this.imgAux;
        
        this.EditarText;
        
        this.IsChange = false;
        
        this.isPaint = false;
        this.drawingLine = null; // handle to the line we are drawing
        this.lastPointerPosition;
        
        this.crear_canvas(this.width, this.height);
    }
    
    CanvasResize(ancho, alto){
    
        this.stage.width(ancho);
        this.stage.height(alto);
        this.stage.draw();
        
        this.width = ancho;
        this.height = alto;
        //this.CanvasFondo.width(ancho);
        //this.CanvasFondo.height(alto);
        
        /*ACTUALIZAR TAMAÑO IMAGEN*/
        this.lienzoAncho = ancho;
        this.lienzoAlto = alto;
        
        var ratio = this.CanvasFondo.width() / this.CanvasFondo.height();
         console.log(ratio);
        
        this.CanvasFondo.width(ancho);
        this.CanvasFondo.height(ancho / ratio);
        this.CanvasFondo.x((ancho - this.CanvasFondo.width()) / 2);
        this.CanvasFondo.y((alto - this.CanvasFondo.height()) / 2);

        this.stage.draw();
        //this.limpiarLayer();
        
        /*
        var scale = ancho / this.width;
        this.stage.width(this.width * scale);
        this.stage.height(this.height * scale);
        this.stage.scale({ x: scale, y: scale });
        this.stage.draw();
        */
        
        //this.width = ancho;
        //this.height = alto;
    }
      
    
    
    CanvasImage(event) { 
		var imagen = document.getElementById("archivo").files[0]; 
		var reader = new FileReader(); 
		
		reader.onload = function () { 
			var img = new Image();
			 img.src = reader.result;
			    img.onload = function() {
		            var width = window.innerWidth;
		            var height = window.innerHeight;
		            if(img.width > width)
		            {
		              do
		              {
		                width = width - 100;
		                height = (width * img.height) / img.width;
		              }
		              while(height > window.innerHeight);
					        crear_canvas(width, height, reader.result);
		            }
		            else if (img.height > window.innerHeight)
		            {
		              do
		              {
		                height = height - 100;
		                width = (height * img.width) / img.height;
		              }
		              while(width > window.innerWidth);
		              crear_canvas(width, height, reader.result);
		            }
		            else
		            {
		              crear_canvas(img.width, img.height, reader.result);
		            }
			    };
		} 
		
		reader.readAsDataURL(imagen); 
	} 


	
   crear_canvas(width, height)
   {
       var lienzoActual = this;
    	$("#"+this.nameElement).css("display","");
    	
    	$("#"+this.nameElement).css("width",width);
    	$("#"+this.nameElement).css("height",height);
    	
    	$("#"+this.nameElement).on('click tap', function(e){
    	    GetlienzoActivo(this.nameElement);
    	}.bind(this));

        this.lienzoAncho = width;
        this.lienzoAlto = height;

    	this.stage = new Konva.Stage({
            container: this.nameElement,
            width: width,
            height: height
        });
      
        this.layerFondo = new Konva.Layer({name:'layer-fondo'});
        this.stage.add(this.layerFondo);
    
        this.layer = new Konva.Layer({name:'layer-draw'});
        this.stage.add(this.layer);  
    
        /*INICIO IMAGEN DE FONDO*/
        this.fondo.width = width;
        this.fondo.height = height;
        this.fondo.x = 0;
        this.fondo.y = 0;
        this.fondo.src = 'plugins/canvas/transparent.png';
        this.imageFondo = new Image();
        this.imageFondo.onload = function() {
            if(this.CanvasFondo){
                this.CanvasFondo.remove();
            }
            
            this.CanvasFondo = new Konva.Image({
                image: this.imageFondo,
                x : this.fondo.x,
                y : this.fondo.y,
                stroke: 'transparent',
                width: this.fondo.width,
                height: this.fondo.height,
                draggable: true,
                shadowBlur: 0,
                name: 'fondo',
                id: 'fondo',
                src: this.fondo.src,
                //name: 'objeto'
            });
            this.layerFondo.add(this.CanvasFondo);
            this.stage.draw();
        }.bind(this);
        this.imageFondo.src = 'plugins/canvas/transparent.png';
        this.imageFondo.id = 'fondo';
        this.layerFondo.listening(false);
         /*FIN IMAGEN DE FONDO*/
         
        /*
        var circle = new Konva.Circle({
            x: 150,
            y: 150,
            radius: 70,
            fill: 'transparent',
            stroke: 'red',
            strokeWidth: 3,
            draggable: true,
            name: 'objeto'
          });  
          this.layer.add(circle);
          this.layer.draw();*/
    
        this.isPaint = false;
        this.drawingLine = null; // handle to the line we are drawing
        this.lastPointerPosition;
           
           
        this.stage.on('mouseout', function() {
                //console.log("MOUSE OUT");
        });
       
        this.stage.on('mouseup touchend', this.stageMouseUp.bind(this));
        this.stage.on('mousemove touchmove', this.stageMouseMove.bind(this));
        this.stage.on('mousedown touchstart', this.stageMouseDown.bind(this));
        //this.stage.on('click tap', this.stageClick.bind(this));
        
        this.stage.on('click tap', function(e){
            $(".Tools").css("display","none");
            
            console.log(e.target);
            
            
            
            if (e.target === this.stage) {
              this.stage.find('Transformer').destroy();
              this.layer.draw();
              return;
            }
            
            // ESTE CODIGO SOLO VA SI TIENE IMAGEN DE FONDO
            if (e.target.hasName('fondo')) {
                this.object_selected = e.target;
                
                if(this.createline == true || this.createimage == true || this.createsquare == true || this.createcircle == true || this.createdraw == true){
                    return;
                }
            
                // remove old transformers
                this.stage.find('Transformer').destroy();
    
                var tr = new Konva.Transformer();
                this.layerFondo.add(tr);
                tr.attachTo(e.target);
                this.layerFondo.draw();
              //this.stage.find('Transformer').destroy();
              //this.layer.draw();
              return;
            }
            
            //ESTE CODIGO ES PARA A UN GRUPO PONERLE TRANSFORMER
            if(e.target.hasName('grupo')){
                var aux = e.target;
                var objeto = aux.attrs.grupo;
                // remove old transformers
                this.stage.find('Transformer').destroy();
                
                const tr = new Konva.Transformer({
                  node: objeto
                })
                this.layerFondo.add(tr);
                this.layerFondo.draw();
                return;
            }
    
            // do nothing if clicked NOT on our rectangles
            if (!e.target.hasName('objeto')) {
              return;
            }
            
            this.object_selected = e.target;
            
            // remove old transformers
            this.stage.find('Transformer').destroy();
            
            if(this.createline == true || this.createimage == true || this.createsquare == true || this.createcircle == true || this.createdraw == true){
                return;
            }

            var tr = new Konva.Transformer();
            this.layer.add(tr);
            tr.attachTo(e.target);
            this.layer.draw();
        }.bind(this));

   }
    /* FIN CREAR CANVAS*/ 
    stageMouseUp(){
        this.isPaint = false;
        this.drawingLine = null;
    }    
    
    stageMouseMove(){
        	 var mousePos = this.stage.getPointerPosition();
        	if(this.createline || this.createsquare || this.createcircle)
        	{
    	        if(this.letsdraw !== null)
    	        {
        	    	  if(this.line_selected !== null){
                        this.line_selected.remove();
                        this.objetos.splice(this.numCambios-1,1);
                        this.objetosTipos.splice(this.numCambios-1,1);
                        this.numCambios--;
                      }
                      if(this.square_selected !== null){
                        this.square_selected.remove();
                        this.objetos.splice(this.numCambios-1,1);
                        this.objetosTipos.splice(this.numCambios-1,1);
                        this.numCambios--;
                      }
                      if(this.circle_selected !== null){
                         this.circle_selected.remove();
                        this.objetos.splice(this.numCambios-1,1);
                        this.objetosTipos.splice(this.numCambios-1,1);
                        this.numCambios--;
                      }
                      
        		     
        		     var objetotipoLine; 
        		     if(this.createline){
        		         if(this.arrow)
            	         	objetotipoLine = this.drawArrow(this.letsdraw.x, this.letsdraw.y, mousePos.x, mousePos.y);
            	         else	       
            	         	objetotipoLine = this.drawLine(this.letsdraw.x, this.letsdraw.y, mousePos.x, mousePos.y);
        		     }
        		     if(this.createsquare){
        		         objetotipoLine = this.Square(this.letsdraw.x, this.letsdraw.y, mousePos.x, mousePos.y);
        		     }
        		     if(this.createcircle){
        		         objetotipoLine = this.Circle(this.letsdraw.x, this.letsdraw.y, mousePos.x, mousePos.y);
        		     }
        
                     var data = {
                       tipo: "linea",
                       evento: "creacion"
                     }
        
                     this.objetos[this.numCambios] = objetotipoLine;
                     this.objetosTipos[this.numCambios] = data;
                     this.numCambios++;
    
    		         this.x1_global = this.letsdraw.x;
    		         this.y1_global = this.letsdraw.y;
    		         this.x2_global = mousePos.x;
    		         this.y2_global = mousePos.y;
    	        }
    	   }
    	   else if(this.createdraw)
    	   {
    	   	   if (!this.isPaint) {
    		        return;
    		      }
              var pos = this.stage.getPointerPosition();
              this.drawingLine.points(this.drawingLine.points().concat(pos.x,pos.y)); 
              this.layer.draw();
    	  }
    }
    
    addCambio(element, tipo){
        this.object_selected = element;
        this.objetos[this.numCambios] = element;
        var data = {
            tipo: tipo,
            evento: "mover",
            x: element.getX(),
            y: element.getY()
        }
        
        this.objetosTipos[this.numCambios] = data;
        this.numCambios++;
    }

    stageMouseDown(){
    	    var mousePos = this.stage.getPointerPosition();
    	    if(this.createline || this.createsquare || this.createcircle)
    	    {
        	    if(this.letsdraw !== null)
        	    {
        	        if(this.createcircle){
        	            
        	        }
        	        if(this.createsquare && this.crop){
                        var imgNuevo = this.ResizeImage(this.CanvasFondo.image(), this.CanvasFondo.width(), this.CanvasFondo.height());
                        
                        this.imgAux = new Konva.Image({
                            image: imgNuevo,
                            x: this.CanvasFondo.x(),
                            y: this.CanvasFondo.y(),
                            width: this.CanvasFondo.width(),
                            height: this.CanvasFondo.height(),
                            draggable: true,
                            name: 'objeto'
                        });
                        
                        this.imgAux.on('dragstart', function() {
                            var element = this.imgAux;
                            this.addCambio(element, "imagen");
                     	 }.bind(this));
                        
                        this.layer.add(this.imgAux);
                        
                        
                        //CAMBIAR DE COLOR
                        /*
                        this.imgAux.cache();
                        this.imgAux.filters([Konva.Filters.RGB]);
                        
                        this.imgAux.red(parseFloat(255));
                        this.imgAux.blue(parseFloat(0));
                        this.imgAux.green(parseFloat(0));
                        */
                        
                        this.imgAux.cropX(this.square_selected.x()-this.imgAux.x());
                        this.imgAux.cropY(this.square_selected.y()-this.imgAux.y());
                        this.imgAux.cropWidth(this.square_selected.width());
                        this.imgAux.cropHeight(this.square_selected.height());
                        
                        this.imgAux.x(this.square_selected.x());  
                        this.imgAux.y(this.square_selected.y());  
                        this.imgAux.width(this.square_selected.width());
                        this.imgAux.height(this.square_selected.height());
                        
                        var rectGray = new Konva.Rect({
                            x: this.square_selected.x(),
                            y: this.square_selected.y(),
                            width: this.square_selected.width(),
                            height: this.square_selected.height(),
                            name: 'noMove',
                            fill: this.colorCrop,
                            stroke: this.colorCrop,
                            strokeWidth: 1,
                            shadowBlur: 0,
                            cornerRadius: 0,
                            draggable: false,
                            opacity: 0.5
                         });
                        this.layer.add(rectGray);
                        
                        this.layer.draw();
                        
                        rectGray.moveToBottom();
                        this.imgAux.moveToTop();
                        
                        this.square_selected.remove();
                        
                        this.layer.draw();
                        
                        
                        /*AGREGAR PASO CUADRADO*/
                        var data = {
                           tipo: "cuadrado",
                           evento: "creacion"
                         }
            
                         this.objetos[this.numCambios] = rectGray;
                         this.objetosTipos[this.numCambios] = data;
                         this.numCambios++;
                         
                         /*AGREGAR PASO IMAGEN*/
                         data = {
                           tipo: "imagen",
                           evento: "creacion"
                         }
            
                         this.objetos[this.numCambios] = this.imgAux;
                         this.objetosTipos[this.numCambios] = data;
                         this.numCambios++;
                        
                        
                        //CANCELAR SEGUIR RECORTANDO
                        this.cancelDrawLine();
                    }
                    
                    this.letsdraw = null;
                    this.line_selected = null;
                    this.square_selected = null;
                    this.circle_selected = null;
                    
    	            
    	            //REGRESAR A SELECCION DESPUES DE CUALQUIER HERRAMIENTA DE TRAZO
    	            //this.cancelDrawLine();
        	    }
        	    else
        	    {
    		           this.letsdraw = {
    			      		x: mousePos.x,
    			      		y: mousePos.y
    			      }
    		   }
    	   }
    	   else if(this.createdraw)
    	   {
    	   	    this.isPaint = true;
                this.lastPointerPosition = this.stage.getPointerPosition();
        
                this.isPaint = true;
                var pos = this.stage.getPointerPosition();
                this.drawingLine = this.newLine(pos.x, pos.y);
                this.drawingLine.points(this.drawingLine.points().concat(pos.x,pos.y)); 
                this.layer.draw();
    	   }
    }
    
    ResizeImage(base64, width, height){
        var img = document.createElement("img");
        img = base64;
        
        var canvas = document.createElement("canvas");
        canvas.width = width;
        canvas.height = height;
 
        var ctx = canvas.getContext("2d");
        ctx.drawImage(img, 0, 0, width, height);
        var dataurl = canvas.toDataURL('image/jpeg');
        
        var imgReturn = document.createElement("img");
        imgReturn.src= dataurl;
        return imgReturn;
    }
    
    videoFondo(video){
        //this.CanvasFondo.fillPatternImage(video);
        this.CanvasFondo.image(video);
        
        var ratio = video.videoWidth / video.videoHeight;
        //img.width = this.lienzoAncho;
        //img.height = this.lienzoAncho / ratio;
        console.log("Alto del lienzo: "+this.lienzoAlto);
        console.log("Alto del Canvas Fondo: "+this.CanvasFondo.height());
        
        this.CanvasFondo.width(this.lienzoAncho);
        this.CanvasFondo.height(this.lienzoAncho / ratio);
        this.CanvasFondo.x((this.lienzoAncho - this.CanvasFondo.width()) / 2);
        this.CanvasFondo.y((this.lienzoAlto - this.CanvasFondo.height()) / 2);
        
        this.layerFondo.draw();
    }
    
    cacheFondo(){
        //this.CanvasFondo._clearCache('patternImage');
        this.layerFondo.draw();
    }
   
    toogleListeningFondo(booleano){
        this.layerFondo.listening(booleano);
        this.cancelDrawLine();
        this.stage.find('Transformer').destroy();
        this.stage.draw();
    }

    newLine(x,y){
        var composite = "source-over";
          if(this.mode == "brush")
            composite = "source-over";
          else
            composite = "destination-out";

        var line = new Konva.Line({
          points: [x,y,x,y],
          stroke: this.colorText,
          strokeWidth: this.line_ancho,
          lineCap: 'round',
          lineJoin: 'round',
          globalCompositeOperation : composite,
        });
        this.layer.add(line);
        
         line.on('dragstart', function(e) {
        	this.addCambio(e.target, "linea");
     	 }.bind(this));

        var data = {
           tipo: "linea",
           evento: "creacion",
         }
        this.objetos[this.numCambios] = line;
        this.objetosTipos[this.numCambios] = data;
        this.numCambios++;


        return line;
    }	

    drawImage(imageObj,x,y,ancho,alto) {
        // Imagen
        var posX = x;
        var posY = y;
        this.balon = new Konva.Image({
            image: imageObj,
            x: posX,
            y: posY,
            width: ancho,
            height: alto,
            draggable: true,
            name: 'objeto',
            src: imageObj.src
        });
        // add cursor styling
       	this.balon.on('mouseover', function() {
            document.body.style.cursor = 'pointer';
        });

        this.balon.on('mouseout', function() {
            document.body.style.cursor = 'default';
        });
        
        this.balon.on('dragstart', function(e) {
        	this.addCambio(e.target, "imagen");
     	 }.bind(this));
     	 
        this.balon.on('dragend', function() {
          this.object_selected = this;
        });
    
       	this.balon.on('click tap', function(e) {
      	    this.object_selected = this;
      	});
	
      	this.balon.on('dblclick dbltap', function() {
      		this.object_selected = this;
      	});
	
        this.object_selected = this.balon;
        this.layer.add(this.balon);
        this.stage.draw();

        var data = {
           tipo: "imagen",
           evento: "creacion",
           x: posX,
           y: posY
         }
        this.objetos[this.numCambios] = this.balon;
        this.objetosTipos[this.numCambios] = data;
        this.numCambios++;
        
        this.cancelDrawLine();
    }
    
    drawText(texto, color, fontSize, bold, textDecoration,fontFamily)
    {
        var posX = this.stage.getWidth() / 2;
        var posY = 30;
        this.simpleText = new Konva.Text({
            x: posX,
            y: posY,
            text: texto,
            fontSize: fontSize,
            fontFamily: fontFamily,
            fontStyle: bold,
            textDecoration: "underline",
            fill: color,
            padding: 20,
            align: 'center',
            name: 'objeto',
            draggable: true
         });
      
  	    this.simpleText.on('mouseover', function() {
            document.body.style.cursor = 'pointer';
        });
        
        this.simpleText.on('mouseout', function() {
            document.body.style.cursor = 'default';
        });
        
        this.simpleText.on('dragstart', function(e) {
            this.addCambio(e.target, "texto");
     	 }.bind(this));
        
      //EDIT TEXT
      this.simpleText.on('dblclick dbltap', function() {
      		object_selected = this;
        });
      
      //FIN EDIT TEXT  
        
      this.layer.add(this.simpleText);
      this.layer.draw();
      //stage.add(layer);
      
      var data = {
           tipo: "texto",
           evento: "creacion",
           x: posX,
           y: posY
         }
        this.objetos[this.numCambios] = this.simpleText;
        this.objetosTipos[this.numCambios] = data;
        this.numCambios++;
        
    }
    
    drawTextBackground(texto, color, fontSize, bold, textDecoration,fontFamily){
        var posX = this.stage.getWidth() / 2;
        var posY = 30;
        var group = new Konva.Group({
            x: posX,
            y: posY,
            name: 'objeto',
            draggable: true,
        });
        
        var texto = new Konva.Text({
            x: 0,
            y: 0,
            text: texto,
            fontSize: fontSize,
            fontFamily: fontFamily,
            fontStyle: bold,
            textDecoration: textDecoration,
            fill: color,
            padding: 20,
            align: 'center',
            name: 'grupo',
            grupo: group
        });
        
        // Imagen
        var imageObj = new Image();
        var imgGroup = new Konva.Image({
            image: imageObj,
            x: 0,
            y: 0,
            width: texto.width(),
            height: texto.height(),
            name: 'grupo',
            grupo: group
        });
        imageObj.src = 'canvas/imagenes/textfondo.png';
        
        //CUADRADO
        var rect = new Konva.Rect({
            x: 0,
            y: 0,
            width: texto.width(),
            height: texto.height(),
            fill: 'white',
            shadowBlur: 3,
            cornerRadius: 5,
            opacity: 0.5,
            name: 'grupo',
            grupo: group
        });
        
        //group.add(imgGroup);
        group.add(rect);
        group.add(texto);
        
        group.on('dragstart', function(e) {
        	this.addCambio(e.target, "group");
     	 }.bind(this));
     	 
     	group.on('click', function(e) {
     	    /*
        	alert("CLICK EN EL GROUP");
        	console.log(this);
        	console.log("deberia ser el group");
        	const tr = new Konva.Transformer({
              node: e.target
            })
            this.layerFondo.add(tr);
            this.stage.draw();*/
     	 }); 
        
        this.layer.add(group);
        this.stage.draw();
    }
    
    agregarTransformer(objeto){
        
    }
    
    FuncEditarText(event)
    {
    	event.preventDefault();
    	object_selected.text($("#EditarText").val());
    	object_selected.fontSize($("#EditarSize").val());
      	object_selected.fill($("#EditarColor").val());
      	object_selected.fontStyle($("#EditarNegrita").val());
      	object_selected.textDecoration($("#EditarSub").val());
      	object_selected.draggable($("#EditarArrastrar").val());

    	layer.draw();
    	CloseModal("ModalText",event);
    }
    
    
    getRelativePointerPosition(node) {
        // the function will return pointer position relative to the passed node
        var transform = node.getAbsoluteTransform().copy();
        // to detect relative position we need to invert transform
        transform.invert();

        // get pointer (say mouse or touch) position
        var pos = node.getStage().getPointerPosition();

        // now we find relative point
        return transform.point(pos);
      }
      
    Circle(x1, y1, x2, y2){
        var w = x2 - x1;
        var h = y2 - y1;
          
        var circle = new Konva.Circle({
            x: x1,
            y: y1,
            radius: (w/2),
            fill: 'transparent',
            stroke: this.colorText,
            strokeWidth: this.line_ancho,
            draggable: true
          });  
          
          this.layer.add(circle);
          this.layer.draw();
          
          this.square_selected = circle;
          
          return circle;
    } 
      
    
    Square(x1, y1, x2, y2){
        var w = x2 - x1;
        var h = y2 - y1;
        
        var x = 0;
        var y = 0;
        if(this.dash)
        {
            x = 30;
            y = 10; 
        }
        
        var opacity = 1;
        var fill = "transparent";
        var color = this.colorText;
        
        if(this.SquareFill){
            fill = color;
            opacity = 0.5;
        }
        
        if(this.crop){
            color = this.colorCrop;
            fill = "transparent";
        }
        
        var rect = new Konva.Rect({
            x: x1,
            y: y1,
            width: w,
            height: h,
            name: 'objeto',
            fill: fill,
            stroke: color,
            strokeWidth: this.line_ancho,
            shadowBlur: 0,
            cornerRadius: 0,
            draggable: true,
            dash: [x, y],
            opacity: opacity
          });
          
          
          this.layer.add(rect);
          this.layer.draw();
          
          this.square_selected = rect;
          
          return rect;
    }
    
    drawArrow(x1, y1, x2, y2)
    {
      var x = 0;
      var y = 0;
      if(this.dash)
      {
          x = 30;
          y = 10; 
      } 
      var arrow = new Konva.Arrow({
       	points: [x1, y1, x2, y2],
        stroke: this.colorText,
        strokeWidth: this.line_ancho,
      	pointerLength: 20,
      	pointerWidth : 20,
      	fill: this.colorText,
      	lineCap: 'round',
        lineJoin: 'round',
        name: 'objeto',
        draggable: true,
        dash: [x, y]
       });
       
       arrow.on('dragstart', function(e) {
        	this.addCambio(e.target, "arrow");
     	 }.bind(this));
       
      	this.layer.add(arrow);
        this.layer.draw();
        this.line_selected = arrow;

        return arrow;
    }
    
    drawLine(x1, y1, x2, y2)
    {
        var x = 0;
        var y = 0;
        if(this.dash)
        {
            x = 30;
            y = 10; 
        } 
      
        this.Line = new Konva.Line({
            points: [x1, y1, x2, y2],
            stroke: this.colorText,
            strokeWidth: this.line_ancho,
            lineCap: 'round',
            lineJoin: 'round',
            name: 'objeto',
            draggable: true,
            dash: [x, y]
        });
        
        this.Line.on('dragstart', function(e) {
        	this.addCambio(e.target, "imagen");
     	 }.bind(this));
      
      
        this.layer.add(this.Line);
        this.layer.draw();
        this.line_selected = this.Line;

        return this.Line;
    }
    
    CanvasJson()
    {
       var json = stage.toJSON();
       console.log(json);
       alert(json);
    }
    
    addimgtocanvas(img, event)
    {
      this.cancelDrawLine();
      imageCanvas = img;
      console.log(img.src);
      //$("#contenedor_canvas").css("cursor","url('images/canvas/jugadores/cursor.png'), auto");
      
      $("#contenedor_canvas").css("cursor","url('images/canvas/jugadores/cursor32.png'), auto");
      createimage = true;
      
      //$("#contenedor_canvas").css("cursor","url("+img.src+") !important", auto");
      //$("body").css("cursor","url('"+img.src+"'), auto");
      //$("#contenedor_canvas").css("cursor","url("+img.src+") !important", auto");
      
      
      //drawImage(img);
    }
    
    addtexttocanvas(texto, number, fontFamily, color)
    {
      this.cancelDrawLine();
      this.drawText(texto, color, number, this.bold, this.underline,fontFamily);
      //this.drawTextBackground(texto, this.colorText, number, this.bold, this.underline,fontFamily);
    }
    
    changeColor(color)
    {
        this.colorText = color;
    }
    
    changeColorCrop(color)
    {
        this.colorCrop = color;
    }
	
    girar(mov, event)
    {
    	if(mov == 'd')
    		object_selected.rotate(45);
    	else
    		object_selected.rotate(-45);
    	layer.draw();
    }
    
    clearCanvas(event)
    {
    	object_selected.remove();
    	layer.draw();
    }
    
    permitCircle()
    {
      this.createline = false;
      this.createdraw = false;
      this.createimage = false;
      this.createsquare = false;
      this.createcircle = true;
    }
    
    permitSquare(crop, fill)
    {
      this.SquareFill = fill;
      this.crop = crop;
      this.dash = crop;
      this.createline = false;
      this.createdraw = false;
      this.createimage = false;
      this.createsquare = true;
      this.createcircle = false;
    }
    
    permitDrawLine(p_arrow, p_dash)
    {
      this.arrow = p_arrow;
      this.dash = p_dash;
      this.createline = true;
      this.createdraw = false;
      this.createimage = false;
      this.createsquare = false;
      this.createcircle = false;
    }

    permitDraw(p_draw, event)
    {
      this.createline = false;
      this.createdraw = true;
      this.createimage = false;
      this.createsquare = false;
      this.createcircle = false;
      this.mode = 'brush';
      console.log("Permite dibujar:" + this.createdraw);
    }
    

    permitEraser()
    {
      this.createline = false;
      this.createdraw = true;
      this.createimage = false;
      this.createsquare = false;
      this.createcircle = false;
      this.mode = 'eraser';
    }

    cancelDrawLine()
    {
      $("#tools_1_selected").prop("src","images/canvas/blanco_cursor.png");
      this.createline = false;
      this.createdraw = false;
      this.createimage = false;
      this.createsquare = false;
      this.createcircle = false;
    }

    activarBold(event)
    {
        if(bold == "normal")
        {
            $("#itemBold").attr("src", "images/canvas/rojo_bold.png");
            bold = "bold";
        }    
        else
        {
            $("#itemBold").attr("src", "images/canvas/negro_bold.png");	
            bold = "normal"; 
        }    
    }

    activarUnderline(event)
    {
        if(underline == "underline")
        {
            $("#itemUnderline").attr("src", "images/canvas/negro_subrayado.png");
            underline = "";
        }    
        else
        {
            $("#itemUnderline").attr("src", "images/canvas/rojo_underline.png");
            underline = "underline";
        }      
    }
    
    changeWidth(ancho)
    {
    	this.line_ancho = parseInt(ancho);
    }
    
    getImg(){
        return this.stage.toDataURL({ pixelRatio: 0.2 });
    }
    
    guardarimagen(event)
    { 
    	event.preventDefault();
        html2canvas($("#make_tarea"), {
            onrendered: function(canvas) {
                theCanvas = canvas;
                /*document.body.appendChild(canvas);*/

                var dataURL = canvas.toDataURL('image/jpeg');
                $.ajax({
		  type: "POST",
		  url: "savepantalla.php",
		  data: { 
		     imgBase64: dataURL,
		     num_pantalla: $("#pantalla_activa").val()
		  }, success: function(datos){
		  		console.log(datos);
				if(datos['success'] == 1)
				{
					$(".selectpages").html(datos['select']);
					msgbox("&Eacute;xito", "TAREA GUARDADA CON EXITO", event);
				}	
				else
					msgbox("Alerta", "PROBLEMAS AL GUARDAR LA TAREA, INTENTELO MAS TARDE.", event);
			}
		});
            }
        });
    }    
    

    activarDelete(event)
    {
    	if(this.StartDelete)
    	{
    		$("#itemDelete").attr("src", "images/canvas/blanco_delete.png");
    		this.StartDelete = false;
            $("#contenedor_canvas").css("cursor","default");
    	}
    	else
    	{
    		$("#itemDelete").attr("src", "images/canvas/rojo_delete.png");
    		this.StartDelete = true;
            $("#contenedor_canvas").css("cursor","no-drop");
    	}
    	this.cancelDrawLine();	
    }

    dragElement(elmnt) {
  var pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0;
  if (document.getElementsByClassName("mydivheader")) {
    document.getElementsByClassName("mydivheader").onmousedown = dragMouseDown;
  } else {
    elmnt.onmousedown = dragMouseDown;
  }

  function dragMouseDown(e) {
    e = e || window.event;
    // get the mouse cursor position at startup:
    pos3 = e.clientX;
    pos4 = e.clientY;
    document.onmouseup = closeDragElement;
    // call a function whenever the cursor moves:
    document.onmousemove = elementDrag;
  }

  function elementDrag(e) {
    e = e || window.event;
    // calculate the new cursor position:
    pos1 = pos3 - e.clientX;
    pos2 = pos4 - e.clientY;
    pos3 = e.clientX;
    pos4 = e.clientY;
    // set the elements new position:
    elmnt.style.top = (elmnt.offsetTop - pos2) + "px";
    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px";
  }

  function closeDragElement() {
    /* stop moving when mouse button is released:*/
    document.onmouseup = null;
    document.onmousemove = null;
  }
}


    undo(){
       if(this.objetos.length > 0){
            console.log(this.objetos);
            var ObjetotoRemove = this.objetos[this.numCambios-1];
            var data = this.objetosTipos[this.numCambios-1];
            
            if(data.evento == "creacion"){
                this.redoObjetos[this.redoNum] = ObjetotoRemove.clone();
                this.redoData[this.redoNum] = data;
                ObjetotoRemove.remove();
            }else if(data.evento == "mover"){
                var dataRedo = {
                    evento: "mover",
                    x: ObjetotoRemove.getX(),
                    y: ObjetotoRemove.getY()
                }
                this.redoData[this.redoNum] = dataRedo;
                this.redoObjetos[this.redoNum] = ObjetotoRemove;
                ObjetotoRemove.setX(data.x);
                ObjetotoRemove.setY(data.y);
            }
            this.redoNum++;
    
            this.objetos.splice(this.numCambios-1,1);
            this.objetosTipos.splice(this.numCambios-1,1);
            this.layer.draw();
            this.numCambios--;
       }
    }
    
    redo(){
        if(this.redoNum > 0){
            console.log("---------REDO-----------");
            console.log(this.redoObjetos);
            var ObjetotoRemove = this.redoObjetos[this.redoNum-1];
            var data = this.redoData[this.redoNum-1];
            
            if(data.evento == "creacion"){
                this.layer.add(ObjetotoRemove);
            }else if(data.evento == "mover"){
                console.log(ObjetotoRemove);
                ObjetotoRemove.setX(data.x);
                ObjetotoRemove.setY(data.y);
            }
            this.layer.draw();
            this.redoObjetos.splice(this.redoNum-1,1);
            this.redoData.splice(this.redoNum-1,1);
            this.redoNum--;
        }else{
            alert("No hay eventos");
        }
        
    }

    resetCanvas(){
        layer.clear();
        layerFondo.clear();
        stage.clear();
        stage.draw();
    }

    updateFondo(url){
       this.imageFondo.src = url;
       this.layerFondo.draw(); 
    }

    updateFondoSize(img){
        var ratio = img.width / img.height;
        img.width = this.lienzoAncho;
        img.height = this.lienzoAncho / ratio;
        
        this.imageFondo.src = img.src;
        this.imageFondo.width = img.width;
        this.imageFondo.height = img.height;
       
        this.fondo.width = img.width;
        this.fondo.height = img.height;
        this.fondo.x = (this.lienzoAncho - img.width) / 2;
        this.fondo.y = (this.lienzoAlto - img.height) / 2;
        
        this.layerFondo.draw();
    }
    
    updateFondoSizeAlto(img){
        var ratio = img.width / img.height;
        img.height = this.lienzoAlto;
        //img.width = this.lienzoAlto / ratio;
        img.width = this.lienzoAncho;
        
        this.imageFondo.src = img.src;
        this.imageFondo.width = img.width;
        this.imageFondo.height = img.height;
       
        this.fondo.width = img.width;
        this.fondo.height = img.height;
        this.fondo.x = (this.lienzoAncho - img.width) / 2;
        this.fondo.y = (this.lienzoAlto - img.height) / 2;
        
        this.layerFondo.draw();
    }
    
    downloadURI(uri, name){
        var link = document.createElement('a');
        link.download = name;
        link.href = uri;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        //delete link;
    }

    exportImg(){
      var dataURL = this.stage.toDataURL({ pixelRatio: 1 });
      this.downloadURI(dataURL, 'stage.png');
    }  
    
    vaciarCanvas(){
        this.layer.clear(); 
        this.stage.clear();
        this.stage.clearCache();
        this.stage.destroyChildren();
    }

    limpiarCanvas(){
        this.layer.clear(); 
        this.stage.clear();
        this.stage.clearCache();
        this.stage.destroyChildren();
        
        this.layerFondo = new Konva.Layer({name:'layer-fondo'});
        this.stage.add(this.layerFondo);
        
        this.layer = new Konva.Layer({name:'layer-draw'});
        this.stage.add(this.layer);
        
        
        /*FONDO*/
        this.fondo.width = this.lienzoAncho;
        this.fondo.height = this.lienzoAlto;
        this.fondo.x = 0;
        this.fondo.y = 0;
        this.fondo.src = 'canvas/imagenes/blanco.png';
        this.imageFondo = new Image();
        this.imageFondo.onload = function() {
            if(this.CanvasFondo){
                this.CanvasFondo.remove();
            }
            
            this.CanvasFondo = new Konva.Image({
                image: this.imageFondo,
                x : this.fondo.x,
                y : this.fondo.y,
                width: this.fondo.width,
                height: this.fondo.height,
                draggable: false,
                shadowBlur: 0,
                name: 'fondo',
                id: 'fondo',
                src: this.fondo.src
            });
            this.layerFondo.add(this.CanvasFondo);
            this.layerFondo.moveToBottom();
            this.stage.draw();   
        }.bind(this);
        this.imageFondo.src = 'canvas/imagenes/blanco.png';
        this.imageFondo.id = 'fondo';
    }
    
    limpiarLayer(){
        this.layer.clear();
        this.layer.destroyChildren();
        
        this.layer.draw();
        //this.stage.clear();
        //this.stage.clearCache();
        //this.stage.destroyChildren();
    
        //this.layer = new Konva.Layer({name:'layer-draw'});
        //this.stage.add(this.layer);
    }

    removeSelected(){
        this.object_selected.remove();
        this.stage.find('Transformer').destroy();
        this.layer.draw();
    }

}