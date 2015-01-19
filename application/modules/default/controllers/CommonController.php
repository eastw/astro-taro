<?php
//TODO: rework all front ajax requests for this controller
class CommonController extends Zend_Controller_Action {

    protected $pollService;

    public function init(){
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function pollResultAction(){
        $service = App_PollService::getInstance();
        $result = $service->incrementValues($this->_getParam('values',false));
        echo Zend_Json::encode($result);
    }

    public function getCardDescriptionAction(){
        $data = json_decode($this->_getParam('data',false));

        $this->divinationService = new App_DivinationService();

        $error = false;
        if(is_array($data)) {
            if(isset($data[0]->divination_id) && is_numeric($data[0]->divination_id)){
                $positions = array();
                foreach($data as $item){
                    if(isset($item->card_number) && is_numeric($item->card_number)){
                        $positions[] = $item->card_number;
                    }else{
                        $error = true;
                        break;
                    }
                }
                if(!$error) {
                    $divination = $this->divinationService->getDivinationById($data[0]->divination_id);
                    $cardsData = $this->divinationService->getCardsByDivinationId($data[0]->divination_id);
                    $divinationData = $this->divinationService->getDivinationDataItemByPositions($positions, $data[0]->divination_id);

                    $matches = array();
                    if ( in_array($divination['type'],array('classic','lenorman')) )
                    {
                        $matches = $this->divinationService->getMatchesByPositionsAndDivinationId($positions, $data[0]->divination_id);
                    }

                    //join with $divinationData with $cardsData
                    foreach($divinationData as $index => $item)
                    {
                        //card position and description
                        foreach($cardsData as $cardItem)
                        {
                            if( ($index + 1) == $cardItem['alignment_position']){
                                $divinationData[$index]['alignment_position'] = $index+1;//$cardItem['alignment_position'];
                                $divinationData[$index]['position_desc'] = $cardItem['position_desc'];
                                break;
                            }
                        }
                        //card side and deck
                        foreach($data as $dataItem)
                        {
                            if($dataItem->card_number == $item['deck_position']){
                                $divinationData[$index]['side'] = $dataItem->side;
                                $divinationData[$index]['deck'] = $dataItem->deck;
                            }
                        }
                        if ( in_array($divination['type'],array('classic','lenorman')) && $divination['matches'] == 'y')
                        {
                            //join with matches
                            $divinationData[$index]['match'] = $matches[$index]['description'];
                        }
                    }
                }
            }else{
                $error = true;
            }
        }else{
            $error = true;
        }
        $json = array();
        if(!$error){
            $this->view->divinationData = $divinationData;
            $json['status'] = 'success';
            $json['response'] = $this->view->render('divination' . DIRECTORY_SEPARATOR . 'divination-description-items.phtml');
        }else{
            $json['status'] = 'fail';
            $json['response'] = '';
        }
        echo Zend_Json::encode($json);
    }

}