<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Pager Library is capable to draw Grid 
 * with help of jquery.datatable plugin. which allows client side searching, sorting, pagination, etc.
 * will write more if i have time. 
 * @name Class JqueryPager
 * @todo [Documentation] (Developer Loves this part..)  
 * @author Neelay
 * @since 5/06/2012
 **/

class Jquerypager {
    
    protected $idPager;
    protected $pageSize;
    protected $PagerHtml;
    protected $PagerScript;
    protected $remotePaging;
    protected $paginSource;
    protected $HTML;
    
    protected $num_cols;
    
    #Data variable for header footer and body
    protected $thData;
    protected $tdData;
    protected $thFooterData;
    protected $tbLink;    
    
    protected $header;
    protected $footer;
    protected $records;    
    
    
    #Html Variable
    protected $title;
    protected $plugin;
    protected $thBody;
    protected $thFooter;
    protected $tdBody;
    protected $ftBody;
    protected $tableStart;
    protected $tableEnd;
    protected $loadImage;
    protected $buttons;

    #Flags
    protected $dlgFlag;       // Overlay Flag
    protected $btnFlag;       // Button Flag
    protected $cnfFlag;       // Confirm Flag
    protected $olyFlag;        // Confirm Flag
    protected $pagerRefreshFlag;// Pager Refresh Flag
    protected $differFlag;     // Differ Flag   
	
    protected $displayTotal;     // Differ Flag   
    protected $differIndex;    // index name to differentiate row
    
    protected $gridDivId;
    
	/**
     * Initializes all pager variables
	 * @name Constructor
	 * @param PagerId
	 **/
    //function __construct($jqplugin=''){
    function __construct(){        
	   $this->header = array();
       $this->thData = array();
       $this->tdData = array();
       $this->records = array();
       /*if(!empty($jqplugin)){
        $this->plugin = $jqplugin;
        print_r($jqplugin);
       }
       */       
       $this->title = "";
       $this->buttons = "";	
       $this->dlgFlag = false;
       $this->btnFlag = false;
       $this->cnfFlag = false;
       $this->olyFlag = false;
       $this->differFlag = false;
       $this->pagerRefreshFlag = false;
       $this->remotePaging = false;
       $this->displayTotal = false;
       $this->loadImage = "<img border='0' src='/static/images/loading_arrows.gif'/>";
       $this->gridDivId = 'grid_div_id_'.uniqid();
	}
    
    
    /**
     * Assign Value to pager variables
	 * @name setRemotePaging
	 * @param $pageSource=remore url; $pagesize;
     * @since 5/06/2010
	 **/ 
    function setRemotePaging($pageSource='',$limit=20)
    {
        $this->pagingSource = $pageSource; 
        $this->remotePaging = true;      
        $this->pageSize = $limit;                   
    }
    
    
    /**
     * prpare query for remote search, sort and paging
	 * @name preparePagerQuery
	 * @param $query='';$select=array();$from=array();$where=array();$orderby=array();
     * @since 12/27/2010
	 **/
    function preparePagerQuery($query='',$select=array(),$from=array(),$where=array(),$orderby=array())
    {
        $sSearch = $_GET['sSearch'];
        $start = $_GET['iDisplayStart'];
        $length = $_GET['iDisplayLength'];
        $arrParams['start'] = $start;
        $arrParams['limit'] = $length;
        $sWhere = "";
        $sQuery = "";
        if(empty($query))
        {
            $sQuery .= $this->_getSelect($select);
            $sQuery .= " ".$this->_getFrom($from);
        }else{
            $sQuery = $query;
        }
        
        if(!empty($where)){
            $sWhere .= "( ".$this->_getWhere($where)." )";
        }
        
        if(!empty($sSearch))
    	{
            foreach($this->header as $key=>$val){
                if(!empty($sWhere)){
                    $sWhere .= " OR ";
                }    
                $sWhere .= " $key LIKE '%".$sSearch."%' ";
            }
            if(!empty($sWhere)){
                $sWhere = "where $sWhere";
            }
    	}
        $d = array_keys($this->header);
        
        $sOrder = "";
        if (isset($_GET['iSortCol_0']) )
    	{    		
    		for ( $i=0 ; $i < $_GET['iSortingCols'] ; $i++ )
    		{
    			$sOrder .= ($d[$_GET['iSortCol_'.$i]])." ".$_GET['sSortDir_'.$i].", ";
    		}
    		$sOrder = substr_replace( $sOrder, "", -2 );
            $sOrder = " ORDER BY ".$sOrder;
    	} 
        
        
        return $sQuery." ".$sWhere." ".$sOrder;
    }
    
    
    
