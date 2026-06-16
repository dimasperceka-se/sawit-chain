<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Goutte\Client;
use Symfony\Component\DomCrawler\Crawler;

class MCocoa extends CI_Model {

    private $data = array();

    public function __construct()
    {
        parent::__construct();
        
    }

    public function getPrice($date='')
    {
        if (empty($date)) {
            $date = date('Y-m-d');
        }
        $date = date('Y-m-d', strtotime($date));
        // echo '<pre>'; print_r($date); echo '</pre>'; 
        $price = $this->getPriceDB($date);
        if ($price === false) {
            $price = $this->getPriceOL($date);
        }
        return $price;
    }

    public function getPriceOL($date)
    {
        $client = new Client();
        
        $crawler = $client->request('GET', 'http://www.icco.org/statistics/cocoa-prices/daily-prices.html?mode=day&begin='.$date);
        // echo '<pre>'; var_dump($crawler); echo '</pre>'; exit;
        // reset data
        $this->data = array();
        $crawler->filter('#com_statistics td')->each(function ($node) {
            // print $node->text()." | ";
            self::add_data($node->text());
        });
        // echo '<pre>'; print_r($this->data); echo '</pre>'; exit;
        if (!empty($this->data)) {            
            $this->addPrice(array_merge(array($date),$this->data));
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            return $this->getPriceDB($date);
        } else {
            // insert NULL
            $this->addPrice(array($date,NULL,NULL,NULL,NULL,NULL));
            // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
            return $this->getPriceDB($date);
        }
        return false;
    }

    public function getPriceDB($date)
    {
        $sql = "SELECT 
    system_date
    , `date`
    , ICCODailyPriceSDRs
    , ICCODailyPriceUS
    , LondonFuturesSterling
    , NewYorkFuturesUS 
FROM
    cocoa_price 
WHERE
    system_date = ?
LIMIT 1
        ";
        $query = $this->db->query($sql, array($date));
        // echo '<pre>'; print_r($this->db->last_query()); echo '</pre>'; 
        if ($query->num_rows()>0) {
            return $query->row_array(0);
        }
        return false;
    }

    public function addPrice($data)
    {
        $sql = "INSERT INTO cocoa_price (
    system_date
    , `date`
    , ICCODailyPriceSDRs
    , ICCODailyPriceUS
    , LondonFuturesSterling
    , NewYorkFuturesUS
) 
VALUES
    (
        ?
        , ?
        , ?
        , ?
        , ?
        , ?
    )
ON DUPLICATE KEY UPDATE
    `date` = VALUES(`date`)
    , ICCODailyPriceSDRs = VALUES(ICCODailyPriceSDRs)
    , ICCODailyPriceUS = VALUES(ICCODailyPriceUS)
    , LondonFuturesSterling = VALUES(LondonFuturesSterling)
    , NewYorkFuturesUS = VALUES(NewYorkFuturesUS)
        ";
        return $this->db->query($sql, $data);
    }

    private function add_data($value)
    {
        $this->data[] = $value;
    }

    public function generatePrice()
    {
        $sql = "SELECT
               a.date
          FROM (
                    SELECT CURRENT_DATE() - INTERVAL (A.A + (10 * B.A) + (100 * C.A)) DAY AS DATE
                    FROM (SELECT 0 AS A UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS A
                    CROSS JOIN (SELECT 0 AS A UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS B
                    CROSS JOIN (SELECT 0 AS A UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS C
          ) a
          WHERE a.date BETWEEN '2016-01-01' AND CURRENT_DATE()
          ORDER BY DATE
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            foreach ($query->result_array() as $key => $value) {
                self::getPrice($value['date']);
            }
        }
        // exit;
        return true;
    }

    public function schedulePrice()
    {
        $start = date('Y-m-d', strtotime(date('Y-m-d').' -7 days'));
        $sql = "SELECT
               a.date
          FROM (
                    SELECT CURRENT_DATE() - INTERVAL (A.A + (10 * B.A) + (100 * C.A)) DAY AS DATE
                    FROM (SELECT 0 AS A UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS A
                    CROSS JOIN (SELECT 0 AS A UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS B
                    CROSS JOIN (SELECT 0 AS A UNION ALL SELECT 1 UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9) AS C
          ) a
          WHERE a.date BETWEEN '{$start}' AND CURRENT_DATE()
          ORDER BY DATE
        ";
        $query = $this->db->query($sql);
        if ($query->num_rows()>0) {
            foreach ($query->result_array() as $key => $value) {
                self::getPriceOL($value['date']);
            }
        }
        // exit;
        return true;
    }

}

/* End of file mcocoa.php */
/* Location: ./application/models/mcocoa.php */