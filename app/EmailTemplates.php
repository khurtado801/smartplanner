<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mail;
use Aws\Ses\SesClient;

require 'vendor/autoload.php';
Class EmailTemplates extends Model {

    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $table = 'email_templates';

    /*
     * Added By: Devang Patel
     * Modified Date: 28/12/2015
     * Reason: get template record
     */

    function scopeGetTemplates($id = "") {
        $page = $this->input->get('sEcho');
        $iDisplayLength = $this->input->get('iDisplayLength');

        if ($page != 1 && $page != 0) {
            $start = $page * $iDisplayLength;
            $end = $start + $iDisplayLength;
        } else {
            $start = 1;
            $end = 10;
        }

        $user = email_templates::find($id);

        return $user;
    }

    /*
     * Added By: Amit sanghani
     * Modified Date: 23/06/2015
     * Reason: Add template to database
     */

    function scopeTemplate_insert($data) {
        // Inserting in Table(students) of Database(college)
        DB::table('email_templates')->save($data);
    }

    /*
     * Added By: Amit sanghani
     * Modified Date: 23/06/2015
     * Reason: update template to database
     */

    function scopeTemplate_update($data, $id = "") {
        // update record 
        DB::table('email_templates')
                ->where('id', $id)
                ->update($data);
        /* $this->db->where('id', $id);
          $this->db->update('email_templates', $data); */
    }

    /*
     * Added By: Amit sanghani
     * Modified Date: 23/06/2015
     * Reason: Convert email template title to slug, create slug
     */

    function scopeSlugify($query, $text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        // trim
        $text = trim($text, '-');
        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        // lowercase
        $text = strtolower($text);
        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        if (empty($text)) {
            return 'n-a';
        }

        //DB::enableQueryLog(); 
        $query = DB::table('email_templates')->select('slug')->where('slug', $text)->get();

        $tot = DB::table('email_templates')->select('slug')->where('slug', $text)->count();

        // if record more then one then add integer
        if ($tot > 0) {
            $text = $text . "-" . $tot;
        }

        return $text;
    }

    function scopeGetTemplate($slug) {
        $query = DB::table('email_templates')->select('id,subject as title,slug,content')->where('slug', $slug)->get();

        return $query;
    }

    

    public function SendEmail($params) {

        // $config = array(
        //     'protocol' => 'smtp',
        //     'smtp_host' => 'ssl://smtp.gmail.com',
        //     'smtp_port' => 465,
        //     'smtp_user' => "smartplanner42@gmail.com", //SITE_EMAIL_ID_SHK change it to yours
        //     'smtp_pass' => "Y#nq3Gb6fWRHp%a", // SITE_PASSWORD_SHK change it to yours 
        //     'mailtype' => 'html',
        //     'charset' => 'iso-8859-1',
        //     'wordwrap' => TRUE
        // );

        $email_template = EmailTemplates::where('slug', '=', $params["template"])->first();

        $message = str_replace($params["search"], $params["replace"], $email_template["email_content"]);
       // print_r($message);exit;

        $user = User::where('email', '=', $params['to'])->first();

        /* $sent = Mail::raw($message, function ($m) use ($params) {
          $m->from($params['from'], 'Smartplanner');
          $m->to($params['to'])->subject($params['subject']);
          });
         */
        $mail_data = array('content' => $message, 'toEmail' => $params["to"], 'subject' => $email_template["subject"], 'from' => "amits@mail.com",'file' => $params["file"]);

        /**
         * AWS SES:
         * Make client to sent file.
         * Key and secret in IAM Management Console.
         */
        $client = SesClient::factory(array(

            'region' => 'us-east-1',
            'version' => '2010-12-01',
            'credentials' => array(
                'key' => 'AKIASJETU5OS5VVSVQ4T',
                'secret'  => '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',
            )
        ));

                /**
         * Call to sendEmail with array as single parameter.
         * Required data in array:
         * Source is required,
         * Source 'Destination', 'ToAddresses'is required,
         * Message 'Subject', 'Data' is required,
         * Body, 'Text', 'Data' is required,
         * Body, 'Html', 'Data' is required
         */
        $emailSentId = $client->sendEmail(array(
            'Source' => $mail_data['from'], 'Smartplanner',
            'Destination' => array(
                'ToAddresses' => array($mail_data['toEmail'])
            ),
            'Message' => array(
                'Subject' => array(
                    'Data' => $mail_data['subject'],
                    'Charset' => 'UTF-8',
                ),
                'Body' => array(
                    'Text' => array(
                        'Data' => $mail_data['content'],
                        'Charset' => 'UTF-8',
                    ),
                    'Html' => array(
                        'Data' => $mail_data['content'],
                        'Charset' => 'UTF-8',
                    ),
                ),
            ),
            'ReplyToAddresses' => array( 'replyTo@email.com' ),
            'ReturnPath' => 'roxi@evolvededucator.com'
        ));

        //print_r($mail_data);exit;
        $sent = Mail::send('emails.mail-template', $mail_data, function($message) use ($mail_data) {
                    $message->from($mail_data['from'], 'Smartplanner');
                    $message->to($mail_data['toEmail']);
                    $message->subject($mail_data['subject']);
                    if($mail_data["file"]!=""){
                        //$message->attach($mail_data["file"],);

                        $message->attach($mail_data["file"], ['as' => "invoice.pdf", 'mime' => "application/pdf"]
                        );
                        

                    }
                });

        if ($sent == true) {
            return true;
        } else {
            show_error($this->email->print_debugger());
            return false;
        }
    }

}

?>
