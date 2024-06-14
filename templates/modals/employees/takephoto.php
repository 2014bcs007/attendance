<div class="modal-header">
    <h5 class="modal-title">Take Student Photo</h5>
    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body">
<form method="POST" action="">
        <div class="row">
          <div class="col-md-6">
            <div id="my_camera">

            </div>
            <br />            
            <input type="button" value="Take Photo" onClick="take_image()" />
            <input type="hidden" name="image" class="image-tag" />
          </div>
          <div class="col-md-6">
            <div id="results">Your captured image will appear here...</div>
          </div>
          <div class="col-md-12 text-center">
            <br />
            <!-- onClick="downloadPhoto('student_name')" -->
            <input class="btn btn-success"  id="usephoto" value="Use Photo"/>
          </div>
        </div>
      </form>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
</div>
<?php //require_once 'inc/footer.php' ?>

<script>
    Webcam.set({
        width:400,
        height:300,
        image_format:'jpeg',
        jpeg_quality:90
    })
    Webcam.attach("#my_camera")

    let data_uri='';
    function take_image(){
        Webcam.snap( function (data_uri,target_url){
            data_uri = data_uri;

            $(".imag-tag").val(data_uri)
            document.getElementById("results").innerHTML = '<img src="'+data_uri+'"/>'
            createInputElement();
            // document.getElementById("file_div").
            // createInputElement();
            // console.log(target_url);  
            // downloadPhoto(name, data_uri);          
        })
    }
    function createInputElement(){
        var elem = document.querySelector('#i_file');
        //console.log(elem);
        elem.parentNode.removeChild(elem);
        var el = document.createElement('INPUT');
        el.type= "file";
        el.accept = "image/*";
        // el.onchange = "readURL(this)";
        el.value = "data_uri";
        el.id = "i_file";
        el.className="i_file";

        // el.setAttribute("class", "form-control");
        // el.setAttribute("id", "i_file");
        // el.setAttribute("name", "std_photo");
        // el.setAttribute("accept", "image/*");
        // el.setAttribute("onchange", "readURL(this);");
        // el.setAttribute("value",data_uri);
        // el.click();
        console.log(el);

        var file_div = document.getElementById('#file_div');
        file_div.appendChild(el);
        file_div.insertAdjacentHTML(beforeend, el)

    }
    function downloadPhoto(name){
        //download photo
        //console.log(data_uri);
        datauri=data_uri;
        var a = document.createElement('a');
        a.setAttribute('download', name + '.png');
        a.setAttribute('href', datauri);
        a.click();
    }
    // document.getElementById("usephoto").addEventListener("click",function (){
    //     name="provisional_photo";
    //     downloadPhoto(name,data_uri);
    // })
</script>
