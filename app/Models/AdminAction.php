<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $description
 * @property string $remark     
 */
class AdminAction extends Model
{
    public $timestamps = false;
    protected $table = TBL_ADMIN_ACTION;
    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['description', 'remark','id'];
    
    /**
     *
     * Activity Constants
     *
     */
    public $ADMIN_LOGIN = 1;
    public $ADMIN_LOGOUT = 2;
    public $UPDATE_PROFILE = 3;
    public $UPDATE_CHANGE_PASSWORD = 4;
    
    public $ADD_ADMIN_ACTION = 5;
    public $EDIT_ADMIN_ACTION = 6;
    public $DELETE_ADMIN_ACTION = 7;

    public $ADD_ADMIN_MODULES_PAGES = 11;
    public $EDIT_ADMIN_MODULES_PAGES = 12;
    public $DETELE_ADMIN_MODULES_PAGES = 13;

    public $UPDATE_RIGHTS = 14;   

    public $ADD_ADMIN_MODULES = 15;  
    public $EDIT_ADMIN_MODULES = 16;
    public $DELETE_ADMIN_MODULES = 17;   

    public $ADD_USER_TYPE = 30;   
    public $EDIT_USER_TYPE = 31;   
    public $DELETE_USER_TYPE = 32;

    public $ADD_PROJECT = 18; 
    public $EDIT_PROJECT = 19; 
    public $DELETE_PROJECT = 20;

    public $ADD_LEAVE_REQUEST = 33;
    public $EDIT_LEAVE_REQUEST = 34;
    public $DELETE_LEAVE_REQUEST = 35;

    public $ADD_CLIENT = 36;
    public $EDIT_CLIENT = 37;
    public $DELETE_CLIENT = 38;

    public $ADD_CLIENT_USER = 39;
    public $EDIT_CLIENT_USER = 40;
    public $DELETE_CLIENT_USER = 41;
    
    public $ADD_TASKS = 42;
    public $EDIT_TASKS = 43;
    public $DELETE_TASKS = 44;

    public $ADD_EMP_DOCUMENT = 45;
    public $EDIT_EMP_DOCUMENT = 46;
    public $DELETE_EMP_DOCUMENT = 47;
   
    public $ADD_USERS = 27;
    public $EDIT_USERS = 28;
    public $DELETE_USERS = 29;
    
    public $ADD_PROJECT_CREDENTIAL = 48;
    public $EDIT_PROJECT_CREDENTIAL = 49;
    public $DELETE_PROJECT_CREDENTIAL = 50;
	
	public $ADD_SALARY_SLIP = 51;
    public $EDIT_SALARY_SLIP = 52;
    public $DELETE_SALARY_SLIP = 53;
	
	public $ADD_HOLIDAYS = 54;
    public $EDIT_HOLIDAYS = 55;
    public $DELETE_HOLIDAYS = 56;
	
	public $ADD_ESTIMATE_TASK = 57;
    public $EDIT_ESTIMATE_TASK = 58;
    public $DELETE_ESTIMATE_TASK = 59;
	
	public $ADD_EXPENSE = 61;
    public $EDIT_EXPENSE = 62;
    public $DELETE_EXPENSE = 63;
	
	public $ADD_INVOICE = 64;
    public $EDIT_INVOICE = 65;
    public $DELETE_INVOICE = 66;
	
	public $ADD_APPRAISAL_FORM = 67;
    public $EDIT_APPRAISAL_FORM = 68;
	
	public $ADD_MEMBER = 69;
    public $EDIT_MEMBER = 70;
    public $DELETE_MEMBER = 71;
    public $GENERATE_OTP = 75;
    public $SEND_SMS = 76;
    public $MEMBER_STATUS = 77;
	
    public $ADD_FAMILY_MEMBER = 72;
    public $EDIT_FAMILY_MEMBER = 73;
    public $DELETE_FAMILY_MEMBER = 74;
	
  

     
}
