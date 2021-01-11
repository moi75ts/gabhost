function loading(){
    document.getElementById("loading").style.display = "block"
}
function OpenTab(evt, TabName) {
    var i, x, tabs, selected;
    x = document.getElementsByClassName("gab-tab");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    tabs = document.getElementsByClassName("tablink");
    for (i = 0; i < tabs.length; i++) {
        console.log(tabs[i]);
        tabs[i].className = "";
    }
    selected = document.getElementById(TabName + "_SPAN")
    selected.className="tablink gab-selected";
    document.getElementById(TabName).style.display = "block";
}
function showpasswd() {
    var icon=document.getElementById("passwdeye");
    var icon2=document.getElementById("passwdeye2");
    var x = document.getElementById("password");
    var y = document.getElementById("password2");
    if (x.type === "password") {
      x.type = "text";
      icon.attributes[2].nodeValue = "svg/eye-slash.svg";
      try{
        y.type = "text";
        icon2.attributes[2].nodeValue = "svg/eye-slash.svg";
      } catch{} 
    } else {
      x.type = "password";
      icon.attributes[2].nodeValue = "svg/eye.svg";
      try{
      y.type = "password";
      icon2.attributes[2].nodeValue = "svg/eye.svg";
      } catch{} 
    }
  }
  function changeegg(){
    var choix = confirm("Voulez vous vraiment changer le type de serveur? \n Pour un résultat optimal il est recommandé de nettoyer les dossier / fichiers du serveur \nCette action entraine une réinstallation du serveur !");
    if (choix){
        loading()
        return true
    }
    else{
        return false
    }
}
function changejava(){
    var choix = confirm("Voulez vous vraiment changer la version de java? \nCette action nécessite un redémarrage du serveur !");
    if (choix){
        loading()
        return true
    }
    else{
        return false
    }
}