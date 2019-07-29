//scroll na dole
var textarea = document.getElementById("scroll");
textarea.scrollTop = textarea.scrollHeight;

//wyslanie wiadomosci
function submitek(){
    var formData = {};
    formData["wiadomosc"] = document.getElementById("wiadomosc").value;
    $.post("addmessage.php", formData).done(function() {
        document.getElementById("wiadomosc").value="";
        reload();
    });
}

//wylogowanie sie
function logout(){
    var formData = {};
    formData["logout"] = "wylogujmito";
    $.post("logout.php", formData).done(function() {
        window.location.href = "login.php";
    });
}

//reload chatboxu i scroll na dole, gdy zmieni się liczba wpisów
var last=$(".wpis").length;
function reload(){
    $("#scroll").load("content.php", function(){
        if (last!=$(".wpis").length){
            textarea.scrollTop = textarea.scrollHeight;
            last=$(".wpis").length;
        }
    })
    $("#userlist").load("activeusers.php");
    $("#hellomsg").load("hellomsg.php");
}
setInterval(reload, 2000);
reload();

//submit na enterek/logout na esc
$('body').keydown(function (e) {
    if(e.keyCode == 27) {
        var r = confirm("Czy chcesz się wylogować?");
        if (r == true) {
            logout();
        }
    }
    else if (e.keyCode == 13) {
        e.preventDefault();
        submitek();
    }
});

//focus podczas nacisniecia przycisku
$(document).bind('keydown',function(e){
    $('#wiadomosc').focus();   
});