<?php 
class Model{
    
    var $dao;
    
    public function setDao(IDao $dao){
        $this->dao = $dao;
    }
    
    function getTbls(){
        $sql="SHOW TABLES";
        $stmt = $this->dao->query($sql);
        $tbls = [];
        foreach ($stmt as $row) {
            $tbls[] = $row[0];
        }
        return $tbls;
    }
    
    
    public function getFields($tbl_name){

            $sql="SHOW FULL COLUMNS FROM {$tbl_name}";
            $stmt = $this->dao->query($sql);
            if(empty($stmt)) return [];
            $data = [];
            foreach ($stmt as $row) {
                //$data[] = $row;
                $ent = [];
                foreach($row as $key => $value){
                    $ent[$key] = $value;
                }
                $data[] = $ent;
            }
            return $data;
        
    }
    
    
    public function createAllFields(&$allTblNames, &$dbs){
        $allFields = [];
        
        foreach($allTblNames as $tbl_name){
            
            $margeFileds = [];
            foreach($dbs as $db_name => $dbEnt){
                $data = $dbEnt['data'];
                if(!empty( $data[$tbl_name])){
                    
                    $fields = $data[$tbl_name];
                    foreach($fields as $fEnt){
                        $field = $fEnt['Field'];
                        $margeFileds[] = $field;
                    }
                }
            }
            
            $margeFileds = array_unique($margeFileds); // 重複を除去
            
            $allFields[$tbl_name] = $margeFileds;
        }

        return $allFields;
    }
    
    
    
    // テーブル名比較表の作成
    public function outputTblDiffHTbls(&$dbs, &$allTblNames){

        $db_name_ths_str = '';
        
        $tbody_html = '';
        
        foreach($dbs as $db_name => $dbEnt){
            $db_name_ths_str .= "<th>{$db_name}</th>";
        }
        
        foreach($allTblNames as $tbl_name){
            $tds_html = '';
            foreach($dbs as $db_name => $dbEnt){
                $tblNames = $dbEnt['tblNames'];
                $res = "<span class='danger'>×</span>";
                if(in_array($tbl_name, $tblNames)) $res = '○';
                
                $tds_html .= "<td>{$res}</td>";
            }
            $tbody_html .= "<tr><td>{$tbl_name}</td>{$tds_html}</tr>";
        }
        
        
        // テーブル名比較一覧
        echo "
            <table class='tbl2'>
                <thead><th>テーブル名</th>{$db_name_ths_str}</thead>
                <tbody>{$tbody_html}</tbody>
            </table>
        ";
    }
    
    
    // フィールド比較表の作成
    public function outputFieldDiffHTbl(&$dbs, &$allTblNames, &$allFields){
        
        $db_name_ths_str = '';
        
        $tbodyHtmlList = [];
        
        foreach($dbs as $db_name => $dbEnt){
            $db_name_ths_str .= "<th>{$db_name}</th>";
        }
        
        foreach($allFields as $tbl_name => $fields){
            
            $tbody_html = '';
            
            foreach($fields as $field){
                $tds_html = '';
                foreach($dbs as $dbEnt){
                    if(!empty($dbEnt['data'][$tbl_name])){
                        $fieldData = $dbEnt['data'][$tbl_name];
                        if($this->checkFieldDataInField($fieldData, $field)){
                            $tds_html .= "<td>○</td>";
                        }else{
                            $tds_html .= "<td><span class='danger'>×</span></td>";
                        }

                    }else{
                        $tds_html .= "<td>-</td>";
                    }
                }
                
                $tbody_html .= "<tr><td>{$field}</td>{$tds_html}</tr>";
            }
            
            $tbodyHtmlList[$tbl_name] = $tbody_html;
        }
        
        foreach($allTblNames as $tbl_name){
            echo "
                <div class='field_tbl_div'>
                    <p>{$tbl_name}</p>
                    <table class='tbl2'>
                        <thead><tr><th>フィールド名</th>{$db_name_ths_str}</thead>
                        <tbody>$tbodyHtmlList[$tbl_name]</tbody>
                    </table>
                </div>
            ";
        }
        

        
    }
    
    
    private function checkFieldDataInField($fieldData, $field){
        
        foreach($fieldData as $fEnt){
            $field2 = $fEnt['Field'];
            if($field == $field2){
                return true;
            }
        }
        
        return false;
        
    }
    
    
    
    
    
    
    
}