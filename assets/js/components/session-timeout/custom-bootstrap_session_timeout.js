var SessionTimeout=function() {
    var e=function() {
        $.sessionTimeout( {
            title:"Session Timeout Notification", 
            message:"Su sesión está a punto de caduca.", 
            keepAliveUrl:"", 
            redirUrl:"auth_lockscreen.html", 
            logoutUrl:"auth_login.html", 
            logoutButton: "Cerrar Sesion",
            keepAliveButton: "Esperar",
            warnAfter:6e3, 
            redirAfter:21e3, 
            ignoreUserActivity:!0, 
            countdownMessage:"Redireccionando en {timer}.", 
            countdownBar: !0
        }
        )
    };
    return {
        init:function() {
            e()
        }
    }
}

();
jQuery(document).ready(function() {
    SessionTimeout.init()
}
);