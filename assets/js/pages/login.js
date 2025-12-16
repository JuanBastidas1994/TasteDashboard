$(document).ready(function(){
    let navigator = getNavegador();
    let systemOperative = detectOS();
    let isMobile = getMobile();

    console.log("Navigator",navigator);
    console.log("Is Mobile",isMobile);
    console.log("OS",systemOperative);

    $("#navigator").html(navigator);
    $("#systemOperative").html(systemOperative);
    $("#isMobile").html(isMobile);

    $(".btnLogin").on("click", function(event){
        event.preventDefault();
        var usuario = $("#username").val().trim();
        var password = $("#password").val();

        if(usuario == "" || password == ""){
            messageDone("El usuario y la clave deben estar llenos",'error'); 
            return;
        }

        var parametros = {
            "username": usuario,
            "password": password,
            "navigator": navigator,
            "operative_system": systemOperative,
            "is_mobile": isMobile
        }
        $.ajax({
            url:'controllers/controlador_usuario.php?metodo=login',
            data: parametros,
            type: "POST",
            success: function(response){
                console.log(response);
                if(response['success']==1){
                    messageDone(response['mensaje'],'success');
                    subscribeTokenToTopic("general");
                    subscribeTokenToTopic(response['alias']);
                    subscribeTokenToTopic("usuario"+response['id']);
                    setLoginFirebase(response['token'], {
                        id: response['id'],
                        business: response['alias'],
                        token: response['token'],
                        username: usuario,
                        navigator: navigator
                    });
                    
                    // return;
                    
                    setTimeout(function () {
                      window.location.href = './index.php';
                    }, 1200);
                }else{
                    messageDone(response['mensaje'],'error');
                }
            },
            error: function(data){
              console.log(data);
              messageDone("Ocurrio un error al iniciar sesión, intentelo más tarde",'error'); 
            },
            complete: function()
            {
              
            }
        });
    });


    $(".btnRecuperar").on("click", function(event){
        event.preventDefault();
        var correo = $("#email").val();
        var parametros = {
            "usuario": correo
        }
        $.ajax({
            url:'controllers/controlador_usuario.php?metodo=recuperar_password',
            data: parametros,
            type: "GET",
            success: function(response){
                console.log(response);
                if(response['success']==1){
                    var pass = response['password'];
                    messageDone(response['mensaje'],'success');
                    //ENVIAR CORREO
                    $.ajax({
                        url: 'correos/recuperar_password.php?id='+correo+'&pass='+pass,
                        type:'GET',
                    });
                    setTimeout(function () {
                      window.location.href = './login.php';
                    }, 1500);
                }else{
                    messageDone(response['mensaje'],'error');
                }
            },
            error: function(data){
              console.log(data);  
            },
            complete: function()
            {
              
            }
        });

    });
});


function getNavegador() {
    try {
        let userAgent = window.navigator.userAgent;
        if ((userAgent.indexOf("Opera") || userAgent.indexOf('OPR')) != -1) {
            return 'Opera';
        }
        else if (userAgent.indexOf('Edg') != -1) {
            return 'Edge';
        }
        else if (userAgent.indexOf("Chrome") != -1) {
            return 'Chrome';
        }
        else if (userAgent.indexOf("Safari") != -1) {
            return 'Safari';
        }
        else if (userAgent.indexOf("Firefox") != -1) {
            return 'Firefox';
        }
        else if ((userAgent.indexOf("MSIE") != -1) || (!!document.documentMode == true)) {
            return 'IE';
        }
        else {
            return 'unknown';
        }
    } catch (error) {
        return 'unknown with error';
    }
}

function detectOS() {
    try {
        let userAgent = window.navigator.userAgent,
            platform = window.navigator.platform,
            macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K'],
            windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
            iosPlatforms = ['iPhone', 'iPad', 'iPod'],
            os = null;

        if (macosPlatforms.indexOf(platform) !== -1) {
            os = 'Mac OS';
        } else if (iosPlatforms.indexOf(platform) !== -1) {
            os = 'iOS';
        } else if (windowsPlatforms.indexOf(platform) !== -1) {
            os = 'Windows';
        } else if (/Android/.test(userAgent)) {
            os = 'Android';
        } else if (!os && /Linux/.test(platform)) {
            os = 'Linux';
        }

        return os;
    } catch (error) {
        return 'unknown with error';
    }

	
}

function getMobile(){
    try {
        return window.navigator.userAgentData.mobile
    } catch (error) {
        return 0;
    }
}