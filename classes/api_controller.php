<?php
namespace modules\core\classes;
use modules\core\interfaces as i;


/**
 * Exactly the same as a regular controller but optimised for delivering JSON
 *
 * @author lordmatt
 */
abstract class api_controller extends controller implements i\controller {
    
    /**
     * If false the API returns a failure status and the script will stop.
     * @param string $what
     * @return boolean 
     */
    protected function can_do_that($what){
        $allow=FALSE;
        if($this->is_allowed('action','api',TRUE)){
            if(is_string($what)){
                if($this->is_allowed('api',$what,TRUE)){
                    $allow=TRUE;
                }
            }else{
                $allow=TRUE;
            }
        }    
        if(!$allow){
            // otherwise NO!!!
            $this->view_set('code', -1);
            $this->view_set('status', 'error');
            $this->view_set('error', 'You do not have permission to do that.');
            $this->api_send(); // ends script
            return FALSE; // this never actually happens
        }
        return $allow;
    }
    /**
     * Set the status value to success in the API return stack
     * @param int $code (optional)
     * @return \modules\core\classes\api_controller 
     */
    protected function success($code=0){
            $this->view_set('code', $code);
            $this->view_set('status', 'success');
            return $this;
    }
    /**
     * Set the status value to fail in the API return stack
     * @param type $code
     * @return \modules\core\classes\api_controller 
     */
    protected function failure($code=-1){
            $this->view_set('code', $code);
            $this->view_set('status', 'failure');
            return $this;
    }
    /**
     * Sends the API response and teminates the script. 
     */
    protected function api_send(){
        header('Content-Type: application/json');
        echo json_encode($this->view_data);
        exit();
    }
    
}
