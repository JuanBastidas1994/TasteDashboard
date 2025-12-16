let sounds = [
    {name: "sirena1", text: "Sirena", active: false},
    {name: "gallo", text: "Gallo", active: false},
    {name: "dragon-ball", text: "Dragon Ball", active: false},
    {name: "latigazo", text: "Latigazo", active: false},
    {name: "vida-mario-bros", text: "Vida Mario Bros", active: false},
    {name: "sonido-whatsapp", text: "WhatsApp", active: false},
    {name: "duende-en-la-ventana", text: "Duende en la ventana", active: false},
    {name: "gravity-falls-theme", text: "Gravity Falls", active: false},
    {name: "ringtones-the-avengers", text: "The Avengers", active: false},
    {name: "ringtones-super-mario-bros", text: "Super Mario Bros", active: false},
    {name: "ringtones-super-mario", text: "Mario Bros", active: false},
    {name: "ringtones-pink-panther", text: "La Pantera Rosa", active: false},
    {name: "ringtones-pato-donald", text: "Pato Donald", active: false},
    {name: "alarma-auto", text: "Alarma Auto", active: false},
    {name: "alarma-error", text: "Alarma Error", active: false},
    {name: "alarma-success", text: "Alarma Error", active: false},
];
let isSoundActive = false;
let SoundName = "";

$(function() {
    initSounds();
});

function initSounds(){
    /*CONFIG SONIDO*/
    let sonido = {};
    let sonidoLocal = JSON.parse(localStorage.getItem('sonido'));
    if (sonidoLocal === null) {
        sonido.sirena = "sirena1";
        sonido.repeat = true;
        sonido.volumen = 1;
        localStorage.setItem('sonido', JSON.stringify(sonido));
    }else{
        // console.log('sonidoLocal.volumen', sonidoLocal.volumen);
        if(sonidoLocal.volumen === null || sonidoLocal.volumen === undefined){
            sonidoLocal.volumen = 1;
            localStorage.setItem('sonido', JSON.stringify(sonidoLocal));
        }
        sonido = sonidoLocal;
        // console.log('sonido JS', sonido);
    }

    if(sonido.volumen === null || sonido.volumen === undefined){
        sonido.volumen = 1;
    }

    ion.sound({
        sounds: sounds,
        path: "assets/sounds/",
        preload: false,
        volume: sonido.volumen,
        loop: sonido.repeat,
        multiplay: true,
        ready_callback: function (obj) {
            // console.log("READY SOUND", obj);
        },
        ended_callback: function (obj) {
            //console.log("FINISH SOUND", obj);
        }
    });
    ion.sound.preload(sonido.sirena);
    ion.sound.preload("alarma-auto");
    ion.sound.preload("alarma-error");
}

function getSoundsAndActive(){
    let sonido = JSON.parse(localStorage.getItem('sonido'));
    Object.entries(sounds).forEach(([key, sound]) => {
        if(sound.name == sonido.sirena){
            sounds[key]['active'] = true;
        }
    });
    //console.log("Sonidos", sounds);
    return sounds;
}

//SONIDOS
function encenderSirena(){
    let sonido = JSON.parse(localStorage.getItem('sonido'));

    if(isSoundActive){
        SoundStop(sonido.sirena);
    }
    setTimeout(() => {
        ion.sound.play(sonido.sirena, {
            loop: sonido.repeat,
            volume: sonido.volumen
        });
    }, 300);
    
    isSoundActive = true;
    SoundName = sonido.sirena;
}

function apagarSirena(){
    SoundStop(SoundName);
}

function SoundPlay(sound){
    ion.sound.play(sound, {
        loop: false,
        volume: 0.5
    });
}

function SoundStop(sound){
    try {
        if(sound !== ""){
            ion.sound.stop(sound);
            // console.log("Se apago la sirena");
        }
    } catch (error) {
        notify('No se pudo apagar la sirena', 'error', 2);
    }
}

function testSoundInit(){
    ion.sound.play("alarma-auto", {
        loop: false,
        volume: 0.5
    });
}

function errorSoundPlay(){
    ion.sound.play("alarma-error", {
        loop: false,
        volume: 0.9
    });
}

function successSoundPlay(){
    ion.sound.play("alarma-success", {
        loop: false,
        volume: 0.5
    });
}

function updateNameSound(radio){
    let sonido = JSON.parse(localStorage.getItem('sonido'));
    sonido.sirena = radio.value;
    localStorage.setItem('sonido', JSON.stringify(sonido));
}

function updateRepeatSound(check){
    let sonido = JSON.parse(localStorage.getItem('sonido'));
    sonido.repeat = check.checked;
    localStorage.setItem('sonido', JSON.stringify(sonido));
}

function updateVolumeSound(nivel){
    nivel = nivel / 10;
    let sonido = JSON.parse(localStorage.getItem('sonido'));
    sonido.volumen = nivel;
    localStorage.setItem('sonido', JSON.stringify(sonido));
}

function updateRecordatorioTiempo(tiempo){
    // console.log("TIEMPO", tiempo);
    let recordatorio = JSON.parse(localStorage.getItem('recordatorio'));
    recordatorio.tiempo = tiempo;
    localStorage.setItem('recordatorio', JSON.stringify(recordatorio));
    initializeWorkers();
}

function updateRecordatorioTiempoAsignacion(tiempo){
    // console.log("TIEMPO", tiempo);
    let recordatorio = JSON.parse(localStorage.getItem('recordatorio'));
    recordatorio.tiempo_asignacion = tiempo;
    localStorage.setItem('recordatorio', JSON.stringify(recordatorio));
    initializeWorkers();
}

function updateRecordatorioAsignacion(check){
    // console.log("ASIGNACION", check.checked);
    let recordatorio = JSON.parse(localStorage.getItem('recordatorio'));
    if(check.checked)
        recordatorio.asignacion = 1;
    else    
        recordatorio.asignacion = 0;
    localStorage.setItem('recordatorio', JSON.stringify(recordatorio));
}

function updateRecordatorioPermiso(check){
    // console.log("PERMISO", check.checked);
    let recordatorio = JSON.parse(localStorage.getItem('recordatorio'));
    if(check.checked)
        recordatorio.permiso = 1;
    else    
        recordatorio.permiso = 0;
    localStorage.setItem('recordatorio', JSON.stringify(recordatorio));
    initializeWorkers();
}