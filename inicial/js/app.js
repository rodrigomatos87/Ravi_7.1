/************** Preloader Js**************/
//

window.onload = function loader() {
    setTimeout(() => {
      document.getElementById("preloader").style.visibility = "hidden";
      document.getElementById("preloader").style.opacity = "0";
    }, 350);
  };
  