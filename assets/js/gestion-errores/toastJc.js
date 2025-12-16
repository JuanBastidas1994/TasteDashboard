
class ToastJc{
    //Configuracion
    delay = 8000;
    autohide = false;
    header_class = "toast-success";
    icon = "loading"; //success - error - warning - loading
    showProgressTimer = true;
    progress = true;
    title = "My Toast JC";
    description = "Hello World";
    position = "bottom-right";

    //Oyentes
    listeners = [
        {name: "render", callback: null},
        {name: "click", callback: null},
        {name: "timerFinish", callback: null}
    ];
    
    //Objs
    self = this;
    toastObj = null;
    progressObj = null;
    contentObj = null;
    IntervalId = null;
    secondsTotal = 0;
    secondCurrent = 1;
    data = null;

    callBack = null;

    constructor(params = undefined){
        if(params !== undefined){
            this.setParams(params)
        }
        this.getContentPosition();
    }

    open(Pdata = null){
        this.data = Pdata;
        let id = "ToastJc"+$.now();
        let icon = this.getIcon();
        let content = `
            <div id="${id}" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" 
                    data-autohide="${this.autohide}" data-delay="${this.delay}" style="width: 300px;cursor: pointer;">
                <div class="toast-header ${this.header_class}">
                    ${icon}
                    <strong class="mr-auto">${this.title}</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="toast-body">
                    ${this.description}
                </div>
                <div class="toast-time text-right pr-2" style="background: white; font-size: 12px;">
                    <div><span class="missing_time">0</span> segundos</div>
                </div>
                <div class="progress mb-0" style="height: 5px;">
                    <div class="progress-bar" role="progressbar" style="width: 100%;border-radius: 0px;" 
                        aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>`;
        this.contentObj.append(content);
        this.toastObj = $("#"+id);
        this.toastObj.toast('show');
        this.progressObj = this.toastObj.find('.progress-bar');

        //Ligar evento
        const tjc = document.getElementById(id)
        tjc.addEventListener("click", this.click.bind(this));

        //Renderizar
        this.emit("render", this.data);

        //Progress Bar Timer
        if(this.progress){
            this.secondsTotal = this.delay / 1000;
            this.IntervalId = setInterval(this.intervalProgress.bind(this), 1000);
            
            if(!this.showProgressTimer){
                this.progressObj.hide();
                this.toastObj.find('.toast-time').hide();
            }
        }else{
            this.progressObj.hide();
            this.toastObj.find('.toast-time').hide();
        }
    }

    setParams(params){
        if (params !== undefined){
            if(params.delay !== undefined ){
                this.delay = params.delay;
            }
            if(params.title !== undefined ){
                this.title = params.title;
            }
            if(params.description !== undefined ){
                this.description = params.description;
            }
            if(params.position !== undefined ){
                this.position = params.position;
            }
            if(params.progress !== undefined ){
                this.progress = params.progress;
            }
            if(params.showProgressTimer !== undefined ){
                this.showProgressTimer = params.showProgressTimer;
            }
            if(params.icon !== undefined ){
                this.icon = params.icon;
            }
        }
    }

    getContentPosition(){
        if(this.position == "bottom-right"){
            this.contentObj = $("#toastJcContent .toast-bottom-right");
        }else if(this.position == "bottom-left"){
            this.contentObj = $("#toastJcContent .toast-bottom-left");
        }else if(this.position == "top-right"){
            this.contentObj = $("#toastJcContent .toast-top-right");
        }else if(this.position == "top-left"){
            this.contentObj = $("#toastJcContent .toast-top-left");
        }else{
            this.contentObj = $("#toastJcContent .toast-bottom-right");
        }
    }

    getIcon(){
        if(this.icon == "loading"){
            return `<div class="spinner-border spinner-border-sm text-light mr-2" role="status">
                    <span class="sr-only">Loading...</span>
                </div>`;
        }else if(this.icon == "success"){
            return `<div class="text-light mr-2" role="status">
                    <i class="far fa-check-circle"></i>
                </div>`;
        }else{
            return "";
        }
    }


    intervalProgress(){
        let percent = (this.secondCurrent / this.secondsTotal) * 100;
        percent = 100 - percent;

        this.toastObj.find('.missing_time').html(`${this.secondsTotal - this.secondCurrent}`);
        this.progressObj.css('width', percent + '%');
        this.secondCurrent = this.secondCurrent + 1;

        if(this.secondCurrent > this.secondsTotal){
            clearTimeout(this.IntervalId);
            this.emit("timerFinish", this.data);
            this.close();
        }
    }

    click(){
        this.emit("click", this.data);
        this.close();
    }

    close(){
        this.toastObj.toast('hide');
        this.toastObj.remove();
    }

    //Funciones de Procesamiento de eventos
    on(name, callback){
        Object.entries(this.listeners).forEach(([key, listener]) => { //Buscar el evento correcto y ligarlo
            if(listener.name == name){
                this.listeners[key]['callback'] = callback;
            }
        });
    }

    emit(name, data){
        console.log("EVENTOS GUARDARDOS", this.listeners);
        Object.entries(this.listeners).forEach(([key, listener]) => { //Buscar el evento correcto y ejecutarlo
            if(listener.name == name){
                if(this.listeners[key]['callback'] !== null)
                    this.listeners[key]['callback'](data);
            }
        });
    }



}