    /**
     * Assign Value to pager variables
	 * @name setPager
	 * @param $pagesize: number of rows per page; PagerId; Header; RecordSet; $highLight;
     * @since 5/06/2010
	 **/  
    function setPager($pagerID,$pagesize,$header,$data=array(),$highLight=false)
    {        
        $this->idPager = $pagerID;
        $this->thData = $header;
        
		if(!$this->remotePaging)
            $this->pageSize = $pagesize;
            
        if(!$this->remotePaging)
            $this->tdData = $data;
        
        if($highLight){$highLightClass = " ui-state-error";}
        
        $this->tableStart = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" id=\"".$this->idPager."\" class=\"display dataTable $highLightClass\">\n";
        $this->tableEnd = "</table>";
    }
    
    
    /**
     * Set Header
	 * @name setHeader
	 * @param $header : array of index=>Displayed_translation, same order as displayed columns 
     * @since 5/06/2010
	 **/
    function setHeader($header) {        
		foreach ($header as $key=>$txt)
		{
			$this->header[$key]['title']=$txt;
		}
		$this->num_cols=count($header);
	}
    
    /**
	 * @name setTitle
	 * @param $title = Pager Title
     * @since 5/06/2010
	 **/
    function setTitle($title) {
		$this->title = "<h3 class='ui-accordion-header ui-helper-reset ui-state-active ui-corner-top'>".$title."</h3>";
	}
    
    
    /**
	 * @name setData
	 * @param $record : array of index=>Displayed_translation, same order as displayed columns 
     * @since 5/06/2010
	 **/
    function setData($data){
        if(!is_array($data))
            return false;        
        $this->records = $data;
    }            
    
    
    /**
     * May be needed in future
	 * @name setFooter
	 * @param $fData : column_key = total where we need sum of it [for now]
     * @since 5/06/2010
	 **/
    function setFooter($fData){
        if(is_array($fData) && !empty($fData)){
            $this->footer = $fData;
            $this->displayTotal = true;
        }
    }
    
