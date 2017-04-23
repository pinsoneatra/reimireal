<?php

namespace models\report;

use models\system\Database;
use models\system\Systema;

class ReportMana {

    private $conn;

    public function __construct() {
        $this->conn = new Database();
        $this->system = new Systema();
    }

    public function getReport() {
        if (isset($_POST['year'])) {
            //$month = $_POST['month'];

            $month = '12';
            $year = $this->conn->escape($_POST['year']);

            if ($month == 12) {
                $lastyear = $year;
                $startmonth = "01";
            } else {
                $lastyear = $year - 1;
                $startmonth = sprintf("%02d", $month + 1);
            }

            $months = array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
            $current = date('M', mktime(0, 0, 0, $month, 10));
            $start = array_search($current, $months) + 1;

            $total = 12;
            $y = $lastyear;
            for ($i = $start; $total > 0; $i++) {
                if ($i >= 12) {
                    $i = 0;
                    $y = $year;
                }
                $total--;
                $getmonth[] = $months[$i] . "-" . $y;
                $allmonths[$months[$i]] = 0;
            }
            //var_dump($getmonth);
            $arr = array();
            $new = array();
            $toshow = array();
            // find home package
            $sqlQry = $this->conn->query("SELECT
                    YEAR(`create_date`) as `productsYears`,
                    DATE_FORMAT(`create_date`,'%b') as `productsMonth`,
                    COUNT(`code`) as `totalProducts`
                    FROM `p_products` WHERE YEAR(`create_date`)='{$year}'
                    GROUP BY YEAR(`create_date`), MONTH(`create_date`)
                    ORDER BY YEAR(`create_date`), MONTH(`create_date`)");
                    //echo $sqlQry->sql();
            $new['name'] = 'News';
            if ($sqlQry->count() > 0) {
              $rows = $sqlQry->result();
              foreach($rows as $row){
                $home[$row->productsMonth] = (int) $row->totalProducts;
              }
              $new['data'] = array_values(array_merge($allmonths, $home));
            } else {
                $new['data'] = array_values(array_merge($allmonths));
            }
            array_push($toshow, $new);
            array_push($arr, $toshow, $getmonth);
            echo json_encode($arr);
        }
    }

}

?>
