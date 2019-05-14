<?php 
ini_set('memory_limit', '2000M');
ini_set('max_execution_time', 150000);
ini_set('xdebug.max_nesting_level', 1000);
date_default_timezone_set('UTC');

require_once dirname(__FILE__) . '/db.php';
require_once dirname(__FILE__) . '/PHPExcel/Classes/PHPExcel/IOFactory.php';

///import csv file for postcode_sectors///
$uploaded_file = 'uploads/postcode_sectors.csv';
        $reader = PHPExcel_IOFactory::createReader('CSV')
            ->setDelimiter(',')
            ->setEnclosure('"')
            //->setLineEnding("\n")
            ->setSheetIndex(0)
            ->load($uploaded_file); 

    $objWorksheet = $reader->setActiveSheetIndex(0);
    $highestRow = $objWorksheet->getHighestRow();
    //$highestColumn = $objWorksheet->getHighestColumn();
    //$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
 
    //read from file and insert into MySQL db//
    $rows_inserted1 = 0;
    for($row = 2; $row <= $highestRow; ++$row){
        $sector = trim($value=$objWorksheet->getCellByColumnAndRow(0, $row)->getValue());
        $district = trim($value=$objWorksheet->getCellByColumnAndRow(1, $row)->getValue());
        $area = trim($value=$objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
        $region = trim($value=$objWorksheet->getCellByColumnAndRow(3, $row)->getValue());
        $yopa_region = trim($value=$objWorksheet->getCellByColumnAndRow(4, $row)->getValue());
            
        $query1 = "select * from ".$postcode_list." where sector='$sector' and district='$district' and area='$area' and region='$region' and yopa_region='$yopa_region';";       
        $statement1 = $conn->prepare($query1);
        $statement1->execute();
        $nRows1 = $statement1->rowCount();
        
        if($nRows1==0){     
                $sql1 = "INSERT INTO ".$postcode_list." (sector, district, area, region, yopa_region) VALUES (:sector, :district, :area, :region, :yopa_region)";
                $stmt= $conn->prepare($sql1);
                $stmt->execute(['sector'=>$sector, 'district'=>$district, 'area'=>$area, 'region'=>$region, 'yopa_region'=>$yopa_region]);
                $rows_inserted1++;
            }       

    }
    echo $rows_inserted1 ." new records from total ". $highestRow . " records of the postcode_sectors.csv file imported. <br/>";
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

///import csv file for national_trend///
$uploaded_file = 'uploads/national_trend.csv';
        $reader = PHPExcel_IOFactory::createReader('CSV')
            ->setDelimiter(',')
            ->setEnclosure('"')
            //->setLineEnding("\n")
            ->setSheetIndex(0)
            ->load($uploaded_file); 

    $objWorksheet = $reader->setActiveSheetIndex(0);
    $highestRow2 = $objWorksheet->getHighestRow();
 
    //read from file and insert into MySQL db//
    $rows_inserted2 = 0;
    for($row = 2; $row <= $highestRow2; ++$row){
        $month = trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue());// $month = gmdate('Y-m-d', PHPExcel_Shared_Date::ExcelToPHP(trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue())));      ///??????
        $_5th_pct_price_paid = floatval($objWorksheet->getCellByColumnAndRow(1, $row)->getValue());
        $_95th_pct_price_paid = floatval($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
        $median_price_paid = floatval($objWorksheet->getCellByColumnAndRow(3, $row)->getValue());
        $avg_price_paid = floatval($objWorksheet->getCellByColumnAndRow(4, $row)->getValue());
        $sales = floatval($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
                  
        $query2 = "select * from ".$national_trend." where month='$month';";       
        $statement2 = $conn->prepare($query2);
        $statement2->execute();
        $nRows2 = $statement2->rowCount();
        
        if($nRows2==0){     
                $sql2 = "INSERT INTO ".$national_trend." (month, _5th_pct_price_paid, _95th_pct_price_paid, median_price_paid, avg_price_paid, sales) VALUES (:month, :_5th_pct_price_paid, :_95th_pct_price_paid, :median_price_paid, :avg_price_paid, :sales)";
                $stmt2= $conn->prepare($sql2);
                $stmt2->execute(['month'=>$month, '_5th_pct_price_paid'=>$_5th_pct_price_paid, '_95th_pct_price_paid'=>$_95th_pct_price_paid, 'median_price_paid'=>$median_price_paid, 'avg_price_paid'=>$avg_price_paid, 'sales'=>$sales]);
                $rows_inserted2++;
            }       
    }
    echo $rows_inserted2 ." new records from total ". $highestRow2 . " records of the national_trend.csv file imported. <br/>";

///import csv file for landreg_marketing_stats.csv//
$uploaded_file = 'uploads/landreg_marketing_stats.csv';
        $reader = PHPExcel_IOFactory::createReader('CSV')
            ->setDelimiter(',')
            ->setEnclosure('"')
            //->setLineEnding("\n")
            ->setSheetIndex(0)
            ->load($uploaded_file); 

    $objWorksheet = $reader->setActiveSheetIndex(0);
    $highestRow3 = $objWorksheet->getHighestRow();
 
    //read from file and insert into MySQL db//
    $rows_inserted3 = 0;
    for($row = 2; $row <= $highestRow3; ++$row){ //$highestRow3  
        $grouping_geo = trim($objWorksheet->getCellByColumnAndRow(0, $row)->getValue());
        $disrtict_or_region_or_yopa_region = trim($objWorksheet->getCellByColumnAndRow(1, $row)->getValue());
        $prop_type = trim($objWorksheet->getCellByColumnAndRow(2, $row)->getValue());
        $year = trim($objWorksheet->getCellByColumnAndRow(3, $row)->getValue());
        $highest_price_paid = floatval($objWorksheet->getCellByColumnAndRow(4, $row)->getValue());
        $highest_price_paid_address = trim($objWorksheet->getCellByColumnAndRow(5, $row)->getValue());
        $second_highest_price_paid = floatval($objWorksheet->getCellByColumnAndRow(6, $row)->getValue());
        $second_highest_price_paid_address = trim($objWorksheet->getCellByColumnAndRow(7, $row)->getValue());
        $third_highest_price_paid = floatval($objWorksheet->getCellByColumnAndRow(8, $row)->getValue());
        $third_highest_price_paid_address = trim($objWorksheet->getCellByColumnAndRow(9, $row)->getValue());
        $_5th_percentile_price_paid = floatval($objWorksheet->getCellByColumnAndRow(10, $row)->getValue());
        $_95th_percentile_price_paid = floatval($objWorksheet->getCellByColumnAndRow(11, $row)->getValue());
        $medain_price_paid = floatval($objWorksheet->getCellByColumnAndRow(12, $row)->getValue());
        $sum_price_paid = floatval($objWorksheet->getCellByColumnAndRow(13, $row)->getValue());
        $avg_price_paid = floatval($objWorksheet->getCellByColumnAndRow(14, $row)->getValue());
        $std_price_paid = floatval($objWorksheet->getCellByColumnAndRow(15, $row)->getValue());
        $sales = trim($objWorksheet->getCellByColumnAndRow(16, $row)->getValue());
        $avg_price_cagr = floatval($objWorksheet->getCellByColumnAndRow(17, $row)->getValue());
        $sales_cagr = floatval($objWorksheet->getCellByColumnAndRow(18, $row)->getValue());
        $last_updated = trim($objWorksheet->getCellByColumnAndRow(19, $row)->getValue()); //. "<br/>";
                          
        $query3 = "select * from ".$bi_marketing." where grouping_geo='$grouping_geo' and disrtict_or_region_or_yopa_region='$disrtict_or_region_or_yopa_region' and prop_type='$prop_type' and year='$year';";       
        $statement3 = $conn->prepare($query3);
        $statement3->execute();
        $nRows3 = $statement3->rowCount();
        
        if($nRows3==0){     
                $sql3 = "INSERT INTO ".$bi_marketing." (        
                                    grouping_geo,
                                    disrtict_or_region_or_yopa_region,
                                    prop_type,
                                    year,
                                    highest_price_paid,
                                    highest_price_paid_address,
                                    second_highest_price_paid,
                                    second_highest_price_paid_address,
                                    third_highest_price_paid,
                                    third_highest_price_paid_address,
                                    _5th_percentile_price_paid,
                                    _95th_percentile_price_paid,
                                    medain_price_paid,
                                    sum_price_paid,
                                    avg_price_paid,
                                    std_price_paid,
                                    sales,
                                    avg_price_cagr,
                                    sales_cagr,
                                    last_updated) 
                                    VALUES (:grouping_geo,
                                            :disrtict_or_region_or_yopa_region,
                                            :prop_type,
                                            :year,
                                            :highest_price_paid,
                                            :highest_price_paid_address,
                                            :second_highest_price_paid,
                                            :second_highest_price_paid_address,
                                            :third_highest_price_paid,
                                            :third_highest_price_paid_address,
                                            :_5th_percentile_price_paid,
                                            :_95th_percentile_price_paid,
                                            :medain_price_paid,
                                            :sum_price_paid,
                                            :avg_price_paid,
                                            :std_price_paid,
                                            :sales,
                                            :avg_price_cagr,
                                            :sales_cagr,
                                            :last_updated)";
                                            
                $stmt3= $conn->prepare($sql3);
                $stmt3->execute([
                                'grouping_geo'=>$grouping_geo,
                                'disrtict_or_region_or_yopa_region'=>$disrtict_or_region_or_yopa_region,
                                'prop_type'=>$prop_type,
                                'year'=>$year,
                                'highest_price_paid'=>$highest_price_paid,
                                'highest_price_paid_address'=>$highest_price_paid_address,
                                'second_highest_price_paid'=>$second_highest_price_paid,
                                'second_highest_price_paid_address'=>$second_highest_price_paid_address,
                                'third_highest_price_paid'=>$third_highest_price_paid,
                                'third_highest_price_paid_address'=>$third_highest_price_paid_address,
                                '_5th_percentile_price_paid'=>$_5th_percentile_price_paid,
                                '_95th_percentile_price_paid'=>$_95th_percentile_price_paid,
                                'medain_price_paid'=>$medain_price_paid,
                                'sum_price_paid'=>$sum_price_paid,
                                'avg_price_paid'=>$avg_price_paid,
                                'std_price_paid'=>$std_price_paid,
                                'sales'=>$sales,
                                'avg_price_cagr'=>$avg_price_cagr,
                                'sales_cagr'=>$sales_cagr,
                                'last_updated'=>$last_updated                             
                                ]);  
                $rows_inserted3++;
            }       

    }
    echo $rows_inserted3 ." new records from total ". $highestRow3 . " records of the landreg_marketing_stats.csv file imported. <br/>"; 
?>