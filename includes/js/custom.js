/*
document.getElementById('myform').onsubmit = function(){
	var input = document.querySelectorAll('#myform input');
	alert(input[1].value);
}

*/

    //
    // Disable workers to avoid yet another cross-origin issue (workers need the URL of
    // the script to be loaded, and dynamically loading a cross-origin script does
    // not work)
    //



    PDFJS.disableWorker = true;
    //
    // Asynchronous download PDF as an ArrayBuffer
    //
    var pdf = document.getElementById('pdf');
    pdf.onchange = function(ev) {
      if (file = document.getElementById('pdf').files[0]) {
        fileReader = new FileReader();
        fileReader.onload = function(ev) {
          console.log(ev);
          PDFJS.getDocument(fileReader.result).then(function getPdfHelloWorld(pdf) {
            //
            // Fetch the first page
            //
            console.log(pdf)
            pdf.getPage(1).then(function getPageHelloWorld(page) {
              //var scale = 1.5;
              //var viewport = page.getViewport(scale);
              var canvas = document.getElementById('the-canvas');
              var viewport = page.getViewport(canvas.width / page.getViewport(1.0).width);
              //
              // Prepare canvas using PDF page dimensions
              //
              
              var context = canvas.getContext('2d');
              canvas.height = viewport.height;
              //canvas.width = viewport.width;
              //canvas.height = 500;
              //canvas.width = 500;
              //
              // Render PDF page into canvas context
              //
              var task = page.render({canvasContext: context, viewport: viewport})
              task.promise.then(function(){
                console.log(canvas.toDataURL('image/jpeg'));
              });
            });
          }, function(error){
            console.log(error);
          });
        };
        fileReader.readAsArrayBuffer(file);
      }
    }



      function getMousePos(canvas, evt) {
        var rect = canvas.getBoundingClientRect();
        return {
          x: evt.clientX - rect.left,
          y: evt.clientY - rect.top
        };
      }
      var canvas = document.getElementById('the-canvas');
      var context = canvas.getContext('2d');

 var key = null;

 function position(val){
     key = val;
     canvas.addEventListener("mousemove", mouseHandler , true);
 }

 function mouseHandler (evt){
     var mousePos = getMousePos(canvas, evt);
     document.getElementById("demox[" + key + "]").value =Math.floor(mousePos.x);
     document.getElementById("demoy[" + key + "]").value =Math.floor(mousePos.y);
 }

canvas.onclick = function() {
  canvas.removeEventListener('mousemove', mouseHandler, true);
}


