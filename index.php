<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Artak Martirosyan">

    <title>Market report</title>
    
    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/bootstrap-select.min.css" rel="stylesheet">
         
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link rel="stylesheet" href="css/jquery-ui.min.css">
    <link rel="stylesheet" href="css/style.css">

<script type="text/javascript" language="javascript" src="js/jquery.js"></script>
<script src="js/jquery-ui.min.js"></script>
<!-- Highcharts--> 
<script src="js/highcharts.js"></script>
    
<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>
<script src="js/bootstrap-select.min.js"></script>

<!-- CANVG -->
<script src="js/canvg/rgbcolor.js"></script>
<script src="js/canvg/StackBlur.js"></script>
<script src="js/canvg/canvg.js"></script>
 
<script src="js/jspdf/jspdf.min.js"></script>
<script src="js/html2canvas.js"></script>
<script src="js/html2canvas.svg.min.js"></script>
       
<script>
  
  /////////////////////////////////////////////////////////
  function getStyle(el, styleProp) {
  var camelize = function(str) {
    return str.replace(/\-(\w)/g, function(str, letter) {
      return letter.toUpperCase();
    });
  };

  if (el.currentStyle) {
    return el.currentStyle[camelize(styleProp)];
  } else if (document.defaultView && document.defaultView.getComputedStyle) {
    return document.defaultView.getComputedStyle(el, null)
      .getPropertyValue(styleProp);
  } else {
    return el.style[camelize(styleProp)];
  }
}

$(function(){
  $(document).ready(function() {
    $('#download').click(function() {
      var svgElements = $("#pdf").find('svg');

      //replace all svgs with a temp canvas
      svgElements.each(function() {
        var canvas, xml;

       //canvg doesn't cope very well with em font sizes so find the calculated size in pixels and replace it in the element.
  //       $.each($(this).find('[style*=em]'), function(index, el) {
  //        $(this).css('font-size', getStyle(el, 'font-size'));
  //      });

        canvas = document.createElement("canvas");
        
//////////////////   
var w = 2000;
var h = 2000;
//var div = document.querySelector('#divtoconvert');
//var canvas = document.createElement('canvas');
canvas.width = w*2;
canvas.height = h*2;
canvas.style.width = w + 'px';
canvas.style.height = h + 'px';
var context = canvas.getContext('2d');
context.scale(2,2);
 ////////////////////       
        
        
        canvas.className = "screenShotTempCanvas";
        //convert SVG into a XML string
        xml = (new XMLSerializer()).serializeToString(this);

        // Removing the name space as IE throws an error
        xml = xml.replace(/xmlns=\"http:\/\/www\.w3\.org\/2000\/svg\"/, '');
        
        //draw the SVG onto a canvas
        canvg(canvas, xml /*, {ignoreDimensions: false, ignoreMouse: true, ignoreAnimation: true, ignoreClear: false, log:false }*/);
        $(canvas).insertAfter(this);
        //hide the SVG element
        ////this.className = "tempHide";
        $(this).attr('class', 'tempHide');
        $(this).hide();
      });
window.scrollTo(0,0);     
      html2canvas($("#pdf"), {
        useCORS: true,
        allowTaint: true,
        letterRendering: true,
        scale: 30,
        //dpi: 1044,
        onrendered: function(canvas) {
            //theCanvas = canvas;
            var ctx = canvas.getContext('2d');
            //ctx.webkitImageSmoothingEnabled = false;
            //ctx.mozImageSmoothingEnabled = false;
            //ctx.imageSmoothingEnabled = false;
            
            ctx.webkitImageSmoothingEnabled = true;
            ctx.mozImageSmoothingEnabled = true;
            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = "high";
            //ctx.drawImage(srcImg,0,0);
            
           // var width         = canvas.width;
           // var height        = canvas.height;
            
            var imgData = canvas.toDataURL('image/png');
                    
            var fontSize = 15, height = 0, doc;
    		//doc = new jsPDF('p', 'pt', 'a4', true);
            doc = new jsPDF('p', 'mm', 'a4');
            //doc.setFontSize(40);            
            //doc = new jsPDF('p', 'pt', 'a4');
            doc.internal.scaleFactor = 10.75;
            //doc.text(5, 10, "Market Report");
    		doc.setFont("CircularStd", "normal");
    		doc.setFontSize(fontSize);
            //doc.addImage(imgData, 'PNG', 45, 60, 500, 500);
            doc.addImage(imgData, 'PNG', 5, 5, 200, 210);
           
////////////////////////////////////////
               
          doc.save('Market-Report.pdf');
          
           $("#pdf").find('.screenShotTempCanvas').remove();
            $("#pdf").find('.tempHide').show().removeClass('tempHide');
        }
      });
      window.scrollTo(0, document.body.scrollHeight || document.documentElement.scrollHeight);
    });
  });
});
</script>
</head>
<body>
<!-- Page Content -->
<div class="container">

<div id="wait" style="display:none;position:absolute;top:50%;left:50%;padding:2px; z-index: 2000;"><img src='images/ajaxloader.gif'/>Loading...</div> 
<div class="row1">
<!-- Entries Column -->
<div class="col-md-12111">
                            
<form id="form_id" class="form-inline">
<legend></legend>
<br />
<legend></legend>

<?php 
    require_once dirname(__FILE__) . '/db.php';
    $query = $conn->query("select distinct district, region, yopa_region from postcode_list");
    
    $options = '<option data-subtext="-- Select --"></option>';
    if($query->num_rows>0){
        foreach($query as $q){$options .= '<option data-subtext="'.$q['region'].' '.$q['yopa_region'].'">'.$q['district'].'</option>'; }
    }
?>

<div class="form-group">
    <label for="postcde">Postcde:</label>
    <div class="input-group col-sm-7">
      <select class="selectpicker" id="postcde" name="postcde" data-show-subtext="true" data-live-search="true" onchange ="showValues()">
      <?php echo $options; ?>
      </select>
    </div>
</div>

</form>
<div class="clearfix"></div>
<br />
<legend></legend>

<div id="pdf" >
<div id="calc-results"></div>
</div>
<div class="clearfix"></div>
<legend></legend>
<center>
<div class="form-group">
    <button type="submit" id="download" class="btn btn-sm btn-default base">Save PDF</button>
</div>
</center>
<div class="clearfix"></div>
<br />
<legend></legend>

    </div>
</div>
</div>
    
<script>
$("#form_id").submit(function(){return false;});
    
  function showValues() {    
     var data=$("#form_id").serialize(); 
         
    $.ajax({
			type: 'post',
			url: 'report.php',
			data: data,
            beforeSend: function(){$("#wait").css("display", "block");},
			success: function (response) {
			     $("#wait").css("display", "none");
			     $( '#calc-results' ).html(response); 
			}
        });
    }
 
  showValues();
</script>     
</body>
</html>