    /**
	 * @name setJqPagerParameter
	 * @abstract set Parameter for jquery pager
     * @since 5/10/2010
     * @param $jqUI = 'true'|'false' use jquery ui interface(style), $sort=array('column_index'=>'asc|desc'))
	 **/
    function setJqPagerParameter($jqUI = 'false',$sort=array(),$aoColumns=array(),$searchbar=true,$pagination=true){
        $this->PagerScript =  "
            var RefreshUrl = \"\";
            var RefreshDiv = \"$this->gridDivId\";
        \n";
                
        $this->PagerScript .=  "
        $(function() {    
            
            jQuery.fn.center = function () {
            this.css(\"position\",\"fixed\");
            this.css(\"top\", ( $(window).height() - this.height() ) / 2 + \"px\");
            this.css(\"left\", ( $(window).width() - this.width() ) / 2 + \"px\");
            
                $('#close_icon').css(\"top\", ((($(window).height() - this.height()) / 2)- 15) + \"px\");
                $('#close_icon').css(\"left\", ((($(window).width() - this.width()) / 2) - 15) + \"px\");
            
            return this;
            }
            
        var $this->idPager = $('#".$this->idPager."').dataTable( {";
    			
                $this->PagerScript .= "\"bJQueryUI\": $jqUI";
                $this->PagerScript .= ",\"sPaginationType\": \"full_numbers\",";
                
                $this->PagerScript .= "                
    			\"iDisplayLength\": ".$this->pageSize;
                
                if(!$searchbar)
                    $this->PagerScript .= "
                                            ,\"bFilter\": false";
                                            
                if(!$pagination)
                    $this->PagerScript .= "
                                            ,\"bPaginate\": false";
        
        if(!empty($aoColumns)){
            $this->PagerScript .= ",
                \"aoColumns\" : [";
                                
                $aoColumnsStr = "";
                foreach($aoColumns as $val){
                    if($aoColumnsStr != ""){$aoColumnsStr .= ", ";}
                    if(is_array($val)){
                        $aoColumnsStr .= "{";
                        $aoIndx = 0;
                        foreach($val as $key1=>$val1){
                            if($aoIndx != 0){$aoColumnsStr .= ", ";}
                            $aoColumnsStr .= "\"".$key1."\" : " . "\"".$val1."\"";  
                            $aoIndx++;
                        }                        
                        $aoColumnsStr .= "}";
                    }else{
                        $aoColumnsStr .= $val;
                    }
                }
                
                $this->PagerScript .= $aoColumnsStr;
                $this->PagerScript .= "
                ]
            ";
        }
        
        if($this->remotePaging){
            $this->PagerScript .= ",
                    'bProcessing': true,
                    'bServerSide': true,
                    'sAjaxSource': '$this->pagingSource'
            ";
        }
        
        if(!empty($sort)){
            $this->PagerScript .= ",
                \"aaSorting\": [";
                $len = sizeof($sort);
                $i = 1;
                foreach($sort as $col=>$order){
                    $this->PagerScript .= "[".$col.", '".$order."']";
                    if($i >= 2){
                        $this->PagerScript .= ", ";
                    }
                    $i++;
                }
                
            $this->PagerScript .= "]";
        }
        
        if($this->displayTotal){
            $this->PagerScript .=  ",
                \"doTotal\": true";
            
            $hKeys = array_keys($this->thData);            
            $stString = ''; $ColIndx = '';
            foreach($this->footer as $kF=>$vF){
                if(!empty($stString)){
                    $stString .= ',';
                    $ColIndx .= ',';
                }
                $ColIndx .= array_search($kF,$hKeys);
                $stString .= "\"".$this->idPager."_footer_".$kF."\"";                
            }
            $this->PagerScript .= ",
                \"totalColIndx\": [".$ColIndx."]";
            $this->PagerScript .= ",
                \"showTotal\": [".$stString."]
            ";    
        }
                        			
        $this->PagerScript .=  "
            });
        ";
        if(!$searchbar && !$pagination){
            $this->PagerScript .=  "\n
            $('.dataTables_wrapper div.fg-toolbar').hide();
            ";
        }
        
        $this->PagerScript .=  "\n
        });";
    }
    
    
        
    /**
	 * @name addLink
	 * @abstract add Link Columns
     * @param array of [title]=> 'Action', [text] => 'Edit', [link] => '../admin/subscriber/', [qstring]=>123, [target] => 'new | self | overlay | confirm | popup | jsfunction'
     * @since 5/10/2010
	 **/
    function addLink($data){
        $this->tbLink = $data;        
    }
    
    
    /**	
     * differentiate field based upon field value.. field value must be boolean[true|false] or [1|0]
	 * @name setDifferentiateCoumn 
     * @param $index = column/index of dataset..
     * @since 5/10/2010
	 **/
    function setDifferentiateCoumn($index=''){
        if(!empty($index)){
            $this->differFlag = true;
            $this->differIndex = $index;
        }
    }
    
    
    /**
     * adds a bottom button for general pager links like a typical ' (+) Add Row ' using jquery-ui css
	 * @name addButton
     * @param $type = add|add_user|save|break|trash|refresh|mail;
     * @param $text = text on button;
     * @param $link = action url
     * @param $target = 'new | self | overlay | confirm | popup'
     * @param $extra = array(height=>100,width=>200)
	 **/  
	function addButton($type,$text="",$link="",$target='blank',$extra=array()){
	    $this->btnFlag = true;
		$path="/static/images/icons/";
		$class="";
		switch ($type){
			case 'add':$class='ui-icon-plusthick';break;
			case 'add_user':$class='ui-icon-person';break;
			case 'save':$class='ui-icon-disk';break;
            case 'break':$class='ui-icon-scissors';break;
            case 'trash':$class='ui-icon-trash';break;
            case 'refresh':$class='ui-icon-refresh';break;
            case 'mail':$class='ui-icon-mail-closed';break;            
		}
        $uniqId = uniqid();
        if(!empty($type)){
            if($this->buttons != ""){$this->buttons .= "&nbsp;&nbsp;";}
            if($target == "overlay"){
                $this->olyFlag = true;
                $this->buttons .= "<a href='Javascript: void(0);' onClick=\"Javascript: openDialog('".$uniqId."')\" url=\"".$link."\" id=\"".$uniqId."\" title=\"".$text."\" class=\"ui-state-default ui-corner-all button_link\">";
            }
            elseif($target == "confirm"){
                $this->cnfFlag = true;
                $this->buttons .= "<a href='Javascript: void(0);' onClick=\"Javascript: openConfirm('".$uniqId."')\" url=\"".$link."\" id=\"".$uniqId."\" title=\"".$text."\" class=\"ui-state-default ui-corner-all button_link\">";
            }
            elseif($target == "popup"){                
                $this->buttons .= "<a href='Javascript: void(0);' onClick=\"Javascript: window.open('$link','$text','width=".$extra['width'].",height=".$extra['height'].",scrollbars=1,toolbar=0');\" id=\"".$uniqId."\" title=\"".$text."\" class=\"ui-state-default ui-corner-all button_link\">";
            }            
            else{
                $this->buttons .= "<a href='".$link."' target=\"_".$target."\" id=\"".$uniqId."\" title=\"".$text."\" class=\"ui-state-default ui-corner-all button_link\">";    
            }
                        
            $this->buttons .= "<span class=\"ui-icon ".$class."\"></span>".$text;
            $this->buttons .= "</a>";
        }        
	}
    
    
    /**
     * get Html for diff part of table[head,body,footer]
	 * @name getHtml
	 **/    
    function getHtml(){
                      
        $this->setHeader($this->thData);
        $this->print_rows('header');                
        if(!empty($this->tdData)){
            $this->setData($this->tdData);
        }
        
        $this->print_rows('body');
        //$this->setFooter();
        $this->print_rows('footer');
        
        //$this->HTML = $this->plugin."\n";
        
        # for Confirm dialog
        if($this->cnfFlag == true)
            $this->_setConfirmParm();
        
        # for overlay dialog    
        if($this->olyFlag == true)
            $this->_setOverlayDialogParm();
            
            
        # for overlay and confirm parm if refresh-url and target-div is not null    
        if($this->pagerRefreshFlag == true)
            $this->_refreshDiv();
            
        $this->HTML .=  "\n\n<script language=\"Javascript\">\n".$this->PagerScript."\n</script>\n\n";
        $this->HTML .= $this->title;
        $this->HTML .= $this->tableStart;
        $this->HTML .= $this->thBody;
        $this->HTML .= $this->tdBody;
        $this->HTML .= $this->ftBody;
        $this->HTML .= $this->tableEnd;
        
        # for Button
        if($this->btnFlag == true){
            $this->HTML .= "\n<p>\n".$this->buttons."\n</p>";
        }
        
        # for Overlay dialog
        if($this->dlgFlag == true || $this->cnfFlag == true)
            $this->HTML .= $this->_getOverlayDiv();
        
        # for Confirm dialog
        if($this->cnfFlag == true || $this->olyFlag){
            $this->HTML .= $this->_getDialogDiv();
        }        
        $Html = $this->HTML;
        $this->_clearParam();
        
        return "<div id=\"$this->gridDivId\">$Html</div>";                 
    }
    
    
    /**
     * return table body data into json formate for jqueryDatatable
	 * @name getAjaxData 
     * @param $data;$iTotalRecords;$iTotalDisplayRecords:filtered records
	 **/
    function getAjaxData($data,$iTotalRecords,$iTotalDisplayRecords)
    {
        $sOutput = '{';
    	$sOutput .= '"sEcho": '.intval($_GET['sEcho']).', ';
    	$sOutput .= '"iTotalRecords": '.$iTotalRecords.', ';
    	$sOutput .= '"iTotalDisplayRecords": '.$iTotalDisplayRecords.', ';
    	$sOutput .= '"aaData": [ ';
    	
        foreach($data as $key=>$aRow)
    	{
            $rowOut = "";
    		foreach($this->header as $hkey=>$hval){
    		  if(!empty($rowOut)){
    		      $rowOut .= ",";      
    		  }
    		  $rowOut .= '"'.(htmlentities($aRow[$hkey],ENT_QUOTES,'UTF-8')).'"';
    		}
            
            if(!empty($this->tbLink)){                        
                foreach($this->tbLink as $val2){
                    $link = array(
                                        'baseLink' => $val2['link'],
                                        'qString' => $val2['qstring']
                    );
        		  if(!empty($rowOut)){
        		      $rowOut .= ",";      
        		  }
        		  $rowOut .= '"'.($url = $this->_genrateLink($link,$aRow,$val2)).'"';
        		}
            }
            
    		$sOutput .= "[".$rowOut."],";
    	}
    	$sOutput = substr_replace( $sOutput, "", -1 );
    	$sOutput .= '] }';
    	
    	return $sOutput;
    }
    
    
    /**
	 * print table rows for header,body,footer
	 * @name print_rows
     * @param $type = 'header|footer|body'
     **/  
    function print_rows($type,$returnHtml=false)
    {
        switch (strtolower($type)) {
			case 'header':                
                if(is_array($this->header)){
                    $this->thBody = "<thead>\n<tr>\n";
                    foreach($this->header as $val){
                        $this->thBody .= "<th>".$val['title']."</th>\n";                        
                    }
                    if(!empty($this->tbLink)){                        
                        foreach($this->tbLink as $val2){
                            $this->thBody .= "<th>".$val2['title']."</th>\n";                       
                        }
                    }
                    $this->thBody .= "</tr>\n</thead>\n";                    
                }                
            break;
            
            case 'body':                
                    $localtdBody = "<tbody>\n";
                    if(is_array($this->records)){
                        $localtdBody .= $this->_getRowsForTableBody($this->records);
                    }
                    $localtdBody .= "</tbody>\n";
                    if($returnHtml){
                        return $localtdBody;
                    }else{
                        $this->tdBody = $localtdBody;    
                    }
            break;
            
            case 'footer':            
                if($this->displayTotal){
                    foreach($this->footer as $kF=>$vF)
                        $this->_getArrayColumnCount($this->records,$kF);
                }
                if(!empty($this->header) && !empty($this->footer)){
                    $this->ftBody = "<tfoot>\n";
                    foreach($this->header as $key=>$value){
                        $fTotal = (!empty($this->footer[$key])) ? $this->footer[$key] : '&nbsp;';
                        $fTotal = round($fTotal, 2); 
                        $this->ftBody  .= "<th id='".$this->idPager."_footer_$key'>$fTotal</th>\n"; 
                    }
                    if(!empty($this->tbLink)){                        
                        foreach($this->tbLink as $val2){
                            $this->ftBody .= "<th>&nbsp;</th>\n";                       
                        }
                    }
                    $this->ftBody .= "</tfoot>\n";
                }
            break;
        }
    }
        
    
    private function _getRowsForTableBody($tableBodyData)
    {
        $localTableBody = "";
        
                    foreach($tableBodyData as $data){
                        if($this->differFlag){                            
                            if(!$data[$this->differIndex] || $data[$this->differIndex] != 1){
                                $class = "class = \"gradeU\"";
                            }
                            else{
                                $class = "";
                            }
                        }
                        $localTableBody .= "<tr $class>\n";
                        foreach($this->header as $key=>$value){
                            $localTableBody .= "<td>".$data[$key]."</td>\n";
                        }
                        if(!empty($this->tbLink)){                        
                            foreach($this->tbLink as $val2){                                
                                $localTableBody .= "<td nowrap align=\"center\">";
                                
                                $link = array(
                                        'baseLink' => $val2['link'],
                                        'qString' => $val2['qstring']
                                );
                                $url = $this->_genrateLink($link,$data,$val2);                                 
                                $localTableBody .= $url."</td>\n";
                            }
                        }
                        $localTableBody .= "</tr>\n";
                    }
        return $localTableBody;
    }
    
    
    /**
     * genrate url string for link from querystring array()
	 * @name _genrateLink 
     * @param $link = array('baseLink'=>'www.abcd.com/home',qString=>array(idUser,idCompany))
     **/ 
    private function _genrateLink($link,$data,$linkData)
    {
        $url = $link['baseLink'];
        if(is_array($link['qString'])){
            foreach($link['qString'] as $qstr){
                if(!empty($qstr)){ 
                    $url .= "/".$data[$qstr];                    
                }                    
            }
        }
        else{
            $url .= "/".$data[$link['qString']];
        }
        
        $linkHtml = $this->_getLinkHtml($linkData,$url,$data);
        return $linkHtml;
    }
    
    
    private function _getLinkHtml($linkData,$url,$data=array())
    {
        $uniqId = uniqid();
        if($linkData['target'] == "overlay"){
            $this->olyFlag = true;
            //if(!empty($linkData['refresh-url']) && !empty($linkData['target-div'])){
            if(!empty($linkData['refresh-url'])){
                $this->pagerRefreshFlag = true;
                $linkHtml .= "<a url=\"".$url."\" class=\"ui-custom-link\" href=\"javascript: void(0);\" onclick=\"Javascript: openDialog('".$uniqId."','".$linkData['refresh-url']."','".$linkData['target-div']."');\" id=\"".$uniqId."\" >";
            }
            else{
                $linkHtml .= "<a url=\"".$url."\" class=\"ui-custom-link\" href=\"javascript: void(0);\" onclick=\"Javascript: openDialog('".$uniqId."','','');\" id=\"".$uniqId."\" >";
            }
        }
        
        elseif($linkData['target'] == "confirm"){
            $this->cnfFlag = true;
            //if(!empty($linkData['refresh-url']) && !empty($linkData['target-div'])){
            if(!empty($linkData['refresh-url'])){
                $this->pagerRefreshFlag = true;
                $linkHtml .= "<a url=\"".$url."\" class=\"ui-custom-link\" href=\"javascript: void(0);\" onclick=\"Javascript: openConfirm('".$uniqId."','".$linkData['refresh-url']."','".$linkData['target-div']."');\" id=\"".$uniqId."\" >";
            }
            else{
                $linkHtml .= "<a url=\"".$url."\" class=\"ui-custom-link\" href=\"javascript: void(0);\" onclick=\"Javascript: openConfirm('".$uniqId."','','');\" id=\"".$uniqId."\" >";
            }
        }
        
        elseif($linkData['target'] == "popup"){
            $linkHtml .= "<a id=\"".$uniqId."\" class=\"ui-custom-link\" onclick=\"Javascript:window.open(\"".$url."\",\"".$linkData['titlebar']."\",\"width=".$linkData['width'].",height=".$linkData['height'].",scrollbars=1,toolbar=0\");' href=\"javascript: void(0);\">";
        }
        
        elseif($linkData['target'] == "jsfunction"){
            $jsfunction = $linkData['function'];
            $pVal = '';
            
            foreach($linkData['param'] as $val){
                if(!empty($pVal))
                    $pVal .= ', ';
                $pVal .= "'".$data[$val]."'";
            }
            $jsfunction .= '('.$pVal.');';
            $linkHtml .= "<a id=\"".$uniqId."\" class=\"ui-custom-link\" onclick=\"".$jsfunction."\" href=\"javascript: void(0);\">";
        }
        
        else{
            $linkHtml .= "<a id=\"".$uniqId."\" class=\"ui-custom-link\" href=\"".$url."\" target=\"_".$linkData['target']."\">";
        }
                                    
        
        if(strtolower($linkData['text']) == "edit" || strtolower($linkData['text']) == "manage"){
            $linkHtml .= "<span class=\"ui-icon ui-icon-pencil\"></span>".$linkData['text'];
        }
        elseif(strtolower($linkData['text']) == "delete" || strtolower($linkData['text']) == "remove"){
            $linkHtml .= "<span class=\"ui-icon ui-icon-trash\"></span>".$linkData['text'];
        }
        elseif(strtolower($linkData['text']) == "create" || strtolower($linkData['text']) == "add"){
            $linkHtml .= "<span class=\"ui-icon ui-icon-plus\"></span>".$linkData['text'];
        }
        elseif(strtolower($linkData['text']) == "download"){
            $linkHtml .= "<span class=\"ui-icon ui-icon-circle-arrow-s\"></span>".$linkData['text'];
        }
        elseif(strtolower($linkData['text']) == "select"){
            $linkHtml .= "<span class=\"ui-icon ui-icon-check\"></span>".$linkData['text'];
        }
        else{
            $linkHtml .= $linkData['text'];    
        }
        
                                        
        //$linkHtml .= $linkData['text']; 
        $linkHtml .= "</a>";
        return $linkHtml; 
    }
        
    
    private function _getOverlayDiv()
    {
        return "\n
        <div id=\"close_icon\" style=\" display:none;\">
            <a onclick=\"$('#overlay_".$this->idPager."_background').click();\" href=\"javascript: void(0);\"><span class=\"ui-icon ui-icon-circle-close\"></span></a>
        </div>\n
        <div class=\"ui-widget-overlay\" style=\"position:fixed; display: none;\" id=\"overlay_".$this->idPager."_background\">\n                        
        </div>
        <div class=\"ui-widget-shadow\" style=\"display: none; position:fixed;\" id=\"overlay_".$this->idPager."\">            
           $this->loadImage
        </div>\n
        ";        
    }
    
    private function _getDialogDiv()
    {
        return "
        <div style=\"display:none;\" id=\"dialog-confirm\" title=\"Confirmation\">
    	   <p><span class=\"ui-icon ui-icon-alert\" style=\"float:left; margin-right:10px;\"></span>Are you sure?</p>
        </div>
        
        <div style=\"display:none;\" id=\"dialog-overlay\" title=\"Dialog\">
        </div>
        ";
    }
    
        
    
    /**
	 * @name setOverlayDialogParm    
	 **/
    function _setOverlayDialogParm()
    {
        $this->setupJqueryDialog('dialog-overlay');
        $this->PagerScript .= "\n
        //var url;
        
        function openDialog(id,refUrl,layer)
        {\n";
            //if($this->pagerRefreshFlag){
                $this->PagerScript .= "\n
                    $('#overlay_".$this->idPager."_background').show();            
                    $('#overlay_".$this->idPager."_background').click(function(){
                        $('#overlay_".$this->idPager."_background').hide();                
                    });    
                ";
            //}
            
        $this->PagerScript .= "    
            if(refUrl != ''){
                RefreshUrl = refUrl;
            }
            if(layer != ''){
                RefreshDiv = layer;
            }
            
            var url = $(\"#\"+id).attr('url');
            $('#dialog-overlay').load(url);
            $('#dialog-overlay').dialog('open');
			return false;

        }\n";
    }
    
    
    
    private function _setConfirmParm()
    {
        $this->setupJqueryDialog('dialog-confirm',true);
        $this->PagerScript .= "\n
        var url;
        
        function openConfirm(id,refUrl,layer)
        {\n";
            //if($this->pagerRefreshFlag){
                $this->PagerScript .= "\n
                    $('#overlay_".$this->idPager."_background').show();            
                    $('#overlay_".$this->idPager."_background').click(function(){
                        $('#overlay_".$this->idPager."_background').hide();                
                    });    
                ";
            //}
            
        $this->PagerScript .= "    
            if(refUrl != ''){
                RefreshUrl = refUrl;
            }
            if(layer != ''){
                RefreshDiv = layer;
            }
            
            url = $(\"#\"+id).attr('url');
            $('#dialog-confirm').dialog('open');
			return false;

        }\n";
        
        $this->PagerScript .= "\n
        function confirmAction(str)
        {
            if(str == 'perform')
            {
                $.post(url,function(data){                
            \n";
        
        //if($this->pagerRefreshFlag){
            $this->PagerScript .= "                
                    if(data == 'true'){
                        refreshDiv(str);
                    }else{
                        //alert(data);
                    }
            \n";
        //}
        
        $this->PagerScript .= "       
                }); 
            }
        }
        \n";
    }
    
    
    
    private function setupJqueryDialog($divid,$confirm=false)
    {
        $this->PagerScript .= "\n
        $(function() {

            $('#$divid').dialog({
                autoOpen: false,
    			resizable: false,
    			modal: true,\n";
       
       if($confirm == true) {
        $this->PagerScript .= "
                buttons: {    				
    				Cancel: function() {
    					$(this).dialog('close');
                        $('#overlay_".$this->idPager."_background').click();
    				},
                    Ok: function() {                        
                        confirmAction('perform'); 
                        $(this).dialog('close');   					
    				}
    			},\n";
       }
       
       $this->PagerScript .= "
                close: function(event, ui) { $('#overlay_".$this->idPager."_background').click(); }
    		});
            
            $('#$divid').bind( 'dialogclose', function(event, ui) {
              $('#overlay_".$this->idPager."_background').click();
            });
        });
        \n";
    }
    
    private function _refreshDiv()
    {
        $this->PagerScript .= "\n
        function refreshDiv(str)
        {            
            if(str == 'perform')
            {                                
                if(RefreshDiv != ''){
                $('#overlay_".$this->idPager."').show('slow');
                $('#overlay_".$this->idPager."').css('height','');
                $('#overlay_".$this->idPager."').css('width','');
                $('#overlay_".$this->idPager."').css('top',$(window).height()/2);
                $('#overlay_".$this->idPager."').css('left',$(window).width()/2);
                $('#overlay_".$this->idPager."').html(\"<img border='0' src='/static/images/loading_arrows.gif'/> Please Wait\");
                                                            
                    $(\"#\"+RefreshDiv).load(RefreshUrl,function(response, status, xhr){
                                if(status == 'success'){";
                                if($this->pagerRefreshFlag){
                                    $this->PagerScript .= "\n
                                        $('#overlay_".$this->idPager."_background').click();
                                    ";
                                }
            $this->PagerScript .= "\n                            
                                }
                    });
                }
                else{
                    if(RefreshUrl != \"\")
                        $(window.location).attr('href', RefreshUrl);
                }    
            }
        }
        \n";
    }
    
    
    private function _getArrayColumnCount($rsArray,$index)
    {
        if(empty($rsArray) || empty($index)){return false;}
        $this->footer[$index] = 0;
        foreach($rsArray as $key=>$val){
            $this->footer[$index] += $val[$index];
        }
    }
    
    
    private function _getSelect($select=array())
    {
        if(empty($select)){return false;}
        $sSelect = "";
        if(is_array($select)){
            foreach($select as $val){
                if(!empty($sSelect))
                    $sSelect .= ", ";
                $sSelect .= $val;
            }
        }else{
            $sSelect = $select;
        }
        return "SELECT ". $sSelect;
    }
    
    
    private function _getFrom($from=array())
    {
        if(empty($from)){return false;}
        $sFrom = "";
        if(is_array($from)){
            foreach($from as $val){
                if(!empty($sFrom))
                    $sFrom .= ", ";
                $sFrom .= "`".$val."`";
            }
        }else{
            $sFrom = $from;
        }
        return "FROM ". $sFrom;
    }
    
    private function _getWhere($where=array())
    {
        if(empty($where)){return falsee;}
        if(is_array($where)){
            $sWhere = "";
            foreach($where as $key=>$val){
                if(!empty($sWhere)){
                    $sWhere .= " AND ";
                }
                $sWhere .= " $key ";
                if(!$this->_has_operator($key))
                    $sWhere .= " = ";
                
                $sWhere .= _mysql_escape_mimic($val); 
            }                
        }else{
            $sWhere = $where;
        }          
        return $sWhere;  
    }
    
    private function _has_operator($str)
    {
        $str = trim($str);
        if ( ! preg_match("/(\s|<|>|!|=|is null|is not null)/i", $str))
        {
            return FALSE;
        }

        return TRUE;
    }
    
    
    private function _mysql_escape_mimic($inp) 
    {
        if(is_array($inp))
            return array_map(__METHOD__, $inp);
    
        if(!empty($inp) && is_string($inp)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp);
        }
    
        return $inp;
    } 

    
    private function _clearParam()
    {
        $this->HTML = "";
        $this->header = array();
        $this->plugin = "";
        $this->title = "";
        $this->buttons = "";	
        $this->tbLink = "";
        $this->dlgFlag = false;
        $this->btnFlag = false;
        $this->cnfFlag = false;
        $this->differFlag = false;
        $this->remotePaging = false;
        $this->pagerRefreshFlag = false;
    }
}