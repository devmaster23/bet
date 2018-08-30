<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worksheets extends CI_Controller {
    private $pageTitles = array(
        'bets'  => 'Bets',
        'bet_summary' =>'Summary', 
        'bet_sheet' =>'RR and Parlay', 
        'bets_pick' =>'Picks',
        'bets_custom' =>'Custom',
    );

    public function __construct() {
        parent::__construct();
        $this->load->library('authlibrary');    
        if (!$this->authlibrary->loggedin()) {
            redirect('login');
        }
        $this->load->model("WorkSheet_model","model");
        $this->load->model("Picks_model","pick_model");
        $this->load->model("CustomBet_model","custombet_model");
        $this->load->library('session');
    }
    public function index()
    {
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $pageType = isset($_GET['type'])? $_GET['type']: 'bets';
        $settingId = isset($_GET['id'])?$_GET['id']:-1;
        $data['betweek'] = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $setting = $this->model->getActiveSetting($data['betweek'],$settingId);
        $data['settingId'] = $settingId;
        $data['setting'] = $setting;
        $data['pageType'] = $pageType;
        $data['pageTitle'] = $this->pageTitles[$pageType];
        $this->load->view('worksheets/'.$pageType, $data);
    }

    public function downloadPDF(){
        $date = new DateTime(date('Y-m-d'));
        $betweek = $date->format('W');
        $betweek = isset($_SESSION['betday']) ? $_SESSION['betday'] :$betweek;
        $data = $this->model->getBetSheet($betweek);
        $sheetData = $data['data'];

        $mpdf = new \Mpdf\Mpdf();
        $mpdf->defaultheaderfontsize = 10; /* in pts */
        $mpdf->defaultheaderfontstyle = 'B'; /* blank, B, I, or BI */
        $mpdf->defaultheaderline = 1; /* 1 to include line below header/above footer */
        $mpdf->defaultfooterfontsize = 12; /* in pts */
        $mpdf->defaultfooterfontstyle = 'B'; /* blank, B, I, or BI */
        $mpdf->defaultfooterline = 1; /* 1 to include line below header/above footer */
        $mpdf->SetHeader($data['type'].'| '. $data['date'] .' |Betday '.$betweek);
        $mpdf->SetFooter('{PAGENO}'); /* defines footer for Odd and Even Pages - placed at Outer margin */
        // $mpdf->watermark_font = 'DejaVuSansCondensed';
        // $mpdf->showWatermarkText = true;
        $mpdf->showWatermarkImage = true;

        $stylesheet = file_get_contents('assets/css/pdf.css');
        $mpdf->WriteHTML($stylesheet,1);

        $currentPage = 1;
        $rowCount = @count($sheetData);
        $colCount = @count($sheetData[0]);

        for($i =0 ; $i<$colCount ;$i++){
            for($j =0 ; $j<$rowCount ;$j++){
                if(isset($sheetData[$j]) && isset($sheetData[$j][$i]))
                    $item = $sheetData[$j][$i];
                else
                    continue;
        // foreach($sheetData as $key => $row_item){
        //     foreach($row_item as $key2 => $item){
                if($currentPage != 1)
                    $mpdf->AddPage();
                // $mpdf->SetWatermarkText('Page '.$currentPage);
                $mpdf->SetWatermarkImage('assets/img/logo_big.png', 0.3);
                $cls = count($item['disabled']) ? true: false;
                $is_parlay = $item['is_parlay'] ? "parlay" : "";

                $html = "<div class='sheet_block ".$is_parlay."'  id='".$item['title']."'>".
                        "<table class='main-table' border='1' cellpadding='0' cellspacing='0'><tbody>";
                foreach($item as $key3 => $team_item){

                    if($key3 === 'title' || $key3 === 'disabled' || $key3 === 'is_parlay')
                        continue;
                    
                    $disableCls = in_array($key3, $item['disabled']) ? "disabled" : "";
                    if($team_item == null || $team_item['team'] == null)
                    {
                        $html .="<tr>".
                              "<td width='10%'></td>".
                              "<td width='20%'></td>".
                              "<td width='35%' class='team'></td>".
                              "<td width='20%'></td>".
                              "<td width='15%'></td>".
                            "</tr>";
                    }else{
                        $html .="<tr class='".$disableCls."'>".
                              "<td width='10%'>".$team_item['vrn']."</td>".
                              "<td width='20%'>".$team_item['type']."</td>".
                              "<td width='35%' class='team'>".$team_item['team']."</td>".
                              "<td width='20%'>".$team_item['line']."</td>".
                              "<td width='15%'>".$team_item['time']."</td>".
                            "</tr>";
                    }
                    $html .="<tr>".
                          "<td width='10%'></td>".
                          "<td width='20%'></td>".
                          "<td width='35%' class='team'></td>".
                          "<td width='20%'></td>".
                          "<td width='15%'></td>".
                        "</tr>";
                };
                $html .="<tr>".
                    "<td colspan=2>Alternates</td>".
                    "<td class='team'></td>".
                    "<td></td>".
                    "<td></td>".
                  "</tr>";
                $html .= "</tbody></table>".
                    "<div class='mark-div'>".$item['title']."</div>".
                    "<div class='bottom-div'><table><tbody><tr><td>".$data['type']."</td><td>".$data['date']."</td><td>Bet Day".$betweek."</td></tr></table></div>";
                if($cls){
                    $mpdf->SetWatermarkImage('assets/img/cross.png', 0.3);
                }
                $html .= "</div>";   

                $mpdf->WriteHTML($html);
                $currentPage ++;
            };
        };
        $filename = 'sheets-'.$data['date'].'.pdf';
        $mpdf->Output($filename, 'D');
        exit;
    }

    public function loadCustomBet(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;

        $data['data'] = $this->custombet_model->getData($betweek, $type, $groupuser_id);
        $data['bets'] = $this->pick_model->getAllList($betweek, $type, $groupuser_id);
        header('Content-Type: application/json');
        echo json_encode( $data);
        die;
    }

    public function loadSummary(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data['summary'] = $this->model->getBetSummary($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
        die;
    }

    public function loadData(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data['games'] = $this->model->getGames($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
        die;
    }

    public function loadAllPickData(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data = $this->pick_model->getAll($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);
    }

    public function loadPickData()
    {
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data = $this->pick_model->getIndividual($betweek, 'pick');
        header('Content-Type: application/json');
        echo json_encode( $data);      
    }

    public function loadBetSetting(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $settingId = $_POST['settingId'];
        $data = $this->model->getBetSetting($betweek,$settingId);
        header('Content-Type: application/json');
        echo json_encode( $data);   
    }

    public function loadBetSheet(){
        $betweek = $_POST['betweek'];
        $_SESSION['betday'] = $betweek;
        $data = $this->model->getBetSheet($betweek);
        header('Content-Type: application/json');
        echo json_encode( $data);   
    }

    public function saveData(){
        $betweek = $_POST['betweek'];
        $data = $_POST['setting'];
        $this->model->saveData($betweek, $data);
        echo 'success';
        die;
    }

    public function savePickSelect(){
        $betweek = $_POST['betweek'];
        $data = $_POST['data'];
        $this->model->savePickSelect($betweek, $data);
        echo 'success';
        die;
    }

    public function saveCustomBet(){
        $betweek = $_POST['betweek'];
        $data = $_POST['data'];
        $type = isset($_SESSION['settingType']) ? $_SESSION['settingType'] : 0;
        $groupuser_id = isset($_SESSION['settingGroupuserId']) ? $_SESSION['settingGroupuserId'] : 0;
        $this->custombet_model->saveCustomBet($betweek, $data, $type, $groupuser_id);
        echo 'success';
        die;   
    }

    public function updateParlay(){
        $betweek = $_POST['betweek'];
        $data = $_POST['data'];
        $this->model->updateParlay($betweek, $data);
        echo 'success';
        die;   
    }
}

