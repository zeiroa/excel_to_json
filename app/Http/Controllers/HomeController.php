<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    public function index()
    {
        Excel::load(public_path('a.xlsx'), function($reader) {
//            var_dump($reader->toArray());
            print_r(json_encode($reader->toArray()));
        });
    }
    
    public function doo()
    {
//        $this->importExcel('a.xls', 'tdprj_2003', '2017-09-25');
    }
    
    protected function importExcel($excleName, $proj, $stationName_date)
    {
        Excel::load(public_path($excleName), function($reader) use ($proj, $stationName_date) {
            foreach ($reader->toArray() as $item) {
                if (isset($item['pos0']) && isset($item['tag0'])) {
                    $arr2['ref'] = '';
                    $arr2['stationName'] = $stationName_date;
                    $arr2['lineIndex'] = 2;
                    $arr2['labels'][] = $item['tag0'];
                    $arr2['labelsPos'][] = $item['pos0'];
                    $arr2['onSwitch'] = 0;
                    $arr2['line'] = '左线';
                    
                    $json2 = json_encode($arr2);
                    $result2 = $this->my_curl("http://app.lituoxy.com:8091/api/{$proj}/sublineLable", $json2, 'POST', $proj);
                    
                    echo $result2 ?? '标签为空';
                    echo '<br>';
                    $arr2 = null;
                    $result2 = null;
                }
                if (isset($item['pos1']) && isset($item['tag1'])) {
                    $arr1['ref'] = '';
                    $arr1['stationName'] = $stationName_date;
                    $arr1['lineIndex'] = 1;
                    $arr1['labelsPos'][] = $item['pos1'];
                    $arr1['labels'][] = $item['tag1'];
                    $arr1['onSwitch'] = 0;
                    $arr1['line'] = '右线';
        
                    $json1 = json_encode($arr1);
                    $result1 = $this->my_curl("http://app.lituoxy.com:8091/api/{$proj}/sublineLable", $json1, 'POST', $proj);
        
                    echo $result1 ?? '标签为空';
                    echo '<br>';
                    $arr1 = null;
                    $result1 = null;
                }
            }
        });
    }
    
    protected function my_curl($url, $json_string, $method, $proj = null)
    {
        $ch = curl_init($url);
        if ($proj == 'tdprj_1101') {
            $password = "admin:456123";
        } elseif ($proj == 'tdprj_0110') {
            $password = "admin:333444";
        } else {
            $password = "admin:admin";
        }
        curl_setopt($ch, CURLOPT_USERPWD, $password);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json;charset=UTF-8',
                'Content-Length: ' . strlen($json_string))
        );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
