<?php 
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 300);
require_once dirname(__FILE__) . '/db.php';

if(isset($_REQUEST['postcde']) && !($_REQUEST['postcde']=='')){
    $postcde = $_REQUEST['postcde'];

//chart 1-1//
$chat1_1 = 0;
$price_growth_1 = 0;
$price_growth_5 = 0;
$price_1 = 0;
$price_2 = 0;
$price_5 = 0;

$query1_1 = "select a.year, a.avg_price_paid price from $bi_marketing a 
            where a.disrtict_or_region_or_yopa_region = '$postcde' and a.prop_type = 'All' and a.year in ('-1', '-2', '-5')
            order by  last_updated desc limit 3";

//echo $query1_1;
$statement1_1 = $conn->prepare($query1_1);
$statement1_1->execute();
$query_1_1 = $statement1_1->fetchAll(\PDO::FETCH_ASSOC);

if($query_1_1){
    $chat1_1 = 1;
    foreach($query_1_1 as $q){
            if($q['year'] == -1){$price_1 = $q['price'];}
            if($q['year'] == -2){$price_2 = $q['price'];}
            if($q['year'] == -5){$price_5 = $q['price'];}        
        }
        
        $price_growth_1 = ($price_1 - $price_2)*100/$price_1;
        $price_growth_5 = ($price_1 - $price_5)*100/$price_1;
}

//chart 1-2//
$chat1_2 = 0;
$categories1 = [];
$last_12_months = [];
$previous_12_months = [];

$sql_2 = "select a.prop_type, sum(if(a.year = -1, a.avg_price_paid, 0)) price_1, sum(if(a.year = -2, a.avg_price_paid, 0)) price_2 from $bi_marketing a 
         where a.disrtict_or_region_or_yopa_region = '$postcde' and a.prop_type not in('All', 'Other') and a.year in ('-1', '-2') group by a.prop_type
         order by FIELD(a.prop_type, 'Detached', 'Semi-Detached', 'Terraced', 'Flats/Maisonettes')";
//echo $sql_2;
$statement1_2 = $conn->prepare($sql_2);
$statement1_2->execute();
$query_1_2 = $statement1_2->fetchAll(\PDO::FETCH_ASSOC);

if($query_1_2){
    $chat1_2 = 1;
    foreach($query_1_2 as $q){
            $categories1[] = $q['prop_type'];
            $last_12_months[] = floatval($q['price_1']);
            $previous_12_months[] = floatval($q['price_2']);        
        }        
}

//chart 2-1//
$chat2_1 = 0;
$table_row = '';

$sql2_1 = "select b.disrtict_or_region_or_yopa_region disrtict_region_yopa,
            sum(if(b.prop_type='Flats/Maisonettes', b.avg_price_paid, 0)) Flats,
            sum(if(b.prop_type='Terraced', b.avg_price_paid, 0)) Terraced,
            sum(if(b.prop_type='Semi-Detached', b.avg_price_paid, 0)) 'Semi-deteched',
            sum(if(b.prop_type='Detached', b.avg_price_paid, 0)) Deteched
            from $bi_marketing b
            where b.year = -1 and b.prop_type not in ('All', 'Other') and b.disrtict_or_region_or_yopa_region = '$postcde'
            group by b.disrtict_or_region_or_yopa_region ";
            
$sql2_2 = "select b.disrtict_or_region_or_yopa_region disrtict_region_yopa, b.year, sum(b.sales) sales
            from $bi_marketing b
            where b.year in (-1, -2) and b.prop_type = 'All' and b.disrtict_or_region_or_yopa_region = '$postcde' 
            group by b.disrtict_or_region_or_yopa_region, b.year";

$st = $conn->prepare("select distinct p.region, p.yopa_region from $postcode_list p where p.district = '$postcde' limit 1");
$st->execute();
$row = $st->fetchAll(\PDO::FETCH_ASSOC);

if($row){
    $region = $row[0]['region'];
    $yopa_region = $row[0]['yopa_region'];

$sql2_1 .= " Union
            select b.disrtict_or_region_or_yopa_region disrtict_region_yopa,
            sum(if(b.prop_type='Flats/Maisonettes', b.avg_price_paid, 0)) Flats,
            sum(if(b.prop_type='Terraced', b.avg_price_paid, 0)) Terraced,
            sum(if(b.prop_type='Semi-Detached', b.avg_price_paid, 0)) 'Semi-deteched',
            sum(if(b.prop_type='Detached', b.avg_price_paid, 0)) Deteched
            from $bi_marketing b
            where b.year = -1 and b.prop_type not in ('All', 'Other') and b.disrtict_or_region_or_yopa_region = '$region'
            group by b.disrtict_or_region_or_yopa_region
            Union
            select b.disrtict_or_region_or_yopa_region disrtict_region_yopa,
            sum(if(b.prop_type='Flats/Maisonettes', b.avg_price_paid, 0)) Flats,
            sum(if(b.prop_type='Terraced', b.avg_price_paid, 0)) Terraced,
            sum(if(b.prop_type='Semi-Detached', b.avg_price_paid, 0)) 'Semi-deteched',
            sum(if(b.prop_type='Detached', b.avg_price_paid, 0)) Deteched
            from $bi_marketing b
            where b.year = -1 and b.prop_type not in ('All', 'Other') and b.disrtict_or_region_or_yopa_region = '$yopa_region'
            group by b.disrtict_or_region_or_yopa_region";
            
$sql2_2 .= " union
            select b.disrtict_or_region_or_yopa_region disrtict_region_yopa, b.year, sum(b.sales) sales
            from $bi_marketing b
            where b.year in (-1, -2) and b.prop_type not in ('All', 'Other') and b.disrtict_or_region_or_yopa_region = '$yopa_region'
            group by b.disrtict_or_region_or_yopa_region, b.year";
}

$statement2_1 = $conn->prepare($sql2_1);
$statement2_1->execute();
$query_2_1 = $statement2_1->fetchAll(\PDO::FETCH_ASSOC);

if($query_2_1){
    $chat2_1 = 1;
    foreach($query_2_1 as $q){
       $table_row.='<tr>
                      <td class="table-part"><img class="house" src="images/house-instant-valuation-01.png">'.$q['disrtict_region_yopa'].'</td>
                      <td>£'.number_format($q['Flats']).'</td>
                      <td>£'.number_format($q['Terraced']).'</td>
                      <td>£'.number_format($q['Semi-deteched']).'</td>
                      <td>£'.number_format($q['Deteched']).'</td>
                    </tr>';         
        }        
}

//chart 2-2//
$chat2_2 = 0;

$class1 = 'up';
$count_1 = 0;
$count_2 = 0;
$percent = 0;
$class2 = 'up';
$yopa_region = '';
$count_y_1 = 0;
$count_y_2 = 0;
$percent_y = 0;

$statement2_2 = $conn->prepare($sql2_2);
$statement2_2->execute();
$query_2_2 = $statement2_2->fetchAll(\PDO::FETCH_ASSOC);

if($query_2_2){
    $chat2_2 = 1;
    foreach($query_2_2 as $q){
        if($q['disrtict_region_yopa']==$postcde){
              if($q['year']==-1){$count_1 = $q['sales'];}else{$count_2 = $q['sales'];}          
        }else{
            $yopa_region = $q['disrtict_region_yopa'];
            if($q['year']==-1){$count_y_1 = $q['sales'];}else{$count_y_2 = $q['sales'];}
        }   
    } 
    
    if($count_1>0){$percent = ($count_1-$count_2)*100/$count_1;}                    
    if($percent<0){$class1 = 'down';}  
    if($count_y_1>0){$percent_y = ($count_y_1 - $count_y_2)*100/$count_y_1;}
    if($percent_y<0){$class2 = 'down';}         
}

//chart 3-1//
$chat3_1 = 0;
$chart_part = '';

$sql3_1 = "select a.highest_price_paid_address first_address, a.highest_price_paid first_transaction,
           a.second_highest_price_paid_address second_address, a.second_highest_price_paid second_transaction,
           a.third_highest_price_paid_address third_address, a.third_highest_price_paid third_transaction 
           from $bi_marketing a 
           where a.disrtict_or_region_or_yopa_region = '$postcde' and a.prop_type = 'All' and a.year ='-1' 
           order by highest_price_paid desc, second_highest_price_paid desc, third_highest_price_paid desc limit 1";

//echo $sql3_1;
$statement3_1 = $conn->prepare($sql3_1);
$statement3_1->execute();
$query_3_1 = $statement3_1->fetchAll(\PDO::FETCH_ASSOC);

if($query_3_1){
    $chat3_1 = 1;
    foreach($query_3_1 as $q){                                  
        $chart_part .= '<div class="tile-stats">
                        <div class="marg">
                          <div class="icon"><img class="feesfinals" src="images/feesfinals_01.png"></div>
                          <h3 class="green1">£'.number_format($q['first_transaction']).'</h3>
                          <p class="elgin_avenue">'.$q['first_address'].'</p>
                        </div>
                        </div>
                        <div class="tile-stats">
                        <div class="marg">
                          <div class="icon"><img class="feesfinals" src="images/feesfinals_02.png"></div>
                          <h3 class="green1">£'.number_format($q['second_transaction']).'</h3>
                          <p class="elgin_avenue">'.$q['second_address'].'</p>
                        </div>
                        </div>
                        <div class="tile-stats">
                        <div class="marg">
                          <div class="icon"><img class="feesfinals" src="images/feesfinals_03.png"></div>
                          <h3 class="green1">£'.number_format($q['third_transaction']).'</h3>
                          <p class="elgin_avenue">'.$q['third_address'].'</p>
                        </div>
                        </div>';             
        }
}

//chart 3-2//
$chat3_2 = 0;
$categories2 = [];
$transactions = [];
$prices = [];
$transactions_sum = 0;
$prices_sum = 0;

$sq3_2 = "select DATE_FORMAT(n.month, '%b %y') month, n.avg_price_paid prices, n.sales transactions from
            (select month, avg_price_paid, sales  from $national_trend  
            order by month desc limit 12) n order by n.month"; 

//select DATE_FORMAT(n.month, '%b %y') month, n.avg_price_paid prices, n.sales transactions from $national_trend n order by n.month desc limit 12";
//echo $sq3_2;
$statement3_2 = $conn->prepare($sq3_2);
$statement3_2->execute();
$query_3_2 = $statement3_2->fetchAll(\PDO::FETCH_ASSOC);

if($query_3_2){
    $chat3_2 = 1;
   
    foreach($query_3_2 as $q){
            $transactions_sum = $transactions_sum + $q['transactions'];
            $prices_sum = $prices_sum + $q['prices'];        
        } 
        
    foreach($query_3_2 as $q){
            $categories2[] = $q['month'];
            if($transactions_sum!==0){$transactions[] = floatval($q['transactions']*100/$transactions_sum);}else{$transactions[] = 0;}
            if($prices_sum!==0){$prices[] = floatval($q['prices']*100/$prices_sum);}else{$prices[] = 0;}       
        }  
}

$conn = null; 
?>

<!--logo part-->
<div class="row">
<div class="col-md-12">
    <a class="logo" href="index.php"><img src="images/logo.bmp"></a> 
    <div class="pull-right">   
         <a id="close-image1" href="https://www.yopa.co.uk/property-valuation" target="_blank"><img src="images/book_valuation_button.png"></a> 
         <a id="close-image" href="https://www.yopa.co.uk/request-a-callback" target="_blank"><img src="images/request_callback_button.png"></a> 
         <div class="clearfix"></div>
         <br />
    </div>
</div>
</div>

<!--banner part-->
<div class="row">
<div class="col-md-12 rectangle_1">
    <h1 class="top_title">Market report for <?php echo $postcde; ?></h1>
    <img class="dyk" src="images/housesimage.png"></img>
</div>
</div>

<div class="clearfix"></div>
<!--chart section 1-->
<div class="row">
<div class="col-md-4">
    <div class="x_panel">
      <div class="x_content">
      <div class="padding_box">
      <?php if($chat1_1){ ?>
      <h4 class="title-text">Price growth in <?php echo $postcde; ?> </h4>
      <div class="row">
            <center>
            <div class="col-md-12">
                <div class="col-md-6 text-block">
                <img class="fp1" src="images/featured_property.png"></img>
                <h1 class="num"> 1 <span class="year1">year</span></h1>
                <p class="percent2"><?php echo number_format($price_growth_1); ?>%</p>
                </div>
                <div class="col-md-6 text-block">
                <img class="fp2" src="images/featured_property.png"></img>
                <h1 class="num"> 5 <span class="year5">year</span></h1>
                <p class="percent2"><?php echo number_format($price_growth_5); ?>%</p>
                </div>
            </div>
           </center>
      </div>
      <?php }else{ echo '<center><img style="height: 100%;" src="images/nodata.png"/></center>';} ?>
      </div>
      </div>
    </div>
</div>
<div class="col-md-8">
    <div class="x_panel">
      <div class="x_content">
      <?php if($chat1_2){ ?>
            <div id="container1" style="width: 100%; height: 259px; margin: 0 auto"></div>
      <?php }else{ echo '<center><img style="height: 100%;" src="images/nodata.png"/></center>';} ?> 
      </div>
    </div>
    </div>
</div>
<!--chart section 2-->             
<div class="row">
    <div class="col-md-8">
    <div class="x_panel houses_01">
      <div class="x_content">
      <div class="padding_box">
      <?php if($chat2_1){ ?>
        <h4 class="title-text">Average property values <small>(last 12 months)</small></h4>
        <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th></th>
              <th>Flats</th>
              <th>Terraced</th>
              <th>Semi-detached</th>
              <th>Detached</th>
            </tr>
          </thead>
          <tbody> 
          <?php echo $table_row; ?>
          </tbody>
        </table>
        </div>
      <?php }else{ echo '<center><img style="height: 100%;" src="images/nodata.png"/></center>';} ?>  
      </div>
      </div>
    </div>
    </div>
    
    <div class="col-md-4">
    <div class="x_panel">
      <div class="x_content">
      <div class="padding_box">
      <?php if($chat2_2){ ?>
        <h4 class="title-text">Number of transactions <small class="small">(last 12 months)</small></h4>
        <div class="tile-stats">
          <div class="icon triangle-<?php echo $class1; ?>"></div>
          <p class="percent-<?php echo $class1; ?>"><?php echo number_format($percent); ?>%</p>
          <div class="code"><?php echo $postcde; ?></div>
          <div class="count"><?php echo number_format($count_1); ?></div>
        </div>
        <div class="tile-stats">
          <div class="icon triangle-<?php echo $class2; ?>"></div>
          <p class="percent-<?php echo $class2; ?>"><?php echo number_format($percent_y); ?>%</p>
          <div class="code"><?php echo $yopa_region; ?></div>
          <div class="count"><?php echo number_format($count_y_1); ?></div>
        </div>
       <?php }else{ echo '<center><img style="height: 100%;" src="images/nodata.png"/></center>';} ?> 
      
      </div>  
      </div>
    </div>
    </div>
</div>

<!--chart section 3-->
<div class="row">

    <div class="col-md-4">
    <div class="x_panel">
      <div class="x_content">
      <div class="padding_box">
      <?php if($chat3_1){ ?>      
            <h4 class="title-text">Most expensive transactions in <?php echo $postcde; ?> <small>(in last 12 months)</small></h4>
            <div class="padding2">
                    <?php echo $chart_part; ?>            
            </div>
      <?php }else{ echo '<center><img style="height: 100%;" src="images/nodata.png"/></center>';} ?>            
      </div>
      </div>
    </div>
    </div>
    
    <div class="col-md-8">
        <div class="x_panel">
          <div class="x_content">
          <?php if($chat3_2){ ?>
            <div id="container2" style="width: 100%; height: 259px; margin: 0 auto"></div>
          <?php }else{ echo '<center><img style="height: 100%;" src="images/nodata.png"/></center>';} ?>
          </div>
        </div>
    </div>
</div>

 <?php if($chat1_2){ ?>
<script>
Highcharts.chart('container1', {
    colors: ['#223366','#45babf'],
    chart: {type: 'column', backgroundColor: '#f5f5f5'},
    title: {text: 'Average prices in ' + '<?php echo $postcde; ?>' + ' vs Previous 12 months', align: 'left', x: 15, y : 25.9,
        style: {fontFamily: 'CircularStd', color: "#263269", width: '515px', height: '25px', fontSize: '20px', fontWeight: '500', height: '25px'}
    },
    xAxis: {lineColor: '#263269', categories: <?php echo json_encode($categories1); ?> }, 
    yAxis: {lineWidth: 1, lineColor: '#263269', gridLineWidth: 0, title: {enabled: false, text: ''},
            labels: {
                formatter: function(){return '£' + this.axis.defaultLabelFormatter.call(this);}
                } 
    },
    credits: {enabled: false},
    plotOptions: {series: {pointWidth: 60, groupPadding: 0.01, borderWidth: 0}},
    legend: {align: 'right', verticalAlign: 'top', y: 10},   
    tooltip: {
        valuePrefix: '£',
        //pointFormat: '<span style="color:{series.color}">{series.name}: £{point.y}</span><br/>',
        formatter:function(e){
                return  this.point.category+'<br/><span style="color:'+this.point.series.color+'">' + this.point.series.name+': <b>£'+Highcharts.numberFormat(this.point.y,0,'.',',')+'</b></span><br/>';
           }
    },
    series: [{name: 'last 12 months', data: <?php echo json_encode($last_12_months); ?>}, 
             {name: 'previous 12 months', data: <?php echo json_encode($previous_12_months); ?>}],
    
        responsive: {
        rules: [{condition: {maxWidth: 700},
            chartOptions: {
                legend: {align: 'center', verticalAlign: 'bottom', layout: 'horizontal'},                 
                plotOptions: {series: {pointWidth: 20, groupPadding: 0.01, borderWidth: 0}},
            }
        }]
    }
});
</script>
<?php } ?>
<?php if($chat3_2){ ?>
<script>
Highcharts.chart('container2', {
    colors: ['#223366','#45babf'],
    chart: {type: 'column', backgroundColor: '#f5f5f5'},
    title: {text: 'National trends', align: 'left', x: 15, y : 25.9, 
        style: {fontFamily: 'CircularStd', color: "#263269", width: '515px', height: '25px', fontSize: '20px', fontWeight: '500'}
    },
    xAxis: {lineColor: '#263269', categories: <?php echo json_encode($categories2); ?>}, 
    yAxis: {lineWidth: 1, lineColor: '#263269', gridLineWidth: 0, title: {enabled: false, text: ''},
            labels: {formatter: function(){return this.axis.defaultLabelFormatter.call(this) + '%';}} 
    },
    credits: {enabled: false},
    plotOptions: {series: {pointWidth: 20, groupPadding: 0.01, borderWidth: 0}},
    legend: {align: 'right', verticalAlign: 'top', y: 10},  
       tooltip: {
        //valuePrefix: '%',
        //pointFormat: '<span style="color:{series.color}">{series.name}: £{point.y}</span><br/>',
        formatter:function(e){
                return  this.point.category+'<br/><span style="color:'+this.point.series.color+'">' + this.point.series.name+': <b>'+Highcharts.numberFormat(this.point.y,1,'.',',')+'%</b></span><br/>';
           }
    }, 
    series: [{name: 'transactions', data: <?php echo json_encode($transactions); ?>}, 
             {name: 'prices', data: <?php echo json_encode($prices); ?>}],
    
    responsive: {
        rules: [{condition: {maxWidth: 700},
            chartOptions: {
                legend: {align: 'center', verticalAlign: 'bottom', layout: 'horizontal'},                
                plotOptions: {series: {pointWidth: 10, groupPadding: 0.01, borderWidth: 0}},
            }
        }]
    } 
});
</script>
<?php } ?>
<?php }else{ echo '<center><img style="height: 350px;" src="images/nodata.png"/></center>'; } ?>